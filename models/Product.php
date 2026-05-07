<?php
class Product {
    public $id;
    public $ma_sp;
    public $ten_sp;
    public $gia_ban;
    public $gia_nhap;
    public $so_luong_ton;
    public $hinh_anh;
    public $trang_thai;
    public $id_loai;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_sp = $data['ma_sp'] ?? '';
            $this->ten_sp = $data['ten_sp'] ?? '';
            $this->gia_ban = $data['gia_ban'] ?? 0;
            $this->gia_nhap = $data['gia_nhap'] ?? 0;
            $this->so_luong_ton = $data['so_luong_ton'] ?? 0;
            $this->hinh_anh = $data['hinh_anh'] ?? '';
            $this->trang_thai = $data['trang_thai'] ?? '';
            $this->id_loai = $data['id_loai'] ?? null;
        }
    }
}