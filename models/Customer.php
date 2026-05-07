<?php
class Customer {
     public $id;
    public $ma_kh;
    public $ten_kh;
    public $sdt;
    public $dia_chi;
    public $email;
    public $loai_khach_hang;      // B2B / B2C
    public $tong_tien_da_chi;
    public $so_luong_don_hang;
    public $lan_mua_gan_nhat;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_kh = $data['ma_kh'] ?? '';
            $this->ten_kh = $data['ten_kh'] ?? '';
            $this->sdt = $data['sdt'] ?? '';
            $this->dia_chi = $data['dia_chi'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->loai_khach_hang = $data['loai_khach_hang'] ?? 'B2C';
            $this->tong_tien_da_chi = $data['tong_tien_da_chi'] ?? 0;
            $this->so_luong_don_hang = $data['so_luong_don_hang'] ?? 0;
            $this->lan_mua_gan_nhat = $data['lan_mua_gan_nhat'] ?? null;
        }
    }
}