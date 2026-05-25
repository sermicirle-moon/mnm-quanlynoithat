<?php
class ShippingRepository {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_nha_van_chuyen';
    }

    public function getAll() {
        global $wpdb;
        $rows = $wpdb->get_results("SELECT * FROM {$this->table} ORDER BY id DESC", ARRAY_A);
        return $this->hydrate($rows);
    }

    public function getAllWithFilters($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $rows = $wpdb->get_results("SELECT * FROM {$this->table} {$where} ORDER BY id DESC", ARRAY_A);
        return $this->hydrate($rows);
    }

    public function getAllPaginated($limit, $offset, $filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $limit, $offset), ARRAY_A);
        return $this->hydrate($rows);
    }

    public function getTotalCount($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} {$where}");
    }

    public function getById($id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id), ARRAY_A);
        return $row ? new ShippingCarrier($row) : null;
    }

    public function createCarrier($data) {
        global $wpdb;
        $wpdb->insert($this->table, $data, ['%s', '%s', '%s', '%s', '%s', '%d']);
        return $wpdb->insert_id;
    }

    public function updateCarrier($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, ['id' => (int) $id]);
    }

    public function deactivateCarrier($id) {
        return $this->updateCarrier($id, ['trang_thai' => 0]);
    }

    private function hydrate($rows) {
        $carriers = [];

        if ($rows) {
            foreach ($rows as $row) {
                $carriers[] = new ShippingCarrier($row);
            }
        }

        return $carriers;
    }

    public function getStats() {
        global $wpdb;
        return [
            'total' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}"),
            'active' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE trang_thai = 1"),
            'inactive' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE trang_thai = 0"),
            'internal' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE loai_hinh = 'Nội bộ'"),
        ];
    }

    private function buildWhereClause($filters = []) {
        global $wpdb;
        $conditions = ['1=1'];
        if (!empty($filters['search'])) {
            $like = '%' . $wpdb->esc_like($filters['search']) . '%';
            $conditions[] = $wpdb->prepare('(ma_nvc LIKE %s OR ten_nvc LIKE %s OR loai_hinh LIKE %s OR sdt_tai_xe LIKE %s OR bien_so_xe LIKE %s)', $like, $like, $like, $like, $like);
        }
        if (($filters['trang_thai'] ?? '') !== '') {
            $conditions[] = $wpdb->prepare('trang_thai = %d', (int) $filters['trang_thai']);
        }
        if (!empty($filters['loai_hinh'])) {
            $conditions[] = $wpdb->prepare('loai_hinh = %s', $filters['loai_hinh']);
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }
}
