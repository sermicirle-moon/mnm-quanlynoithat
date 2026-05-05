<?php if (!defined('ABSPATH')) exit; ?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Ẩn các thông báo rác (update, cảnh báo plugin) của WP để giao diện app sạch sẽ */
    .notice, .updated, .error, .update-nag { display: none !important; }
    #wpfooter { display: none !important; } /* Ẩn footer wp */
    
    /* Tối ưu khoảng cách gốc của WordPress */
    #wpbody-content { padding-bottom: 0 !important; }

    /* Thanh cuộn đẹp cho App */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>

<div class="flex bg-gray-50 rounded-xl overflow-hidden shadow-md border border-gray-200 mt-4 mr-4" style="height: calc(100vh - 70px);">

    <aside class="w-64 bg-slate-900 text-white flex flex-col shrink-0 z-10">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center shadow-lg shadow-green-600/30">
                    <i class="fa-solid fa-couch text-sm"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">TimberFlow</span>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            <p class="text-[10px] uppercase font-bold text-slate-500 ml-2 mb-3 mt-2 tracking-wider">Menu Hệ Thống</p>
            
            <?php 
            $current_role = isset($_SESSION['qln_role_id']) ? $_SESSION['qln_role_id'] : 1; 
            
            if ($current_role == 1): ?>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-users-gear w-5 text-center"></i> Quản lý nhân viên
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-truck-field w-5 text-center"></i> Nhà cung cấp
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-magnifying-glass-chart w-5 text-center"></i> Phân tích báo cáo
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-truck-fast w-5 text-center"></i> Vận chuyển
                </a>
                <a href="<?php echo admin_url('admin.php?page=qln-customer'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-users w-5 text-center"></i> Khách hàng
                </a>
                <a href="<?php echo admin_url('admin.php?page=qln-invoice'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Hóa đơn
                </a>
                <a href="<?php echo admin_url('admin.php?page=qln-product'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-box w-5 text-center"></i> Sản phẩm
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-file-import w-5 text-center"></i> Nhập hàng
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                    <i class="fa-solid fa-file-export w-5 text-center"></i> Xuất hàng
                </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-800 shrink-0">
            <a href="?page=qln-logout" class="flex items-center gap-3 px-4 py-2.5 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition duration-200">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Thoát hệ thống
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 z-10">
            <h2 class="font-bold text-gray-800 text-lg">Tổng quan quản trị</h2>
            <div class="flex items-center gap-5">
                <div class="relative cursor-pointer hover:bg-gray-100 p-2 rounded-full transition">
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <i class="fa-solid fa-bell text-gray-500 text-lg"></i>
                </div>
                <div class="h-6 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-800"><?php echo isset($userName) ? esc_html($userName) : 'Quản trị viên'; ?></p>
                        <p class="text-[11px] font-semibold text-green-600">Admin Online</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-green-100 border border-green-200 flex items-center justify-center font-bold text-green-700 shadow-sm">
                        <?php echo isset($userName) ? substr($userName, 0, 1) : 'A'; ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Nhân sự</p>
                            <h3 class="text-3xl font-bold text-gray-800">12</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="fa-solid fa-user-tie"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> 15%</span>
                        <span class="text-xs text-gray-400 font-medium">so với tháng trước</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Đơn hàng mới</p>
                            <h3 class="text-3xl font-bold text-gray-800">48</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl"><i class="fa-solid fa-cart-shopping"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-clock"></i> 5 đơn</span>
                        <span class="text-xs text-gray-400 font-medium">đang chờ xử lý</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Doanh thu</p>
                            <h3 class="text-3xl font-bold text-gray-800">150tr</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> 8%</span>
                        <span class="text-xs text-gray-400 font-medium">tăng trưởng</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Sản phẩm kho</p>
                            <h3 class="text-3xl font-bold text-gray-800">1,205</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl"><i class="fa-solid fa-warehouse"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> 2 mặt hàng</span>
                        <span class="text-xs text-gray-400 font-medium">sắp hết hàng</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-gray-800 text-lg">Nhân sự mới tham gia</h3>
                    <button class="text-sm text-green-600 font-semibold hover:text-green-700 hover:underline px-3 py-1.5 rounded-lg hover:bg-green-50 transition">Xem tất cả</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Nhân viên</th>
                                <th class="px-6 py-4 border-b border-gray-100">Chức vụ</th>
                                <th class="px-6 py-4 border-b border-gray-100">Quê quán</th>
                                <th class="px-6 py-4 border-b border-gray-100">Trạng thái</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 bg-white">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm font-bold">AN</div>
                                        <div>
                                            <p class="font-bold text-gray-800">Nguyễn Văn An</p>
                                            <p class="text-xs text-gray-500 mt-0.5">an.nguyen@timber.vn</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">Quản lý kho</td>
                                <td class="px-6 py-4 text-gray-500">Hà Nam</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Đang làm việc
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="p-2 hover:bg-gray-100 rounded-lg text-gray-400 transition"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>