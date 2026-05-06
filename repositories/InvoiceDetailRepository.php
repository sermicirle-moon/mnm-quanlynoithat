<?php
class InvoiceDetailRepository {
    private $table;
    private $table_product;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_hoa_don_chi_tiet';
        $this->table_product = $wpdb->prefix . 'qln_san_pham';
    }

    // Lấy chi tiết theo hóa đơn
    public function getByInvoiceId($hoa_don_id) {
        global $wpdb;
        $sql = $wpdb->prepare(
            "SELECT d.*, p.ten_sp, p.ma_sp 
             FROM {$this->table} d
             JOIN {$this->table_product} p ON d.san_pham_id = p.id
             WHERE d.hoa_don_id = %d
             ORDER BY d.id ASC",
            $hoa_don_id
        );
        $results = $wpdb->get_results($sql, ARRAY_A);
        $details = [];
        foreach ($results as $row) {
            $details[] = new InvoiceDetail($row);
        }
        return $details;
    }

    // Thêm mới chi tiết
    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data);
    }

    // Xóa tất cả chi tiết của một hóa đơn (dùng khi cập nhật lại toàn bộ)
    public function deleteByInvoiceId($hoa_don_id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['hoa_don_id' => $hoa_don_id], ['%d']);
    }
    
}