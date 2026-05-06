<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Hóa Đơn</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi trạng thái các đơn hàng.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã HĐ</th>
                    <th class="px-6 py-4">Khách Hàng</th>
                    <th class="px-6 py-4">Tổng Tiền</th>
                    <th class="px-6 py-4">Ngày Tạo</th>
                    <th class="px-6 py-4">Trạng Thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($invoices as $inv): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-blue-600"><?php echo esc_html($inv->ma_hd); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-medium"><?php echo esc_html($inv->ten_kh); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo number_format($inv->tong_tien); ?>đ</td>
                    <td class="px-6 py-4 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($inv->ngay_tao)); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                            $statusClass = 'bg-gray-100 text-gray-700';
                            if ($inv->trang_thai == 'Đã thanh toán') $statusClass = 'bg-green-100 text-green-700';
                            if ($inv->trang_thai == 'Chờ thanh toán') $statusClass = 'bg-yellow-100 text-yellow-700';
                            if ($inv->trang_thai == 'Đang xử lý') $statusClass = 'bg-blue-100 text-blue-700';
                            if ($inv->trang_thai == 'Đã hủy') $statusClass = 'bg-red-100 text-red-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase <?php echo $statusClass; ?>">
                            <?php echo esc_html($inv->trang_thai); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
