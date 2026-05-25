<?php
class ShippingCarrier {
    public $id;
    public $ma_nvc;
    public $ten_nvc;
    public $loai_hinh;
    public $sdt_tai_xe;
    public $bien_so_xe;
    public $trang_thai;
    public $ngay_tao;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_nvc = $data['ma_nvc'] ?? '';
            $this->ten_nvc = $data['ten_nvc'] ?? '';
            $this->loai_hinh = $data['loai_hinh'] ?? '';
            $this->sdt_tai_xe = $data['sdt_tai_xe'] ?? '';
            $this->bien_so_xe = $data['bien_so_xe'] ?? '';
            $this->trang_thai = $data['trang_thai'] ?? 1;
            $this->ngay_tao = $data['ngay_tao'] ?? '';
        }
    }

    public function getTrangThaiText() {
        return ((int) $this->trang_thai === 1) ? 'Đang hoạt động' : 'Tạm ngừng';
    }
}
