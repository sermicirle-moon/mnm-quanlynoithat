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
            
            <a href="<?php echo admin_url('admin.php?page=qln-dashboard'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-invoices'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Hóa đơn
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-customers'); ?>" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
                <i class="fa-solid fa-users w-5 text-center"></i> Khách hàng
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-products'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-box w-5 text-center"></i> Sản phẩm
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 shrink-0">
            <a href="?page=qln-logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-400/10 rounded-xl transition">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Thoát hệ thống
            </a>
        </div>
    </aside>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Khách Hàng</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý đối tác và khách hàng mua nội thất.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Thêm Khách Hàng</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã KH</th>
                    <th class="px-6 py-4">Tên Khách Hàng</th>
                    <th class="px-6 py-4">Số Điện Thoại</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Địa Chỉ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($customers as $c): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-700"><?php echo esc_html($c->ma_kh); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-bold"><?php echo esc_html($c->ten_kh); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo esc_html($c->sdt); ?></td>
                    <td class="px-6 py-4 text-gray-600"><?php echo esc_html($c->email); ?></td>
                    <td class="px-6 py-4 text-gray-500 truncate max-w-xs"><?php echo esc_html($c->dia_chi); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>