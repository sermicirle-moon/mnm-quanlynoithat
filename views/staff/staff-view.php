<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<?php
$totalStaff = count($staff);
$filters = $filters ?? ['search' => '', 'role_id' => '', 'trang_thai' => ''];
$stats = $stats ?? ['total' => $totalStaff, 'active' => 0, 'sale' => 0, 'warehouse' => 0];
$exportUrl = add_query_arg(array_filter([
    'page' => 'qln-staff',
    'action' => 'export',
    'search' => $filters['search'] ?? '',
    'role_id' => $filters['role_id'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
$pdfUrl = add_query_arg(array_filter([
    'page' => 'qln-staff',
    'action' => 'pdf',
    'search' => $filters['search'] ?? '',
    'role_id' => $filters['role_id'] ?? '',
    'trang_thai' => $filters['trang_thai'] ?? '',
], static fn($value) => $value !== ''), admin_url('admin.php'));
?>
<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff]">
    <div class="flex justify-between items-end mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-id-badge"></i>
                Staff Management
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Quản lý Nhân viên</h1>
            <p class="text-sm text-slate-500 mt-1">Theo dõi và quản lý hồ sơ nhân sự trong hệ thống TimberFlow.</p>
        </div>
        <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff&action=create')); ?>" class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-700/20 flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Thêm nhân viên
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng nhân sự</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) $stats['total'])); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-700 flex items-center justify-center"><i class="fa-solid fa-user-check"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Đang làm việc</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) $stats['active'])); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-headset"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Bán hàng</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) $stats['sale'])); ?></p></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"><i class="fa-solid fa-warehouse"></i></div>
            <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kho vận</p><p class="text-2xl font-bold text-slate-900"><?php echo esc_html(number_format((int) $stats['warehouse'])); ?></p></div>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" class="js-auto-filter grid grid-cols-1 lg:grid-cols-6 gap-3 items-center">
            <input type="hidden" name="page" value="qln-staff">
            <div class="lg:col-span-2 flex items-center bg-slate-50 border border-slate-100 rounded-xl overflow-hidden">
                <span class="px-3 text-slate-400 border-r border-slate-100"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input name="search" value="<?php echo esc_attr($filters['search'] ?? ''); ?>" class="w-full px-3 py-2 bg-transparent text-sm outline-none" placeholder="Tìm nhân viên..." type="text">
            </div>
            <div class="flex gap-2 lg:col-span-2">
                <select name="role_id" class="min-w-0 flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
                    <option value="">Tất cả vai trò</option>
                    <option value="1" <?php selected($filters['role_id'] ?? '', '1'); ?>>Quản lý</option>
                    <option value="2" <?php selected($filters['role_id'] ?? '', '2'); ?>>NV Bán hàng</option>
                    <option value="3" <?php selected($filters['role_id'] ?? '', '3'); ?>>NV Kho</option>
                </select>
                <select name="trang_thai" class="min-w-0 flex-1 px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-sm outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?php selected($filters['trang_thai'] ?? '', '1'); ?>>Đang làm việc</option>
                    <option value="0" <?php selected($filters['trang_thai'] ?? '', '0'); ?>>Đã nghỉ</option>
                </select>
                <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff')); ?>" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold" title="Reset bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
            <div class="flex gap-2 lg:col-span-2 lg:justify-end">
                <a href="<?php echo esc_url($exportUrl); ?>" class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-bold border border-emerald-100"><i class="fa-solid fa-file-csv mr-2"></i>CSV</a>
                <a href="<?php echo esc_url($pdfUrl); ?>" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-sm font-bold border border-rose-100"><i class="fa-solid fa-file-pdf mr-2"></i>PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Hồ sơ nhân viên</h2>
            <p class="text-xs text-slate-400">Tổng <?php echo esc_html(number_format((int) ($total_items ?? 0))); ?> nhân viên - mỗi trang hiển thị 5 nhân viên</p>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Mã NV</th>
                    <th class="px-6 py-4">Nhân viên</th>
                    <th class="px-6 py-4">Số điện thoại</th>
                    <th class="px-6 py-4">Quê quán</th>
                    <th class="px-6 py-4">Vai trò</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($staff as $u): ?>
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="px-6 py-4 font-bold text-emerald-700">NV<?php echo str_pad((string) $u->id, 4, '0', STR_PAD_LEFT); ?></td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-900"><?php echo esc_html($u->ho_ten); ?></p>
                        <p class="text-xs text-slate-400"><?php echo esc_html($u->email); ?></p>
                    </td>
                    <td class="px-6 py-4 text-slate-600"><?php echo esc_html($u->sdt); ?></td>
                    <td class="px-6 py-4 text-slate-500"><?php echo esc_html($u->que_quan); ?></td>
                    <td class="px-6 py-4">
                        <?php
                            $roleClass = 'bg-blue-100 text-blue-700';
                            if ((int) $u->role_id === 1) $roleClass = 'bg-purple-100 text-purple-700';
                            if ((int) $u->role_id === 3) $roleClass = 'bg-amber-100 text-amber-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $roleClass; ?>"><?php echo esc_html($u->getRoleName()); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php $statusClass = ((int) $u->trang_thai === 1) ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'; ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $statusClass; ?>"><?php echo esc_html($u->getTrangThaiText()); ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-3">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff&action=edit&id=' . (int) $u->id)); ?>" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff&action=delete&id=' . (int) $u->id)); ?>" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($staff)): ?>
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium italic">Không tìm thấy nhân viên phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (($total_pages ?? 1) > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff&paged=' . $i . ($url_params ?? ''))); ?>" class="px-3 py-2 rounded-lg text-sm font-bold <?php echo ((int) ($current_page ?? 1) === $i) ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-slate-100'; ?>"><?php echo esc_html((string) $i); ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.js-auto-filter').forEach(function(form){var timer;var paged=form.querySelector('[name="paged"]');form.querySelectorAll('input[name="search"]').forEach(function(input){input.addEventListener('input',function(){clearTimeout(timer);timer=setTimeout(function(){if(paged){paged.value='1';}form.submit();},350);});});form.querySelectorAll('select').forEach(function(select){select.addEventListener('change',function(){if(paged){paged.value='1';}form.submit();});});});
</script>
