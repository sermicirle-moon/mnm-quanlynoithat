<?php
if (!defined('ABSPATH')) exit;

$summary = $data['summary'] ?? [];
$recentInvoices = $data['recent_invoices'] ?? [];
$categorySales = $data['category_sales'] ?? [];
$revenueTrend = $data['revenue_trend'] ?? [];
$monthlyComparison = $data['monthly_comparison'] ?? [];
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
    $color = $donutColors[$index % count($donutColors)];
    $gradientStops[] = $color . ' ' . round($currentPercent, 1) . '% ' . round($nextPercent, 1) . '%';
    $currentPercent = $nextPercent;
}
$donutBackground = !empty($gradientStops) ? implode(', ', $gradientStops) : '#e2e8f0 0% 100%';

$csvUrl = esc_url(add_query_arg('qln_report_export', 'csv', admin_url('admin.php?page=qln-reports')));
$pdfUrl = esc_url(add_query_arg('qln_report_export', 'pdf', admin_url('admin.php?page=qln-reports')));
?>

<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff] text-slate-900">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-chart-line"></i>
                Analytics & Reports
            </div>
            <h1 class="text-3xl font-bold tracking-tight">Phân tích &amp; Báo cáo</h1>
            <p class="text-sm text-slate-500 mt-1">Dữ liệu doanh thu, nhập hàng, tồn kho và hiệu suất vận hành từ cơ sở dữ liệu TimberFlow.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo $csvUrl; ?>" class="px-4 py-2 rounded-xl bg-white text-slate-700 text-sm font-bold border border-slate-200 shadow-sm hover:bg-slate-50">
                <i class="fa-solid fa-file-csv mr-2"></i>Xuất CSV
            </a>
            <a href="<?php echo $pdfUrl; ?>" target="_blank" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-emerald-700/20">
                <i class="fa-solid fa-print mr-2"></i>In / PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex justify-between items-start"><span class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-coins"></i></span><span class="text-xs font-bold bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full"><?php echo esc_html((string) ($summary['profit_rate'] ?? 0)); ?>%</span></div>
            <div class="mt-6"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng doanh thu</p><p class="text-2xl font-bold"><?php echo esc_html(number_format((float) ($summary['total_revenue'] ?? 0))); ?>đ</p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex justify-between items-start"><span class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-file-invoice-dollar"></i></span><span class="text-xs font-bold bg-blue-50 text-blue-700 px-2 py-1 rounded-full"><?php echo esc_html((string) ($summary['completed_invoice_count'] ?? 0)); ?> hoàn thành</span></div>
            <div class="mt-6"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng hóa đơn</p><p class="text-2xl font-bold"><?php echo esc_html(number_format((int) ($summary['invoice_count'] ?? 0))); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex justify-between items-start"><span class="w-11 h-11 rounded-xl bg-red-100 text-red-700 flex items-center justify-center"><i class="fa-solid fa-truck-ramp-box"></i></span><span class="text-xs font-bold bg-red-50 text-red-700 px-2 py-1 rounded-full">Đã nhập kho</span></div>
            <div class="mt-6"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Chi phí nhập hàng</p><p class="text-2xl font-bold"><?php echo esc_html(number_format((float) ($summary['stock_in_cost'] ?? 0))); ?>đ</p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex justify-between items-start"><span class="w-11 h-11 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fa-solid fa-warehouse"></i></span><span class="text-xs font-bold bg-amber-50 text-amber-700 px-2 py-1 rounded-full"><?php echo esc_html(number_format((float) ($summary['inventory_value'] ?? 0))); ?>đ</span></div>
            <div class="mt-6"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tồn kho hiện tại</p><p class="text-2xl font-bold"><?php echo esc_html(number_format((int) ($summary['inventory_quantity'] ?? 0))); ?> SP</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-bold">Danh mục bán chạy</h2><i class="fa-solid fa-chart-pie text-slate-400"></i></div>
            <div class="flex flex-col items-center">
                <div class="w-48 h-48 rounded-full flex items-center justify-center" style="background: conic-gradient(<?php echo esc_attr($donutBackground); ?>);">
                    <div class="w-32 h-32 bg-white rounded-full shadow-inner flex flex-col items-center justify-center">
                        <span class="text-xs font-bold text-slate-500">TỔNG</span>
                        <span class="text-lg font-bold"><?php echo esc_html(qln_format_compact_money((float) $totalCategoryRevenue)); ?>đ</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-6 w-full">
                    <?php foreach ($categorySales as $index => $row): $percent = $totalCategoryRevenue > 0 ? round(((float) $row['revenue'] / $totalCategoryRevenue) * 100, 1) : 0; ?>
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <span class="w-3 h-3 rounded-full" style="background: <?php echo esc_attr($donutColors[$index % count($donutColors)]); ?>"></span>
                            <span><?php echo esc_html($row['category']); ?> (<?php echo esc_html((string) $percent); ?>%)</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-bold">Xu hướng tăng trưởng doanh thu</h2><span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full">Hóa đơn hoàn thành</span></div>
            <div class="h-64">
                <svg class="w-full h-full" viewBox="0 0 800 220" role="img" aria-label="Xu hướng doanh thu theo tháng">
                    <line x1="20" y1="40" x2="780" y2="40" stroke="#e2e8f0" stroke-dasharray="4" />
                    <line x1="20" y1="90" x2="780" y2="90" stroke="#e2e8f0" stroke-dasharray="4" />
                    <line x1="20" y1="140" x2="780" y2="140" stroke="#e2e8f0" stroke-dasharray="4" />
                    <line x1="20" y1="190" x2="780" y2="190" stroke="#e2e8f0" stroke-dasharray="4" />
                    <?php if (!empty($chartPoints)): ?>
                        <polyline points="<?php echo esc_attr(implode(' ', $chartPoints)); ?>" fill="none" stroke="#006c4a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <?php foreach ($chartPoints as $point): [$cx, $cy] = explode(',', $point); ?>
                            <circle cx="<?php echo esc_attr($cx); ?>" cy="<?php echo esc_attr($cy); ?>" r="5" fill="#006c4a" />
                        <?php endforeach; ?>
                    <?php endif; ?>
                </svg>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-xs text-slate-500">
                <?php foreach ($revenueTrend as $row): ?>
                    <div class="rounded-xl bg-slate-50 p-3"><p class="font-bold text-slate-700"><?php echo esc_html($row['label']); ?></p><p><?php echo esc_html(number_format((float) $row['revenue'])); ?>đ</p></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-bold">Tổng hợp Chi phí &amp; Doanh thu</h2>
            <p class="text-sm text-slate-500 mt-1">So sánh hàng tháng giữa phiếu nhập đã nhập kho và hóa đơn hoàn thành.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                    <tr><th class="px-6 py-4">Tháng</th><th class="px-6 py-4">Chi phí nhập hàng</th><th class="px-6 py-4">Doanh thu bán ra</th><th class="px-6 py-4">Lợi nhuận gộp</th><th class="px-6 py-4 text-right">Tỷ lệ lãi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($monthlyComparison as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold"><?php echo esc_html($row['label']); ?></td>
                            <td class="px-6 py-4"><?php echo esc_html(number_format((float) $row['cost'])); ?>đ</td>
                            <td class="px-6 py-4"><?php echo esc_html(number_format((float) $row['revenue'])); ?>đ</td>
                            <td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html(number_format((float) $row['profit'])); ?>đ</td>
                            <td class="px-6 py-4 text-right"><span class="font-bold"><?php echo esc_html((string) $row['profit_rate']); ?>%</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold">Hóa đơn gần đây</h2><p class="text-xs text-slate-400 mt-1">Dòng tiền và trạng thái xử lý mới nhất.</p></div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100"><tr><th class="px-6 py-4">Mã HĐ</th><th class="px-6 py-4">Tổng tiền</th><th class="px-6 py-4">Ngày tạo</th><th class="px-6 py-4">Trạng thái</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentInvoices as $inv): ?>
                        <tr class="hover:bg-slate-50/80"><td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($inv['ma_hd']); ?></td><td class="px-6 py-4 font-bold"><?php echo esc_html(number_format((float) $inv['tong_tien'])); ?>đ</td><td class="px-6 py-4 text-slate-500"><?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($inv['ngay_tao']))); ?></td><td class="px-6 py-4"><?php echo esc_html($inv['trang_thai']); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="font-bold mb-5">Tổng quan vận hành</h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500 font-bold uppercase">Nhà cung cấp</p><p class="text-xl font-bold mt-1"><?php echo esc_html(number_format((int) ($summary['supplier_count'] ?? 0))); ?></p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500 font-bold uppercase">Nhân viên</p><p class="text-xl font-bold mt-1"><?php echo esc_html(number_format((int) ($summary['staff_count'] ?? 0))); ?></p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500 font-bold uppercase">Phiếu nhập</p><p class="text-xl font-bold mt-1"><?php echo esc_html(number_format((int) ($summary['stock_in_count'] ?? 0))); ?></p></div>
                <div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500 font-bold uppercase">Phiếu xuất</p><p class="text-xl font-bold mt-1"><?php echo esc_html(number_format((int) ($summary['stock_out_count'] ?? 0))); ?></p></div>
            </div>
        </div>
    </div>
</div>
