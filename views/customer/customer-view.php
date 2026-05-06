<?php if (!defined('ABSPATH')) exit; ?>

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
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Khách hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý và theo dõi thông tin khách hàng B2B & B2C toàn hệ thống.</p>
        </div>
        <a href="?page=qln-customers&action=create" 
           class="bg-[#0a5c36] hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
            + Thêm Khách Hàng
        </a>
    </div>

    <!-- 3 cards thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">TỔNG KHÁCH HÀNG</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo number_format($stats->total); ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">KHÁCH B2B</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-1"><?php echo $stats->b2b; ?></h3>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $stats->total ? round($stats->b2b / $stats->total * 100, 1) : 0; ?>%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">KHÁCH B2C</p>
                    <h3 class="text-3xl font-bold text-orange-600 mt-1"><?php echo $stats->b2c; ?></h3>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $stats->total ? round($stats->b2c / $stats->total * 100, 1) : 0; ?>%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="page" value="qln-customers">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Loại khách hàng</label>
                <select name="loai" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="B2B" <?php echo ($_GET['loai'] ?? '') == 'B2B' ? 'selected' : ''; ?>>B2B (Doanh nghiệp)</option>
                    <option value="B2C" <?php echo ($_GET['loai'] ?? '') == 'B2C' ? 'selected' : ''; ?>>B2C (Cá nhân)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Đặc biệt</label>
                <select name="thuong_hieu" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="than_thiet" <?php echo ($_GET['thuong_hieu'] ?? '') == 'than_thiet' ? 'selected' : ''; ?>>⭐ Khách thân thiết (≥3 đơn)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tìm kiếm</label>
                <input type="text" name="search" placeholder="Mã, tên, email, SĐT" value="<?php echo esc_attr($_GET['search'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm w-full">Lọc dữ liệu</button>
            </div>
        </form>
    </div>

    <!-- Bảng khách hàng -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">TÊN KHÁCH HÀNG</th>
                        <th class="px-6 py-4">LOẠI KHÁCH</th>
                        <th class="px-6 py-4">THÔNG TIN LIÊN HỆ</th>
                        <th class="px-6 py-4 text-right">TỔNG CHI TIÊU</th>
                        <th class="px-6 py-4 text-center">SỐ ĐƠN</th>
                        <th class="px-6 py-4 text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Không tìm thấy khách hàng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800"><?php echo esc_html($c->ten_kh); ?></div>
                                <div class="text-xs text-gray-400 mt-0.5">Mã: <?php echo esc_html($c->ma_kh); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($c->loai_khach_hang == 'B2B'): ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">B2B</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">B2C</span>
                                <?php endif; ?>
                                <?php if ($c->so_luong_don_hang >= 3): ?>
                                    <span class="ml-2 px-2 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700">⭐ Thân thiết</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($c->sdt): ?><i class="fa-solid fa-phone mr-1 text-gray-400"></i><?php echo esc_html($c->sdt); ?><br><?php endif; ?>
                                <?php if ($c->email): ?><i class="fa-solid fa-envelope mr-1 text-gray-400"></i><?php echo esc_html($c->email); ?><br><?php endif; ?>
                                <?php if ($c->dia_chi): ?><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i><?php echo esc_html($c->dia_chi); ?><?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-blue-600"><?php echo number_format($c->tong_tien_da_chi); ?>đ</td>
                            <td class="px-6 py-4 text-center font-semibold"><?php echo $c->so_luong_don_hang; ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="?page=qln-customers&action=edit&id=<?php echo $c->id; ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="?page=qln-customers&action=delete&id=<?php echo $c->id; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?')"
                                   class="text-red-600 hover:text-red-800 mx-1" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>