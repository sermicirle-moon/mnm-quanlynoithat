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
}