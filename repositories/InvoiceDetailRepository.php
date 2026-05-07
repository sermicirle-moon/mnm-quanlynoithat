<?php
class InvoiceDetailRepository {
    private $table;
    private $table_product;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_hoa_don_chi_tiet';
        $this->table_product = $wpdb->prefix . 'qln_san_pham';
    }

    public function getByInvoiceId($hoa_don_id) {
        global $wpdb;
        // Sử dụng LEFT JOIN để tránh mất dòng dữ liệu nếu sản phẩm bị trục trặc
        $sql = $wpdb->prepare(
            "SELECT d.*, p.ten_sp, p.ma_sp 
             FROM {$this->table} d
             LEFT JOIN {$this->table_product} p ON d.san_pham_id = p.id
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

    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data);
    }

    public function deleteByInvoiceId($hoa_don_id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['hoa_don_id' => $hoa_don_id], ['%d']);
    }
}