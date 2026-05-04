<?php
class User {
    public $id;
    public $email;
    public $mat_khau;
    public $ho_ten;
    public $que_quan; // Mới
    public $role_id;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->email = $data['email'] ?? '';
            $this->mat_khau = $data['mat_khau'] ?? '';
            $this->ho_ten = $data['ho_ten'] ?? '';
            $this->que_quan = $data['que_quan'] ?? '';
            $this->role_id = $data['role_id'] ?? 2; // Mặc định là NV Bán hàng
        }
    }
}