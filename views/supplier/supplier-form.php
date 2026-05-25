<?php if (!defined('ABSPATH')) exit;
$actionUrl = admin_url('admin.php?page=qln-suppliers&action=store');
?>
<div class="p-8 bg-[#f8f9ff] min-h-full">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Thêm nhà cung cấp</h1>
            <p class="text-sm text-slate-500 mt-1">Nhập thông tin đối tác cung ứng mới.</p>
        </div>
        <a href="<?php echo esc_url(admin_url('admin.php?page=qln-suppliers')); ?>" class="text-slate-500 hover:text-slate-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo esc_url($actionUrl); ?>" method="POST" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mã NCC</label>
                <input type="text" name="ma_ncc" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tên nhà cung cấp</label>
                <input type="text" name="ten_ncc" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Người liên hệ</label>
                <input type="text" name="nguoi_lien_he" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Số điện thoại</label>
                <input type="text" name="sdt" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Trạng thái</label>
                <select name="trang_thai" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
                    <option value="1">Đang giao dịch</option>
                    <option value="0">Ngừng giao dịch</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Địa chỉ</label>
            <textarea name="dia_chi" rows="3" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600"></textarea>
        </div>

        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2 px-6 rounded-lg transition">Thêm nhà cung cấp</button>
    </form>
</div>