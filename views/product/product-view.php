<?php if (!defined('ABSPATH')) exit; ?>

<!-- Flash messages -->
<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
<?php endif; ?>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Sản phẩm</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi và cập nhật danh mục nội thất của TimberFlow.</p>
        </div>
        <a href="?page=qln-products&action=create" 
           class="bg-[#0a5c36] hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
            + Thêm Sản Phẩm
        </a>
    </div>

    <!-- 4 cards thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">TỔNG SẢN PHẨM</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($total_products); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-cubes"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-green-600 bg-green-50 inline-block px-2 py-1 rounded-md">
                <i class="fa-solid fa-arrow-trend-up"></i> +12% so với tháng trước
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">GIÁ TRỊ TỒN KHO</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1 "><?php echo qln_format_compact_money($total_inventory_value); ?>đ</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">Tổng giá trị theo giá bán</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">SẮP HẾT HÀNG</p>
                    <h3 class="text-3xl font-bold text-orange-600 mt-1"><?php echo $almost_out; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">Sản phẩm tồn kho dưới 10</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">CẦN NHẬP GẤP</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-1"><?php echo $need_urgent; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">Tồn kho ≤ 5 cái</div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="page" value="qln-products">
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Danh mục</label>
                <select name="category" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat->id_loai; ?>" <?php echo ($_GET['category'] ?? '') == $cat->id_loai ? 'selected' : ''; ?>><?php echo esc_html($cat->ten_loai); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="Đang bán" <?php echo ($_GET['status'] ?? '') == 'Đang bán' ? 'selected' : ''; ?>>Đang bán</option>
                    <option value="Sắp hết hàng" <?php echo ($_GET['status'] ?? '') == 'Sắp hết hàng' ? 'selected' : ''; ?>>Sắp hết hàng</option>
                    <option value="Cần nhập gấp" <?php echo ($_GET['status'] ?? '') == 'Cần nhập gấp' ? 'selected' : ''; ?>>Cần nhập gấp</option>
                    <option value="Đã hết" <?php echo ($_GET['status'] ?? '') == 'Đã hết' ? 'selected' : ''; ?>>Đã hết</option>
                    <option value="Không bán" <?php echo ($_GET['status'] ?? '') == 'Không bán' ? 'selected' : ''; ?>>Không bán</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tìm kiếm</label>
                <input type="text" name="search" placeholder="Mã SP / Tên SP" value="<?php echo esc_attr($_GET['search'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm w-full">Lọc dữ liệu</button>
            </div>
        </form>
    </div>

    <!-- Bảng sản phẩm -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">HÌNH ẢNH</th>
                        <th class="px-6 py-4">SẢN PHẨM & SKU</th>
                        <th class="px-6 py-4">DANH MỤC</th>
                        <th class="px-6 py-4">GIÁ NHẬP</th>
                        <th class="px-6 py-4">GIÁ BÁN</th>
                        <th class="px-6 py-4 text-center">TỒN KHO</th>
                        <th class="px-6 py-4">TRẠNG THÁI</th>
                        <th class="px-6 py-4 text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($products as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Ảnh -->
                        <td class="px-6 py-4">
                            <?php if (!empty($p->hinh_anh)): ?>
                                <img src="<?php echo esc_url($p->hinh_anh); ?>" class="w-12 h-12 rounded-lg object-cover border border-gray-200" alt="<?php echo esc_attr($p->ten_sp); ?>">
                            <?php else: ?>
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 border border-gray-200">
                                    <i class="fa-regular fa-image text-xl"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <!-- Tên + SKU -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800"><?php echo esc_html($p->ten_sp); ?></div>
                            <div class="text-xs text-gray-400 mt-0.5">SKU: <?php echo esc_html($p->ma_sp); ?></div>
                        </td>
                        <!-- Danh mục -->
                        <td class="px-6 py-4 text-gray-600"><?php echo esc_html($p->ten_loai ?? 'Chưa phân loại'); ?></td>
                        <!-- Giá nhập -->
                        <td class="px-6 py-4 font-semibold text-green-600"><?php echo number_format($p->gia_nhap); ?>đ</td>
                        <!-- Giá bán -->
                        <td class="px-6 py-4 font-semibold text-blue-600"><?php echo number_format($p->gia_ban); ?>đ</td>
                        <!-- Tồn kho -->
                        <td class="px-6 py-4 text-center font-bold <?php echo $p->so_luong_ton <= 5 ? 'text-red-600' : ($p->so_luong_ton < 10 ? 'text-orange-500' : 'text-gray-700'); ?>">
                            <?php echo number_format($p->so_luong_ton); ?>
                        </td>
                        <!-- Trạng thái -->
                        <td class="px-6 py-4">
                            <?php 
                                $statusClass = 'bg-gray-100 text-gray-700';
                                if ($p->trang_thai == 'Đang bán') $statusClass = 'bg-green-100 text-green-700';
                                elseif ($p->trang_thai == 'Sắp hết hàng') $statusClass = 'bg-yellow-100 text-yellow-700';
                                elseif ($p->trang_thai == 'Cần nhập gấp') $statusClass = 'bg-orange-100 text-orange-700';
                                elseif ($p->trang_thai == 'Đã hết') $statusClass = 'bg-red-100 text-red-700';
                                elseif ($p->trang_thai == 'Không bán') $statusClass = 'bg-gray-300 text-gray-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $statusClass; ?>">
                                <?php echo esc_html($p->trang_thai); ?>
                            </span>
                        </td>
                        <!-- Thao tác icon -->
                        <td class="px-6 py-4 text-center">
                            <a href="?page=qln-products&action=edit&id=<?php echo $p->id; ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="?page=qln-products&action=delete&id=<?php echo $p->id; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                               class="text-red-600 hover:text-red-800 mx-1" title="Xóa">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>