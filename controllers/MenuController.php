<?php
class MenuController {
    public function initMenu() {
        // Menu chính ẩn sidebar mặc định của WP bằng CSS trong View
        add_menu_page(
            'TimberFlow', 'TimberFlow', 'read', 'qln-dashboard',
            [$this, 'renderDashboard'], 'dashicons-store', 2
        );

        // Các sub-menu ẩn (dùng để định tuyến trang Đăng nhập / Đăng ký mà không hiện lên menu WP)
        add_submenu_page('qln-dashboard', 'Đăng nhập', 'Đăng nhập', 'read', 'qln-login', [$this, 'renderLogin']);
        add_submenu_page('qln-dashboard', 'Đăng ký', 'Đăng ký', 'read', 'qln-register', [$this, 'renderRegister']);
        add_submenu_page('qln-dashboard', 'Nhập hàng', 'Nhập hàng', 'read', 'qln-nhap-hang', [$this, 'renderNhapHang']);
    }

    public function renderDashboard() {
        $dashboardController = new DashboardController();
        $dashboardController->index();
    }

    public function renderLogin() {
        include plugin_dir_path(__FILE__) . '../views/login-view.php';
    }

    public function renderRegister() {
        include plugin_dir_path(__FILE__) . '../views/register-view.php';
    }
    public function renderNhapHang() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        $controller = new PhieuNhapController();
        $controller->index();
    }
}