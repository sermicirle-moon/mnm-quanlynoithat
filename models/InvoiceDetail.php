<?php
class InvoiceDetail {
    public $id;
    public $hoa_don_id;
    public $san_pham_id;
    public $so_luong;
    public $don_gia;
    public $thanh_tien; // tự động từ DB

    // Thông tin thêm để hiển thị
    public $ten_sp;
    public $ma_sp;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->hoa_don_id = $data['hoa_don_id'] ?? null;
            $this->san_pham_id = $data['san_pham_id'] ?? null;
            $this->so_luong = $data['so_luong'] ?? 0;
            $this->don_gia = $data['don_gia'] ?? 0;
            $this->thanh_tien = $data['thanh_tien'] ?? 0;
            $this->ten_sp = $data['ten_sp'] ?? '';
            $this->ma_sp = $data['ma_sp'] ?? '';
        }
    }
}