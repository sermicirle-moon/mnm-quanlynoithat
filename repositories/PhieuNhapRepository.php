<?php
class PhieuNhapRepository {
    private $table_pn;
    private $table_ncc;
    private $table_users;

    public function __construct() {
        global $wpdb;
        $this->table_pn = $wpdb->prefix . 'qln_phieu_nhap';
        $this->table_ncc = $wpdb->prefix . 'qln_nha_cung_cap';
        $this->table_users = $wpdb->prefix . 'qln_users';
    }

    // Lấy tất cả (không phân trang - dùng cho các trường hợp khác nếu cần)
    public function getAll() {
        global $wpdb;
        
        $sql = "SELECT pn.*, ncc.ten_ncc, u.ho_ten AS nguoi_tao 
                FROM {$this->table_pn} pn 
                LEFT JOIN {$this->table_ncc} ncc ON pn.ncc_id = ncc.id 
                LEFT JOIN {$this->table_users} u ON pn.nguoi_tao_id = u.id 
                ORDER BY pn.ngay_nhap DESC";
                
        return $wpdb->get_results($sql, ARRAY_A);
    }

    // --- MỚI: Hàm lấy dữ liệu có giới hạn (Dùng cho Phân trang) ---
    public function getAllPaginated($limit, $offset) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT pn.*, ncc.ten_ncc, u.ho_ten AS nguoi_tao 
                FROM {$this->table_pn} pn 
                LEFT JOIN {$this->table_ncc} ncc ON pn.ncc_id = ncc.id 
                LEFT JOIN {$this->table_users} u ON pn.nguoi_tao_id = u.id 
                ORDER BY pn.ngay_nhap DESC 
                LIMIT %d OFFSET %d", $limit, $offset);
                
        return $wpdb->get_results($sql, ARRAY_A);
    }

    // --- MỚI: Hàm đếm tổng số lượng phiếu nhập để chia số trang ---
    public function getTotalCount() {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_pn}");
    }

    // Lấy số liệu thống kê cho 4 Cards trên Dashboard
    public function getStats() {
        global $wpdb;
        
        $total_phieu = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_pn}");
        $total_value = $wpdb->get_var("SELECT SUM(tong_tien) FROM {$this->table_pn}");
        $cho_thanh_toan = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_pn} WHERE trang_thai = 'Chưa thanh toán'");
        $ncc_moi = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_ncc} WHERE MONTH(ngay_tao) = MONTH(CURRENT_DATE())");

        return [
            'total_phieu' => $total_phieu ?: 0,
            'total_value' => $total_value ?: 0,
            'cho_thanh_toan' => $cho_thanh_toan ?: 0,
            'ncc_moi' => $ncc_moi ?: 0
        ];
    }

    // Xử lý tạo Phiếu Nhập kho và Cập nhật Tồn kho
    public function createPhieuNhap($data, $details) {
        global $wpdb;
        $table_ctpn = $wpdb->prefix . 'qln_chi_tiet_phieu_nhap'; 
        $table_sp = $wpdb->prefix . 'qln_san_pham'; 
        
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Lưu Phiếu nhập chính (Đã bổ sung thêm ngay_nhap)
            $wpdb->insert($this->table_pn, [
                'ma_pn' => $data['ma_pn'],
                'ncc_id' => $data['ncc_id'],
                'nguoi_tao_id' => $data['nguoi_tao_id'],
                'tong_tien' => $data['tong_tien'],
                'ghi_chu' => $data['ghi_chu'],
                'trang_thai' => 'Đã nhập kho',
                'ngay_nhap' => $data['ngay_nhap'] // <-- Lấy ngày do người dùng chọn
            ]);
            $phieu_nhap_id = $wpdb->insert_id;
            
            if (!$phieu_nhap_id) throw new Exception("Lỗi tạo phiếu nhập");

            // 2. Lưu Chi tiết & UPDATE NGƯỢC LẠI BẢNG SẢN PHẨM
            foreach ($details as $item) {
                // Lưu vào chi tiết phiếu nhập
                $wpdb->insert($table_ctpn, [
                    'phieu_nhap_id' => $phieu_nhap_id,
                    'san_pham_id' => $item['san_pham_id'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'thanh_tien' => $item['thanh_tien']
                ]);

                // UPDATE VÀO BẢNG SẢN PHẨM: Cộng dồn tồn kho và cập nhật giá nhập mới
                $wpdb->query($wpdb->prepare(
                    "UPDATE {$table_sp} 
                     SET so_luong_ton = so_luong_ton + %d, 
                         gia_nhap = %f 
                     WHERE id = %d",
                    $item['so_luong'],     
                    $item['gia_nhap'],
                    $item['san_pham_id'] 
                ));
            }

            $wpdb->query('COMMIT');
            return true;

        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }
}