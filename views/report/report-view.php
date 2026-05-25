<?php
if (!defined('ABSPATH')) exit;

$summary = $data['summary'] ?? [];
$filters = $data['filters'] ?? ['from_date' => '', 'to_date' => ''];
$recentInvoices = $data['recent_invoices'] ?? [];
$categorySales = $data['category_sales'] ?? [];
$revenueTrend = $data['revenue_trend'] ?? [];
$monthlyComparison = $data['monthly_comparison'] ?? [];
$topProducts = $data['top_products'] ?? [];
$topCustomers = $data['top_customers'] ?? [];
$invoiceStatuses = $data['invoice_status_breakdown'] ?? [];
$shippingStatuses = $data['shipping_status_breakdown'] ?? [];
$stockAlerts = $data['stock_alerts'] ?? [];
$supplierPurchasing = $data['supplier_purchasing'] ?? [];
$stockOutStatuses = $data['stock_out_status_breakdown'] ?? [];
$carrierSummary = $data['carrier_shipping_summary'] ?? [];

$totalCategoryRevenue = array_sum(array_map(static fn($row) => (float) $row['revenue'], $categorySales));
$maxTrendRevenue = max(1, ...array_map(static fn($row) => (float) $row['revenue'], $revenueTrend));
$chartPoints = [];
$trendCount = count($revenueTrend);

foreach ($revenueTrend as $index => $row) {
    $x = $trendCount > 1 ? (int) round(($index / ($trendCount - 1)) * 760 + 20) : 400;
    $y = (int) round(180 - (((float) $row['revenue'] / $maxTrendRevenue) * 150));
    $chartPoints[] = $x . ',' . max(20, $y);
}

$donutColors = ['#006c4a', '#82f5c1', '#131b2e', '#ffb86a', '#bec6e0', '#ba1a1a'];
$gradientStops = [];
$currentPercent = 0;
foreach ($categorySales as $index => $row) {
    $percent = $totalCategoryRevenue > 0 ? (((float) $row['revenue'] / $totalCategoryRevenue) * 100) : 0;
    $nextPercent = $currentPercent + $percent;
    $gradientStops[] = $donutColors[$index % count($donutColors)] . ' ' . round($currentPercent, 1) . '% ' . round($nextPercent, 1) . '%';
    $currentPercent = $nextPercent;
}
$donutBackground = !empty($gradientStops) ? implode(', ', $gradientStops) : '#e2e8f0 0% 100%';

$exportArgs = array_filter([
    'from_date' => $filters['from_date'] ?? '',
    'to_date' => $filters['to_date'] ?? '',
], static fn($value) => $value !== '');
$csvUrl = esc_url(add_query_arg(array_merge($exportArgs, ['qln_report_export' => 'csv']), admin_url('admin.php?page=qln-reports')));
$pdfUrl = esc_url(add_query_arg(array_merge($exportArgs, ['qln_report_export' => 'pdf']), admin_url('admin.php?page=qln-reports')));
$clearUrl = esc_url(admin_url('admin.php?page=qln-reports'));
?>

