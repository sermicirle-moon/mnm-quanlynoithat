<?php if (!defined('ABSPATH')) exit; ?>

<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-bold border-l-4 border-green-500 shadow-sm">
        <i class="fa-solid fa-check-circle mr-1"></i> <?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?>
    </div>
<?php endif; ?>
<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-bold border-l-4 border-red-500 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?>
    </div>
<?php endif; ?>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Quản lý Hóa Đơn</h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">Theo dõi thanh toán và tiến độ xuất hàng.</p>
        </div>
        <a href="?page=qln-invoices&action=create" class="bg-[#0a5c36] hover:bg-green-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg transition transform hover:scale-105">
            <i class="fa-solid fa-plus mr-1"></i> Lập Hóa Đơn
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tổng hóa đơn</p>
            <h3 class="text-2xl font-black text-gray-800"><?php echo number_format(count($invoices)); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
            <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Doanh thu thực tế</p>
            <h3 class="text-2xl font-black text-gray-800"><?php 
                $rev = 0; foreach($invoices as $i) if($i->trang_thai == 'Đã thanh toán') $rev += $i->tong_tien;
                echo number_format($rev); ?>đ</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-yellow-500">
            <p class="text-[10px] font-black text-yellow-500 uppercase tracking-widest mb-1">Chờ thu tiền</p>
            <h3 class="text-2xl font-black text-gray-800"><?php 
                $pnd = 0; foreach($invoices as $i) if($i->trang_thai == 'Chờ thanh toán') $pnd++;
                echo $pnd; ?></h3>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
            <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Hủy / Hoàn tiền</p>
            <h3 class="text-2xl font-black text-gray-800"><?php 
                $err = 0; foreach($invoices as $i) if($i->trang_thai == 'Hoàn tiền' || $i->trang_thai == 'Đã hủy') $err++;
                echo $err; ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-gray-800 text-white text-[11px] uppercase font-black tracking-wider">
                <tr>
                    <th class="px-6 py-4">Mã HĐ</th>
                    <th class="px-6 py-4">Khách hàng</th>
                    <th class="px-6 py-4">Tổng tiền</th>
                    <th class="px-6 py-4">Thanh toán</th>
                    <th class="px-6 py-4 border-l border-gray-700">Giao hàng</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($invoices as $inv): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-gray-400">#<?php echo esc_html($inv->ma_hd); ?></td>
                    <td class="px-6 py-4 font-black text-gray-800"><?php echo esc_html($inv->ten_kh); ?></td>
                    <td class="px-6 py-4 font-black text-gray-900"><?php echo number_format($inv->tong_tien); ?>đ</td>
                    
                    <td class="px-6 py-4">
                        <?php 
                            if ($inv->trang_thai == 'Đã thanh toán') $st_c = 'bg-green-100 text-green-700 border-green-200';
                            elseif ($inv->trang_thai == 'Chờ thanh toán') $st_c = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                            elseif ($inv->trang_thai == 'Hoàn tiền') $st_c = 'bg-purple-100 text-purple-700 border-purple-200';
                            else $st_c = 'bg-red-100 text-red-700 border-red-200';
                        ?>
                        <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase border <?php echo $st_c; ?>">
                            <?php echo esc_html($inv->trang_thai); ?>
                        </span>
                    </td>

                    <td class="px-6 py-4 border-l border-gray-50">
                        <?php 
                            $tt_giao = $inv->trang_thai_giao ?? 'Chờ giao';
                            if ($tt_giao == 'Đã giao' || $tt_giao == 'Tại quầy') $g_c = 'text-green-600 bg-green-50';
                            elseif ($tt_giao == 'Đang giao') $g_c = 'text-blue-600 bg-blue-50';
                            elseif ($tt_giao == 'Đã hủy') $g_c = 'text-red-400 bg-red-50';
                            else $g_c = 'text-gray-400 bg-gray-100';

                            $text = ($tt_giao == 'Tại quầy') ? 'Nhận tại quầy' : $tt_giao;
                        ?>
                        <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase <?php echo $g_c; ?>">
                            <i class="fa-solid <?php echo ($tt_giao == 'Tại quầy') ? 'fa-shop' : 'fa-truck-fast'; ?> mr-1"></i> 
                            <?php echo esc_html($text); ?>
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="?page=qln-invoices&action=view&id=<?php echo $inv->id; ?>" class="text-gray-400 hover:text-gray-800 transition"><i class="fa-solid fa-eye text-lg"></i></a>
                            
                            <?php if ($inv->trang_thai == 'Chờ thanh toán'): ?>
                                <a href="?page=qln-invoices&action=mark_paid&id=<?php echo $inv->id; ?>" onclick="return confirm('Xác nhận ĐÃ THU TIỀN?')" class="text-green-600 hover:text-green-800"><i class="fa-solid fa-money-check-dollar text-lg"></i></a>
                                
                                <?php if ($inv->trang_thai_giao === 'Chờ giao' || empty($inv->trang_thai_giao)): ?>
                                    <a href="?page=qln-invoices&action=edit&id=<?php echo $inv->id; ?>" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square text-lg"></i></a>
                                    <a href="?page=qln-invoices&action=cancel&id=<?php echo $inv->id; ?>" onclick="return confirm('Bạn có chắc muốn HỦY?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can text-lg"></i></a>
                                
                                <?php elseif ($inv->trang_thai_giao === 'Tại quầy'): ?>
                                    <span class="text-gray-200 cursor-not-allowed" title="Hàng đã bốc, không thể sửa số lượng! Hãy Hủy và làm lại đơn mới nếu cần."><i class="fa-solid fa-pen-to-square text-lg"></i></span>
                                    <a href="?page=qln-invoices&action=cancel&id=<?php echo $inv->id; ?>" onclick="return confirm('Khách không lấy nữa? Hệ thống sẽ HỦY và cộng lại hàng vào kho.')" class="text-red-500 hover:text-red-700" title="Hủy và hoàn kho"><i class="fa-solid fa-trash-can text-lg"></i></a>
                                
                                <?php else: ?>
                                    <span class="text-gray-200 cursor-not-allowed" title="Hàng đang trên xe, không thể can thiệp!"><i class="fa-solid fa-lock text-lg"></i></span>
                                <?php endif; ?>
                            
                            <?php elseif ($inv->trang_thai == 'Đã thanh toán'): ?>
                                <a href="?page=qln-invoices&action=refund&id=<?php echo $inv->id; ?>" onclick="return confirm('Xác nhận hoàn tiền? Hàng sẽ được cộng lại vào kho.')" class="text-purple-600 hover:text-purple-800"><i class="fa-solid fa-rotate-left text-lg"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>