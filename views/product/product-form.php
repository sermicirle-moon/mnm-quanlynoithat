<?php if (!defined('ABSPATH')) exit; 
$isEdit = isset($product) && $product != null;

// Khởi tạo đường dẫn submit an toàn qua admin_url
$actionUrl = $isEdit 
    ? admin_url("admin.php?page=qln-products&action=update&id={$product->id}") 
    : admin_url("admin.php?page=qln-products&action=store");
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
                <input type="text" name="ma_sp" value="<?php echo $isEdit ? esc_attr($product->ma_sp) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm</label>
                <input type="text" name="ten_sp" value="<?php echo $isEdit ? esc_attr($product->ten_sp) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá nhập</label>
                <input type="number" name="gia_nhap" value="<?php echo $isEdit ? esc_attr($product->gia_nhap) : '0'; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán</label>
                <input type="number" name="gia_ban" value="<?php echo $isEdit ? esc_attr($product->gia_ban) : '0'; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600 font-bold text-green-600" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng tồn</label>
                <input type="number" name="so_luong_ton" value="<?php echo $isEdit ? esc_attr($product->so_luong_ton) : '0'; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600 font-bold text-blue-600" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái kinh doanh</label>
                <select name="trang_thai" class="w-full px-4 py-2 border rounded-lg bg-gray-50 font-bold outline-none focus:border-green-600">
                    <option value="Đang bán" <?php echo ($isEdit && $product->trang_thai !== 'Không bán') ? 'selected' : ''; ?>>
                        🟢 Bán tự động (Hệ thống tự phân loại theo tồn kho)
                    </option>
                    <option value="Không bán" <?php echo ($isEdit && $product->trang_thai === 'Không bán') ? 'selected' : ''; ?>>
                        🔴 Ngừng kinh doanh (Khóa thủ công)
                    </option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh (URL)</label>
            <input type="text" name="hinh_anh" value="<?php echo $isEdit ? esc_attr($product->hinh_anh) : ''; ?>" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại sản phẩm</label>
            <select name="id_loai" class="w-full px-4 py-2 border rounded-lg outline-none focus:border-green-600">
                <option value="">-- Chọn loại --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->id_loai; ?>" <?php echo ($isEdit && $product->id_loai == $cat->id_loai) ? 'selected' : ''; ?>><?php echo esc_html($cat->ten_loai); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition">
            <?php echo $isEdit ? 'Lưu cập nhật' : 'Thêm sản phẩm'; ?>
        </button>
    </form>
</div>