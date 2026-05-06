<?php if (!defined('ABSPATH')) exit; ?>
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
