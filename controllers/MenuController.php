<?php
class MenuController {
    public function initMenu() {
        // 1. Menu chính (Trang Dashboard)
        add_menu_page(
            'TimberFlow', 'TimberFlow', 'read', 'qln-dashboard',
            [$this, 'renderDashboard'], 'dashicons-store', 2
        );

        // 2. Các sub-menu ẩn (null) - Đã SỬA LẠI SLUG CÓ CHỮ "s" ĐỂ KHỚP VỚI LINK TRONG VIEW
        add_submenu_page(null, 'Sản phẩm', 'Sản phẩm', 'read', 'qln-products', [$this, 'renderProducts']);
        add_submenu_page(null, 'Khách hàng', 'Khách hàng', 'read', 'qln-customers', [$this, 'renderCustomers']);
        add_submenu_page(null, 'Hóa đơn', 'Hóa đơn', 'read', 'qln-invoices', [$this, 'renderInvoices']);
        
        // 3. Các trang hệ thống
        add_submenu_page(null, 'Đăng nhập', 'Đăng nhập', 'read', 'qln-login', [$this, 'renderLogin']);
        add_submenu_page(null, 'Đăng ký', 'Đăng ký', 'read', 'qln-register', [$this, 'renderRegister']);
        add_submenu_page(null, 'Đăng xuất', 'Đăng xuất', 'read', 'qln-logout', function(){});
        }

    // --- CÁC HÀM XỬ LÝ GỌI CONTROLLER TƯƠNG ỨNG ---

    public function renderDashboard() {
        $controller = new DashboardController();
        $controller->index();
    }

    public function renderProducts() {
        $controller = new ProductController();
        $controller->index();
    }

    public function renderCustomers() {
        $controller = new CustomerController();
        $controller->index();
    }

    public function renderInvoices() {
        $controller = new InvoiceController();
        $controller->index();
    }

    public function renderLogin() {
        include plugin_dir_path(__FILE__) . '../views/login-view.php';
    }

    public function renderRegister() {
        include plugin_dir_path(__FILE__) . '../views/register-view.php';
    }
}