<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="?page=qln-xuat-kho" class="text-gray-500 hover:text-gray-800 font-bold flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Về danh sách
        </a>
        <button onclick="window.print()" class="bg-gray-800 hover:bg-black text-white px-5 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> In Lệnh Giao Hàng
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200" id="print-area">
        <div class="bg-[#0a5c36] p-8 text-white flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-black uppercase tracking-widest text-green-50">Lệnh Xuất Kho</h2>
                <p class="text-green-200 font-bold mt-1 text-lg">Mã số: <?php echo esc_html($phieuXuat['ma_px']); ?></p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-green-300 uppercase tracking-wider mb-1">Trạng thái chuyến xe</p>
                <span class="px-4 py-1.5 bg-white text-green-800 rounded-lg font-black text-sm uppercase shadow-sm">
                    <?php echo esc_html($phieuXuat['trang_thai']); ?>
                </span>
            </div>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-2 gap-10 mb-10 pb-10 border-b border-gray-100">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-user"></i></div>
                        <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Giao đến</h4>
                    </div>
                    <p class="font-black text-xl text-gray-800"><?php echo esc_html($khachHang['ten_kh']); ?></p>
                    <p class="text-gray-600 font-medium mt-1"><i class="fa-solid fa-phone mr-1 text-gray-400"></i> <?php echo esc_html($khachHang['sdt']); ?></p>
                    <p class="mt-2 text-sm text-gray-500 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <i class="fa-solid fa-location-dot mr-1 text-gray-400"></i> <?php echo esc_html($phieuXuat['dia_chi_giao_hang']); ?>
                    </p>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-3 justify-end">
                        <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Đơn vị vận chuyển</h4>
                        <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-truck"></i></div>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-lg text-gray-800"><?php echo esc_html($nvc['ten_nvc']); ?></p>
                        <p class="text-gray-600 font-medium mt-1">Biển số: <span class="text-gray-800 font-bold"><?php echo esc_html($nvc['bien_so_xe']); ?></span></p>
                        <p class="text-gray-600 font-medium mt-1">Tài xế: <?php echo esc_html($nvc['sdt_tai_xe']); ?></p>
                        <p class="text-gray-600 font-medium mt-1">Phí dự kiến: <span class="text-red-600 font-bold"><?php echo number_format($phieuXuat['phi_van_chuyen']); ?> đ</span></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-end mb-4">
                <h4 class="text-[11px] font-black text-gray-400 uppercase tracking-widest"><i class="fa-solid fa-box-open mr-1"></i> Bảng kê hàng hóa bốc lên xe</h4>
                <span class="text-xs text-gray-400 font-medium italic">Gom từ <?php echo count($hoaDons); ?> hóa đơn</span>
            </div>
            
            <div class="space-y-4">
                <?php foreach ($hoaDons as $hd): ?>
                <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-white border border-gray-200 flex items-center justify-center text-gray-400 text-xs"><i class="fa-solid fa-file-invoice"></i></span>
                            <span class="font-black text-gray-800 text-sm">Hóa đơn: #<?php echo esc_html($hd['ma_hd']); ?></span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-gray-500 bg-white px-2 py-1 rounded border"><?php echo date('d/m/Y', strtotime($hd['ngay_tao'])); ?></span>
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="p-6 bg-white hidden print:block border-t border-gray-100"> 
                        <table class="w-full text-sm text-left">
                            <thead class="text-gray-400 text-xs uppercase font-bold border-b border-gray-100">
                                <tr>
                                    <th class="pb-3 w-16">Mã SP</th>
                                    <th class="pb-3">Tên sản phẩm (Hàng hóa)</th>
                                    <th class="pb-3 text-center">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php 
                                $details = $wpdb->get_results($wpdb->prepare("SELECT sp.ma_sp, sp.ten_sp, ct.so_luong FROM {$wpdb->prefix}qln_hoa_don_chi_tiet ct JOIN {$wpdb->prefix}qln_san_pham sp ON ct.san_pham_id = sp.id WHERE ct.hoa_don_id = %d", $hd['id']));
                                foreach ($details as $row):
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 font-mono text-xs text-gray-400"><?php echo esc_html($row->ma_sp); ?></td>
                                    <td class="py-3 font-bold text-gray-700"><?php echo esc_html($row->ten_sp); ?></td>
                                    <td class="py-3 text-center font-black text-green-700 text-lg"><?php echo $row->so_luong; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 grid grid-cols-3 gap-4 text-center pb-8">
                <div>
                    <p class="font-bold text-gray-800 text-sm">Người Lập Phiếu</p>
                    <p class="text-xs text-gray-400 mt-1 italic">(Ký, ghi rõ họ tên)</p>
                    <div class="h-20"></div>
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Thủ Kho Xuất Hàng</p>
                    <p class="text-xs text-gray-400 mt-1 italic">(Ký, ghi rõ họ tên)</p>
                    <div class="h-20"></div>
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Tài Xế Nhận Hàng</p>
                    <p class="text-xs text-gray-400 mt-1 italic">(Ký, ghi rõ họ tên)</p>
                    <div class="h-20"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
    .hidden.print\\:block { display: block !important; }
    .fa-chevron-down { display: none; }
}
</style>