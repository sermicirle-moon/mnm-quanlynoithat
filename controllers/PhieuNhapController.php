<?php
class PhieuNhapController {
    private $repo;

    public function __construct() {
        // Require file repo nếu chưa được require ở file gốc
        require_once plugin_dir_path(__FILE__) . '../repositories/PhieuNhapRepository.php';
        $this->repo = new PhieuNhapRepository();
    }

    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        // Lấy dữ liệu
        $stats = $this->repo->getStats();
        $phieuNhaps = $this->repo->getAll();
        $userName = $_SESSION['qln_user_name'];

        // Gọi View
        include plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-list.php';
    }
}