<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 bg-gray-50 flex-1">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-800 tracking-tight">Hệ Thống Quản Trị Trung Tâm</h1>
        <p class="text-sm text-gray-500 mt-1">Dữ liệu tổng hợp toàn bộ tình hình kinh doanh thực tế trong ngày.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-24 h-24 bg-green-50 rounded-full translate-x-6 translate-y-6 opacity-60 flex items-center justify-center text-green-500 text-3xl"><i class="fa-solid fa-sack-dollar"></i></div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Doanh thu thực tế</p>
            <h3 class="text-3xl font-black text-green-600 mt-1"><?php echo number_format($total_revenue); ?>đ</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Lượng đơn phát sinh</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($total_orders); ?> HĐ</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Mặt hàng trong hệ thống</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($total_products); ?> mã</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-wider">Khách hàng dữ liệu</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($total_customers); ?> KH</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border p-6 shadow-sm">
            <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider mb-4"><i class="fa-solid fa-receipt mr-1 text-gray-400"></i> Hóa đơn mới phát sinh</h4>
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-gray-800 text-white font-bold uppercase tracking-wider">
                        <tr>
                            <th class="p-3">Mã HĐ</th>
                            <th class="p-3">Khách hàng</th>
                            <th class="p-3">Giá trị đơn</th>
                            <th class="p-3">Trạng thái dòng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y font-medium text-gray-600">
                        <?php foreach($recent_invoices as $hd): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-mono font-bold text-gray-400">#<?php echo esc_html($hd['ma_hd']); ?></td>
                            <td class="p-3 font-bold text-gray-800"><?php echo esc_html($hd['ten_kh']); ?></td>
                            <td class="p-3 font-black text-gray-900"><?php echo number_format($hd['tong_tien']); ?>đ</td>
                            <td class="p-3">
                                <?php $color = ($hd['trang_thai'] == 'Đã thanh toán') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase <?php echo $color; ?>">
                                    <?php echo esc_html($hd['trang_thai']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_invoices)): ?>
                            <tr><td colspan="4" class="p-4 text-center text-gray-400 italic">Chưa ghi nhận hóa đơn giao dịch nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-1 bg-white rounded-2xl border p-6 shadow-sm flex flex-col">
            <h4 class="font-black text-red-500 text-sm uppercase tracking-wider mb-4"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Cảnh báo sắp hết hàng</h4>
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar">
                <?php foreach($low_stock_products as $sp): ?>
                <div class="flex justify-between items-center bg-red-50 p-3 rounded-xl border border-red-100">
                    <div class="max-w-[70%]">
                        <p class="font-black text-gray-800 text-xs truncate"><?php echo esc_html($sp['ten_sp']); ?></p>
                        <p class="text-[10px] text-gray-400 font-mono mt-0.5">Mã: <?php echo esc_html($sp['ma_sp']); ?></p>
                    </div>
                    <span class="px-2.5 py-1 bg-red-600 text-white font-black text-xs rounded-lg shadow-sm whitespace-nowrap"><?php echo $sp['so_luong_ton']; ?> cái</span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($low_stock_products)): ?>
                    <div class="text-center py-8 text-gray-400 text-xs italic">Số lượng tồn kho vật liệu đang ở mức an toàn.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border p-6 shadow-sm">
        <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider mb-4"><i class="fa-solid fa-user-tie mr-1 text-gray-400"></i> Đội ngũ nhân sự vận hành</h4>
        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-gray-800 text-white font-bold uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Họ và tên nhân viên</th>
                        <th class="p-3">Bộ phận chức vụ</th>
                        <th class="p-3">Quê quán</th>
                    </tr>
                </thead>
                <tbody class="divide-y font-medium text-gray-600">
                    <?php foreach($staff_members as $user): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-black text-gray-500 uppercase"><?php echo mb_substr($user['ho_ten'], 0, 2); ?></div>
                                <div>
                                    <p class="font-bold text-gray-800"><?php echo esc_html($user['ho_ten']); ?></p>
                                    <p class="text-[10px] text-gray-400 font-mono mt-0.5"><?php echo esc_html($user['email']); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 font-bold text-gray-700">
                            <?php 
                                if($user['role_id'] == 1) echo 'Giám đốc hệ thống';
                                elseif($user['role_id'] == 2) echo 'Nhân viên kinh doanh';
                                else echo 'Quản lý kho hàng';
                            ?>
                        </td>
                        <td class="p-3 text-gray-500"><?php echo esc_html($user['que_quan'] ?: 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>