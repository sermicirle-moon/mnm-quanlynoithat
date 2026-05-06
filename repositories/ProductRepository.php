<?php
class ProductRepository {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_san_pham';
    }

    public function getAll() {
        global $wpdb;
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $products = [];
        if ($results) {
            foreach ($results as $row) {
                $products[] = new Product($row);
            }
        }
        return $products;
    }
    public function getById($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return $row ? new Product($row) : null;
    }

    public function create($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data);
    }

    public function update($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['id' => $id], ['%d']);
    }
    /**
     * Lấy số lượng tồn kho hiện tại của sản phẩm
     */
    public function getStock($product_id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT so_luong_ton FROM {$this->table} WHERE id = %d", $product_id);
        return (int) $wpdb->get_var($sql);
    }

    /**
     * Cập nhật trực tiếp số lượng tồn kho
     */
    public function updateStock($product_id, $new_quantity) {
        global $wpdb;
        return $wpdb->update($this->table, ['so_luong_ton' => $new_quantity], ['id' => $product_id]);
    }

    /**
     * Tăng số lượng tồn kho (cộng thêm)
     */
    public function increaseStock($product_id, $quantity) {
        global $wpdb;
        $sql = $wpdb->prepare("UPDATE {$this->table} SET so_luong_ton = so_luong_ton + %d WHERE id = %d", $quantity, $product_id);
        return $wpdb->query($sql);
    }

    /**
     * Giảm số lượng tồn kho (trừ đi)
     */
    public function decreaseStock($product_id, $quantity) {
        global $wpdb;
        $sql = $wpdb->prepare("UPDATE {$this->table} SET so_luong_ton = so_luong_ton - %d WHERE id = %d", $quantity, $product_id);
        return $wpdb->query($sql);
    }
    /**
     * Lấy danh sách sản phẩm có hỗ trợ lọc và kèm tên loại
     */
    public function getAllWithFilters($search = '', $status = '', $category = '') {
        global $wpdb;
        $sql = "SELECT p.*, l.ten_loai 
                FROM {$this->table} p
                LEFT JOIN {$wpdb->prefix}qln_loai_sp l ON p.id_loai = l.id_loai
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.ma_sp LIKE %s OR p.ten_sp LIKE %s)";
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($status) && $status !== 'all') {
            $sql .= " AND p.trang_thai = %s";
            $params[] = $status;
        }
        if (!empty($category) && $category !== 'all') {
            $sql .= " AND p.id_loai = %d";
            $params[] = (int)$category;
        }

        $sql .= " ORDER BY p.id DESC";

        if (empty($params)) {
            $results = $wpdb->get_results($sql, ARRAY_A);
        } else {
            $results = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
        }

        $products = [];
        foreach ($results as $row) {
            $product = new Product($row);
            $product->ten_loai = $row['ten_loai'] ?? 'Chưa phân loại';
            $products[] = $product;
        }
        return $products;
    }
    /**
     * Lấy danh sách sản phẩm có thể bán (trạng thái khác 'Không bán')
     * @return Product[]
     */
    public function getAvailableProducts() {
        global $wpdb;
        $sql = "SELECT p.*, l.ten_loai 
                FROM {$this->table} p
                LEFT JOIN {$wpdb->prefix}qln_loai_sp l ON p.id_loai = l.id_loai
                WHERE p.trang_thai != 'Không bán'
                ORDER BY p.id DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $products = [];
        foreach ($results as $row) {
            $product = new Product($row);
            $product->ten_loai = $row['ten_loai'] ?? 'Chưa phân loại';
            $products[] = $product;
        }
        return $products;
    }
}