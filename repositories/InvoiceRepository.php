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
        $sql = "SELECT h.*, k.ten_kh 
                FROM {$this->table_invoice} h 
                LEFT JOIN {$this->table_customer} k ON h.khach_hang_id = k.id 
                ORDER BY h.ngay_tao DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $invoices = [];
        foreach ($results as $row) {
            $invoices[] = new Invoice($row);
        }
        return $invoices;
    }

    public function getById($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->table_invoice} WHERE id = %d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row ? new Invoice($row) : null;
    }

    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table_invoice, $data);
    }

    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table_invoice, $data, ['id' => $id]);
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table_invoice, ['id' => $id], ['%d']);
    }
    public function getLastInsertId() {
        global $wpdb;
        return $wpdb->insert_id;
    }
    public function updateTotalAmount($invoice_id) {
        global $wpdb;
        $table_detail = $wpdb->prefix . 'qln_hoa_don_chi_tiet';
        $sql = $wpdb->prepare(
            "UPDATE {$this->table_invoice} 
            SET tong_tien = (SELECT IFNULL(SUM(thanh_tien), 0) FROM $table_detail WHERE hoa_don_id = %d)
            WHERE id = %d",
            $invoice_id, $invoice_id
        );
        return $wpdb->query($sql);
    }
    public function getStatus($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT trang_thai FROM {$this->table_invoice} WHERE id = %d", $id);
        return $wpdb->get_var($sql);
    }
    /**
     * Lấy danh sách hóa đơn có bộ lọc
     *
     * @param string $search     Tìm kiếm theo mã HĐ hoặc tên khách hàng
     * @param string $status     Trạng thái hóa đơn
     * @param string $date_from  Ngày bắt đầu (Y-m-d)
     * @param string $date_to    Ngày kết thúc (Y-m-d)
     * @param string $total_from Tổng tiền từ
     * @param string $total_to   Tổng tiền đến
     * @return Invoice[]
     */
    public function getAllWithFilters($search = '', $status = '', $date_from = '', $date_to = '', $total_from = '', $total_to = '') {
        global $wpdb;
        $sql = "SELECT h.*, k.ten_kh 
                FROM {$this->table_invoice} h 
                LEFT JOIN {$this->table_customer} k ON h.khach_hang_id = k.id 
                WHERE 1=1";
        $params = [];

        // Tìm kiếm theo mã hóa đơn hoặc tên khách hàng
        if (!empty($search)) {
            $sql .= " AND (h.ma_hd LIKE %s OR k.ten_kh LIKE %s)";
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        // Lọc theo trạng thái
        if (!empty($status) && $status !== 'all') {
            $sql .= " AND h.trang_thai = %s";
            $params[] = $status;
        }

        // Lọc theo ngày
        if (!empty($date_from)) {
            $sql .= " AND DATE(h.ngay_tao) >= %s";
            $params[] = $date_from;
        }
        if (!empty($date_to)) {
            $sql .= " AND DATE(h.ngay_tao) <= %s";
            $params[] = $date_to;
        }

        // Lọc theo tổng tiền (khoảng)
        if (!empty($total_from) && is_numeric($total_from)) {
            $sql .= " AND h.tong_tien >= %d";
            $params[] = (int)$total_from;
        }
        if (!empty($total_to) && is_numeric($total_to)) {
            $sql .= " AND h.tong_tien <= %d";
            $params[] = (int)$total_to;
        }

        $sql .= " ORDER BY h.ngay_tao DESC";

        if (empty($params)) {
            $results = $wpdb->get_results($sql, ARRAY_A);
        } else {
            $results = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
        }

        $invoices = [];
        foreach ($results as $row) {
            $invoices[] = new Invoice($row);
        }
        return $invoices;
    }
}