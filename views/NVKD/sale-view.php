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
            
            <a href="admin.php?page=qln-dashboard" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            
            <a href="admin.php?page=qln-dashboard&view=invoices" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Hóa đơn
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-customers'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-users w-5 text-center"></i> Khách hàng
            </a>
            
            <a href="admin.php?page=qln-dashboard&view=products" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-box w-5 text-center"></i> Sản phẩm
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
            <h2 class="font-bold text-gray-800 text-lg">Quản lý kinh doanh</h2>
            <div class="flex items-center gap-5">
                <div class="relative cursor-pointer hover:bg-gray-100 p-2 rounded-full transition">
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <i class="fa-solid fa-bell text-gray-500 text-lg"></i>
                </div>
                <div class="h-6 w-[1px] bg-gray-200"></div>
                <div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-800"><?php echo isset($userName) ? esc_html($userName) : 'Nhân viên sales'; ?></p>
                        <p class="text-[10px] font-semibold text-green-600 underline decoration-2 offset-2 uppercase">Sales Online</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-green-100 border border-green-200 flex items-center justify-center font-bold text-green-700 shadow-sm">
                        <?php echo isset($userName) ? substr($userName, 0, 1) : 'S'; ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Đơn hàng hôm nay</p>
                            <h3 class="text-3xl font-bold text-gray-800">42</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl"><i class="fa-solid fa-cart-shopping"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-clock"></i> ORD-9121</span>
                        <span class="text-xs text-gray-400 font-medium">mới nhất</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Doanh thu tháng</p>
                            <h3 class="text-3xl font-bold text-gray-800">1.2tr</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> 12%</span>
                        <span class="text-xs text-gray-400 font-medium">tăng trưởng</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Khách hàng mới</p>
                            <h3 class="text-3xl font-bold text-gray-800">15</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="fa-solid fa-user-plus"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-calendar-plus"></i> 3 tháng</span>
                        <span class="text-xs text-gray-400 font-medium">so với tháng trước</span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1 tracking-tight">Đơn hàng chờ duyệt</p>
                            <h3 class="text-3xl font-bold text-red-600">8</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="fa-solid fa-clock"></i></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1"><i class="fa-solid fa-exclamation-triangle"></i> Cần xử lý</span>
                        <span class="text-xs text-gray-400 font-medium">trong hôm nay</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.05)] overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                    <h3 class="font-bold text-gray-800 text-lg">Hóa đơn mới nhất</h3>
                    <button class="text-sm text-green-600 font-semibold hover:text-green-700 hover:underline px-3 py-1.5 rounded-lg hover:bg-green-50 transition">Xem tất cả</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/80 text-xs uppercase font-bold text-gray-500 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Mã hóa đơn</th>
                                <th class="px-6 py-4 border-b border-gray-100">Khách hàng</th>
                                <th class="px-6 py-4 border-b border-gray-100">Tổng tiền</th>
                                <th class="px-6 py-4 border-b border-gray-100">Trạng thái</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-right">Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-100 bg-white">
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-green-700">#INV-8911</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Nội thất ABC - Hà Nội</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">12.500.000đ</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Đã thanh toán
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-right">04/05/2026</td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-green-700">#INV-8910</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Gỗ Việt Mỹ - HCM</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">8.200.000đ</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>
                                        Chờ thanh toán
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-right">03/05/2026</td>
                            </tr>
                             <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-green-700">#INV-8909</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">Nội thất Modern - Đà Nẵng</td>
                                <td class="px-6 py-4 text-gray-800 font-medium">15.000.000đ</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                        Đã hủy
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-right">02/05/2026</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>