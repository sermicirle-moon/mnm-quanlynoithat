<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TimberFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body class="bg-gray-50 font-sans flex h-screen overflow-hidden">

    <script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex bg-gray-50 rounded-xl overflow-hidden shadow-sm border border-gray-200" style="min-height: 85vh; margin-top: 20px;">
    
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-couch text-xl"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">TimberFlow</span>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <p class="text-[10px] uppercase font-bold text-slate-500 ml-2 mb-2">Chính</p>
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-green-600/10 text-green-400 rounded-xl border border-green-600/20">
                <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-users w-5"></i> Nhân viên
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-box-open w-5"></i> Sản phẩm
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-receipt w-5"></i> Đơn hàng
            </a>

            <p class="text-[10px] uppercase font-bold text-slate-500 ml-2 mt-6 mb-2">Hệ thống</p>
            <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-gear w-5"></i> Cấu hình
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="?page=qln-logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl transition">
                <i class="fa-solid fa-right-from-bracket w-5"></i> Thoát hệ thống
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col">
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-8">
            <h2 class="font-semibold text-gray-700">Tổng quan quản trị</h2>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    <i class="fa-solid fa-bell text-gray-400"></i>
                </div>
                <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-800"><?php echo esc_html($userName); ?></p>
                        <p class="text-[10px] text-green-600">Admin Online</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-slate-200 border border-gray-300 flex items-center justify-center font-bold text-slate-600">
                        <?php echo substr($userName, 0, 1); ?>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-8 overflow-y-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Nhân sự</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">12</h3>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><i class="fa-solid fa-user-tie"></i></div>
                    </div>
                    <p class="text-xs text-green-600 mt-4 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-arrow-up"></i> 15% <span class="text-gray-400 font-normal">so với tháng trước</span>
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Đơn hàng mới</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">48</h3>
                        </div>
                        <div class="p-3 bg-orange-50 text-orange-600 rounded-xl"><i class="fa-solid fa-cart-shopping"></i></div>
                    </div>
                    <p class="text-xs text-orange-600 mt-4 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-clock"></i> 5 đơn <span class="text-gray-400 font-normal">chờ xử lý</span>
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Doanh thu</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">150tr</h3>
                        </div>
                        <div class="p-3 bg-green-50 text-green-600 rounded-xl"><i class="fa-solid fa-money-bill-trend-up"></i></div>
                    </div>
                    <p class="text-xs text-green-600 mt-4 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-arrow-up"></i> 8% <span class="text-gray-400 font-normal">tăng trưởng</span>
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Sản phẩm kho</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">1,205</h3>
                        </div>
                        <div class="p-3 bg-purple-50 text-purple-600 rounded-xl"><i class="fa-solid fa-warehouse"></i></div>
                    </div>
                    <p class="text-xs text-red-600 mt-4 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-triangle-exclamation"></i> 2 mặt hàng <span class="text-gray-400 font-normal">sắp hết</span>
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Nhân sự mới tham gia</h3>
                    <button class="text-sm text-green-700 font-medium hover:underline">Xem tất cả</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase font-bold text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Nhân viên</th>
                                <th class="px-6 py-4">Chức vụ</th>
                                <th class="px-6 py-4">Quê quán</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">AN</div>
                                        <div>
                                            <p class="font-bold text-gray-800">Nguyễn Văn An</p>
                                            <p class="text-[11px] text-gray-500">an.nguyen@timber.vn</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium">Quản lý kho</td>
                                <td class="px-6 py-4 text-gray-500">Hà Nam</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">Đang làm việc</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="p-2 hover:bg-gray-100 rounded-lg text-gray-400"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>