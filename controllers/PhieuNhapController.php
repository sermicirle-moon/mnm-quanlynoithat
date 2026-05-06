<?php
class PhieuNhapController {
    private $repo;

    public function __construct() {
        require_once plugin_dir_path(__FILE__) . '../repositories/PhieuNhapRepository.php';
        $this->repo = new PhieuNhapRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }
        global $wpdb;

        $stats = $this->repo->getStats();
        
        // --- 1. BẮT TẤT CẢ THÔNG TIN TÌM KIẾM, LỌC VÀ SẮP XẾP ---
        $sort_order = isset($_GET['sort_order']) ? sanitize_text_field($_GET['sort_order']) : 'id_desc';
        $sort_parts = explode('_', $sort_order);
        $sort_dir = strtoupper(array_pop($sort_parts)); // Lấy ASC hoặc DESC ở cuối
        $sort_by = implode('_', $sort_parts); // Lấy phần còn lại làm tên cột (ví dụ: tong_tien)

        $filters = [
            'search'    => isset($_GET['search_pn']) ? sanitize_text_field($_GET['search_pn']) : '',
            'status'    => isset($_GET['status_pn']) ? sanitize_text_field($_GET['status_pn']) : '',
            'ncc_id'    => isset($_GET['ncc_id']) ? intval($_GET['ncc_id']) : '',
            'tu_ngay'   => isset($_GET['tu_ngay']) ? sanitize_text_field($_GET['tu_ngay']) : '',
            'den_ngay'  => isset($_GET['den_ngay']) ? sanitize_text_field($_GET['den_ngay']) : '',
            'min_price' => isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : '',
            'max_price' => isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : '',
            'sort_by'   => $sort_by,
            'sort_dir'  => $sort_dir
        ];

        // --- 2. XỬ LÝ PHÂN TRANG ---
        $limit = 10; 
        $trang_hien_tai = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($trang_hien_tai - 1) * $limit;
        
        $total_items = $this->repo->getTotalCount($filters);
        $total_pages = ceil($total_items / $limit);
        $phieuNhaps = $this->repo->getAllPaginated($limit, $offset, $filters);

        // Lấy danh sách Nhà cung cấp để hiển thị ra thẻ <select>
        $nhaCungCaps = $wpdb->get_results("SELECT id, ten_ncc FROM {$wpdb->prefix}qln_nha_cung_cap WHERE trang_thai = 1", ARRAY_A);

