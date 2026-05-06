<?php
class PhieuNhapRepository {
    private $table_pn;
    private $table_ncc;
    private $table_users;

    public function __construct() {
        global $wpdb;
        $this->table_pn = $wpdb->prefix . 'qln_phieu_nhap';
        $this->table_ncc = $wpdb->prefix . 'qln_nha_cung_cap';
        $this->table_users = $wpdb->prefix . 'qln_users';
    }

    // Lấy tất cả (không phân trang - dùng cho các trường hợp khác nếu cần)
    public function getAll() {
        global $wpdb;
        
        $sql = "SELECT pn.*, ncc.ten_ncc, u.ho_ten AS nguoi_tao 
                FROM {$this->table_pn} pn 
                LEFT JOIN {$this->table_ncc} ncc ON pn.ncc_id = ncc.id 
                LEFT JOIN {$this->table_users} u ON pn.nguoi_tao_id = u.id 
                ORDER BY pn.ngay_nhap DESC";
                
        return $wpdb->get_results($sql, ARRAY_A);
    }

    // --- Hàm lấy dữ liệu có giới hạn (Dùng cho Phân trang) ---
    public function getAllPaginated($limit, $offset, $filters = []) {
        global $wpdb;
        $filterData = $this->buildFilterQuery($filters);
        $where = $filterData['where'];
        $params = $filterData['params'];

        // Xử lý sắp xếp (Mặc định là mới nhất lên đầu)
        $sort_by = $filters['sort_by'] ?? 'id';
        $sort_dir = $filters['sort_dir'] ?? 'DESC';
        
        $allowed_cols = ['id' => 'pn.id', 'tong_tien' => 'pn.tong_tien', 'ngay_nhap' => 'pn.ngay_nhap'];
        $order_col = $allowed_cols[$sort_by] ?? 'pn.id';
        $order_dir = ($sort_dir === 'ASC') ? 'ASC' : 'DESC';

        $sql = "SELECT pn.*, ncc.ten_ncc, u.ho_ten AS nguoi_tao 
                FROM {$this->table_pn} pn 
                LEFT JOIN {$this->table_ncc} ncc ON pn.ncc_id = ncc.id 
                LEFT JOIN {$this->table_users} u ON pn.nguoi_tao_id = u.id 
                $where 
                ORDER BY $order_col $order_dir 
                LIMIT %d OFFSET %d";
        
        $params[] = $limit;
        $params[] = $offset;

        return $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    }

    // --- Hàm đếm tổng số lượng phiếu nhập để chia số trang ---
    public function getTotalCount($filters = []) {
        global $wpdb;
        $filterData = $this->buildFilterQuery($filters);
        $where = $filterData['where'];
        $params = $filterData['params'];

        $sql = "SELECT COUNT(id) FROM {$this->table_pn} pn $where";
        
        if (!empty($params)) {
            return $wpdb->get_var($wpdb->prepare($sql, $params));
        }
        return $wpdb->get_var($sql);
    }

