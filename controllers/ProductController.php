<?php
class ProductController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }
        $repo = new ProductRepository();
        $products = $repo->getAll();
        $roleId = $_SESSION['qln_role_id'];

        // Truyền biến ra View
        switch ($roleId) {
            case 1:
                include plugin_dir_path(__FILE__) . '../views/Admin/product-view.php'; // Trang Admin hiện tại
                break;
            case 2:
                include plugin_dir_path(__FILE__) . '../views/Sale/product-view.php';
                break;
            case 3:
                include plugin_dir_path(__FILE__) . '../views/Warehouse/product-view.php';
                break;
        }
    }
}