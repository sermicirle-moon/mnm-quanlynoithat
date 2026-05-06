<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Khách Hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý đối tác và khách hàng mua nội thất.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Thêm Khách Hàng</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã KH</th>
                    <th class="px-6 py-4">Tên Khách Hàng</th>
                    <th class="px-6 py-4">Số Điện Thoại</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Địa Chỉ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($customers as $c): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-700"><?php echo esc_html($c->ma_kh); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-bold"><?php echo esc_html($c->ten_kh); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo esc_html($c->sdt); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo esc_html($c->email); ?></td>
                    <td class="px-6 py-4 text-gray-500 truncate max-w-xs"><?php echo esc_html($c->dia_chi); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
