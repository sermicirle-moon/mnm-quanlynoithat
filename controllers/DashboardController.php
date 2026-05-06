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

        // Xác định file view con dựa trên Role
        $view_path = plugin_dir_path(__FILE__) . '../views/dashboard/';
        switch ($roleId) {
            case 1: $view_name = 'admin-view.php'; break;
            case 2: $view_name = 'sale-view.php'; break;
            case 3: $view_name = 'warehouse-view.php'; break;
            default: $view_name = 'admin-view.php';
        }

        // Tạo biến $view_content để masterlayout.php sử dụng
        $view_content = $view_path . $view_name;

        // Gọi file Master Layout - File này sẽ tự include Sidebar và $view_content
        // Đảm bảo đường dẫn này đúng với vị trí file masterlayout.php của bạn
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php'; 
    
    }
}