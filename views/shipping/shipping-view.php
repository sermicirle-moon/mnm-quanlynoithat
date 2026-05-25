<?php if (!defined('ABSPATH')) exit;
$filters = $filters ?? ['search' => '', 'trang_thai' => '', 'loai_hinh' => ''];
$exportUrl = add_query_arg(array_filter([
    'page' => 'qln-shipping',
    'action' => 'export',
    'search' => $filters['search'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
    'loai_hinh' => $filters['loai_hinh'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
$pdfUrl = add_query_arg(array_filter([
    'page' => 'qln-shipping',
    'action' => 'pdf',
    'search' => $filters['search'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
    'loai_hinh' => $filters['loai_hinh'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
?>
<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff]">
    <div class="flex justify-between items-end mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-shipping-fast"></i>
                Shipping Management
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Nhà vận chuyển</h1>
            <p class="text-sm text-slate-500 mt-1">Theo dõi đội giao hàng nội bộ và các đối tác vận chuyển đang dùng cho phiếu xuất.</p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping&action=create')); ?>" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-emerald-700/20 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Thêm nhà vận chuyển
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-truck-fast"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng đơn vị</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) ($stats['total'] ?? 0))); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center"><i class="fa-solid fa-circle-check"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Đang hoạt động</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) ($stats['active'] ?? 0))); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center"><i class="fa-solid fa-ban"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tạm ngừng</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) ($stats['inactive'] ?? 0))); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-warehouse"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Nội bộ</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) ($stats['internal'] ?? 0))); ?></p></div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" class="js-auto-filter grid grid-cols-1 lg:grid-cols-6 gap-3 items-center">
            <input type="hidden" name="page" value="qln-shipping">
            <div class="lg:col-span-2 flex items-center bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                <span class="px-3 text-slate-400 border-r border-slate-100"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input name="search" value="<?php echo esc_attr($filters['search'] ?? ''); ?>" class="w-full px-3 py-2 bg-transparent text-sm outline-none" placeholder="Tìm nhà vận chuyển..." type="text">
            </div>
            <div class="flex gap-2 lg:col-span-2">
                <select name="trang_thai" class="min-w-0 flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?php selected($filters['trang_thai'] ?? '', '1'); ?>>Đang hoạt động</option>
                    <option value="0" <?php selected($filters['trang_thai'] ?? '', '0'); ?>>Tạm ngừng</option>
                </select>
                <select name="loai_hinh" class="min-w-0 flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
                    <option value="">Tất cả loại hình</option>
                    <option value="Nội bộ" <?php selected($filters['loai_hinh'] ?? '', 'Nội bộ'); ?>>Nội bộ</option>
                    <option value="Đối tác" <?php selected($filters['loai_hinh'] ?? '', 'Đối tác'); ?>>Đối tác</option>
                </select>
                <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping')); ?>" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold" title="Reset bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
            <div class="flex gap-2 lg:col-span-2 lg:justify-end">
                <a href="<?php echo esc_url($exportUrl); ?>" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100"><i class="fa-solid fa-file-csv mr-2"></i>CSV</a>
                <a href="<?php echo esc_url($pdfUrl); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-sm font-bold border border-rose-100"><i class="fa-solid fa-file-pdf mr-2"></i>PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Danh sách nhà vận chuyển</h2>
            <p class="text-xs text-slate-400">Tổng <?php echo esc_html(number_format((int) ($total_items ?? 0))); ?> nhà vận chuyển - mỗi trang hiển thị 5 nhà vận chuyển</p>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Mã NVC</th>
                    <th class="px-6 py-4">Nhà vận chuyển</th>
                    <th class="px-6 py-4">Loại hình</th>
                    <th class="px-6 py-4">SĐT tài xế</th>
                    <th class="px-6 py-4">Biển số xe</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($carriers as $carrier): ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($carrier->ma_nvc); ?></td>
                        <td class="px-6 py-4 font-bold text-slate-900"><?php echo esc_html($carrier->ten_nvc); ?></td>
                        <td class="px-6 py-4 text-slate-600"><?php echo esc_html($carrier->loai_hinh); ?></td>
                        <td class="px-6 py-4 text-slate-600"><?php echo esc_html($carrier->sdt_tai_xe ?: 'Chưa cập nhật'); ?></td>
                        <td class="px-6 py-4 text-slate-600"><?php echo esc_html($carrier->bien_so_xe ?: 'Không áp dụng'); ?></td>
                        <td class="px-6 py-4">
                            <?php $statusClass = ((int) $carrier->trang_thai === 1) ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo esc_attr($statusClass); ?>"><?php echo esc_html($carrier->getTrangThaiText()); ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping&action=edit&id=' . (int) $carrier->id)); ?>" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping&action=delete&id=' . (int) $carrier->id)); ?>" onclick="return confirm('Chuyển nhà vận chuyển này sang trạng thái tạm ngừng?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($carriers)): ?>
                    <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium italic">Không tìm thấy nhà vận chuyển phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (($total_pages ?? 1) > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping&paged=' . $i . ($url_params ?? ''))); ?>" class="px-3 py-2 rounded-lg text-sm font-bold <?php echo ((int) ($current_page ?? 1) === $i) ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-slate-100'; ?>"><?php echo esc_html((string) $i); ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.js-auto-filter').forEach(function(form){var timer;var paged=form.querySelector('[name="paged"]');form.querySelectorAll('input[name="search"]').forEach(function(input){input.addEventListener('input',function(){clearTimeout(timer);timer=setTimeout(function(){if(paged){paged.value='1';}form.submit();},350);});});form.querySelectorAll('select').forEach(function(select){select.addEventListener('change',function(){if(paged){paged.value='1';}form.submit();});});});
</script>
