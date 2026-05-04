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
}