<?php
class ProductController {
    private $repo;

    public function __construct() {
        $this->repo = new ProductRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        $action = $_GET['action'] ?? 'list';

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

    private function list() {
        global $wpdb;
        $search = sanitize_text_field($_GET['search'] ?? '');
        $status = sanitize_text_field($_GET['status'] ?? '');
        $category = sanitize_text_field($_GET['category'] ?? '');

        $products = $this->repo->getAllWithFilters($search, $status, $category);
        
        $total_products = count($products);
        $total_inventory_value = 0;
        $almost_out = 0;
        $need_urgent = 0;

        foreach ($products as $p) {
            $total_inventory_value += $p->gia_ban * $p->so_luong_ton;
            if ($p->so_luong_ton > 0 && $p->so_luong_ton < 10) $almost_out++;
            if ($p->so_luong_ton <= 5 && $p->so_luong_ton > 0) $need_urgent++;
        }

        // Lấy danh mục cho dropdown lọc
        $categories = $wpdb->get_results("SELECT id_loai, ten_loai FROM {$wpdb->prefix}qln_loai_sp ORDER BY ten_loai");
        $view_data = compact('products', 'total_products', 'total_inventory_value', 'almost_out', 'need_urgent', 'categories');
        extract($view_data);

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        // 2. Gán vào biến $view_content để masterlayout.php sử dụng
        $view_content = $base_view_path . 'product/product-view.php';

        // 3. Gọi Layout chính (Layout này sẽ tự include Sidebar và $view_content)
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function showForm($id = null) {
        $product = null;
        if ($id) {
            $product = $this->repo->getById($id);
            if (!$product) {
                $_SESSION['qln_error'] = "Sản phẩm không tồn tại!";
                wp_redirect(admin_url('admin.php?page=qln-products'));
                exit;
            }
        }
        global $wpdb;
        $categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_loai_sp ORDER BY id_loai");
        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content   = $base_view_path . 'product/product-form.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    private function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        // Bổ sung dòng này để lấy lỗi trực tiếp từ Database
        global $wpdb; 
        
        // FIX LỖI KHÓA NGOẠI: Nếu không chọn loại SP, nạp NULL thay vì nạp số 0
        $id_loai = !empty($_POST['id_loai']) ? intval($_POST['id_loai']) : null;

        $data = [
            'ma_sp'        => sanitize_text_field($_POST['ma_sp']),
            'ten_sp'       => sanitize_text_field($_POST['ten_sp']),
            'gia_ban'      => intval($_POST['gia_ban']),
            'gia_nhap'     => intval($_POST['gia_nhap']),
            'so_luong_ton' => intval($_POST['so_luong_ton']),
            'hinh_anh'     => sanitize_text_field($_POST['hinh_anh']),
            'trang_thai'   => sanitize_text_field($_POST['trang_thai']),
            'id_loai'      => $id_loai
        ];
        
        if ($this->repo->create($data)) {
            $_SESSION['qln_success'] = "Thêm sản phẩm thành công!";
        } else {
            $db_error = $wpdb->last_error;
            $_SESSION['qln_error'] = "Thêm thất bại! Chi tiết lỗi: " . ($db_error ? $db_error : 'Không xác định được lỗi.');
        }
        
        wp_redirect(admin_url('admin.php?page=qln-products'));
        exit;
    }

    private function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        global $wpdb;
        
        // SỬA LỖI KHÓA NGOẠI: Nếu không chọn loại SP, nạp NULL để tránh làm gãy ràng buộc DB
        $id_loai = !empty($_POST['id_loai']) ? intval($_POST['id_loai']) : null;

        $data = [
            'ma_sp'        => sanitize_text_field($_POST['ma_sp']),
            'ten_sp'       => sanitize_text_field($_POST['ten_sp']),
            'gia_ban'      => intval($_POST['gia_ban']),
            'gia_nhap'     => intval($_POST['gia_nhap']),
            'so_luong_ton' => intval($_POST['so_luong_ton']),
            'hinh_anh'     => sanitize_text_field($_POST['hinh_anh']),
            'trang_thai'   => sanitize_text_field($_POST['trang_thai']),
            'id_loai'      => $id_loai
        ];
        
        $result = $this->repo->update($id, $data);
        
        if ($result !== false) {
            $_SESSION['qln_success'] = "Cập nhật sản phẩm thành công!";
        } else {
            $_SESSION['qln_error'] = "Cập nhật thất bại! Chi tiết lỗi: " . ($wpdb->last_error ? $wpdb->last_error : 'Không xác định.');
        }
        
        wp_redirect(admin_url('admin.php?page=qln-products'));
        exit;
    }

    private function delete($id) {
        if ($this->repo->delete($id)) {
            $_SESSION['qln_success'] = "Xóa sản phẩm thành công!";
        } else {
            $_SESSION['qln_error'] = "Xóa thất bại!";
        }
        wp_redirect(admin_url('admin.php?page=qln-products'));
        exit;
    }
}