<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff] text-slate-900">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-chart-line"></i>
                Analytics & Reports
            </div>
            <h1 class="text-3xl font-bold tracking-tight">Phân tích &amp; Báo cáo</h1>
            <p class="text-sm text-slate-500 mt-1">Báo cáo doanh thu, nhập hàng, tồn kho, khách hàng và vận chuyển từ dữ liệu TimberFlow.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo $csvUrl; ?>" class="px-4 py-2 rounded-xl bg-white text-slate-700 text-sm font-bold border border-slate-200 shadow-sm hover:bg-slate-50"><i class="fa-solid fa-file-csv mr-2"></i>Xuất CSV</a>
            <a href="<?php echo $pdfUrl; ?>" target="_blank" rel="noopener" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-emerald-700/20"><i class="fa-solid fa-print mr-2"></i>In / PDF</a>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            <input type="hidden" name="page" value="qln-reports">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Từ ngày</label>
                <input type="date" name="from_date" value="<?php echo esc_attr($filters['from_date'] ?? ''); ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Đến ngày</label>
                <input type="date" name="to_date" value="<?php echo esc_attr($filters['to_date'] ?? ''); ?>" class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
            </div>
            <button class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold"><i class="fa-solid fa-filter mr-2"></i>Lọc báo cáo</button>
            <a href="<?php echo $clearUrl; ?>" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold text-center"><i class="fa-solid fa-rotate-right mr-2"></i>Reset</a>
            <p class="text-xs text-slate-400 md:text-right">Các chỉ số theo dòng tiền dùng trạng thái <b>Hoàn thành</b>; nhập kho dùng <b>Đã nhập kho</b>.</p>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Doanh thu hoàn thành</p><p class="text-2xl font-bold mt-2 text-emerald-700"><?php echo esc_html(number_format((float) ($summary['total_revenue'] ?? 0))); ?>đ</p><p class="text-xs text-slate-400 mt-2"><?php echo esc_html((string) ($summary['completed_invoice_count'] ?? 0)); ?> hóa đơn</p></div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Chi phí nhập</p><p class="text-2xl font-bold mt-2 text-rose-700"><?php echo esc_html(number_format((float) ($summary['stock_in_cost'] ?? 0))); ?>đ</p><p class="text-xs text-slate-400 mt-2">Phiếu đã nhập kho</p></div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Lợi nhuận gộp</p><p class="text-2xl font-bold mt-2 text-slate-900"><?php echo esc_html(number_format((float) ($summary['gross_profit'] ?? 0))); ?>đ</p><p class="text-xs text-slate-400 mt-2"><?php echo esc_html((string) ($summary['profit_rate'] ?? 0)); ?>% trên doanh thu</p></div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Giá trị tồn kho</p><p class="text-2xl font-bold mt-2 text-amber-700"><?php echo esc_html(number_format((float) ($summary['inventory_value'] ?? 0))); ?>đ</p><p class="text-xs text-slate-400 mt-2"><?php echo esc_html(number_format((int) ($summary['inventory_quantity'] ?? 0))); ?> sản phẩm tồn</p></div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Phí vận chuyển</p><p class="text-2xl font-bold mt-2 text-blue-700"><?php echo esc_html(number_format((float) ($summary['stock_out_fees'] ?? 0))); ?>đ</p><p class="text-xs text-slate-400 mt-2"><?php echo esc_html((string) ($summary['stock_out_count'] ?? 0)); ?> phiếu xuất</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-bold">Danh mục bán chạy</h2><i class="fa-solid fa-chart-pie text-slate-400"></i></div>
            <div class="flex flex-col items-center">
                <div class="w-48 h-48 rounded-full flex items-center justify-center" style="background: conic-gradient(<?php echo esc_attr($donutBackground); ?>);"><div class="w-32 h-32 bg-white rounded-full shadow-inner flex flex-col items-center justify-center"><span class="text-xs font-bold text-slate-500">TỔNG</span><span class="text-lg font-bold"><?php echo esc_html(qln_format_compact_money((float) $totalCategoryRevenue)); ?>đ</span></div></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6 w-full">
                    <?php foreach ($categorySales as $index => $row): $percent = $totalCategoryRevenue > 0 ? round(((float) $row['revenue'] / $totalCategoryRevenue) * 100, 1) : 0; ?>
                        <div class="flex items-center gap-2 text-xs text-slate-600"><span class="w-3 h-3 rounded-full" style="background: <?php echo esc_attr($donutColors[$index % count($donutColors)]); ?>"></span><span><?php echo esc_html($row['category']); ?> (<?php echo esc_html((string) $percent); ?>%)</span></div>
                    <?php endforeach; ?>
                    <?php if (empty($categorySales)): ?><p class="text-sm text-slate-400">Chưa có dữ liệu danh mục.</p><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-bold">Xu hướng tăng trưởng doanh thu</h2><span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">Hóa đơn hoàn thành</span></div>
            <div class="h-64"><svg class="w-full h-full" viewBox="0 0 800 220" role="img" aria-label="Xu hướng doanh thu theo tháng"><line x1="20" y1="40" x2="780" y2="40" stroke="#e2e8f0" stroke-dasharray="4" /><line x1="20" y1="90" x2="780" y2="90" stroke="#e2e8f0" stroke-dasharray="4" /><line x1="20" y1="140" x2="780" y2="140" stroke="#e2e8f0" stroke-dasharray="4" /><line x1="20" y1="190" x2="780" y2="190" stroke="#e2e8f0" stroke-dasharray="4" /><?php if (!empty($chartPoints)): ?><polyline points="<?php echo esc_attr(implode(' ', $chartPoints)); ?>" fill="none" stroke="#006c4a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /><?php foreach ($chartPoints as $point): [$cx, $cy] = explode(',', $point); ?><circle cx="<?php echo esc_attr($cx); ?>" cy="<?php echo esc_attr($cy); ?>" r="5" fill="#006c4a" /><?php endforeach; ?><?php endif; ?></svg></div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-xs text-slate-500"><?php foreach ($revenueTrend as $row): ?><div class="rounded-xl bg-slate-50 p-3"><p class="font-bold text-slate-700"><?php echo esc_html($row['label']); ?></p><p><?php echo esc_html(number_format((float) $row['revenue'])); ?>đ</p></div><?php endforeach; ?></div>
        </div>
    </div>

    <?php
    $renderTable = static function (string $title, string $subtitle, array $headers, array $rows, callable $rowRenderer, string $emptyText = 'Chưa có dữ liệu phù hợp.') {
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50"><h2 class="font-bold text-slate-900"><?php echo esc_html($title); ?></h2><p class="text-xs text-slate-400 mt-1"><?php echo esc_html($subtitle); ?></p></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100"><tr><?php foreach ($headers as $header): ?><th class="px-6 py-4"><?php echo esc_html($header); ?></th><?php endforeach; ?></tr></thead><tbody class="divide-y divide-slate-100"><?php foreach ($rows as $row) $rowRenderer($row); ?><?php if (empty($rows)): ?><tr><td colspan="<?php echo esc_attr((string) count($headers)); ?>" class="px-6 py-10 text-center text-slate-400 italic"><?php echo esc_html($emptyText); ?></td></tr><?php endif; ?></tbody></table></div>
        </div>
        <?php
    };
    ?>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <?php $renderTable('Top sản phẩm', 'Sản phẩm đóng góp doanh thu nhiều nhất.', ['Mã', 'Sản phẩm', 'SL bán', 'Doanh thu'], $topProducts, static function ($row) { ?><tr class="hover:bg-slate-50"><td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($row['ma_sp'] ?? ''); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html($row['ten_sp'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html(number_format((float) ($row['quantity_sold'] ?? 0))); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html(number_format((float) ($row['revenue'] ?? 0))); ?>đ</td></tr><?php }); ?>
        <?php $renderTable('Top khách hàng', 'Khách hàng có tổng chi tiêu cao nhất.', ['Mã', 'Khách hàng', 'Số HĐ', 'Tổng chi'], $topCustomers, static function ($row) { ?><tr class="hover:bg-slate-50"><td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($row['ma_kh'] ?? ''); ?></td><td class="px-6 py-4"><p class="font-bold"><?php echo esc_html($row['ten_kh'] ?? ''); ?></p><p class="text-xs text-slate-400"><?php echo esc_html($row['sdt'] ?? ''); ?></p></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['invoice_count'] ?? 0)); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html(number_format((float) ($row['total_spent'] ?? 0))); ?>đ</td></tr><?php }); ?>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <?php $renderTable('Trạng thái hóa đơn', 'Phân bổ số lượng và giá trị hóa đơn.', ['Trạng thái', 'Số lượng', 'Giá trị'], $invoiceStatuses, static function ($row) { ?><tr><td class="px-6 py-4 font-bold"><?php echo esc_html($row['status'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['count'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html(number_format((float) ($row['amount'] ?? 0))); ?>đ</td></tr><?php }); ?>
        <?php $renderTable('Trạng thái giao hóa đơn', 'Tình trạng giao hàng ghi trên hóa đơn.', ['Trạng thái', 'Số lượng', 'Giá trị'], $shippingStatuses, static function ($row) { ?><tr><td class="px-6 py-4 font-bold"><?php echo esc_html($row['status'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['count'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html(number_format((float) ($row['amount'] ?? 0))); ?>đ</td></tr><?php }); ?>
        <?php $renderTable('Trạng thái phiếu xuất', 'Theo dõi luồng vận chuyển từ phiếu xuất.', ['Trạng thái', 'Số phiếu', 'Phí VC'], $stockOutStatuses, static function ($row) { ?><tr><td class="px-6 py-4 font-bold"><?php echo esc_html($row['status'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['count'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html(number_format((float) ($row['shipping_fee'] ?? 0))); ?>đ</td></tr><?php }); ?>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <?php $renderTable('Cảnh báo tồn kho', 'Sản phẩm có tồn kho thấp hơn hoặc bằng 5.', ['Mã', 'Sản phẩm', 'Tồn', 'Trạng thái'], $stockAlerts, static function ($row) { ?><tr class="hover:bg-rose-50/60"><td class="px-6 py-4 font-bold text-rose-700"><?php echo esc_html($row['ma_sp'] ?? ''); ?></td><td class="px-6 py-4"><p class="font-bold"><?php echo esc_html($row['ten_sp'] ?? ''); ?></p><p class="text-xs text-slate-400"><?php echo esc_html($row['category'] ?? ''); ?></p></td><td class="px-6 py-4 font-bold"><?php echo esc_html((string) ($row['so_luong_ton'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html($row['trang_thai'] ?? ''); ?></td></tr><?php }); ?>
        <?php $renderTable('Nhập hàng theo nhà cung cấp', 'Nhà cung cấp đóng góp nhiều chi phí nhập nhất.', ['Mã', 'Nhà cung cấp', 'Số phiếu', 'Tổng nhập'], $supplierPurchasing, static function ($row) { ?><tr><td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($row['ma_ncc'] ?? ''); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html($row['ten_ncc'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['receipt_count'] ?? 0)); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html(number_format((float) ($row['total_purchase'] ?? 0))); ?>đ</td></tr><?php }); ?>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <?php $renderTable('Hiệu suất nhà vận chuyển', 'Số chuyến và phí vận chuyển theo đơn vị giao hàng.', ['Đơn vị', 'Loại hình', 'Số chuyến', 'Đã giao', 'Phí VC'], $carrierSummary, static function ($row) { ?><tr><td class="px-6 py-4 font-bold"><?php echo esc_html($row['ten_nvc'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html($row['loai_hinh'] ?? ''); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['shipment_count'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html((string) ($row['delivered_count'] ?? 0)); ?></td><td class="px-6 py-4"><?php echo esc_html(number_format((float) ($row['shipping_fee'] ?? 0))); ?>đ</td></tr><?php }); ?>
        <?php $renderTable('Hóa đơn gần đây', 'Dòng tiền và trạng thái xử lý mới nhất.', ['Mã HĐ', 'Tổng tiền', 'Ngày tạo', 'Trạng thái'], $recentInvoices, static function ($row) { ?><tr><td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($row['ma_hd'] ?? ''); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html(number_format((float) ($row['tong_tien'] ?? 0))); ?>đ</td><td class="px-6 py-4 text-slate-500"><?php echo esc_html(!empty($row['ngay_tao']) ? date_i18n('d/m/Y H:i', strtotime($row['ngay_tao'])) : ''); ?></td><td class="px-6 py-4"><?php echo esc_html($row['trang_thai'] ?? ''); ?></td></tr><?php }); ?>
    </div>
</div>
