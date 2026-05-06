<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết Hóa Đơn</h2>
        <a href="?page=qln-invoices" class="text-gray-500 hover:text-gray-800 underline">Quay lại</a>
    </div>

    <!-- Thông tin chung -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">Thông tin hóa đơn</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mã hóa đơn</label>
                <p class="px-4 py-2 bg-gray-50 rounded-lg border"><?php echo esc_html($invoice->ma_hd); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng</label>
                <p class="px-4 py-2 bg-gray-50 rounded-lg border">
                    <?php 
                        $kh = array_filter($customers, function($c) use ($invoice) { return $c->id == $invoice->khach_hang_id; });
                        $ten_kh = !empty($kh) ? reset($kh)->ten_kh : 'Không xác định';
                        echo esc_html($ten_kh);
                    ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <p class="px-4 py-2 bg-gray-50 rounded-lg border"><?php echo esc_html($invoice->trang_thai); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày tạo</label>
                <p class="px-4 py-2 bg-gray-50 rounded-lg border"><?php echo date('d/m/Y H:i', strtotime($invoice->ngay_tao)); ?></p>
            </div>
        </div>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="font-bold text-lg mb-4">Chi tiết sản phẩm</h3>
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-2 text-left">Sản phẩm</th>
                    <th class="p-2 text-left">Số lượng</th>
                    <th class="p-2 text-left">Đơn giá</th>
                    <th class="p-2 text-left">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $item): ?>
                <tr class="border-b">
                    <td class="p-2"><?php echo esc_html($item->ten_sp); ?></td>
                    <td class="p-2"><?php echo $item->so_luong; ?></td>
                    <td class="p-2"><?php echo number_format($item->don_gia); ?>đ</td>
                    <td class="p-2"><?php echo number_format($item->thanh_tien); ?>đ</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="border-t bg-gray-50">
                    <td colspan="3" class="p-2 text-right font-bold">Tổng cộng:</td>
                    <td class="p-2 font-bold text-red-600"><?php echo number_format($invoice->tong_tien); ?>đ</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="mt-6">
        <a href="?page=qln-invoices" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-6 rounded-lg">Quay lại</a>
    </div>
</div>