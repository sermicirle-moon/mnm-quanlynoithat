<?php
class Invoice {
    public $id;
    public $ma_hd;
    public $khach_hang_id;
    public $ten_kh; // Chứa tên khách hàng sau khi JOIN bảng
    public $tong_tien;
    public $trang_thai;
    public $ngay_tao;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_hd = $data['ma_hd'] ?? '';
            $this->khach_hang_id = $data['khach_hang_id'] ?? null;
            $this->ten_kh = $data['ten_kh'] ?? ''; 
            $this->tong_tien = $data['tong_tien'] ?? 0;
            $this->trang_thai = $data['trang_thai'] ?? '';
            $this->ngay_tao = $data['ngay_tao'] ?? '';
        }
    }
}