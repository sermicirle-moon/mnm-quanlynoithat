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

    public function getAll() {
        global $wpdb;
        $sql = "SELECT pn.*, ncc.ten_ncc, u.ho_ten AS nguoi_tao 
                FROM {$this->table_pn} pn 
                LEFT JOIN {$this->table_ncc} ncc ON pn.nha_cung_cap_id = ncc.id 
                LEFT JOIN {$this->table_users} u ON pn.user_id = u.id 
                ORDER BY pn.ngay_tao DESC";
        return $wpdb->get_results($sql, ARRAY_A);
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
}