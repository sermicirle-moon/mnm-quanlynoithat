<?php
class DashboardController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        $userName = $_SESSION['qln_user_name'];
        $roleId = $_SESSION['qln_role_id'];

        // Nhân viên bán hàng (role = 2) dùng dashboard riêng
        if ($roleId == 2) {
            $this->saleDashboard($userName);
        } else {
            // Admin và kho giữ nguyên view cũ
            $view_path = plugin_dir_path(__FILE__) . '../views/';
            switch ($roleId) {
                case 1: $view_name = 'dashboard/admin-view.php'; break;
                case 3: $view_name = 'dashboard/warehouse-view.php'; break;
                default: $view_name = 'dashboard/admin-view.php';
            }
            $view_content = $view_path . $view_name;
            include plugin_dir_path(__FILE__) . '../layout/masterlayout.php';
        }
    }

    private function saleDashboard($userName) {
        global $wpdb;

        // 1. Số đơn hàng cần xử lý (Đang xử lý hoặc Chờ thanh toán)
        $pending_orders = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->prefix}qln_hoa_don 
            WHERE trang_thai IN ('Đang xử lý', 'Chờ thanh toán')
        ");

        // 2. Doanh thu tháng hiện tại (không tính đơn đã hủy)
        $current_month_revenue = $wpdb->get_var("
            SELECT SUM(tong_tien) FROM {$wpdb->prefix}qln_hoa_don 
            WHERE MONTH(ngay_tao) = MONTH(CURDATE()) 
              AND YEAR(ngay_tao) = YEAR(CURDATE())
              AND trang_thai != 'Đã hủy'
        ");
        $current_month_revenue = $current_month_revenue ?: 0;

        // 3. Số đơn hàng trong tháng (không tính đã hủy)
        $monthly_orders = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->prefix}qln_hoa_don 
            WHERE MONTH(ngay_tao) = MONTH(CURDATE()) 
              AND YEAR(ngay_tao) = YEAR(CURDATE())
              AND trang_thai != 'Đã hủy'
        ");

        // 4. Mục tiêu (có thể thay bằng số thực tế từ bảng setting, tạm hardcode)
        $target_revenue = 50000000;   // 50 triệu
        $target_orders = 50;
        $revenue_percent = ($target_revenue > 0) ? round($current_month_revenue / $target_revenue * 100) : 0;
        $orders_percent = ($target_orders > 0) ? round($monthly_orders / $target_orders * 100) : 0;

        // 5. Tỷ lệ tăng trưởng so với tháng trước
        $last_month_revenue = $wpdb->get_var("
            SELECT SUM(tong_tien) FROM {$wpdb->prefix}qln_hoa_don 
            WHERE MONTH(ngay_tao) = MONTH(CURDATE() - INTERVAL 1 MONTH)
              AND YEAR(ngay_tao) = YEAR(CURDATE() - INTERVAL 1 MONTH)
              AND trang_thai != 'Đã hủy'
        ");
        $last_month_revenue = $last_month_revenue ?: 0;
        $growth = ($last_month_revenue > 0) ? round(($current_month_revenue - $last_month_revenue) / $last_month_revenue * 100) : 0;

        // 6. Dữ liệu biểu đồ doanh thu 7 ngày gần nhất
        $daily_revenue = [];
        $week_days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $week_days[] = date('D', strtotime($date)); // Thứ 2,3,4...
            $revenue = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(tong_tien) FROM {$wpdb->prefix}qln_hoa_don 
                WHERE DATE(ngay_tao) = %s AND trang_thai != 'Đã hủy'
            ", $date));
            $daily_revenue[] = $revenue ?: 0;
        }

        // 7. Danh sách đơn hàng cần xử lý (lấy 5 đơn mới nhất)
        $pending_list = $wpdb->get_results("
            SELECT h.ma_hd, k.ten_kh, h.tong_tien 
            FROM {$wpdb->prefix}qln_hoa_don h
            LEFT JOIN {$wpdb->prefix}qln_khach_hang k ON h.khach_hang_id = k.id
            WHERE h.trang_thai IN ('Đang xử lý', 'Chờ thanh toán')
            ORDER BY h.ngay_tao DESC
            LIMIT 5
        ");

        // 8. Khách hàng tiềm năng (mới trong 30 ngày, chưa có hóa đơn)
        $potential_customers = $wpdb->get_results("
            SELECT c.id, c.ten_kh, c.email, c.sdt, c.ngay_tao
            FROM {$wpdb->prefix}qln_khach_hang c
            LEFT JOIN {$wpdb->prefix}qln_hoa_don h ON c.id = h.khach_hang_id
            WHERE h.id IS NULL
              AND DATE(c.ngay_tao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY c.ngay_tao DESC
            LIMIT 5
        ");

        // Truyền biến vào view
        $view_content = plugin_dir_path(__FILE__) . '../views/dashboard/sale-view.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }
}
?>