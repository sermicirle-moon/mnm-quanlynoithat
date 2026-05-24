<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 bg-gray-50 flex-1 custom-scrollbar overflow-y-auto">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Bảng Điều Khiển Nhà Kho</h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">Tài khoản thủ kho: <span class="text-[#0a5c36] font-bold"><?php echo esc_html($userName); ?></span>. Giám sát thời gian thực luồng bốc dỡ hàng hóa.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="?page=qln-nhap-hang-add" class="bg-gray-800 hover:bg-black text-white font-bold px-5 py-3 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i> Lập phiếu nhập kho
            </a>
            <a href="?page=qln-xuat-kho&action=create" class="bg-[#0a5c36] hover:bg-green-800 text-white font-bold px-5 py-3 rounded-xl text-xs shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box"></i> Điều xe xuất kho
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-blue-500">
            <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1">🚚 Chuyến xe chờ xuất kho</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($pending_stock_out); ?> <span class="text-sm font-medium text-gray-400">lệnh</span></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-yellow-500">
            <p class="text-[10px] font-black text-yellow-500 uppercase tracking-widest mb-1">📥 Hàng NCC chờ bốc dỡ</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($pending_stock_in); ?> <span class="text-sm font-medium text-gray-400">lệnh</span></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">🚨 Mã nội thất sắp hết</p>
            <h3 class="text-3xl font-black text-red-600 mt-1"><?php echo number_format($low_stock_count); ?> <span class="text-sm font-medium text-gray-400">mã</span></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">📦 Mặt hàng lưu kho</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1"><?php echo number_format($total_items); ?> <span class="text-sm font-medium text-gray-400">kiểu</span></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-truck-arrow-right text-gray-400"></i> Điều phối hành trình xuất kho mới nhất
                    </h4>
                    <a href="?page=qln-xuat-kho" class="text-xs text-green-700 font-bold hover:underline">Xem tất cả →</a>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-800 text-white font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Mã phiếu</th>
                                <th class="p-3.5">Khách hàng nhận</th>
                                <th class="p-3.5">Trạng thái xe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y font-medium text-gray-600">
                            <?php foreach($recent_stock_out as $px): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3.5 font-bold text-green-700 font-mono"><?php echo esc_html($px['ma_px']); ?></td>
                                <td class="p-3.5 font-bold text-gray-800"><?php echo esc_html($px['ten_kh']); ?></td>
                                static
                                <td class="p-3.5">
                                    <?php $color_out = ($px['trang_thai'] == 'Đã giao') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>
                                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase <?php echo $color_out; ?>">
                                        <?php echo esc_html($px['trang_thai']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recent_stock_out)): ?>
                                <tr><td colspan="3" class="p-5 text-center text-gray-400 italic">Hệ thống chưa phát sinh lệnh bốc xếp xuất kho nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-gray-400"></i> Tiến độ nhập kho nguyên vật liệu từ nhà cung cấp
                    </h4>
                    <a href="?page=qln-nhap-hang" class="text-xs text-green-700 font-bold hover:underline">Xem tất cả →</a>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-gray-800 text-white font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-3.5">Mã phiếu</th>
                                <th class="p-3.5">Nhà cung cấp đối tác</th>
                                <th class="p-3.5">Trạng thái kiểm đếm</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y font-medium text-gray-600">
                            <?php foreach($recent_stock_in as $pn): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3.5 font-bold text-blue-700 font-mono"><?php echo esc_html($pn['ma_pn']); ?></td>
                                <td class="p-3.5 font-bold text-gray-800"><?php echo esc_html($pn['ten_ncc']); ?></td>
                                <td class="p-3.5">
                                    <?php $color_in = ($pn['trang_thai'] == 'Đã nhập kho') ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>
                                    <span class="px-2.5 py-1 rounded-md text-[9px] font-black uppercase <?php echo $color_in; ?>">
                                        <?php echo esc_html($pn['trang_thai']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recent_stock_in)): ?>
                                <tr><td colspan="3" class="p-5 text-center text-gray-400 italic">Chưa ghi nhận tiến độ nhập kho vật liệu mới.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-full flex flex-col">
                <div class="mb-4">
                    <h4 class="font-black text-red-500 text-sm uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation animate-pulse"></i> Mặt hàng cần ưu tiên đặt thêm / sản xuất
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">Danh sách sản phẩm nội thất có tồn kho dưới 10 đơn vị.</p>
                </div>
                
                <div class="space-y-3 flex-1 overflow-y-auto pr-1 custom-scrollbar">
                    <?php foreach($low_stock_details as $item): ?>
                    <div class="flex justify-between items-center bg-red-50/60 p-4 rounded-xl border border-red-100 hover:bg-red-50 transition">
                        <div class="max-w-[70%]">
                            <p class="font-black text-gray-800 text-xs truncate"><?php echo esc_html($item['ten_sp']); ?></p>
                            <p class="text-[10px] text-gray-400 font-mono mt-1 uppercase tracking-wider"><i class="fa-solid fa-barcode"></i> SKU: <?php echo esc_html($item['ma_sp']); ?></p>
                        </div>
                        <div class="text-right whitespace-nowrap">
                            <span class="px-3 py-1.5 bg-red-600 text-white font-black text-xs rounded-lg shadow-sm">
                                <?php echo $item['so_luong_ton']; ?> cái
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($low_stock_details)): ?>
                    <div class="text-center py-12 text-gray-400 text-xs italic flex flex-col items-center justify-center gap-2 flex-1">
                        <i class="fa-regular fa-circle-check text-3xl text-green-500"></i>
                        Tồn kho toàn bộ sản phẩm gỗ đang ở mức an toàn.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>