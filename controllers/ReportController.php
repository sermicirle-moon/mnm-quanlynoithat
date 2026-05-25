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

        $filters = $this->getFilters();
        $data = $this->buildReportData($filters);
        $roleId = $_SESSION['qln_role_id'];

        $base_view_path = plugin_dir_path(__FILE__) . '../views/report/';
        switch ($roleId) {
            case 1: $view_name = 'report-view.php'; break;
            default: $view_name = 'report-view.php';
        }

        $view_content = $base_view_path . $view_name;
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function buildReportData(array $filters = []): array {
        $repo = new ReportRepository();
        return $repo->getReportData($filters);
    }

    private function getFilters(): array {
        $from = sanitize_text_field(wp_unslash($_GET['from_date'] ?? ''));
        $to = sanitize_text_field(wp_unslash($_GET['to_date'] ?? ''));

        return [
            'from_date' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ? $from : '',
            'to_date' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $to) ? $to : '',
        ];
    }

    private function requireLogin(): void {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }
    }

    private function exportCsv(): void {
        $this->requireLogin();
        $filters = $this->getFilters();
        $data = $this->buildReportData($filters);

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-report-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        if ($output === false) {
            exit;
        }

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['Kỳ báo cáo', $this->formatDateRange($filters)]);
        fputcsv($output, []);
        fputcsv($output, ['Chỉ số', 'Giá trị']);
        foreach ($data['summary'] as $key => $value) {
            fputcsv($output, [$key, $value]);
        }

        $this->writeCsvSection($output, 'Tổng hợp tháng', ['Tháng', 'Chi phí nhập hàng', 'Doanh thu bán ra', 'Lợi nhuận gộp', 'Tỷ lệ lãi (%)'], $data['monthly_comparison'], ['label', 'cost', 'revenue', 'profit', 'profit_rate']);
        $this->writeCsvSection($output, 'Hóa đơn gần đây', ['Mã HĐ', 'Tổng tiền', 'Ngày tạo', 'Trạng thái'], $data['recent_invoices'], ['ma_hd', 'tong_tien', 'ngay_tao', 'trang_thai']);
        $this->writeCsvSection($output, 'Top sản phẩm', ['Mã SP', 'Tên SP', 'Số lượng bán', 'Doanh thu', 'Số đơn'], $data['top_products'], ['ma_sp', 'ten_sp', 'quantity_sold', 'revenue', 'order_count']);
        $this->writeCsvSection($output, 'Top khách hàng', ['Mã KH', 'Tên KH', 'SĐT', 'Số hóa đơn', 'Tổng chi tiêu'], $data['top_customers'], ['ma_kh', 'ten_kh', 'sdt', 'invoice_count', 'total_spent']);
        $this->writeCsvSection($output, 'Trạng thái hóa đơn', ['Trạng thái', 'Số lượng', 'Giá trị'], $data['invoice_status_breakdown'], ['status', 'count', 'amount']);
        $this->writeCsvSection($output, 'Trạng thái giao hóa đơn', ['Trạng thái', 'Số lượng', 'Giá trị'], $data['shipping_status_breakdown'], ['status', 'count', 'amount']);
        $this->writeCsvSection($output, 'Cảnh báo tồn kho', ['Mã SP', 'Tên SP', 'Danh mục', 'Tồn kho', 'Trạng thái'], $data['stock_alerts'], ['ma_sp', 'ten_sp', 'category', 'so_luong_ton', 'trang_thai']);
        $this->writeCsvSection($output, 'Nhập hàng theo nhà cung cấp', ['Mã NCC', 'Tên NCC', 'Số phiếu', 'Tổng nhập'], $data['supplier_purchasing'], ['ma_ncc', 'ten_ncc', 'receipt_count', 'total_purchase']);
        $this->writeCsvSection($output, 'Trạng thái phiếu xuất', ['Trạng thái', 'Số phiếu', 'Phí vận chuyển'], $data['stock_out_status_breakdown'], ['status', 'count', 'shipping_fee']);
        $this->writeCsvSection($output, 'Hiệu suất nhà vận chuyển', ['Nhà vận chuyển', 'Loại hình', 'Số chuyến', 'Đã giao', 'Phí vận chuyển'], $data['carrier_shipping_summary'], ['ten_nvc', 'loai_hinh', 'shipment_count', 'delivered_count', 'shipping_fee']);

        fclose($output);
        exit;
    }

    private function exportPdf(): void {
        $this->requireLogin();
        $filters = $this->getFilters();
        $data = $this->buildReportData($filters);
        $summary = $data['summary'];
        $filename = $this->buildPdfFilename('report');

        nocache_headers();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
        <!doctype html>
        <html lang="vi">
        <head>
            <meta charset="utf-8">
            <title><?php echo esc_html($filename); ?></title>
            <style>
                body { font-family: Arial, sans-serif; color: #0f172a; margin: 32px; }
                h1 { margin-bottom: 4px; } h2 { margin-top: 24px; }
                .actions { display: flex; gap: 8px; margin-bottom: 16px; }
                button { border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; padding: 8px 12px; font-weight: 700; cursor: pointer; }
                table { border-collapse: collapse; width: 100%; margin-top: 12px; font-size: 12px; }
                th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
                th { background: #f1f5f9; }
                .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 24px 0; }
                .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 14px; }
                .label { color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; }
                .value { font-size: 22px; font-weight: 700; margin-top: 8px; }
                @media print { .actions { display: none; } body { margin: 16px; } }
            </style>
        </head>
        <body>
            <div class="actions"><button onclick="window.print()">In / Lưu PDF</button><button onclick="window.close()">Đóng</button></div>
            <h1>Báo cáo TimberFlow</h1>
            <p>Tên file gợi ý: <?php echo esc_html($filename); ?>.pdf</p>
            <p>Kỳ báo cáo: <?php echo esc_html($this->formatDateRange($filters)); ?> - Xuất ngày <?php echo esc_html(date_i18n('d/m/Y H:i')); ?></p>
            <div class="grid">
                <div class="card"><div class="label">Doanh thu</div><div class="value"><?php echo esc_html(number_format((float) $summary['total_revenue'])); ?>đ</div></div>
                <div class="card"><div class="label">Chi phí nhập</div><div class="value"><?php echo esc_html(number_format((float) $summary['stock_in_cost'])); ?>đ</div></div>
                <div class="card"><div class="label">Lợi nhuận</div><div class="value"><?php echo esc_html(number_format((float) $summary['gross_profit'])); ?>đ</div></div>
                <div class="card"><div class="label">Tồn kho</div><div class="value"><?php echo esc_html(number_format((int) $summary['inventory_quantity'])); ?> SP</div></div>
            </div>
            <?php $this->renderPdfTable('Tổng hợp tháng', ['Tháng', 'Chi phí', 'Doanh thu', 'Lợi nhuận', 'Tỷ lệ lãi'], $data['monthly_comparison'], ['label', 'cost', 'revenue', 'profit', 'profit_rate']); ?>
            <?php $this->renderPdfTable('Top sản phẩm', ['Mã SP', 'Tên SP', 'SL bán', 'Doanh thu'], $data['top_products'], ['ma_sp', 'ten_sp', 'quantity_sold', 'revenue']); ?>
            <?php $this->renderPdfTable('Top khách hàng', ['Mã KH', 'Tên KH', 'Số HĐ', 'Tổng chi'], $data['top_customers'], ['ma_kh', 'ten_kh', 'invoice_count', 'total_spent']); ?>
            <?php $this->renderPdfTable('Trạng thái hóa đơn', ['Trạng thái', 'Số lượng', 'Giá trị'], $data['invoice_status_breakdown'], ['status', 'count', 'amount']); ?>
            <?php $this->renderPdfTable('Cảnh báo tồn kho', ['Mã SP', 'Tên SP', 'Tồn kho', 'Trạng thái'], $data['stock_alerts'], ['ma_sp', 'ten_sp', 'so_luong_ton', 'trang_thai']); ?>
            <?php $this->renderPdfTable('Nhập hàng theo nhà cung cấp', ['Mã NCC', 'Tên NCC', 'Số phiếu', 'Tổng nhập'], $data['supplier_purchasing'], ['ma_ncc', 'ten_ncc', 'receipt_count', 'total_purchase']); ?>
            <script>window.addEventListener('load',function(){window.print();});</script>
        </body>
        </html>
        <?php
        exit;
    }

    private function buildPdfFilename(string $module): string {
        $username = sanitize_file_name((string) ($_SESSION['qln_user_name'] ?? 'admin'));
        return $username . '_' . $module . '_' . date_i18n('Y-m-d_H-i-s');
    }

    private function formatDateRange(array $filters): string {
        if (!empty($filters['from_date']) && !empty($filters['to_date'])) return date_i18n('d/m/Y', strtotime($filters['from_date'])) . ' - ' . date_i18n('d/m/Y', strtotime($filters['to_date']));
        if (!empty($filters['from_date'])) return 'Từ ' . date_i18n('d/m/Y', strtotime($filters['from_date']));
        if (!empty($filters['to_date'])) return 'Đến ' . date_i18n('d/m/Y', strtotime($filters['to_date']));
        return 'Toàn bộ dữ liệu';
    }

    private function writeCsvSection($output, string $title, array $headers, array $rows, array $keys): void {
        fputcsv($output, []);
        fputcsv($output, [$title]);
        fputcsv($output, $headers);
        foreach ($rows as $row) fputcsv($output, array_map(static fn($key) => $row[$key] ?? '', $keys));
    }

    private function renderPdfTable(string $title, array $headers, array $rows, array $keys): void {
        if (empty($rows)) return;
        echo '<h2>' . esc_html($title) . '</h2><table><thead><tr>';
        foreach ($headers as $header) echo '<th>' . esc_html($header) . '</th>';
        echo '</tr></thead><tbody>';
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($keys as $key) echo '<td>' . esc_html((string) ($row[$key] ?? '')) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
}
