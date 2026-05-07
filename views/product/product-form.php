<?php if (!defined('ABSPATH')) exit; 
$isEdit = isset($product) && $product != null;
$actionUrl = $isEdit ? "?page=qln-products&action=update&id={$product->id}" : "?page=qln-products&action=store";
?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?php echo $isEdit ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm Mới'; ?></h2>
        <a href="?page=qln-products" class="text-gray-500 hover:text-gray-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo $actionUrl; ?>" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã sản phẩm</label>
                <input type="text" name="ma_sp" value="<?php echo $isEdit ? esc_attr($product->ma_sp) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm</label>
                <input type="text" name="ten_sp" value="<?php echo $isEdit ? esc_attr($product->ten_sp) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán (VNĐ)</label>
                <input type="number" name="gia_ban" value="<?php echo $isEdit ? esc_attr($product->gia_ban) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá nhập (VNĐ)</label>
                <input type="number" name="gia_nhap" value="<?php echo $isEdit ? esc_attr($product->gia_nhap) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng tồn</label>
                <input type="number" name="so_luong_ton" value="<?php echo $isEdit ? esc_attr($product->so_luong_ton) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="trang_thai" class="w-full px-4 py-2 border rounded-lg">
                    <option value="Đang bán" <?php echo ($isEdit && $product->trang_thai == 'Đang bán') ? 'selected' : ''; ?>>Đang bán</option>
                    <option value="Không bán" <?php echo ($isEdit && $product->trang_thai == 'Không bán') ? 'selected' : ''; ?>>Không bán</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh (URL)</label>
            <input type="text" name="hinh_anh" value="<?php echo $isEdit ? esc_attr($product->hinh_anh) : ''; ?>" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại sản phẩm</label>
            <select name="id_loai" class="w-full px-4 py-2 border rounded-lg">
                <option value="">-- Chọn loại --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->id_loai; ?>" <?php echo ($isEdit && $product->id_loai == $cat->id_loai) ? 'selected' : ''; ?>><?php echo esc_html($cat->ten_loai); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-2 px-6 rounded-lg transition">
            <?php echo $isEdit ? 'Lưu thay đổi' : 'Thêm mới'; ?>
        </button>
    </form>
</div>