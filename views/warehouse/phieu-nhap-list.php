<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Quản lý Nhập hàng</h2>
            <p class="text-gray-500 text-sm mt-1">Theo dõi và quản lý các phiếu nhập kho.</p>
        </div>
        <a href="?page=qln-nhap-hang-add" class="bg-[#0a5c36] hover:bg-green-800 text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition flex items-center gap-2">
            + Tạo phiếu nhập mới
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tổng phiếu</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo str_pad($stats['total_phieu'] ?? 0, 2, '0', STR_PAD_LEFT); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Giá trị nhập kho</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['total_value'] ?? 0); ?>đ</h3>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left border-collapse">
                <thead class="text-[11px] uppercase font-bold text-gray-400 tracking-wider border-b">
                    <tr>
                        <th class="px-4 py-3">Mã phiếu</th>
                        <th class="px-4 py-3">Nhà cung cấp</th>
                        <th class="px-4 py-3">Ngày nhập</th>
                        <th class="px-4 py-3">Người tạo</th>
                        <th class="px-4 py-3">Tổng tiền</th>
                        <th class="px-4 py-3">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <?php if(!empty($phieuNhaps)): foreach ($phieuNhaps as $pn): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 font-bold text-green-700"><?php echo esc_html($pn['ma_pn']); ?></td>
                        <td class="px-4 py-4 font-bold text-gray-800"><?php echo esc_html($pn['ten_ncc']); ?></td>
                        <td class="px-4 py-4 text-gray-600"><?php echo date('d/m/Y', strtotime($pn['ngay_nhap'])); ?></td>
                        <td class="px-4 py-4 text-gray-600"><?php echo esc_html($pn['nguoi_tao']); ?></td>
                        <td class="px-4 py-4 font-bold text-gray-800"><?php echo number_format($pn['tong_tien']); ?> đ</td>
                        <td class="px-4 py-4">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-[11px] font-bold">
                                <?php echo esc_html($pn['trang_thai']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">Chưa có phiếu nhập.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- THANH PHÂN TRANG -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="p-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500 bg-gray-50">
            <p>Hiển thị trang <b class="text-gray-800"><?php echo $trang_hien_tai; ?></b> trên tổng số <?php echo $total_pages; ?> trang (Tổng: <?php echo $total_items; ?> phiếu)</p>
            <div class="flex gap-1">
                <!-- Nút Lùi -->
                <?php if ($trang_hien_tai > 1): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $trang_hien_tai - 1; ?>" class="w-8 h-8 flex items-center justify-center border border-gray-200 bg-white rounded hover:bg-gray-100 transition"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <!-- Các trang 1, 2, 3... -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $i; ?>" class="w-8 h-8 flex items-center justify-center border <?php echo ($i == $trang_hien_tai) ? 'border-[#0a5c36] bg-[#0a5c36] text-white font-bold' : 'border-gray-200 bg-white hover:bg-gray-100 font-bold text-gray-600'; ?> rounded transition shadow-sm"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <!-- Nút Tiến -->
                <?php if ($trang_hien_tai < $total_pages): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $trang_hien_tai + 1; ?>" class="w-8 h-8 flex items-center justify-center border border-gray-200 bg-white rounded hover:bg-gray-100 transition"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>