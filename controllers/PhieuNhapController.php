<?php
class PhieuNhapController {
    private $repo;

    public function __construct() {
        require_once plugin_dir_path(__FILE__) . '../repositories/PhieuNhapRepository.php';
        $this->repo = new PhieuNhapRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }

        $stats = $this->repo->getStats();
        
        // --- XỬ LÝ PHÂN TRANG (Đã đổi tên biến thành $trang_hien_tai) ---
        $limit = 10; // Cứ 10 phiếu thì sang trang mới
        $trang_hien_tai = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($trang_hien_tai - 1) * $limit;
        
        $total_items = $this->repo->getTotalCount();
        $total_pages = ceil($total_items / $limit);
        
        // Lấy dữ liệu theo trang hiện tại thay vì lấy tất cả
        $phieuNhaps = $this->repo->getAllPaginated($limit, $offset);
        // ------------------------

        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-list.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    public function create() {
        if (!isset($_SESSION['qln_user_id'])) { wp_redirect(admin_url('admin.php?page=qln-login')); exit; }
        global $wpdb;

        // Lấy danh sách để đổ vào Form
        $nhaCungCaps = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_nha_cung_cap WHERE trang_thai = 1", ARRAY_A);
        $sanPhams = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}qln_san_pham", ARRAY_A);
        
        $view_content = plugin_dir_path(__FILE__) . '../views/warehouse/phieu-nhap-add.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_qln']) && $_POST['action_qln'] === 'store_phieu_nhap') {
            $ma_pn = 'PNK-' . date('Ymd') . '-' . rand(100, 999);
            
            // Xử lý ngày nhập. Nếu có thì lấy ngày form gửi, không thì lấy ngày hôm nay
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
}