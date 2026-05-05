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
            
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition duration-200">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=qln-products'); ?>" class="flex items-center gap-3 px-4 py-2.5 bg-green-500/10 text-green-400 rounded-xl border border-green-500/20 transition duration-200">
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

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Sản Phẩm</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách nội thất hiện có trong kho.</p>
        </div>
        <button class="bg-[#0a5c36] hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">+ Thêm Sản Phẩm</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Mã SP</th>
                    <th class="px-6 py-4">Tên Sản Phẩm</th>
                    <th class="px-6 py-4">Giá Bán</th>
                    <th class="px-6 py-4 text-center">Tồn Kho</th>
                    <th class="px-6 py-4">Trạng Thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($products as $p): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-semibold text-gray-700"><?php echo esc_html($p->ma_sp); ?></td>
                    <td class="px-6 py-4 text-gray-800 font-medium"><?php echo esc_html($p->ten_sp); ?></td>
                    <td class="px-6 py-4 text-blue-600 font-semibold"><?php echo number_format($p->gia_ban); ?>đ</td>
                    <td class="px-6 py-4 text-center font-bold text-gray-700"><?php echo esc_html($p->so_luong_ton); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                            $statusClass = 'bg-gray-100 text-gray-700';
                            if ($p->trang_thai == 'Đang bán') $statusClass = 'bg-green-100 text-green-700';
                            if ($p->trang_thai == 'Sắp hết hàng') $statusClass = 'bg-yellow-100 text-yellow-700';
                            if ($p->trang_thai == 'Hết hàng') $statusClass = 'bg-red-100 text-red-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $statusClass; ?>">
                            <?php echo esc_html($p->trang_thai); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>