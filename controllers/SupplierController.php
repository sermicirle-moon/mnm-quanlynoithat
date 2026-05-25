<?php
class SupplierController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }

        $repo = new SupplierRepository();
        $action = sanitize_key($_GET['action'] ?? 'list');
        $filters = [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'trang_thai' => sanitize_text_field($_GET['trang_thai'] ?? ''),
        ];

        if ($action === 'create') {
            $this->showForm($repo);
            return;
        }
        if ($action === 'edit') {
            $this->showForm($repo, (int) ($_GET['id'] ?? 0));
            return;
        }
        if ($action === 'store') {
            $this->store($repo);
            return;
        }
        if ($action === 'update') {
            $this->update($repo, (int) ($_GET['id'] ?? 0));
            return;
        }
        if ($action === 'delete') {
            $this->delete($repo, (int) ($_GET['id'] ?? 0));
            return;
        }
        if ($action === 'export') {
            $this->exportCsv($repo->getAllWithFilters($filters));
        }
        if ($action === 'pdf') {
            $this->exportPdf($repo->getAllWithFilters($filters));
        }

        $limit = 5;
        $current_page = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
        $offset = ($current_page - 1) * $limit;
        $suppliers = $repo->getAllPaginated($limit, $offset, $filters);
        $stats = $repo->getStats();
        $total_items = $repo->getTotalCount($filters);
        $total_pages = max(1, (int) ceil($total_items / $limit));
        $url_params = $this->buildUrlParams($filters);
        $roleId = $_SESSION['qln_role_id'];

        $base_view_path = plugin_dir_path(__FILE__) . '../views/supplier/';
        switch ($roleId) {
            case 1: $view_name = 'supplier-view.php'; break;
            case 3: $view_name = 'supplier-view.php'; break;
            default: $view_name = 'supplier-view.php';
        }

        $view_content = $base_view_path . $view_name;
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function showForm($repo, $id = null) {
        $supplier = $id ? $repo->getById($id) : null;
        if ($id && !$supplier) {
            $_SESSION['qln_error'] = "Nhà cung cấp không tồn tại!";
            wp_redirect(admin_url('admin.php?page=qln-suppliers'));
            exit;
        }

        $base_view_path = plugin_dir_path(__FILE__) . '../views/supplier/';
        $view_content = $base_view_path . 'supplier-form.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function store($repo) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        global $wpdb;
        $data = $this->getSupplierPostData();

        if ($data['ma_ncc'] === '' || $data['ten_ncc'] === '' || $data['sdt'] === '') {
            $_SESSION['qln_error'] = "Vui lòng nhập mã, tên và số điện thoại nhà cung cấp!";
        } elseif ($repo->create($data)) {
            $_SESSION['qln_success'] = "Thêm nhà cung cấp thành công!";
        } else {
            $_SESSION['qln_error'] = "Thêm nhà cung cấp thất bại! Chi tiết lỗi: " . ($wpdb->last_error ?: 'Không xác định được lỗi.');
        }

        wp_redirect(admin_url('admin.php?page=qln-suppliers'));
        exit;
    }

    private function update($repo, $id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        global $wpdb;
        $data = $this->getSupplierPostData();

        if ($data['ma_ncc'] === '' || $data['ten_ncc'] === '' || $data['sdt'] === '') {
            $_SESSION['qln_error'] = "Vui lòng nhập mã, tên và số điện thoại nhà cung cấp!";
        } elseif ($id > 0 && $repo->update($id, $data) !== false) {
            $_SESSION['qln_success'] = "Cập nhật nhà cung cấp thành công!";
        } else {
            $_SESSION['qln_error'] = "Cập nhật nhà cung cấp thất bại! Chi tiết lỗi: " . ($wpdb->last_error ?: 'Không xác định được lỗi.');
        }

        wp_redirect(admin_url('admin.php?page=qln-suppliers'));
        exit;
    }

    private function delete($repo, $id) {
        if ($id > 0 && $repo->delete($id)) {
            $_SESSION['qln_success'] = "Xóa nhà cung cấp thành công!";
        } else {
            $_SESSION['qln_error'] = "Xóa nhà cung cấp thất bại!";
        }

        wp_redirect(admin_url('admin.php?page=qln-suppliers'));
        exit;
    }

    private function getSupplierPostData() {
        return [
            'ma_ncc'        => sanitize_text_field($_POST['ma_ncc'] ?? ''),
            'ten_ncc'       => sanitize_text_field($_POST['ten_ncc'] ?? ''),
            'nguoi_lien_he' => sanitize_text_field($_POST['nguoi_lien_he'] ?? ''),
            'sdt'           => sanitize_text_field($_POST['sdt'] ?? ''),
            'email'         => sanitize_email($_POST['email'] ?? ''),
            'dia_chi'       => sanitize_textarea_field($_POST['dia_chi'] ?? ''),
            'trang_thai'    => isset($_POST['trang_thai']) ? (int) $_POST['trang_thai'] : 1,
        ];
    }

    private function buildUrlParams($filters) {
        $params = '';
        foreach ($filters as $key => $value) {
            if ($value !== '') {
                $params .= '&' . $key . '=' . urlencode($value);
            }
        }
        return $params;
    }

    private function exportCsv($suppliers) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-suppliers-' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        if ($output === false) exit;
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['ID', 'Mã NCC', 'Tên NCC', 'Người liên hệ', 'SĐT', 'Email', 'Địa chỉ', 'Trạng thái', 'Ngày tạo']);
        foreach ($suppliers as $supplier) {
            fputcsv($output, [$supplier->id, $supplier->ma_ncc, $supplier->ten_ncc, $supplier->nguoi_lien_he, $supplier->sdt, $supplier->email, $supplier->dia_chi, $supplier->getTrangThaiText(), $supplier->ngay_tao]);
        }
        fclose($output);
        exit;
    }

    private function exportPdf($suppliers) {
        $filename = $this->buildPdfFilename('suppliers');

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"><title><?php echo esc_html($filename); ?></title>
        <style>body{font-family:Arial,sans-serif;color:#0f172a;padding:24px}h1{font-size:24px;margin-bottom:4px}.meta{color:#64748b;margin-bottom:20px}.actions{display:flex;gap:8px;margin-bottom:16px}button{border:1px solid #cbd5e1;background:#fff;border-radius:8px;padding:8px 12px;font-weight:700;cursor:pointer}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #e2e8f0;padding:8px;text-align:left}th{background:#f1f5f9}@page{margin:12mm}@media print{body{padding:0}.actions{display:none}}</style>
        </head><body><div class="actions"><button onclick="window.print()">In / Lưu PDF</button><button onclick="window.close()">Đóng</button></div><h1>Báo cáo nhà cung cấp</h1><p class="meta">Tên file gợi ý: <?php echo esc_html($filename); ?>.pdf</p><p class="meta">Tổng: <?php echo esc_html((string) count($suppliers)); ?> nhà cung cấp - Ngày xuất: <?php echo esc_html(date_i18n('d/m/Y H:i')); ?></p><table><thead><tr><th>Mã NCC</th><th>Tên NCC</th><th>Người liên hệ</th><th>SĐT</th><th>Email</th><th>Địa chỉ</th><th>Trạng thái</th></tr></thead><tbody>
        <?php foreach ($suppliers as $supplier): ?><tr><td><?php echo esc_html($supplier->ma_ncc); ?></td><td><?php echo esc_html($supplier->ten_ncc); ?></td><td><?php echo esc_html($supplier->nguoi_lien_he); ?></td><td><?php echo esc_html($supplier->sdt); ?></td><td><?php echo esc_html($supplier->email); ?></td><td><?php echo esc_html($supplier->dia_chi); ?></td><td><?php echo esc_html($supplier->getTrangThaiText()); ?></td></tr><?php endforeach; ?>
        </tbody></table></body></html>
        <?php
        exit;
    }

    private function buildPdfFilename($module) {
        $username = sanitize_file_name((string) ($_SESSION['qln_user_name'] ?? 'admin'));
        return $username . '_' . $module . '_' . date_i18n('Y-m-d_H-i-s');
    }
}
