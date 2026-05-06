<?php
class CustomerRepository {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_khach_hang';
    }

    public function getAll() {
        global $wpdb;
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $customers = [];
        if ($results) {
            foreach ($results as $row) {
                $customers[] = new Customer($row);
            }
        }
        return $customers;
    }
    // Lấy 1 khách hàng theo ID
    public function getById($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id);
        $result = $wpdb->get_row($sql, ARRAY_A);
        return $result ? new Customer($result) : null;
    }

    // Thêm khách hàng mới
    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data); // $data là mảng ['ma_kh' => '...', 'ten_kh' => '...']
    }

    // Cập nhật khách hàng
    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, ['id' => $id]);
    }

    // Xóa khách hàng
    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['id' => $id], ['%d']);
    }
    public function getAllWithFilters($search = '', $loai = '', $thuong_hieu = '') {
        global $wpdb;
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (ma_kh LIKE %s OR ten_kh LIKE %s OR email LIKE %s OR sdt LIKE %s)";
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params = array_fill(0, 4, $like);
        }
        if (!empty($loai) && $loai !== 'all') {
            $sql .= " AND loai_khach_hang = %s";
            $params[] = $loai;
        }
        // 'thuong_hieu' có thể dùng để lọc khách hàng thân thiết (so_luong_don_hang >= 3)
        if ($thuong_hieu === 'than_thiet') {
            $sql .= " AND so_luong_don_hang >= 3";
        }
        
        $sql .= " ORDER BY ten_kh ASC";
        $results = empty($params) ? $wpdb->get_results($sql, ARRAY_A) : $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
        
        $customers = [];
        foreach ($results as $row) {
            $customers[] = new Customer($row);
        }
        return $customers;
    }

    public function getStats() {
        global $wpdb;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
        $b2b = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE loai_khach_hang = 'B2B'");
        $b2c = $total - $b2b;
        $than_thiet = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE so_luong_don_hang >= 3");
        return (object)[
            'total' => $total,
            'b2b' => $b2b,
            'b2c' => $b2c,
            'than_thiet' => $than_thiet
        ];
    }
}