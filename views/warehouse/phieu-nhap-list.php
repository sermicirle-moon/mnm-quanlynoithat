<?php if (!defined('ABSPATH')) exit; ?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .notice, .updated, .error, .update-nag { display: none !important; }
    #wpfooter { display: none !important; }
    #wpbody-content { padding-bottom: 0 !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>

<div class="flex bg-gray-50 rounded-xl overflow-hidden shadow-sm border border-gray-200" style="min-height: 85vh; margin-top: 20px; margin-right: 20px;">
    
    <!-- Nội dung chính (Tôi bỏ qua phần Sidebar vì bạn có thể tự include Sidebar chung) -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4 bg-gray-100 px-4 py-2 rounded-lg w-96">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                <input type="text" placeholder="Tìm kiếm phiếu nhập..." class="bg-transparent border-none focus:outline-none w-full text-sm">
            </div>
            <div class="flex items-center gap-5">
                <div class="relative cursor-pointer hover:bg-gray-100 p-2 rounded-full transition">
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <i class="fa-solid fa-bell text-gray-500 text-lg"></i>
                </div>
                <div class="h-6 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-800"><?php echo esc_html($userName); ?></p>
                        <p class="text-[10px] font-semibold text-gray-500 uppercase">Warehouse Manager</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userName); ?>&background=0D8B4E&color=fff" class="w-9 h-9 rounded-full shadow-sm">
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <!-- Title & Button -->
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Quản lý Nhập hàng</h2>
                    <p class="text-gray-500 text-sm mt-1">Theo dõi và quản lý các phiếu nhập kho nguyên vật liệu gỗ.</p>
                </div>
                <button class="bg-[#0a5c36] hover:bg-green-800 text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tạo phiếu nhập mới
                </button>
            </div>

            <!-- 4 Stats Cards -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tổng phiếu tháng này</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo str_pad($stats['total_phieu'], 2, '0', STR_PAD_LEFT); ?></h3>
                        <span class="text-xs font-bold text-green-600 mb-1 flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> +12%</span>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Giá trị nhập kho</p>
                    <div class="flex items-end gap-1">
                        <h3 class="text-3xl font-bold text-gray-800">
                            <?php echo $stats['total_value'] > 1000000000 ? round($stats['total_value']/1000000000, 1) . 'B' : number_format($stats['total_value']); ?>
                        </h3>
                        <span class="text-sm font-bold text-gray-500 mb-1">VNĐ</span>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Chờ thanh toán</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo str_pad($stats['cho_thanh_toan'], 2, '0', STR_PAD_LEFT); ?></h3>
                        <i class="fa-solid fa-circle-exclamation text-red-500 mb-2"></i>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nhà cung cấp mới</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo str_pad($stats['ncc_moi'], 2, '0', STR_PAD_LEFT); ?></h3>
                        <i class="fa-solid fa-circle-check text-green-500 mb-2"></i>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                
                <!-- Filters/Tabs -->
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                    <div class="flex gap-6">
                        <button class="text-green-700 font-bold border-b-2 border-green-700 pb-4 -mb-4">Tất cả</button>
                        <button class="text-gray-500 font-medium hover:text-gray-800 pb-4 -mb-4">Đã thanh toán</button>
                        <button class="text-gray-500 font-medium hover:text-gray-800 pb-4 -mb-4">Chưa thanh toán</button>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-filter"></i> Lọc
                        </button>
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                            <i class="fa-solid fa-download"></i> Xuất Excel
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto p-4">
                    <table class="w-full text-left border-collapse">
                        <thead class="text-[11px] uppercase font-bold text-gray-400 tracking-wider">
                            <tr>
                                <th class="px-4 py-3 border-b border-gray-100">Mã phiếu</th>
                                <th class="px-4 py-3 border-b border-gray-100">Nhà cung cấp</th>
                                <th class="px-4 py-3 border-b border-gray-100">Ngày nhập</th>
                                <th class="px-4 py-3 border-b border-gray-100">Người tạo</th>
                                <th class="px-4 py-3 border-b border-gray-100">Tổng tiền</th>
                                <th class="px-4 py-3 border-b border-gray-100">Trạng thái</th>
                                <th class="px-4 py-3 border-b border-gray-100 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 bg-white">
                            <?php foreach ($phieuNhaps as $pn): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-5 font-bold text-green-700 w-32"><?php echo esc_html($pn['ma_pn']); ?></td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-500 uppercase">
                                            <?php echo substr($pn['ten_ncc'], 0, 2); ?>
                                        </div>
                                        <span class="font-bold text-gray-800"><?php echo esc_html($pn['ten_ncc']); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <p class="font-medium text-gray-800"><?php echo date('d/m/Y', strtotime($pn['ngay_tao'])); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo date('H:i', strtotime($pn['ngay_tao'])); ?></p>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[11px] font-semibold border border-gray-200">
                                        <?php echo esc_html($pn['nguoi_tao']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-5 font-bold text-lg text-gray-800">
                                    <?php echo number_format($pn['tong_tien'], 0, ',', '.'); ?> đ
                                </td>
                                <td class="px-4 py-5">
                                    <?php if($pn['trang_thai'] == 'Đã thanh toán'): ?>
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-[11px] font-bold border border-green-200 flex items-center gap-1 w-max">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div> Đã thanh toán
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-md text-[11px] font-bold border border-red-100 flex items-center gap-1 w-max">
                                            <div class="w-1.5 h-1.5 bg-red-500 rounded-full"></div> Chưa thanh toán
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-5 text-center text-gray-400">
                                    <button class="hover:text-green-600 transition mx-1"><i class="fa-regular fa-eye"></i></button>
                                    <button class="hover:text-gray-800 transition mx-1"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                    <p>Hiển thị 1 - <?php echo count($phieuNhaps); ?> trên tổng số <?php echo $stats['total_phieu']; ?> phiếu nhập</p>
                    <div class="flex gap-1">
                        <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50"><i class="fa-solid fa-chevron-left"></i></button>
                        <button class="w-8 h-8 flex items-center justify-center border border-green-600 bg-green-600 text-white rounded font-bold">1</button>
                        <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50 font-bold">2</button>
                        <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50 font-bold">3</button>
                        <button class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded hover:bg-gray-50"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Bottom Banners (Dựa theo ảnh UI) -->
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-slate-800 rounded-xl p-6 relative overflow-hidden text-white shadow-sm h-40 flex flex-col justify-center">
                    <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1611078502570-5231c6a2c262?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');"></div>
                    <div class="relative z-10">
                        <h3 class="font-bold text-xl mb-1">Báo cáo kho chi tiết</h3>
                        <p class="text-sm text-slate-300 mb-4 max-w-sm">Xem phân tích dữ liệu nhập hàng theo từng loại gỗ và nhà cung cấp.</p>
                        <button class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded font-bold text-xs tracking-wider transition">XEM CHI TIẾT</button>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-100 rounded-xl p-6 flex items-center gap-6 shadow-sm">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-green-600 text-2xl border border-green-100">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Cảnh báo tồn kho thấp</h3>
                        <p class="text-sm text-gray-600 mb-2">Có 3 loại nguyên vật liệu đang ở dưới mức an toàn. Hãy tạo phiếu nhập ngay.</p>
                        <a href="#" class="text-green-700 font-bold text-sm hover:underline flex items-center gap-1">Xem danh sách nguyên liệu <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>