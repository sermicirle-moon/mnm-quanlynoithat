<?php
class SupplierRepository {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_nha_cung_cap';
    }

    public function getAll() {
        global $wpdb;
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $suppliers = [];
        if ($results) {
            foreach ($results as $row) {
                $suppliers[] = new Supplier($row);
            }
        }
        return $suppliers;
    }

    public function getAllWithFilters($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $rows = $wpdb->get_results("SELECT * FROM {$this->table} {$where} ORDER BY id DESC", ARRAY_A);
        $suppliers = [];

        if ($rows) {
            foreach ($rows as $row) {
                $suppliers[] = new Supplier($row);
            }
        }

        return $suppliers;
    }

    public function getAllPaginated($limit, $offset, $filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $limit, $offset), ARRAY_A);
        $suppliers = [];

        if ($rows) {
            foreach ($rows as $row) {
                $suppliers[] = new Supplier($row);
            }
        }

        return $suppliers;
    }

    public function getById($id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", (int) $id), ARRAY_A);
        return $row ? new Supplier($row) : null;
    }
    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data);
    }
    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, ['id' => (int) $id]);
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['id' => (int) $id], ['%d']);
    }
    public function getTotalCount($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} {$where}");
    }

    public function getStats() {
        global $wpdb;
        return [
            'total' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}"),
            'active' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE trang_thai = 1"),
            'inactive' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE trang_thai = 0"),
        ];
    }

    private function buildWhereClause($filters = []) {
        global $wpdb;
        $conditions = ['1=1'];
        if (!empty($filters['search'])) {
            $like = '%' . $wpdb->esc_like($filters['search']) . '%';
            $conditions[] = $wpdb->prepare('(ma_ncc LIKE %s OR ten_ncc LIKE %s OR nguoi_lien_he LIKE %s OR sdt LIKE %s OR email LIKE %s OR dia_chi LIKE %s)', $like, $like, $like, $like, $like, $like);
        }
        if (($filters['trang_thai'] ?? '') !== '') {
            $conditions[] = $wpdb->prepare('trang_thai = %d', (int) $filters['trang_thai']);
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }
}
