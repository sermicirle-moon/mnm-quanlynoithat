<?php if (!defined('ABSPATH')) exit; ?>
<?php
$isEdit = isset($carrier) && $carrier !== null;
$actionUrl = $isEdit ? admin_url('admin.php?page=qln-shipping&action=update&id=' . (int) $carrier->id) : admin_url('admin.php?page=qln-shipping&action=store');
?>
<div class="flex-1 overflow-y-auto p-8 bg-[#f8f9ff]">
    <div class="flex justify-between items-end mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700 mb-2">
                <i class="fa-solid fa-shipping-fast"></i>
                Shipping Management
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900"><?php echo $isEdit ? 'Sửa nhà vận chuyển' : 'Thêm nhà vận chuyển'; ?></h1>
            <p class="text-sm text-slate-500 mt-1">Cập nhật thông tin đội giao hàng nội bộ hoặc đối tác vận chuyển.</p>
        </div>
        <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping')); ?>" class="text-slate-500 hover:text-slate-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo esc_url($actionUrl); ?>" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Mã NVC</label>
                <input type="text" name="ma_nvc" value="<?php echo $isEdit ? esc_attr($carrier->ma_nvc) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tên nhà vận chuyển</label>
                <input type="text" name="ten_nvc" value="<?php echo $isEdit ? esc_attr($carrier->ten_nvc) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Loại hình</label>
                <select name="loai_hinh" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
                    <option value="Nội bộ" <?php selected($isEdit ? $carrier->loai_hinh : 'Nội bộ', 'Nội bộ'); ?>>Nội bộ</option>
                    <option value="Đối tác" <?php selected($isEdit ? $carrier->loai_hinh : 'Nội bộ', 'Đối tác'); ?>>Đối tác</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Trạng thái</label>
                <select name="trang_thai" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
                    <option value="1" <?php selected($isEdit ? $carrier->trang_thai : 1, 1); ?>>Đang hoạt động</option>
                    <option value="0" <?php selected($isEdit ? $carrier->trang_thai : 1, 0); ?>>Tạm ngừng</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">SĐT tài xế</label>
                <input type="text" name="sdt_tai_xe" value="<?php echo $isEdit ? esc_attr($carrier->sdt_tai_xe) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Biển số xe</label>
                <input type="text" name="bien_so_xe" value="<?php echo $isEdit ? esc_attr($carrier->bien_so_xe) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-2">
            <a href="<?php echo esc_url(admin_url('admin.php?page=qln-shipping')); ?>" class="px-5 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold">Thoát</a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-bold"><?php echo $isEdit ? 'Cập nhật' : 'Thêm mới'; ?></button>
        </div>
    </form>
</div>
