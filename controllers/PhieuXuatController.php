<?php
class PhieuXuatController {
    private $repo;

    public function __construct() {
        require_once plugin_dir_path(__FILE__) . '../repositories/PhieuXuatRepository.php';
        $this->repo = new PhieuXuatRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }

        $action = $_GET['action'] ?? 'list';
        switch ($action) {
            case 'create': $this->create(); break;
            case 'store': $this->store(); break;
            case 'deliver': $this->markDelivered($_GET['id']); break;
            case 'cancel': $this->cancel($_GET['id']); break;
            case 'view': $this->view($_GET['id']); break;
            default: $this->list(); break;
        }
    }

    private function list() {
        // Bắt các tham số lọc
        $filters = [
            'search'    => sanitize_text_field($_GET['search'] ?? ''),
            'status'    => sanitize_text_field($_GET['status'] ?? ''),
            'date_from' => sanitize_text_field($_GET['date_from'] ?? ''),
            'date_to'   => sanitize_text_field($_GET['date_to'] ?? '')
        ];

        $limit = 10;
        $trang_hien_tai = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($trang_hien_tai - 1) * $limit;
        
        // Lấy dữ liệu
        $stats = $this->repo->getStats();
        $total_items = $this->repo->getTotalCount($filters);
        $total_pages = ceil($total_items / $limit);
        $phieuXuats = $this->repo->getAllPaginated($limit, $offset, $filters);

        // Tạo URL phân trang giữ nguyên bộ lọc
        $url_params = "";
        foreach ($filters as $k => $v) if($v) $url_params .= "&$k=" . urlencode($v);

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'warehouse/phieu-xuat-list.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    private function create() {
        global $wpdb;
        // Tự động sinh mã chuyến xe
        $ma_px_du_kien = 'PXK-' . date('Ymd') . '-' . rand(100, 999);
        
        // Lấy danh sách đổ vào Dropdown
        $khachHangs = $wpdb->get_results("SELECT id, ten_kh, sdt FROM {$wpdb->prefix}qln_khach_hang", ARRAY_A);
        $nhaVanChuyens = $wpdb->get_results("SELECT id, ten_nvc, sdt_tai_xe, bien_so_xe FROM {$wpdb->prefix}qln_nha_van_chuyen WHERE trang_thai = 1", ARRAY_A);

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'warehouse/phieu-xuat-form.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    private function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $hoa_don_ids = $_POST['hoa_don_ids'] ?? [];
            
            // Validate: Phải chọn ít nhất 1 hóa đơn thì xe mới được chạy
            if (empty($hoa_don_ids)) {
                $_SESSION['qln_error'] = "Bạn phải chọn ít nhất 1 hóa đơn để bốc lên xe!";
                wp_redirect(admin_url('admin.php?page=qln-xuat-kho&action=create')); exit;
            }

            $data = [
                'ma_px' => sanitize_text_field($_POST['ma_px']),
                'khach_hang_id' => intval($_POST['khach_hang_id']),
                'nvc_id' => intval($_POST['nvc_id']),
                'nguoi_tao_id' => $_SESSION['qln_user_id'],
                'dia_chi_giao_hang' => sanitize_textarea_field($_POST['dia_chi_giao_hang']),
                'phi_van_chuyen' => floatval($_POST['phi_van_chuyen']),
                'trang_thai' => 'Chờ xuất kho'
            ];

            if ($this->repo->createPhieuXuat($data, $hoa_don_ids)) {
                $_SESSION['qln_success'] = "Tạo lệnh xuất kho (Gom đơn) thành công! Các hóa đơn đã được khóa.";
            } else {
                $_SESSION['qln_error'] = "Lỗi hệ thống khi tạo phiếu xuất.";
            }
            wp_redirect(admin_url('admin.php?page=qln-xuat-kho')); exit;
        }
    }

    private function markDelivered($id) {
        if ($this->repo->markAsDelivered($id)) {
            $_SESSION['qln_success'] = "Xác nhận giao thành công! Tồn kho đã được trừ thực tế.";
        } else {
            $_SESSION['qln_error'] = "Có lỗi xảy ra khi trừ kho.";
        }
        wp_redirect(admin_url('admin.php?page=qln-xuat-kho')); exit;
    }

    private function cancel($id) {
        if ($this->repo->cancelPhieuXuat($id)) {
            $_SESSION['qln_success'] = "Đã hủy chuyến xe. Các hóa đơn đã được nhả ra thành 'Chờ giao'.";
        }
        wp_redirect(admin_url('admin.php?page=qln-xuat-kho')); exit;
    }

    private function view($id) {
        global $wpdb;
        $phieuXuat = $this->repo->getById($id);
        if (!$phieuXuat) wp_die("Không tìm thấy Phiếu Xuất!");

        $khachHang = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}qln_khach_hang WHERE id = %d", $phieuXuat['khach_hang_id']), ARRAY_A);
        $nvc = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}qln_nha_van_chuyen WHERE id = %d", $phieuXuat['nvc_id']), ARRAY_A);
        
        // Lấy danh sách hóa đơn kẹp trong phiếu xuất này
        $hoaDons = $this->repo->getInvoicesByPhieuXuat($id);

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'warehouse/phieu-xuat-view.php';
        include $base_view_path . 'layout/masterlayout.php';
    }
}