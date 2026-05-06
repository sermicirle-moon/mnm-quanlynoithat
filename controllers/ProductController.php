<?php
class ProductController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }
        $repo = new ProductRepository();
        $products = $repo->getAll();
        $roleId = $_SESSION['qln_role_id'];
        $userName = $_SESSION['qln_user_name'];

        // 1. Xác định đường dẫn file view nội dung
        $base_view_path = plugin_dir_path(__FILE__) . '../views/product/';
        switch ($roleId) {
            case 1: $view_name = 'product-view.php'; break;
            case 2: $view_name = 'product-view.php'; break;
            case 3: $view_name = 'product-view.php'; break;
            default: $view_name = 'product-view.php';
        }

        // 2. Gán vào biến $view_content để masterlayout.php sử dụng
        $view_content = $base_view_path . $view_name;

        // 3. Gọi Layout chính (Layout này sẽ tự include Sidebar và $view_content)
        include $base_view_path . 'layout/masterlayout.php';
    }
}