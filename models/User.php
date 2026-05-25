<?php
class User {
    public $id;
    public $email;
    public $mat_khau;
    public $ho_ten;
    public $que_quan;
    public $sdt;
    public $role_id;
    public $trang_thai;
    public $ngay_tao;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->email = $data['email'] ?? '';
            $this->mat_khau = $data['mat_khau'] ?? '';
            $this->ho_ten = $data['ho_ten'] ?? '';
            $this->que_quan = $data['que_quan'] ?? '';
            $this->sdt = $data['sdt'] ?? '';
            $this->role_id = $data['role_id'] ?? 2; // Mặc định là NV Bán hàng
            $this->trang_thai = $data['trang_thai'] ?? 1;
            $this->ngay_tao = $data['ngay_tao'] ?? '';
        }
    }

    public function getRoleName() {
        switch ((int) $this->role_id) {
            case 1: return 'Quản lý';
            case 3: return 'NV Kho';
            case 2:
            default: return 'NV Bán hàng';
        }
    }

    public function getTrangThaiText() {
        return ((int) $this->trang_thai === 1) ? 'Đang làm việc' : 'Đã nghỉ';
    }
}
