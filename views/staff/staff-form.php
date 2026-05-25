<?php if (!defined('ABSPATH')) exit;
$isEdit = isset($staffMember) && $staffMember !== null;
$actionUrl = $isEdit ? admin_url('admin.php?page=qln-staff&action=update&id=' . (int) $staffMember->id) : admin_url('admin.php?page=qln-staff&action=store');
?>
<div class="p-8 bg-[#f8f9ff] min-h-full">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900"><?php echo $isEdit ? 'Cập nhật nhân viên' : 'Thêm nhân viên'; ?></h1>
            <p class="text-sm text-slate-500 mt-1">Quản lý thông tin đăng nhập và vai trò nhân sự.</p>
        </div>
        <a href="<?php echo esc_url(admin_url('admin.php?page=qln-staff')); ?>" class="text-slate-500 hover:text-slate-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo esc_url($actionUrl); ?>" method="POST" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên</label>
                <input type="text" name="ho_ten" value="<?php echo $isEdit ? esc_attr($staffMember->ho_ten) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo $isEdit ? esc_attr($staffMember->email) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Mật khẩu <?php echo $isEdit ? '(để trống nếu không đổi)' : ''; ?></label>
                <input type="password" name="mat_khau" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600" <?php echo $isEdit ? '' : 'required'; ?>>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Số điện thoại</label>
                <input type="text" name="sdt" value="<?php echo $isEdit ? esc_attr($staffMember->sdt) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Quê quán</label>
                <input type="text" name="que_quan" value="<?php echo $isEdit ? esc_attr($staffMember->que_quan) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Vai trò</label>
                <select name="role_id" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
                    <option value="1" <?php selected($isEdit ? $staffMember->role_id : 2, 1); ?>>Quản lý</option>
                    <option value="2" <?php selected($isEdit ? $staffMember->role_id : 2, 2); ?>>NV Bán hàng</option>
                    <option value="3" <?php selected($isEdit ? $staffMember->role_id : 2, 3); ?>>NV Kho</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Trạng thái</label>
                <select name="trang_thai" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-emerald-600">
                    <option value="1" <?php selected($isEdit ? $staffMember->trang_thai : 1, 1); ?>>Đang làm việc</option>
                    <option value="0" <?php selected($isEdit ? $staffMember->trang_thai : 1, 0); ?>>Đã nghỉ</option>
                </select>
            </div>
        </div>
        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2 px-6 rounded-lg transition"><?php echo $isEdit ? 'Lưu thay đổi' : 'Thêm nhân viên'; ?></button>
    </form>
</div>
