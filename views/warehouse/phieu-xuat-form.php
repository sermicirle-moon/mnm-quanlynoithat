<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 max-w-5xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="?page=qln-xuat-kho" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-[#0a5c36] transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Lập Phiếu Xuất Gộp</h2>
            <p class="text-sm text-gray-500">Tạo chuyến xe và gom các hóa đơn cần giao.</p>
        </div>
    </div>

    <?php if(isset($_SESSION['qln_error'])): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 font-bold text-sm">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?>
        </div>
    <?php endif; ?>

    <form action="?page=qln-xuat-kho&action=store" method="POST" id="formPhieuXuat">
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="col-span-1 space-y-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Mã Phiếu Xuất</label>
                    <input type="text" name="ma_px" value="<?php echo esc_attr($ma_px_du_kien); ?>" class="w-full bg-gray-50 border-none rounded-lg font-bold text-green-700 outline-none" readonly>
                </div>
                
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Đơn vị vận chuyển</label>
                    <select name="nvc_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-green-600 font-medium" required>
                        <option value="">-- Chọn tài xế/xe --</option>
                        <?php foreach($nhaVanChuyens as $nvc): ?>
                            <option value="<?php echo $nvc['id']; ?>"><?php echo esc_html($nvc['ten_nvc'] . " - " . $nvc['bien_so_xe']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Phí vận chuyển (Dự kiến)</label>
                    <input type="number" name="phi_van_chuyen" value="0" min="0" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-green-600 font-bold">
                </div>
            </div>

            <div class="col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col gap-5">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Khách hàng nhận hàng</label>
                    <select name="khach_hang_id" id="selectKhachHang" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold outline-none focus:border-green-600" required>
                        <option value="">-- Bấm để chọn Khách hàng --</option>
                        <?php foreach($khachHangs as $kh): ?>
                            <option value="<?php echo $kh['id']; ?>"><?php echo esc_html($kh['ten_kh'] . " (" . $kh['sdt'] . ")"); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-1 flex flex-col">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-wider mb-2">Địa chỉ giao thực tế</label>
                    <textarea name="dia_chi_giao_hang" class="flex-1 w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-green-600 resize-none font-medium" placeholder="Nhập địa chỉ chính xác để tài xế tìm đường..." required></textarea>
                </div>
            </div>
        </div>

        <div id="sectionGomDon" class="hidden">
            <h3 class="font-black text-gray-400 text-xs uppercase mb-4 tracking-widest flex items-center gap-2"><i class="fa-solid fa-layer-group"></i> Chọn các hóa đơn để bốc lên xe</h3>
            <div class="grid grid-cols-2 gap-4" id="listInvoicesPending">
                </div>
        </div>
        
        <div id="emptyInvoiceMsg" class="bg-gray-100 p-10 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-medium mb-8">
            <i class="fa-solid fa-inbox text-3xl mb-3 text-gray-300"></i><br>
            Vui lòng chọn Khách hàng ở trên để hệ thống quét các hóa đơn đang chờ giao.
        </div>

        <div class="text-right">
            <button type="submit" class="bg-[#0a5c36] hover:bg-green-800 text-white px-10 py-3.5 rounded-xl font-bold shadow-lg transition text-lg">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu & Khóa Đơn Hàng
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectKH = document.getElementById('selectKhachHang');
    const container = document.getElementById('listInvoicesPending');
    const section = document.getElementById('sectionGomDon');
    const msg = document.getElementById('emptyInvoiceMsg');
    const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    selectKH.addEventListener('change', function() {
        const khId = this.value;
        if (!khId) {
            section.classList.add('hidden');
            msg.classList.remove('hidden');
            return;
        }

        // Hiện loading cho ngầu
        msg.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-2xl text-green-600 mb-2"></i><br><span class="text-gray-500">Đang quét kho...</span>';
        msg.classList.remove('hidden');
        section.classList.add('hidden');

        // Gọi AJAX lấy hóa đơn (Đã được định nghĩa trong file quanly-noithat.php)
        const formData = new FormData();
        formData.append('action', 'qln_get_pending_invoices');
        formData.append('khach_hang_id', khId);

        fetch(ajaxurl, { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';
            if (data.success && data.data.length > 0) {
                section.classList.remove('hidden');
                msg.classList.add('hidden');
                
                // Render từng hóa đơn thành checkbox card
                data.data.forEach(hd => {
                    const tien = parseInt(hd.tong_tien).toLocaleString('vi-VN') + ' đ';
                    container.innerHTML += `
                        <label class="bg-white p-5 rounded-2xl border-2 border-gray-100 cursor-pointer hover:border-green-600 transition flex items-center gap-5 shadow-sm">
                            <input type="checkbox" name="hoa_don_ids[]" value="${hd.id}" class="w-6 h-6 rounded text-green-700 focus:ring-green-600 cursor-pointer">
                            <div class="flex-1">
                                <p class="font-black text-gray-800 text-lg">Mã HĐ: #${hd.ma_hd}</p>
                                <p class="text-xs text-gray-400 font-medium mt-1"><i class="fa-regular fa-clock"></i> Tạo lúc: ${hd.ngay_tao}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Giá trị đơn</p>
                                <p class="font-black text-green-600 text-lg">${tien}</p>
                            </div>
                        </label>
                    `;
                });
            } else {
                section.classList.add('hidden');
                msg.innerHTML = '<i class="fa-regular fa-circle-check text-3xl mb-3 text-green-500"></i><br>Khách hàng này hiện không còn hóa đơn nào nợ hàng.';
                msg.classList.remove('hidden');
            }
        }).catch(err => {
            msg.innerHTML = '<span class="text-red-500">Lỗi kết nối máy chủ! Vui lòng thử lại.</span>';
        });
    });
});
</script>