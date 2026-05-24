<?php
class DashboardController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        global $wpdb;
        $userName = $_SESSION['qln_user_name'];
        $roleId = $_SESSION['qln_role_id'];
        $view_path = plugin_dir_path(__FILE__) . '../views/';

        if ($roleId == 2) {
            $this->saleDashboard($userName);
            return;
        }

        // --- DASHBOARD ADMIN (ROLE = 1) ---
        if ($roleId == 1) {
            $total_revenue = $wpdb->get_var("SELECT SUM(tong_tien) FROM {$wpdb->prefix}qln_hoa_don WHERE trang_thai = 'Đã thanh toán'") ?: 0;
            $total_orders = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_hoa_don WHERE trang_thai != 'Đã hủy'") ?: 0;
            $total_products = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_san_pham") ?: 0;
            $total_customers = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_khach_hang") ?: 0;

            $recent_invoices = $wpdb->get_results("
                SELECT hd.*, kh.ten_kh 
                FROM {$wpdb->prefix}qln_hoa_don hd
                LEFT JOIN {$wpdb->prefix}qln_khach_hang kh ON hd.khach_hang_id = kh.id
                ORDER BY hd.id DESC LIMIT 5
            ", ARRAY_A);

            $low_stock_products = $wpdb->get_results("
                SELECT ma_sp, ten_sp, so_luong_ton 
                FROM {$wpdb->prefix}qln_san_pham 
                WHERE so_luong_ton < 10 AND trang_thai != 'Không bán'
                ORDER BY so_luong_ton ASC LIMIT 5
            ", ARRAY_A);

            $staff_members = $wpdb->get_results("SELECT id, ho_ten, email, que_quan, role_id FROM {$wpdb->prefix}qln_users ORDER BY id DESC", ARRAY_A);

            $view_content = $view_path . 'dashboard/admin-view.php';
        } 
        
        // --- DASHBOARD NHÂN VIÊN KHO (ROLE = 3) ---
        else if ($roleId == 3) {
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_san_pham") ?: 0;
            $low_stock_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_san_pham WHERE so_luong_ton < 10") ?: 0;
            $pending_stock_in = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_phieu_nhap WHERE trang_thai = 'Chờ xử lý'") ?: 0;
            $pending_stock_out = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}qln_phieu_xuat WHERE trang_thai = 'Chờ xuất kho'") ?: 0;

            // Truy vấn bổ sung danh sách sản phẩm tồn thấp phục vụ Widget kho
            $low_stock_details = $wpdb->get_results("
                SELECT ma_sp, ten_sp, so_luong_ton 
                FROM {$wpdb->prefix}qln_san_pham 
                WHERE so_luong_ton < 10
                ORDER BY so_luong_ton ASC LIMIT 6
            ", ARRAY_A);

            $recent_stock_in = $wpdb->get_results("
                SELECT pn.*, ncc.ten_ncc 
                FROM {$wpdb->prefix}qln_phieu_nhap pn
                LEFT JOIN {$wpdb->prefix}qln_nha_cung_cap ncc ON pn.ncc_id = ncc.id
                ORDER BY pn.id DESC LIMIT 5
            ", ARRAY_A);

            $recent_stock_out = $wpdb->get_results("
                SELECT px.*, kh.ten_kh 
                FROM {$wpdb->prefix}qln_phieu_xuat px
                LEFT JOIN {$wpdb->prefix}qln_khach_hang kh ON px.khach_hang_id = kh.id
                ORDER BY px.id DESC LIMIT 5
            ", ARRAY_A);

            $view_content = $view_path . 'dashboard/warehouse-view.php';
        }

        include $view_path . 'layout/masterlayout.php';
    }

    private function saleDashboard($userName) {
        global $wpdb;
        // Giữ nguyên hàm xử lý dữ liệu cho bộ phận bán hàng cũ
    }
}