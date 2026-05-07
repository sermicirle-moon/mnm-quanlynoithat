<?php
class CustomerController {
    private $repo;

    // Phải có hàm này để khởi tạo repo dùng chung
    public function __construct() {
        $this->repo = new CustomerRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }

        $action = $_GET['action'] ?? 'list'; // Mặc định là hiển thị danh sách

        // Chỉ điều hướng, KHÔNG include layout ở đây
        switch ($action) {
            case 'create': 
                $this->showForm(); 
                break;
            case 'store':  
                $this->store(); 
                break;
            case 'edit':   
                $this->showForm($_GET['id']); 
                break;
            case 'update': 
                $this->update($_GET['id']); 
                break;
            case 'delete': 
                $this->delete($_GET['id']); 
                break;
            default:       
                $this->list(); 
                break;
        }
    }

    // 1. Hàm hiển thị danh sách (Thay thế phần code bị lỗi của bạn)
    private function list() {
         $search = sanitize_text_field($_GET['search'] ?? '');
        $loai = sanitize_text_field($_GET['loai'] ?? '');
        $thuong_hieu = sanitize_text_field($_GET['thuong_hieu'] ?? '');
        
        $customers = $this->repo->getAllWithFilters($search, $loai, $thuong_hieu);
        $stats = $this->repo->getStats();
        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        // Tùy theo cấu trúc thư mục của bạn, ví dụ: 'customer/customer-view.php'
        $view_content = $base_view_path . 'customer/customer-view.php'; 
        
        // Gọi Layout chính
        include $base_view_path . 'layout/masterlayout.php';
    }

    // 2. Hiển thị Form Thêm / Sửa
    private function showForm($id = null) {
        $customer = null;
        if ($id) {
            $customer = $this->repo->getById($id);
        }
        
        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'customer/customer-form.php'; 
        include $base_view_path . 'layout/masterlayout.php';
    }

    // 3. Xử lý lưu Thêm mới
    private function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_kh' => sanitize_text_field($_POST['ma_kh']),
                'ten_kh' => sanitize_text_field($_POST['ten_kh']),
                'sdt' => sanitize_text_field($_POST['sdt']),
                'email' => sanitize_email($_POST['email']),
                'dia_chi' => sanitize_text_field($_POST['dia_chi'])
            ];
            $this->repo->create($data);
            wp_redirect(admin_url('admin.php?page=qln-customers')); 
            exit;
        }
    }

    // 4. Xử lý Cập nhật
    private function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ma_kh' => sanitize_text_field($_POST['ma_kh']),
                'ten_kh' => sanitize_text_field($_POST['ten_kh']),
                'sdt' => sanitize_text_field($_POST['sdt']),
                'email' => sanitize_email($_POST['email']),
                'dia_chi' => sanitize_text_field($_POST['dia_chi'])
            ];
            $this->repo->update($id, $data);
            wp_redirect(admin_url('admin.php?page=qln-customers')); 
            exit;
        }
    }

    // 5. Xử lý Xóa
    private function delete($id) {
        $this->repo->delete($id);
        wp_redirect(admin_url('admin.php?page=qln-customers')); 
        exit;
    }
}