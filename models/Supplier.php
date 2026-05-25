<?php
class Supplier {
    public $id;
    public $ma_ncc;
    public $ten_ncc;
    public $nguoi_lien_he;
    public $sdt;
    public $email;
    public $dia_chi;
    public $trang_thai;
    public $ngay_tao;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_ncc = $data['ma_ncc'] ?? '';
            $this->ten_ncc = $data['ten_ncc'] ?? '';
            $this->nguoi_lien_he = $data['nguoi_lien_he'] ?? '';
            $this->sdt = $data['sdt'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->dia_chi = $data['dia_chi'] ?? '';
            $this->trang_thai = $data['trang_thai'] ?? 1;
            $this->ngay_tao = $data['ngay_tao'] ?? '';
        }
    }

    public function getTrangThaiText() {
        return ((int) $this->trang_thai === 1) ? 'Đang giao dịch' : 'Ngừng giao dịch';
    }
}