    // Lấy số liệu thống kê cho 4 Cards trên Dashboard
    public function getStats() {
        global $wpdb;
        
        $total_phieu = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_pn}");
        $total_value = $wpdb->get_var("SELECT SUM(tong_tien) FROM {$this->table_pn}");
        $cho_thanh_toan = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_pn} WHERE trang_thai = 'Chưa thanh toán'");
        $ncc_moi = $wpdb->get_var("SELECT COUNT(id) FROM {$this->table_ncc} WHERE MONTH(ngay_tao) = MONTH(CURRENT_DATE())");

        return [
            'total_phieu' => $total_phieu ?: 0,
            'total_value' => $total_value ?: 0,
            'cho_thanh_toan' => $cho_thanh_toan ?: 0,
            'ncc_moi' => $ncc_moi ?: 0
        ];
    }

    // ==========================================
    // CÁC HÀM MỚI BỔ SUNG ĐỂ XEM & SỬA
    // ==========================================
    
    // 1. Lấy thông tin chung của 1 phiếu nhập theo ID
    public function getById($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM {$this->table_pn} WHERE id = %d", $id);
        return $wpdb->get_row($sql, ARRAY_A);
    }

    // 2. Lấy danh sách sản phẩm chi tiết của 1 phiếu
    public function getDetails($phieu_nhap_id) {
        global $wpdb;
        $table_ctpn = $wpdb->prefix . 'qln_chi_tiet_phieu_nhap';
        $sql = $wpdb->prepare("SELECT * FROM {$table_ctpn} WHERE phieu_nhap_id = %d", $phieu_nhap_id);
        return $wpdb->get_results($sql, ARRAY_A);
    }

    // 3. Xử lý lưu cập nhật Phiếu nhập (Dành riêng cho trạng thái Chờ xử lý)
    public function updatePhieuNhap($id, $data, $details) {
        global $wpdb;
        $table_ctpn = $wpdb->prefix . 'qln_chi_tiet_phieu_nhap';

        $wpdb->query('START TRANSACTION');

        try {
            // Vì phiếu đang Chờ xử lý (chưa cộng kho) nên ta xóa thẳng chi tiết cũ không cần bù trừ kho
            $wpdb->delete($table_ctpn, ['phieu_nhap_id' => $id]);

            // Cập nhật thông tin phiếu chính
            $wpdb->update($this->table_pn, [
                'ncc_id' => $data['ncc_id'],
                'tong_tien' => $data['tong_tien'],
                'ghi_chu' => $data['ghi_chu'],
                'ngay_nhap' => $data['ngay_nhap']
            ], ['id' => $id]);

            // Thêm chi tiết mới (Cũng chỉ lưu nháp, không cộng kho)
            foreach ($details as $item) {
                $wpdb->insert($table_ctpn, [
                    'phieu_nhap_id' => $id,
                    'san_pham_id' => $item['san_pham_id'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'thanh_tien' => $item['thanh_tien']
                ]);
            }

            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    // ==========================================
    // CÁC HÀM CRUD CƠ BẢN ĐÃ CÓ
    // ==========================================

    // Xử lý tạo Phiếu Nhập kho và Cập nhật Tồn kho
    public function createPhieuNhap($data, $details) {
        global $wpdb;
        $table_ctpn = $wpdb->prefix . 'qln_chi_tiet_phieu_nhap'; 
        
        $wpdb->query('START TRANSACTION');

        try {
            // 1. Lưu Phiếu nhập chính với trạng thái "Chờ xử lý"
            $wpdb->insert($this->table_pn, [
                'ma_pn' => $data['ma_pn'],
                'ncc_id' => $data['ncc_id'],
                'nguoi_tao_id' => $data['nguoi_tao_id'],
                'tong_tien' => $data['tong_tien'],
                'ghi_chu' => $data['ghi_chu'],
                'trang_thai' => 'Chờ xử lý', // <--- ĐÃ SỬA THÀNH CHỜ XỬ LÝ
                'ngay_nhap' => $data['ngay_nhap']
            ]);
            $phieu_nhap_id = $wpdb->insert_id;
            
            if (!$phieu_nhap_id) throw new Exception("Lỗi tạo phiếu nhập");

            // 2. CHỈ LƯU CHI TIẾT (Đã xóa lệnh UPDATE cộng kho ở đây)
            foreach ($details as $item) {
                $wpdb->insert($table_ctpn, [
                    'phieu_nhap_id' => $phieu_nhap_id,
                    'san_pham_id' => $item['san_pham_id'],
                    'so_luong' => $item['so_luong'],
                    'gia_nhap' => $item['gia_nhap'],
                    'thanh_tien' => $item['thanh_tien']
                ]);
            }

            $wpdb->query('COMMIT');
            return true;

        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    public function approvePhieuNhap($id) {
        global $wpdb;
        $table_ctpn = $wpdb->prefix . 'qln_chi_tiet_phieu_nhap';
        $table_sp = $wpdb->prefix . 'qln_san_pham';

        $wpdb->query('START TRANSACTION');
        try {
            // 1. Đổi trạng thái thành Đã nhập kho
            $wpdb->update($this->table_pn, ['trang_thai' => 'Đã nhập kho'], ['id' => $id]);

            // 2. Lấy danh sách chi tiết của phiếu này
            $details = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_ctpn} WHERE phieu_nhap_id = %d", $id), ARRAY_A);

            // 3. Tiến hành CỘNG TỒN KHO & CẬP NHẬT GIÁ
            foreach ($details as $item) {
                $wpdb->query($wpdb->prepare(
                    "UPDATE {$table_sp} SET so_luong_ton = so_luong_ton + %d, gia_nhap = %f WHERE id = %d",
                    $item['so_luong'], $item['gia_nhap'], $item['san_pham_id']
                ));
            }

            $wpdb->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return false;
        }
    }

    public function deletePhieuNhap($id) {
        global $wpdb;

        $result = $wpdb->update(
            $this->table_pn, 
            ['trang_thai' => 'Đã hủy'], // Đổi trạng thái thay vì xóa
            ['id' => $id]
        );

        if ($result !== false) {
            return true;
        }
        return false;
    }

    private function buildFilterQuery($filters) {
        global $wpdb;
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (pn.ma_pn LIKE %s OR pn.ghi_chu LIKE %s)";
            $params[] = '%' . $wpdb->esc_like($filters['search']) . '%';
            $params[] = '%' . $wpdb->esc_like($filters['search']) . '%';
        }
        if (!empty($filters['status'])) {
            $where .= " AND pn.trang_thai = %s";
            $params[] = $filters['status'];
        }
        if (!empty($filters['ncc_id'])) {
            $where .= " AND pn.ncc_id = %d";
            $params[] = $filters['ncc_id'];
        }
        if (!empty($filters['tu_ngay'])) {
            $where .= " AND DATE(pn.ngay_nhap) >= %s";
            $params[] = $filters['tu_ngay'];
        }
        if (!empty($filters['den_ngay'])) {
            $where .= " AND DATE(pn.ngay_nhap) <= %s";
            $params[] = $filters['den_ngay'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where .= " AND pn.tong_tien >= %f";
            $params[] = floatval($filters['min_price']);
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where .= " AND pn.tong_tien <= %f";
            $params[] = floatval($filters['max_price']);
        }

        return ['where' => $where, 'params' => $params];
    }
}