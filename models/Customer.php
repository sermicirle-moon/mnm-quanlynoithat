<?php
class Customer {
    public $id;
    public $ma_kh;
    public $ten_kh;
    public $sdt;
    public $dia_chi;
    public $email;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_kh = $data['ma_kh'] ?? '';
            $this->ten_kh = $data['ten_kh'] ?? '';
            $this->sdt = $data['sdt'] ?? '';
            $this->dia_chi = $data['dia_chi'] ?? '';
            $this->email = $data['email'] ?? '';
        }
    }
}