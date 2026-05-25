<?php
class ShippingController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        $repo = new ShippingRepository();
        $action = sanitize_key($_GET['action'] ?? 'list');

        switch ($action) {
            case 'create': $this->showForm($repo); return;
            case 'store': $this->store($repo); return;
            case 'edit': $this->showForm($repo, (int) ($_GET['id'] ?? 0)); return;
            case 'update': $this->update($repo, (int) ($_GET['id'] ?? 0)); return;
            case 'delete': $this->delete($repo, (int) ($_GET['id'] ?? 0)); return;
        }

        $filters = [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'trang_thai' => sanitize_text_field($_GET['trang_thai'] ?? ''),
            'loai_hinh' => sanitize_text_field($_GET['loai_hinh'] ?? ''),
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
        $carriers = $repo->getAllPaginated($limit, $offset, $filters);
        $stats = $repo->getStats();
        $total_items = $repo->getTotalCount($filters);
        $total_pages = max(1, (int) ceil($total_items / $limit));
        $url_params = $this->buildUrlParams($filters);
        $roleId = $_SESSION['qln_role_id'];

        $base_view_path = plugin_dir_path(__FILE__) . '../views/shipping/';
        switch ($roleId) {
            case 1: $view_name = 'shipping-view.php'; break;
            default: $view_name = 'shipping-view.php';
        }

        $view_content = $base_view_path . $view_name;
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function showForm($repo, $id = null) {
        $carrier = $id ? $repo->getById($id) : null;
        $base_view_path = plugin_dir_path(__FILE__) . '../views/shipping/';
        $view_content = $base_view_path . 'shipping-form.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function store($repo) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_redirect(admin_url('admin.php?page=qln-shipping')); exit;
        }
        $repo->createCarrier($this->getCarrierPostData());
        wp_redirect(admin_url('admin.php?page=qln-shipping')); exit;
    }

    private function update($repo, $id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_redirect(admin_url('admin.php?page=qln-shipping')); exit;
        }
        if ($id > 0) {
            $repo->updateCarrier($id, $this->getCarrierPostData());
        }
        wp_redirect(admin_url('admin.php?page=qln-shipping')); exit;
    }

    private function delete($repo, $id) {
        if ($id > 0) {
            $repo->deactivateCarrier($id);
        }
        wp_redirect(admin_url('admin.php?page=qln-shipping')); exit;
    }

    private function getCarrierPostData() {
        return [
            'ma_nvc' => sanitize_text_field($_POST['ma_nvc'] ?? ''),
            'ten_nvc' => sanitize_text_field($_POST['ten_nvc'] ?? ''),
            'loai_hinh' => sanitize_text_field($_POST['loai_hinh'] ?? ''),
            'sdt_tai_xe' => sanitize_text_field($_POST['sdt_tai_xe'] ?? ''),
            'bien_so_xe' => sanitize_text_field($_POST['bien_so_xe'] ?? ''),
            'trang_thai' => (int) ($_POST['trang_thai'] ?? 1),
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

    private function exportCsv($carriers) {
        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-shipping-' . date('Y-m-d') . '.csv"');
        $output = fopen('php://output', 'w');
        if ($output === false) exit;
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['Mã NVC', 'Tên NVC', 'Loại hình', 'SĐT tài xế', 'Biển số xe', 'Trạng thái']);
        foreach ($carriers as $carrier) {
            fputcsv($output, [$carrier->ma_nvc, $carrier->ten_nvc, $carrier->loai_hinh, $carrier->sdt_tai_xe, $carrier->bien_so_xe, $carrier->getTrangThaiText()]);
        }
        fclose($output);
        exit;
    }

    private function exportPdf($carriers) {
        nocache_headers();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"><title>Bao cao nha van chuyen</title>
        <style>body{font-family:Arial,sans-serif;color:#0f172a;padding:24px}h1{font-size:24px;margin-bottom:4px}.meta{color:#64748b;margin-bottom:20px}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #e2e8f0;padding:8px;text-align:left}th{background:#f1f5f9}.print{margin-bottom:16px}@media print{.print{display:none}}</style>
        </head><body><button class="print" onclick="window.print()">In / Lưu PDF</button><h1>Bao cao nha van chuyen</h1><p class="meta">Tong: <?php echo esc_html((string) count($carriers)); ?> nha van chuyen - Ngay xuat: <?php echo esc_html(date('d/m/Y H:i')); ?></p><table><thead><tr><th>Ma NVC</th><th>Ten NVC</th><th>Loai hinh</th><th>SDT tai xe</th><th>Bien so xe</th><th>Trang thai</th></tr></thead><tbody>
        <?php foreach ($carriers as $carrier): ?><tr><td><?php echo esc_html($carrier->ma_nvc); ?></td><td><?php echo esc_html($carrier->ten_nvc); ?></td><td><?php echo esc_html($carrier->loai_hinh); ?></td><td><?php echo esc_html($carrier->sdt_tai_xe); ?></td><td><?php echo esc_html($carrier->bien_so_xe); ?></td><td><?php echo esc_html($carrier->getTrangThaiText()); ?></td></tr><?php endforeach; ?>
        </tbody></table><script>window.addEventListener('load',function(){window.print();});</script></body></html>
        <?php
        exit;
    }
}
