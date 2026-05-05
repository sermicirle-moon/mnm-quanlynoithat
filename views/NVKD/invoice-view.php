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
            
            <a href="<?php echo admin_url('admin.php?page=qln-invoices'); ?>" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i> Hóa đơn
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-customers'); ?>" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
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
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Hóa Đơn</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi trạng thái các đơn hàng.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã HĐ</th>
                    <th class="px-6 py-4">Khách Hàng</th>
                    <th class="px-6 py-4">Tổng Tiền</th>
                    <th class="px-6 py-4">Ngày Tạo</th>
                    <th class="px-6 py-4">Trạng Thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($invoices as $inv): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-blue-600"><?php echo esc_html($inv->ma_hd); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-medium"><?php echo esc_html($inv->ten_kh); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo number_format($inv->tong_tien); ?>đ</td>
                    <td class="px-6 py-4 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($inv->ngay_tao)); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                            $statusClass = 'bg-gray-100 text-gray-700';
                            if ($inv->trang_thai == 'Đã thanh toán') $statusClass = 'bg-green-100 text-green-700';
                            if ($inv->trang_thai == 'Chờ thanh toán') $statusClass = 'bg-yellow-100 text-yellow-700';
                            if ($inv->trang_thai == 'Đang xử lý') $statusClass = 'bg-blue-100 text-blue-700';
                            if ($inv->trang_thai == 'Đã hủy') $statusClass = 'bg-red-100 text-red-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase <?php echo $statusClass; ?>">
                            <?php echo esc_html($inv->trang_thai); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>