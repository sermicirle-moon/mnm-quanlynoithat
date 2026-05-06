<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 max-w-5xl mx-auto">
    <!-- Nút trở về và Header -->
    <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <div class="flex items-center gap-4">
            <a href="?page=qln-nhap-hang" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-green-600 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Chi tiết Phiếu Nhập: <span class="text-green-600"><?php echo esc_html($phieuNhap['ma_pn']); ?></span></h2>
                <p class="text-sm text-gray-500 mt-1">Trạng thái: 
                    <span class="font-bold <?php echo ($phieuNhap['trang_thai'] === 'Đã nhập kho') ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php echo esc_html($phieuNhap['trang_thai']); ?>
                    </span>
                </p>
            </div>
        </div>
        <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> In phiếu
        </button>
    </div>

    <!-- Thông tin chung -->
    <div class="grid grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div>
            <p class="text-sm text-gray-500 mb-1">Nhà cung cấp:</p>
            <p class="font-bold text-gray-800 text-lg"><?php echo esc_html($ncc['ten_ncc'] ?? 'Không rõ'); ?></p>
            <p class="text-sm text-gray-600 mt-1"><i class="fa-solid fa-phone mr-1"></i> <?php echo esc_html($ncc['sdt'] ?? ''); ?></p>
        </div>
        <div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Người lập phiếu:</p>
                    <p class="font-bold text-gray-800"><?php echo esc_html($nguoiTao['ho_ten'] ?? 'Không rõ'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Ngày lập:</p>
                    <p class="font-bold text-gray-800"><?php echo date('d/m/Y H:i', strtotime($phieuNhap['ngay_nhap'])); ?></p>
                </div>
                <div class="col-span-2 mt-2">
                    <p class="text-sm text-gray-500 mb-1">Ghi chú:</p>
                    <p class="text-gray-800 bg-gray-50 p-2 rounded border border-gray-100"><?php echo esc_html($phieuNhap['ghi_chu'] ?: 'Không có ghi chú'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng chi tiết sản phẩm -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-800 text-white text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Mã SP</th>
                    <th class="px-6 py-4">Tên Sản phẩm (Tham chiếu ID)</th>
                    <th class="px-6 py-4 text-center">Số lượng</th>
                    <th class="px-6 py-4 text-right">Đơn giá nhập</th>
                    <th class="px-6 py-4 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($chiTiet as $ct): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-bold text-gray-500">#<?php echo $ct['san_pham_id']; ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800">Sản phẩm ID: <?php echo $ct['san_pham_id']; ?></td>
                    <td class="px-6 py-4 text-center font-bold"><?php echo $ct['so_luong']; ?></td>
                    <td class="px-6 py-4 text-right text-gray-600"><?php echo number_format($ct['gia_nhap']); ?> đ</td>
                    <td class="px-6 py-4 text-right font-bold text-green-700"><?php echo number_format($ct['thanh_tien']); ?> đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Tổng kết -->
        <div class="bg-green-50 p-6 border-t border-green-100 flex justify-end">
            <div class="text-right">
                <p class="text-sm text-green-700 font-bold uppercase mb-1">Tổng cộng thanh toán</p>
                <p class="text-3xl font-black text-green-800"><?php echo number_format($phieuNhap['tong_tien']); ?> VNĐ</p>
            </div>
        </div>
    </div>
</div>