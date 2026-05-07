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

// AJAX: Lấy danh sách Hóa đơn "Chờ giao" để Gom đơn vào Phiếu xuất
add_action('wp_ajax_qln_get_pending_invoices', 'qln_get_pending_invoices_callback');
function qln_get_pending_invoices_callback() {
    global $wpdb;
    $kh_id = isset($_POST['khach_hang_id']) ? intval($_POST['khach_hang_id']) : 0;
    
    // Chỉ lấy hóa đơn chưa hủy, chưa hoàn tiền và chưa có ai giao
    $sql = "SELECT id, ma_hd, tong_tien, ngay_tao 
            FROM {$wpdb->prefix}qln_hoa_don 
            WHERE khach_hang_id = %d 
            AND trang_thai != 'Đã hủy' AND trang_thai != 'Hoàn tiền' 
            AND (trang_thai_giao = 'Chờ giao' OR trang_thai_giao IS NULL)";
            
    $invoices = $wpdb->get_results($wpdb->prepare($sql, $kh_id), ARRAY_A);
    
    // Format lại ngày tháng cho đẹp trước khi gửi về giao diện
    if ($invoices) {
        foreach ($invoices as &$inv) {
            $inv['ngay_tao'] = date('d/m/Y H:i', strtotime($inv['ngay_tao']));
        }
    }
    
    wp_send_json_success($invoices);
}

// 1. Nhúng Models
require_once plugin_dir_path(__FILE__) . 'models/User.php';
require_once plugin_dir_path(__FILE__) . 'models/Product.php';
require_once plugin_dir_path(__FILE__) . 'models/Customer.php';
require_once plugin_dir_path(__FILE__) . 'models/Invoice.php';
require_once plugin_dir_path(__FILE__) . 'models/InvoiceDetail.php';

// 2. Nhúng Repositories
require_once plugin_dir_path(__FILE__) . 'repositories/UserRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/ProductRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/CustomerRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/InvoiceRepository.php';
require_once plugin_dir_path(__FILE__) . 'repositories/InvoiceDetailRepository.php';

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
add_action('wp_ajax_qln_get_product_stock', function() {
    if (!isset($_POST['product_id'])) wp_die('0');
    $product_id = intval($_POST['product_id']);
    $repo = new ProductRepository();
    $stock = $repo->getStock($product_id);
    echo $stock;
    wp_die();
});
/**
 * Định dạng số tiền rút gọn (K, M, B)
 */
function qln_format_compact_money($amount) {
    if ($amount >= 1e9) {
        return round($amount / 1e9, 1) . ' B';
    }
    if ($amount >= 1e6) {
        return round($amount / 1e6, 1) . ' M';
    }
    if ($amount >= 1e3) {
        return round($amount / 1e3, 0) . 'K';
    }
    return (string)$amount;
}
?>