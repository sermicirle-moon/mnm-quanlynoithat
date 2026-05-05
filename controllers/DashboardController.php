<?php
class DashboardController {
    public function index() {
        // Kiểm tra xem đã đăng nhập chưa, chưa thì đá về trang Login
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        // Lấy dữ liệu tổng quan (Ví dụ: gọi các Repository khác ở đây)
        $userName = $_SESSION['qln_user_name'];
        $roleId = $_SESSION['qln_role_id'];

        // Truyền biến ra View
        switch ($roleId) {
            case 1:
                include plugin_dir_path(__FILE__) . '../views/Admin/admin-view.php'; // Trang Admin hiện tại
                break;
            case 2:
                include plugin_dir_path(__FILE__) . '../views/Sale/sale-view.php';
                break;
            case 3:
                include plugin_dir_path(__FILE__) . '../views/Warehouse/warehouse-view.php';
                break;
        }
    }
}