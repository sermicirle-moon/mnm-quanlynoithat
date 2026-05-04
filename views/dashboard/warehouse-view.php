<?php if (!defined('ABSPATH')) exit; ?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Thanh cuộn đẹp cho Sidebar và Main Content */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
</style>

<div class="flex bg-gray-50 rounded-xl overflow-hidden shadow-sm border border-gray-200" style="min-height: 85vh; margin-top: 20px; margin-right: 20px;">
    
    <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0 h-screen shadow-xl z-20">
        <div class="p-6 border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-couch text-xl"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">TimberFlow</span>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            <p class="text-[10px] uppercase font-bold text-slate-500 ml-2 mb-3 mt-2 tracking-wider">Menu Hệ Thống</p>
            
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-box w-5 text-center"></i> Sản phẩm
            </a>
            
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-file-import w-5 text-center"></i> Nhập hàng
            </a>
            
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-file-export w-5 text-center"></i> Xuất hàng
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 shrink-0">
            <a href="?page=qln-logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl transition">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Thoát hệ thống
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50">
        
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 z-10 shadow-sm">
            <h2 class="font-bold text-gray-800 text-lg">Quản lý kho</h2>
            <div class="flex items-center gap-5">
                <div class="relative cursor-pointer hover:bg-gray-100 p-2 rounded-full transition">
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <i class="fa-solid fa-bell text-gray-500 text-lg"></i>
                </div>
                <div class="h-6 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-800"><?php echo isset($userName) ? esc_html($userName) : 'Nhân viên kho'; ?></p>
                        <p class="text-[10px] font-semibold text-green-600 underline decoration-2 offset-2 uppercase">Warehouse Online</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-green-100 border border-green-200 flex items-center justify-center font-bold text-green-700 shadow-sm">
                        <?php echo isset($userName) ? substr($userName, 0, 1) : 'W'; ?>
                    </div>
                </div>
            </div>
        </header>
        <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Tổng sản phẩm</p>
                            <h3 class="text-3xl font-bold text-gray-800">1,205</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl"><i class="fa-solid fa-warehouse"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-couch"></i> 83%</span>
                        <span class="text-xs text-gray-400 font-medium">tỉ lệ lấp đầy</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Sản phẩm sắp hết</p>
                            <h3 class="text-3xl font-bold text-red-600">2</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-file-import"></i> Nhập hàng</span>
                        <span class="text-xs text-gray-400 font-medium">cần ưu tiên</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Phiếu nhập hôm nay</p>
                            <h3 class="text-3xl font-bold text-gray-800">5</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fa-solid fa-truck-ramp-box"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-clock"></i> 2 phiếu</span>
                        <span class="text-xs text-gray-400 font-medium">đang xử lý</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Phiếu xuất hôm nay</p>
                            <h3 class="text-3xl font-bold text-gray-800">8</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl"><i class="fa-solid fa-dolly"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-clock"></i> 3 phiếu</span>
                        <span class="text-xs text-gray-400 font-medium">đang chờ lấy hàng</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                    <h3 class="font-bold text-gray-800 text-lg">Yêu cầu nhập/xuất kho mới nhất</h3>
                    <button class="text-sm text-green-600 font-semibold hover:text-green-700 hover:underline px-3 py-1.5 rounded-lg hover:bg-green-50 transition">Xem tất cả</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 text-xs uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Loại phiếu</th>
                                <th class="px-6 py-4 border-b border-gray-100">Mã phiếu</th>
                                <th class="px-6 py-4 border-b border-gray-100">Sản phẩm chính</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-center">Số lượng</th>
                                <th class="px-6 py-4 border-b border-gray-100">Trạng thái</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-right">Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 bg-white">
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 flex items-center gap-2.5 font-bold text-orange-700">
                                    <i class="fa-solid fa-file-export"></i> Xuất kho
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">#ORD-9921</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Ghế Sofa Lux v2.0</td>
                                <td class="px-6 py-4 text-center font-bold text-gray-800">2</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Đã hoàn thành
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-right">04/05/2026</td>
                            </tr>
                             <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 flex items-center gap-2.5 font-bold text-green-700">
                                    <i class="fa-solid fa-file-import"></i> Nhập kho
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-800">#PO-211</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Bàn Ăn Walnut v1.5</td>
                                <td class="px-6 py-4 text-center font-bold text-gray-800">5</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                        Đang xử lý
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-right">04/05/2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>