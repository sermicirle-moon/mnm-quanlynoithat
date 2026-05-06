<?php if (!defined('ABSPATH')) exit; 
$isEdit = isset($customer) && $customer != null;
$actionUrl = $isEdit ? "?page=qln-customers&action=update&id={$customer->id}" : "?page=qln-customers&action=store";
?>

<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?php echo $isEdit ? 'Cập nhật Khách Hàng' : 'Thêm Khách Hàng Mới'; ?></h2>
        <a href="?page=qln-customers" class="text-gray-500 hover:text-gray-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo $actionUrl; ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã Khách Hàng</label>
                <input type="text" name="ma_kh" value="<?php echo $isEdit ? esc_attr($customer->ma_kh) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên Khách Hàng</label>
                <input type="text" name="ten_kh" value="<?php echo $isEdit ? esc_attr($customer->ten_kh) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" name="sdt" value="<?php echo $isEdit ? esc_attr($customer->sdt) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?php echo $isEdit ? esc_attr($customer->email) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
            <input type="text" name="dia_chi" value="<?php echo $isEdit ? esc_attr($customer->dia_chi) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600">
        </div>

        <?php if ($isEdit): ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại khách hàng</label>
            <input type="text" value="<?php echo esc_attr($customer->loai_khach_hang); ?>" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tổng chi tiêu</label>
            <input type="text" value="<?php echo number_format($customer->tong_tien_da_chi); ?>đ" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Số đơn hàng</label>
            <input type="text" value="<?php echo $customer->so_luong_don_hang; ?>" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg">
        </div>
        <?php endif; ?>

        <button type="submit" class="bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-2 px-6 rounded-lg transition">
            <?php echo $isEdit ? 'Lưu thay đổi' : 'Thêm mới'; ?>
        </button>
    </form>
</div>