<?php
class ReportController {
    public function handleRequest(): void {
        $action = isset($_GET['qln_report_export']) ? sanitize_key(wp_unslash($_GET['qln_report_export'])) : '';

        if ($action === 'csv') {
            $this->exportCsv();
            return;
        }

        if ($action === 'pdf') {
            $this->exportPdf();
            return;
        }

        $this->index();
    }

    public function index(): void {
        $this->requireLogin();

        $data = $this->buildReportData();
        $roleId = $_SESSION['qln_role_id'];

        $base_view_path = plugin_dir_path(__FILE__) . '../views/report/';
        switch ($roleId) {
            case 1: $view_name = 'report-view.php'; break;
            default: $view_name = 'report-view.php';
        }

        $view_content = $base_view_path . $view_name;
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function buildReportData(): array {
        $repo = new ReportRepository();
        return $repo->getReportData();
    }

    private function requireLogin(): void {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }
    }

    private function exportCsv(): void {
        $this->requireLogin();
        $data = $this->buildReportData();

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-report-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        if ($output === false) {
            exit;
        }

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['Chỉ số', 'Giá trị']);
        foreach ($data['summary'] as $key => $value) {
            fputcsv($output, [$key, $value]);
        }

        fputcsv($output, []);
        fputcsv($output, ['Tổng hợp tháng', 'Chi phí nhập hàng', 'Doanh thu bán ra', 'Lợi nhuận gộp', 'Tỷ lệ lãi (%)']);
        foreach ($data['monthly_comparison'] as $row) {
            fputcsv($output, [$row['label'], $row['cost'], $row['revenue'], $row['profit'], $row['profit_rate']]);
        }

        fputcsv($output, []);
        fputcsv($output, ['Hóa đơn gần đây', 'Tổng tiền', 'Ngày tạo', 'Trạng thái']);
        foreach ($data['recent_invoices'] as $invoice) {
            fputcsv($output, [$invoice['ma_hd'], $invoice['tong_tien'], $invoice['ngay_tao'], $invoice['trang_thai']]);
        }

        fclose($output);
        exit;
    }

    private function exportPdf(): void {
        $this->requireLogin();
        $data = $this->buildReportData();
        $summary = $data['summary'];
        ?>
        <!doctype html>
        <html lang="vi">
        <head>
            <meta charset="utf-8">
            <title><?php echo esc_html__('Báo cáo TimberFlow', 'qln'); ?></title>
            <style>
                body { font-family: Arial, sans-serif; color: #0f172a; margin: 32px; }
                h1 { margin-bottom: 4px; }
                table { border-collapse: collapse; width: 100%; margin-top: 18px; }
                th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
                th { background: #f1f5f9; }
                .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 24px 0; }
                .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 14px; }
                .label { color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; }
                .value { font-size: 22px; font-weight: 700; margin-top: 8px; }
                @media print { button { display: none; } body { margin: 16px; } }
            </style>
        </head>
        <body>
            <button onclick="window.print()">In / Lưu PDF</button>
            <h1>Báo cáo TimberFlow</h1>
            <p>Xuất ngày <?php echo esc_html(date_i18n('d/m/Y H:i')); ?></p>
            <div class="grid">
                <div class="card"><div class="label">Doanh thu</div><div class="value"><?php echo esc_html(number_format((float) $summary['total_revenue'])); ?>đ</div></div>
                <div class="card"><div class="label">Chi phí nhập</div><div class="value"><?php echo esc_html(number_format((float) $summary['stock_in_cost'])); ?>đ</div></div>
                <div class="card"><div class="label">Lợi nhuận</div><div class="value"><?php echo esc_html(number_format((float) $summary['gross_profit'])); ?>đ</div></div>
                <div class="card"><div class="label">Tồn kho</div><div class="value"><?php echo esc_html(number_format((int) $summary['inventory_quantity'])); ?> SP</div></div>
            </div>
            <h2>Tổng hợp tháng</h2>
            <table>
                <thead><tr><th>Tháng</th><th>Chi phí</th><th>Doanh thu</th><th>Lợi nhuận</th><th>Tỷ lệ lãi</th></tr></thead>
                <tbody>
                    <?php foreach ($data['monthly_comparison'] as $row): ?>
                        <tr>
                            <td><?php echo esc_html($row['label']); ?></td>
                            <td><?php echo esc_html(number_format((float) $row['cost'])); ?>đ</td>
                            <td><?php echo esc_html(number_format((float) $row['revenue'])); ?>đ</td>
                            <td><?php echo esc_html(number_format((float) $row['profit'])); ?>đ</td>
                            <td><?php echo esc_html((string) $row['profit_rate']); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <script>window.print();</script>
        </body>
        </html>
        <?php
        exit;
    }
}
