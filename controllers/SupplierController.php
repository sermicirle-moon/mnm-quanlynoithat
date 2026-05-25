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
        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-suppliers-' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        if ($output === false) exit;
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['Mã NCC', 'Tên NCC', 'Người liên hệ', 'SĐT', 'Email', 'Địa chỉ', 'Trạng thái']);
        foreach ($suppliers as $supplier) {
            fputcsv($output, [$supplier->ma_ncc, $supplier->ten_ncc, $supplier->nguoi_lien_he, $supplier->sdt, $supplier->email, $supplier->dia_chi, $supplier->getTrangThaiText()]);
        }
        fclose($output);
        exit;
    }

    private function exportPdf($suppliers) {
        nocache_headers();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"><title>Bao cao nha cung cap</title>
        <style>body{font-family:Arial,sans-serif;color:#0f172a;padding:24px}h1{font-size:24px;margin-bottom:4px}.meta{color:#64748b;margin-bottom:20px}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #e2e8f0;padding:8px;text-align:left}th{background:#f1f5f9}.print{margin-bottom:16px}@media print{.print{display:none}}</style>
        </head><body><button class="print" onclick="window.print()">In / Lưu PDF</button><h1>Bao cao nha cung cap</h1><p class="meta">Tong: <?php echo esc_html((string) count($suppliers)); ?> nha cung cap - Ngay xuat: <?php echo esc_html(date('d/m/Y H:i')); ?></p><table><thead><tr><th>Ma NCC</th><th>Ten NCC</th><th>Nguoi lien he</th><th>SDT</th><th>Email</th><th>Dia chi</th><th>Trang thai</th></tr></thead><tbody>
        <?php foreach ($suppliers as $supplier): ?><tr><td><?php echo esc_html($supplier->ma_ncc); ?></td><td><?php echo esc_html($supplier->ten_ncc); ?></td><td><?php echo esc_html($supplier->nguoi_lien_he); ?></td><td><?php echo esc_html($supplier->sdt); ?></td><td><?php echo esc_html($supplier->email); ?></td><td><?php echo esc_html($supplier->dia_chi); ?></td><td><?php echo esc_html($supplier->getTrangThaiText()); ?></td></tr><?php endforeach; ?>
        </tbody></table><script>window.addEventListener('load',function(){window.print();});</script></body></html>
        <?php
        exit;
    }
}
