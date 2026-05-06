<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Sản Phẩm</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách nội thất hiện có trong kho.</p>
        </div>
        <button class="bg-[#0a5c36] hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Thêm Sản Phẩm</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã SP</th>
                    <th class="px-6 py-4">Tên Sản Phẩm</th>
                    <th class="px-6 py-4">Giá Bán</th>
                    <th class="px-6 py-4 text-center">Tồn Kho</th>
                    <th class="px-6 py-4">Trạng Thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($products as $p): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-700"><?php echo esc_html($p->ma_sp); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-medium"><?php echo esc_html($p->ten_sp); ?></td>
                    <td class="px-6 py-4 text-blue-600 font-semibold"><?php echo number_format($p->gia_ban); ?>đ</td>
                    <td class="px-6 py-4 text-center font-bold text-gray-700"><?php echo esc_html($p->so_luong_ton); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                            $statusClass = 'bg-gray-100 text-gray-700';
                            if ($p->trang_thai == 'Đang bán') $statusClass = 'bg-green-100 text-green-700';
                            if ($p->trang_thai == 'Sắp hết hàng') $statusClass = 'bg-yellow-100 text-yellow-700';
                            if ($p->trang_thai == 'Hết hàng') $statusClass = 'bg-red-100 text-red-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $statusClass; ?>">
                            <?php echo esc_html($p->trang_thai); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>