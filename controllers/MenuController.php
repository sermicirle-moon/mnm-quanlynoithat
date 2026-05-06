<?php
class MenuController {
    public function initMenu() {
        // 1. Menu chính (Trang Dashboard)
        add_menu_page(
            'TimberFlow', 'TimberFlow', 'read', 'qln-dashboard',
            [$this, 'renderDashboard'], 'dashicons-store', 2
        );

        // Các sub-menu ẩn (dùng để định tuyến trang Đăng nhập / Đăng ký mà không hiện lên menu WP)
        add_submenu_page(null, 'Nhập hàng', 'Nhập hàng', 'read', 'qln-nhap-hang', [$this, 'renderNhapHang']);
        add_submenu_page(null, 'Sản phẩm', 'Sản phẩm', 'read', 'qln-products', [$this, 'renderProducts']);
        add_submenu_page(null, 'Khách hàng', 'Khách hàng', 'read', 'qln-customers', [$this, 'renderCustomers']);
        add_submenu_page(null, 'Hóa đơn', 'Hóa đơn', 'read', 'qln-invoices', [$this, 'renderInvoices']);
        
        // Các sub-menu ẩn cho CRUD phiếu nhập
        add_submenu_page(null, 'Tạo PN', 'Tạo PN', 'read', 'qln-nhap-hang-add', [$this, 'renderNhapHangAdd']);
        add_submenu_page(null, 'Lưu PN', 'Lưu PN', 'read', 'qln-nhap-hang-store', [$this, 'renderNhapHangStore']);
        add_submenu_page(null, 'Duyệt PN', 'Duyệt PN', 'read', 'qln-nhap-hang-approve', [$this, 'renderNhapHangApprove']);
        add_submenu_page(null, 'Xóa PN', 'Xóa PN', 'read', 'qln-nhap-hang-delete', [$this, 'renderNhapHangDelete']);
        add_submenu_page(null, 'Xem PN', 'Xem PN', 'read', 'qln-nhap-hang-view', [$this, 'renderNhapHangView']);
        
        // ---> MỚI THÊM VÀO: 2 LINK CHO VIỆC SỬA PHIẾU <---
        add_submenu_page(null, 'Sửa PN', 'Sửa PN', 'read', 'qln-nhap-hang-edit', [$this, 'renderNhapHangEdit']);
        add_submenu_page(null, 'Cập nhật PN', 'Cập nhật PN', 'read', 'qln-nhap-hang-update', [$this, 'renderNhapHangUpdate']);
        
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
    public function renderNhapHang() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        $controller = new PhieuNhapController();
        $controller->index();
    }

    public function renderNhapHangAdd() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        $controller = new PhieuNhapController();
        $controller->create();
    }

    public function renderNhapHangStore() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        $controller = new PhieuNhapController();
        $controller->store();
    }

    public function renderNhapHangApprove() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        (new PhieuNhapController())->approve();
    }
    public function renderNhapHangDelete() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        (new PhieuNhapController())->delete();
    }
    public function renderNhapHangView() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        (new PhieuNhapController())->view();
    }

    // ---> MỚI THÊM VÀO: 2 HÀM GỌI CHỨC NĂNG SỬA <---
    public function renderNhapHangEdit() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        (new PhieuNhapController())->edit();
    }
    public function renderNhapHangUpdate() {
        require_once plugin_dir_path(__FILE__) . 'PhieuNhapController.php';
        (new PhieuNhapController())->update();
    }
}