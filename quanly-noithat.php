<?php
/**
 * Plugin Name: Quản Lý Nội Thất TimberFlow
 * Description: Hệ thống quản lý bán hàng, kho hàng chuẩn MVC.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit; // Bảo mật: Không cho truy cập trực tiếp

// Khởi tạo Session cho hệ thống custom login
add_action('init', 'qln_start_session', 1);
function qln_start_session() {
    if (!session_id()) {
        session_start();
    }
    ob_start();
}

// 1. Nhúng Models
require_once plugin_dir_path(__FILE__) . 'models/User.php';
require_once plugin_dir_path(__FILE__) . 'models/Product.php';
require_once plugin_dir_path(__FILE__) . 'models/Customer.php';
require_once plugin_dir_path(__FILE__) . 'models/Invoice.php';
// 2. Nhúng Repositories
require_once plugin_dir_path(__FILE__) . 'repositories/UserRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/ProductRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/CustomerRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/InvoiceRepository.php';

// 3. Nhúng Controllers
require_once plugin_dir_path(__FILE__) . 'controllers/AuthController.php';
require_once plugin_dir_path(__FILE__) . 'controllers/DashboardController.php';
require_once plugin_dir_path(__FILE__) . 'controllers/MenuController.php';
require_once plugin_dir_path(__FILE__) . 'controllers/ProductController.php';
require_once plugin_dir_path(__FILE__) . 'controllers/CustomerController.php';
require_once plugin_dir_path(__FILE__) . 'controllers/InvoiceController.php';

// Khởi chạy Menu
$menuController = new MenuController();
add_action('admin_menu', [$menuController, 'initMenu']);

// Bắt các request form (POST) từ View
add_action('admin_init', function() {
    $authController = new AuthController();
    $authController->handleRequest();
});