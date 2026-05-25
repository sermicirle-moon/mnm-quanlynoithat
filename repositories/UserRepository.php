<?php
class UserRepository {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'qln_users'; // wp_qln_users
    }

    public function findByEmail($email) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->table} WHERE email = %s", $email);
        $result = $wpdb->get_row($sql, ARRAY_A);
        return $result ? new User($result) : null;
    }

    public function getAll() {
        global $wpdb;
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $results = $wpdb->get_results($sql, ARRAY_A);
        $users = [];
        if ($results) {
            foreach ($results as $row) {
                $users[] = new User($row);
            }
        }
        return $users;
    }

    public function getById($id) {
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id), ARRAY_A);
        return $row ? new User($row) : null;
    }

    public function getAllPaginated($limit, $offset, $filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $limit, $offset), ARRAY_A);
        $users = [];

        if ($rows) {
            foreach ($rows as $row) {
                $users[] = new User($row);
            }
        }

        return $users;
    }

    public function getAllWithFilters($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        $rows = $wpdb->get_results("SELECT * FROM {$this->table} {$where} ORDER BY id DESC", ARRAY_A);
        $users = [];

        if ($rows) {
            foreach ($rows as $row) {
                $users[] = new User($row);
            }
        }

        return $users;
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
            'sale' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE role_id = 2"),
            'warehouse' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table} WHERE role_id = 3"),
        ];
    }

    public function create(User $user) {
        global $wpdb;
        $wpdb->insert(
            $this->table,
            [
                'email' => $user->email,
                'mat_khau' => $user->mat_khau,
                'ho_ten' => $user->ho_ten,
                'que_quan' => $user->que_quan,
                'role_id' => $user->role_id
            ],
            ['%s', '%s', '%s', '%s', '%d']
        );
        return $wpdb->insert_id;
    }

    public function createStaff($data) {
        global $wpdb;
        $wpdb->insert(
            $this->table,
            [
                'email' => $data['email'],
                'mat_khau' => $data['mat_khau'],
                'ho_ten' => $data['ho_ten'],
                'que_quan' => $data['que_quan'],
                'sdt' => $data['sdt'],
                'role_id' => $data['role_id'],
                'trang_thai' => $data['trang_thai'],
            ],
            ['%s', '%s', '%s', '%s', '%s', '%d', '%d']
        );
        return $wpdb->insert_id;
    }

    public function updateStaff($id, $data) {
        global $wpdb;
        return $wpdb->update($this->table, $data, ['id' => (int) $id]);
    }

    public function deleteStaff($id) {
        global $wpdb;
        return $wpdb->delete($this->table, ['id' => (int) $id], ['%d']);
    }

    private function buildWhereClause($filters = []) {
        global $wpdb;
        $conditions = ['1=1'];

        if (!empty($filters['search'])) {
            $like = '%' . $wpdb->esc_like($filters['search']) . '%';
            $conditions[] = $wpdb->prepare('(ho_ten LIKE %s OR email LIKE %s OR sdt LIKE %s OR que_quan LIKE %s)', $like, $like, $like, $like);
        }
        if (($filters['role_id'] ?? '') !== '') {
            $conditions[] = $wpdb->prepare('role_id = %d', (int) $filters['role_id']);
        }
        if (($filters['trang_thai'] ?? '') !== '') {
            $conditions[] = $wpdb->prepare('trang_thai = %d', (int) $filters['trang_thai']);
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }
}