        // --- 3. TẠO URL PHÂN TRANG GIỮ NGUYÊN BỘ LỌC ---
        $url_params = "";
        $get_params = $_GET;
        unset($get_params['page'], $get_params['paged']); // Bỏ page gốc và paged cũ
        foreach ($get_params as $key => $val) {
            if ($val !== '') $url_params .= "&" . $key . "=" . urlencode($val);
        }

        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-list.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    public function create() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }
        global $wpdb;

        $ma_pn_du_kien = 'PNK-' . date('Ymd') . '-' . rand(100, 999);
        $nhaCungCaps = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_nha_cung_cap WHERE trang_thai = 1", ARRAY_A);
        $sanPhams = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_san_pham", ARRAY_A);
        
        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-add.php'; // Đã sửa tên file
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_qln']) && $_POST['action_qln'] === 'store_phieu_nhap') {
            
            $ma_pn = !empty($_POST['ma_pn']) ? sanitize_text_field($_POST['ma_pn']) : 'PNK-' . date('Ymd') . '-' . rand(100, 999);
            $ngay_nhap = !empty($_POST['ngay_nhap']) ? sanitize_text_field($_POST['ngay_nhap']) . ' ' . date('H:i:s') : current_time('mysql');

            $data = [
                'ma_pn' => $ma_pn,
                'ncc_id' => intval($_POST['ncc_id']),
                'nguoi_tao_id' => $_SESSION['qln_user_id'],
                'tong_tien' => 0,
                'ghi_chu' => sanitize_textarea_field($_POST['ghi_chu']),
                'ngay_nhap' => $ngay_nhap
            ];

            $details = [];
            $tong_tien = 0;

            $sp_ids = $_POST['san_pham_id'];
            $so_luongs = $_POST['so_luong'];
            $gia_nhaps = $_POST['gia_nhap'];

            for ($i = 0; $i < count($sp_ids); $i++) {
                if (!empty($sp_ids[$i]) && $so_luongs[$i] > 0) {
                    $thanh_tien = $so_luongs[$i] * $gia_nhaps[$i];
                    $tong_tien += $thanh_tien;

                    $details[] = [
                        'san_pham_id' => intval($sp_ids[$i]),
                        'so_luong' => intval($so_luongs[$i]),
                        'gia_nhap' => floatval($gia_nhaps[$i]),
                        'thanh_tien' => $thanh_tien
                    ];
                }
            }
            $data['tong_tien'] = $tong_tien;

            if ($this->repo->createPhieuNhap($data, $details)) {
                wp_redirect(admin_url('admin.php?page=qln-nhap-hang&status=success')); exit;
            } else {
                wp_redirect(admin_url('admin.php?page=qln-nhap-hang-add&status=error')); exit;
            }
        }
    }

    public function delete() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($this->repo->deletePhieuNhap($id)) {
            wp_redirect(admin_url('admin.php?page=qln-nhap-hang&status=cancelled')); exit;
        } else {
            wp_die("Lỗi khi hủy phiếu!");
        }
    }

    public function approve() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($this->repo->approvePhieuNhap($id)) {
            wp_redirect(admin_url('admin.php?page=qln-nhap-hang&status=approved')); exit;
        } else {
            wp_die("Lỗi khi duyệt phiếu vào kho!");
        }
    }

    public function view() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }
        global $wpdb;

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $phieuNhap = $this->repo->getById($id);
        if (!$phieuNhap) wp_die("Không tìm thấy phiếu nhập này!");

        $chiTiet = $this->repo->getDetails($id);
        $ncc = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}qln_nha_cung_cap WHERE id = %d", $phieuNhap['ncc_id']), ARRAY_A);
        $nguoiTao = $wpdb->get_row($wpdb->prepare("SELECT ho_ten FROM {$wpdb->prefix}qln_users WHERE id = %d", $phieuNhap['nguoi_tao_id']), ARRAY_A);
        
        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-view.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    // ---> MỚI THÊM VÀO: HÀM MỞ GIAO DIỆN SỬA <---
    public function edit() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }
        global $wpdb;

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $phieuNhap = $this->repo->getById($id);
        
        if (!$phieuNhap) {
            wp_die("Không tìm thấy phiếu nhập này!");
        }

        $chiTiet = $this->repo->getDetails($id);
        $nhaCungCaps = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_nha_cung_cap WHERE trang_thai = 1", ARRAY_A);
        $sanPhams = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_san_pham", ARRAY_A);
        
        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-add.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    // ---> MỚI THÊM VÀO: HÀM LƯU DỮ LIỆU SỬA <---
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_qln']) && $_POST['action_qln'] === 'update_phieu_nhap') {
            $id = intval($_POST['id']);
            $ngay_nhap = !empty($_POST['ngay_nhap']) ? sanitize_text_field($_POST['ngay_nhap']) . ' ' . date('H:i:s') : current_time('mysql');

            $data = [
                'ncc_id' => intval($_POST['ncc_id']),
                'tong_tien' => 0,
                'ghi_chu' => sanitize_textarea_field($_POST['ghi_chu']),
                'ngay_nhap' => $ngay_nhap
            ];

            $details = [];
            $tong_tien = 0;
            $sp_ids = $_POST['san_pham_id'];
            $so_luongs = $_POST['so_luong'];
            $gia_nhaps = $_POST['gia_nhap'];

            for ($i = 0; $i < count($sp_ids); $i++) {
                if (!empty($sp_ids[$i]) && $so_luongs[$i] > 0) {
                    $thanh_tien = $so_luongs[$i] * $gia_nhaps[$i];
                    $tong_tien += $thanh_tien;
                    $details[] = [
                        'san_pham_id' => intval($sp_ids[$i]),
                        'so_luong' => intval($so_luongs[$i]),
                        'gia_nhap' => floatval($gia_nhaps[$i]),
                        'thanh_tien' => $thanh_tien
                    ];
                }
            }
            $data['tong_tien'] = $tong_tien;

            if ($this->repo->updatePhieuNhap($id, $data, $details)) {
                wp_redirect(admin_url('admin.php?page=qln-nhap-hang&status=updated')); exit;
            } else {
                wp_die("Có lỗi xảy ra khi cập nhật!");
            }
        }
    }

    
}