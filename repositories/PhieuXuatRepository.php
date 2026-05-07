<?php
class PhieuXuatRepository {
    private $table_px;
    private $table_px_hd;
    private $table_hd;
    private $table_hdct;
    private $table_sp;

    public function __construct() {
        global $wpdb;
        $this->table_px = $wpdb->prefix . 'qln_phieu_xuat';
        $this->table_px_hd = $wpdb->prefix . 'qln_phieu_xuat_hoa_don';
        $this->table_hd = $wpdb->prefix . 'qln_hoa_don';
        $this->table_hdct = $wpdb->prefix . 'qln_hoa_don_chi_tiet';
        $this->table_sp = $wpdb->prefix . 'qln_san_pham';
    }

    public function getStats() {
        global $wpdb;
        return $wpdb->get_row("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN trang_thai = 'Chờ xuất kho' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN trang_thai = 'Đã giao' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN trang_thai = 'Đã hủy' THEN 1 ELSE 0 END) as cancelled
            FROM {$this->table_px}
        ", ARRAY_A);
    }

    public function getAllPaginated($limit, $offset, $filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        
        $sql = "SELECT px.*, k.ten_kh, nvc.ten_nvc, nvc.bien_so_xe 
                FROM {$this->table_px} px 
                LEFT JOIN {$wpdb->prefix}qln_khach_hang k ON px.khach_hang_id = k.id 
                LEFT JOIN {$wpdb->prefix}qln_nha_van_chuyen nvc ON px.nvc_id = nvc.id 
                $where
                ORDER BY px.id DESC LIMIT %d OFFSET %d";
                
        return $wpdb->get_results($wpdb->prepare($sql, $limit, $offset), ARRAY_A);
    }

    public function getTotalCount($filters = []) {
        global $wpdb;
        $where = $this->buildWhereClause($filters);
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_px} px $where");
    }

    public function getById($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_px} WHERE id = %d", $id), ARRAY_A);
    }

    public function getInvoicesByPhieuXuat($px_id) {
        global $wpdb;
        $sql = "SELECT hd.* FROM {$this->table_hd} hd 
                JOIN {$this->table_px_hd} pxhd ON hd.id = pxhd.hoa_don_id 
                WHERE pxhd.phieu_xuat_id = %d";
        return $wpdb->get_results($wpdb->prepare($sql, $px_id), ARRAY_A);
    }

    // TẠO PHIẾU XUẤT (GOM ĐƠN) VÀ KHÓA HÓA ĐƠN
    public function createPhieuXuat($data, $hoa_don_ids) {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        try {
            $wpdb->insert($this->table_px, $data);
            $px_id = $wpdb->insert_id;

            foreach ($hoa_don_ids as $hd_id) {
                // Kẹp hóa đơn vào phiếu xuất
                $wpdb->insert($this->table_px_hd, [
                    'phieu_xuat_id' => $px_id, 
                    'hoa_don_id' => intval($hd_id)
                ]);
                // Khóa hóa đơn lại để tránh bị gom vào chuyến khác
                $wpdb->update($this->table_hd, ['trang_thai_giao' => 'Đang giao'], ['id' => intval($hd_id)]);
            }
            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    // XÁC NHẬN GIAO THÀNH CÔNG -> TRỪ TỒN KHO
    public function markAsDelivered($px_id) {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        try {
            // 1. Chuyển chuyến xe thành Đã giao
            $wpdb->update($this->table_px, ['trang_thai' => 'Đã giao'], ['id' => $px_id]);

            // 2. Lấy các hóa đơn trên xe
            $hds = $this->getInvoicesByPhieuXuat($px_id);

            // 3. Đổi trạng thái HĐ & Trừ kho từng sản phẩm
            foreach ($hds as $hd) {
                $wpdb->update($this->table_hd, ['trang_thai_giao' => 'Đã giao'], ['id' => $hd['id']]);

                // Quét chi tiết hóa đơn để lấy hàng ra khỏi kho
                $details = $wpdb->get_results($wpdb->prepare("SELECT san_pham_id, so_luong FROM {$this->table_hdct} WHERE hoa_don_id = %d", $hd['id']));
                foreach ($details as $item) {
                    $wpdb->query($wpdb->prepare(
                        "UPDATE {$this->table_sp} SET so_luong_ton = so_luong_ton - %d WHERE id = %d",
                        $item->so_luong, $item->san_pham_id
                    ));
                }
            }
            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    // HỦY PHIẾU XUẤT -> NHẢ HÓA ĐƠN RA LẠI
    public function cancelPhieuXuat($px_id) {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        try {
            $wpdb->update($this->table_px, ['trang_thai' => 'Đã hủy'], ['id' => $px_id]);
            
            // Tìm và nhả các hóa đơn ra thành "Chờ giao" để mốt xếp xe khác
            $hds = $this->getInvoicesByPhieuXuat($px_id);
            foreach ($hds as $hd) {
                $wpdb->update($this->table_hd, ['trang_thai_giao' => 'Chờ giao'], ['id' => $hd['id']]);
            }
            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    private function buildWhereClause($filters) {
        global $wpdb;
        $conditions = ["1=1"];
        
        if (!empty($filters['search'])) {
            $conditions[] = $wpdb->prepare("(px.ma_px LIKE %s OR k.ten_kh LIKE %s)", '%' . $wpdb->esc_like($filters['search']) . '%', '%' . $wpdb->esc_like($filters['search']) . '%');
        }
        if (!empty($filters['status'])) {
            $conditions[] = $wpdb->prepare("px.trang_thai = %s", $filters['status']);
        }
        if (!empty($filters['date_from'])) {
            $conditions[] = $wpdb->prepare("DATE(px.ngay_xuat) >= %s", $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $conditions[] = $wpdb->prepare("DATE(px.ngay_xuat) <= %s", $filters['date_to']);
        }
        
        return "WHERE " . implode(" AND ", $conditions);
    }
}