<?php
class Invoice {
    public $id;
    public $ma_hd;
    public $khach_hang_id;
    public $ten_kh;
    public $tong_tien;
    public $trang_thai;
    public $trang_thai_giao; // <-- Thêm cột này
    public $ngay_tao;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_hd = $data['ma_hd'] ?? '';
            $this->khach_hang_id = $data['khach_hang_id'] ?? null;
            $this->ten_kh = $data['ten_kh'] ?? ''; 
            $this->tong_tien = $data['tong_tien'] ?? 0;
            $this->trang_thai = $data['trang_thai'] ?? '';
            $this->trang_thai_giao = $data['trang_thai_giao'] ?? 'Chờ giao'; // <-- Thêm gán giá trị
            $this->ngay_tao = $data['ngay_tao'] ?? '';
        }
    }
}