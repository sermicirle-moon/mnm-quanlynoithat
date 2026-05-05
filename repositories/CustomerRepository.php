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
}