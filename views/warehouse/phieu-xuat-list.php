<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Điều phối Xuất kho</h2>
            <p class="text-gray-500 text-sm mt-1">Quản lý các chuyến xe giao hàng và gom đơn hóa đơn.</p>
        </div>
        <a href="?page=qln-xuat-kho&action=create" class="bg-[#0a5c36] hover:bg-green-800 text-white px-5 py-2.5 rounded-lg font-bold shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i> Lập chuyến mới
        </a>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng chuyến</p>
            <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($stats['total'] ?? 0); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-yellow-500 uppercase tracking-widest mb-1">Đang chờ đi</p>
            <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($stats['pending'] ?? 0); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Giao thành công</p>
            <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($stats['delivered'] ?? 0); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Đã hủy chuyến</p>
            <h3 class="text-2xl font-black text-gray-800"><?php echo number_format($stats['cancelled'] ?? 0); ?></h3>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="grid grid-cols-6 gap-3 items-end">
            <input type="hidden" name="page" value="qln-xuat-kho">
            <div class="col-span-2">
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Tìm kiếm</label>
                <input type="text" name="search" placeholder="Mã phiếu / Tên khách..." value="<?php echo esc_attr($_GET['search'] ?? ''); ?>" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-green-600 text-sm">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
                    <option value="">Tất cả</option>
                    <option value="Chờ xuất kho" <?php echo ($_GET['status'] ?? '') == 'Chờ xuất kho' ? 'selected' : ''; ?>>Chờ đi</option>
                    <option value="Đã giao" <?php echo ($_GET['status'] ?? '') == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                    <option value="Đã hủy" <?php echo ($_GET['status'] ?? '') == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Từ ngày</label>
                <input type="date" name="date_from" value="<?php echo esc_attr($_GET['date_from'] ?? ''); ?>" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Đến ngày</label>
                <input type="date" name="date_to" value="<?php echo esc_attr($_GET['date_to'] ?? ''); ?>" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-bold text-sm flex-1">Lọc</button>
                <a href="?page=qln-xuat-kho" class="bg-gray-100 text-gray-500 px-3 py-2 rounded-lg"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-800 text-white text-[11px] uppercase font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Mã Phiếu</th>
                    <th class="px-6 py-4">Khách hàng</th>
                    <th class="px-6 py-4">Đơn vị vận chuyển</th>
                    <th class="px-6 py-4">Ngày xuất</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                <?php if(!empty($phieuXuats)): foreach ($phieuXuats as $px): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-green-700"><?php echo esc_html($px['ma_px']); ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800"><?php echo esc_html($px['ten_kh']); ?></td>
                    <td class="px-6 py-4 text-gray-600">
                        <span class="block font-medium"><?php echo esc_html($px['ten_nvc']); ?></span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase">BSX: <?php echo esc_html($px['bien_so_xe']); ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 font-medium"><?php echo date('d/m/Y H:i', strtotime($px['ngay_xuat'])); ?></td>
                    <td class="px-6 py-4">
                        <?php 
                            $st = $px['trang_thai'];
                            $color = ($st == 'Đã giao') ? 'bg-green-100 text-green-700' : (($st == 'Đã hủy') ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                        ?>
                        <span class="<?php echo $color; ?> px-3 py-1 rounded-md text-[10px] font-black uppercase">
                            <?php echo esc_html($st); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="?page=qln-xuat-kho&action=view&id=<?php echo $px['id']; ?>" class="text-gray-400 hover:text-gray-800" title="Xem chi tiết"><i class="fa-solid fa-eye"></i></a>
                            <?php if ($px['trang_thai'] == 'Chờ xuất kho'): ?>
                                <a href="?page=qln-xuat-kho&action=deliver&id=<?php echo $px['id']; ?>" onclick="return confirm('Xác nhận giao thành công?')" class="text-green-600 hover:text-green-800"><i class="fa-solid fa-truck-ramp-box"></i></a>
                                <a href="?page=qln-xuat-kho&action=cancel&id=<?php echo $px['id']; ?>" onclick="return confirm('Hủy chuyến này?')" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-ban"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="p-10 text-center text-gray-400">Không tìm thấy phiếu xuất nào phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
    <div class="mt-6 flex justify-center gap-1">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=qln-xuat-kho&paged=<?php echo $i; ?><?php echo $url_params; ?>" 
               class="w-8 h-8 flex items-center justify-center rounded-lg font-bold text-xs transition <?php echo ($i == $trang_hien_tai) ? 'bg-[#0a5c36] text-white shadow-md' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>