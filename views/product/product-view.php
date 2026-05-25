<?php if (!defined('ABSPATH')) exit; ?>

<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-bold shadow-sm border-l-4 border-green-500"><i class="fa-solid fa-check-circle mr-1"></i> <?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold shadow-sm border-l-4 border-red-500"><i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
<?php endif; ?>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Quản lý Sản phẩm</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi và cập nhật danh mục nội thất của TimberFlow.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" onclick="document.getElementById('categoryModal').classList.remove('hidden')"
                    class="bg-white hover:bg-gray-50 text-[#0a5c36] border border-[#0a5c36] px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition">
                <i class="fa-solid fa-tags mr-1"></i> Thêm loại
            </button>
            <a href="?page=qln-products&action=create" 
               class="bg-[#0a5c36] hover:bg-green-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition">
                <i class="fa-solid fa-plus mr-1"></i> Thêm Sản Phẩm
            </a>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="grid grid-cols-4 gap-4">
            <input type="hidden" name="page" value="qln-products">
            <div class="col-span-2">
                <input type="text" name="search" placeholder="Mã hoặc tên sản phẩm..." 
                       value="<?php echo esc_attr($_GET['search'] ?? ''); ?>" 
                       class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg outline-none focus:border-green-600 transition">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg outline-none focus:border-green-600 transition">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="Đang bán" <?php selected($_GET['status'] ?? '', 'Đang bán'); ?>>Đang bán</option>
                    <option value="Sắp hết hàng" <?php selected($_GET['status'] ?? '', 'Sắp hết hàng'); ?>>Sắp hết hàng</option>
                    <option value="Cần nhập gấp" <?php selected($_GET['status'] ?? '', 'Cần nhập gấp'); ?>>Cần nhập gấp</option>
                    <option value="Đã hết" <?php selected($_GET['status'] ?? '', 'Đã hết'); ?>>Đã hết</option>
                    <option value="Không bán" <?php selected($_GET['status'] ?? '', 'Không bán'); ?>>Không bán</option>
                </select>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="submit" class="bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg font-bold shadow transition">Tìm</button>
                <a href="?page=qln-products" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold transition flex items-center justify-center"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-800 text-white text-[11px] uppercase font-black tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Mã SP</th>
                        <th class="px-6 py-4">Tên sản phẩm</th>
                        <th class="px-6 py-4">Phân loại</th>
                        <th class="px-6 py-4">Giá nhập</th>
                        <th class="px-6 py-4">Giá bán</th>
                        <th class="px-6 py-4 text-center">Tồn kho</th>
                        <th class="px-6 py-4 border-l border-gray-700">Trạng thái</th>
                        <th class="px-6 py-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($products as $p): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono font-bold text-gray-400"><?php echo esc_html($p->ma_sp); ?></td>
                        <td class="px-6 py-4 font-black text-gray-800">
                            <div class="flex items-center gap-3">
                                <?php if($p->hinh_anh): ?>
                                    <img src="<?php echo esc_url($p->hinh_anh); ?>" alt="sp" class="w-10 h-10 object-cover rounded shadow-sm border border-gray-200">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-gray-400 border border-gray-200"><i class="fa-solid fa-box"></i></div>
                                <?php endif; ?>
                                <span><?php echo esc_html($p->ten_sp); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 font-medium"><?php echo esc_html($p->ten_loai ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 text-gray-500 font-medium"><?php echo number_format($p->gia_nhap); ?>đ</td>
                        <td class="px-6 py-4 font-black text-gray-900 text-base"><?php echo number_format($p->gia_ban); ?>đ</td>
                        <td class="px-6 py-4 text-center font-black text-lg <?php echo $p->so_luong_ton <= 5 ? 'text-red-600' : 'text-blue-600'; ?>">
                            <?php echo $p->so_luong_ton; ?>
                        </td>
                        
                        <td class="px-6 py-4 border-l border-gray-50">
                            <?php 
                                $st = $p->trang_thai;
                                if ($st == 'Đang bán') $statusClass = 'bg-green-100 text-green-700 border-green-200';
                                elseif ($st == 'Sắp hết hàng') $statusClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                elseif ($st == 'Cần nhập gấp') $statusClass = 'bg-orange-100 text-orange-700 border-orange-200';
                                elseif ($st == 'Đã hết') $statusClass = 'bg-red-100 text-red-700 border-red-200';
                                else $statusClass = 'bg-gray-100 text-gray-500 border-gray-200';
                            ?>
                            <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase border shadow-sm <?php echo $statusClass; ?>">
                                <?php echo esc_html($st); ?>
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-3">
                                <a href="?page=qln-products&action=edit&id=<?php echo $p->id; ?>" class="text-blue-600 hover:text-blue-800 transition" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square text-lg"></i>
                                </a>
                                <a href="?page=qln-products&action=delete&id=<?php echo $p->id; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi hệ thống?')"
                                   class="text-red-500 hover:text-red-700 transition" title="Xóa">
                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium italic">Không tìm thấy sản phẩm nào phù hợp.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="categoryModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Thêm loại sản phẩm</h2>
                <p class="text-sm text-gray-500 mt-1">Tạo loại mới để chọn khi thêm hoặc sửa sản phẩm.</p>
            </div>
            <button type="button" onclick="document.getElementById('categoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="<?php echo esc_url(admin_url('admin.php?page=qln-products&action=store-category')); ?>" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tên loại</label>
                <input type="text" name="ten_loai" class="w-full px-4 py-2 border border-gray-200 rounded-lg outline-none focus:border-green-600" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Mô tả</label>
                <textarea name="mo_ta" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg outline-none focus:border-green-600"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('categoryModal').classList.add('hidden')" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold">Hủy</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#0a5c36] hover:bg-green-800 text-white font-bold shadow-sm">Lưu loại</button>
            </div>
        </form>
    </div>
</div>