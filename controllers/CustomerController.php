<?php
class CustomerController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }
        $repo = new CustomerRepository();
        $customers = $repo->getAll();
        $roleId = $_SESSION['qln_role_id'];

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        switch ($roleId) {
            case 1: $view_name = 'customer-view.php'; break;
            case 2: $view_name = 'customer-view.php'; break;
            default: $view_name = 'customer-view.php';
        }

        // 2. Gán vào biến $view_content để masterlayout.php sử dụng
        $view_content = $base_view_path . $view_name;

        // 3. Gọi Layout chính (Layout này sẽ tự include Sidebar và $view_content)
        include $base_view_path . 'layout/masterlayout.php';
    }
}