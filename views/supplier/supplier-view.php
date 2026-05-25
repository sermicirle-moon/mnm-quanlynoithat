<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<?php
$totalSuppliers = (int) ($stats['total'] ?? count($suppliers));
$activeSuppliers = (int) ($stats['active'] ?? 0);
$pausedSuppliers = (int) ($stats['inactive'] ?? 0);
$filters = $filters ?? ['search' => '', 'trang_thai' => ''];
$exportUrl = add_query_arg(array_filter([
    'page' => 'qln-suppliers',
    'action' => 'export',
    'search' => $filters['search'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
$pdfUrl = add_query_arg(array_filter([
    'page' => 'qln-suppliers',
    'action' => 'pdf',
    'search' => $filters['search'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
?>
<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff]">
<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-bold shadow-sm border-l-4 border-green-500"><i class="fa-solid fa-check-circle mr-1"></i> <?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold shadow-sm border-l-4 border-red-500"><i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
<?php endif; ?>

    <div class="flex justify-between items-end mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-industry"></i>
                Suppliers Management
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Nhà Cung Cấp</h1>
            <p class="text-sm text-slate-500 mt-1">Quản lý đối tác gỗ nguyên liệu và phụ kiện nội thất cao cấp.</p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-suppliers&action=create')); ?>" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-emerald-700/20 flex items-center gap-2"><i class="fa-solid fa-plus"></i> Thêm nhà cung cấp</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-building-circle-check"></i></div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng nhà cung cấp</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo esc_html($totalSuppliers); ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center"><i class="fa-solid fa-handshake"></i></div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Đang giao dịch</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo esc_html($activeSuppliers); ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center"><i class="fa-solid fa-ban"></i></div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Ngừng giao dịch</p>
                <p class="text-2xl font-bold text-slate-900"><?php echo esc_html($pausedSuppliers); ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" class="js-auto-filter grid grid-cols-1 lg:grid-cols-5 gap-3 items-center">
            <input type="hidden" name="page" value="qln-suppliers">
            <div class="lg:col-span-2 flex items-center bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                <span class="px-3 text-slate-400 border-r border-slate-100"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input name="search" value="<?php echo esc_attr($filters['search'] ?? ''); ?>" class="w-full px-3 py-2 bg-transparent text-sm outline-none" placeholder="Tìm nhà cung cấp..." type="text">
            </div>
            <div class="flex gap-2">
                <select name="trang_thai" class="min-w-0 flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?php selected($filters['trang_thai'] ?? '', '1'); ?>>Đang giao dịch</option>
                    <option value="0" <?php selected($filters['trang_thai'] ?? '', '0'); ?>>Ngừng giao dịch</option>
                </select>
                <a href="<?php echo esc_url(admin_url('admin.php?page=qln-suppliers')); ?>" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold" title="Reset bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
            <div class="flex gap-2 lg:col-span-2 lg:justify-end">
                <a href="<?php echo esc_url($exportUrl); ?>" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100"><i class="fa-solid fa-file-csv mr-2"></i>CSV</a>
                <a href="<?php echo esc_url($pdfUrl); ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-sm font-bold border border-rose-100"><i class="fa-solid fa-file-pdf mr-2"></i>PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Danh sách nhà cung cấp</h2>
            <p class="text-xs text-slate-400">Tổng <?php echo esc_html(number_format((int) ($total_items ?? 0))); ?> nhà cung cấp - mỗi trang hiển thị 5 nhà cung cấp</p>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Mã NCC</th>
                    <th class="px-6 py-4">Đối tác</th>
                    <th class="px-6 py-4">Người liên hệ</th>
                    <th class="px-6 py-4">Liên lạc</th>
                    <th class="px-6 py-4">Địa chỉ</th>
                    <th class="px-6 py-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($suppliers as $s): ?>
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="px-6 py-4 font-bold text-emerald-700"><?php echo esc_html($s->ma_ncc); ?></td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-900"><?php echo esc_html($s->ten_ncc); ?></p>
                        <p class="text-xs text-slate-400"><?php echo esc_html($s->email); ?></p>
                    </td>
                    <td class="px-6 py-4 text-slate-700"><?php echo esc_html($s->nguoi_lien_he); ?></td>
                    <td class="px-6 py-4 text-slate-600"><?php echo esc_html($s->sdt); ?></td>
                    <td class="px-6 py-4 text-slate-500 truncate max-w-xs"><?php echo esc_html($s->dia_chi); ?></td>
                    <td class="px-6 py-4">
                        <?php $statusClass = ((int) $s->trang_thai === 1) ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $statusClass; ?>"><?php echo esc_html($s->getTrangThaiText()); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($suppliers)): ?>
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">Không tìm thấy nhà cung cấp phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (($total_pages ?? 1) > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-suppliers&paged=' . $i . ($url_params ?? ''))); ?>" class="px-3 py-2 rounded-lg text-sm font-bold <?php echo ((int) ($current_page ?? 1) === $i) ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-slate-100'; ?>"><?php echo esc_html((string) $i); ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.js-auto-filter').forEach(function(form){var timer;var paged=form.querySelector('[name="paged"]');form.querySelectorAll('input[name="search"]').forEach(function(input){input.addEventListener('input',function(){clearTimeout(timer);timer=setTimeout(function(){if(paged){paged.value='1';}form.submit();},350);});});form.querySelectorAll('select').forEach(function(select){select.addEventListener('change',function(){if(paged){paged.value='1';}form.submit();});});});
</script>

