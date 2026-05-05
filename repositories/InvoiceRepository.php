<?php
class InvoiceRepository {
    private $table_invoice;
    private $table_customer;

    public function __construct() {
        global $wpdb;
        $this->table_invoice = $wpdb->prefix . 'qln_hoa_don';
        $this->table_customer = $wpdb->prefix . 'qln_khach_hang';
    }

    public function getAll() {
        global $wpdb;
        // JOIN bảng hóa đơn và khách hàng để lấy tên khách
        $sql = "SELECT h.*, k.ten_kh 
                FROM {$this->table_invoice} h 
                LEFT JOIN {$this->table_customer} k ON h.khach_hang_id = k.id 
                ORDER BY h.ngay_tao DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $invoices = [];
        if ($results) {
            foreach ($results as $row) {
                $invoices[] = new Invoice($row);
            }
        }
        return $invoices;
    }
}