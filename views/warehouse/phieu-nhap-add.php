<?php 
if (!defined('ABSPATH')) exit; 
// Biến cờ (flag) để nhận biết đang ở chế độ Sửa hay Thêm mới
$is_edit = isset($phieuNhap) && !empty($phieuNhap);
?>
<div class="p-8 max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="?page=qln-nhap-hang" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-[#0a5c36] transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <!-- Tiêu đề thay đổi linh hoạt -->
            <h2 class="text-2xl font-bold text-gray-800">
                <?php echo $is_edit ? 'Xem / Sửa Phiếu Nhập' : 'Tạo Phiếu Nhập Kho Mới'; ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                <?php echo $is_edit ? 'Sửa đổi thông tin phiếu đang ở trạng thái Chờ xử lý.' : 'Nhập nguyên vật liệu, nội thất từ nhà cung cấp vào kho'; ?>
            </p>
        </div>
    </div>

    <!-- Action form cũng thay đổi linh hoạt -->
    <form method="POST" action="?page=<?php echo $is_edit ? 'qln-nhap-hang-update' : 'qln-nhap-hang-store'; ?>" id="frm-nhap-kho" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
        
        <input type="hidden" name="action_qln" value="<?php echo $is_edit ? 'update_phieu_nhap' : 'store_phieu_nhap'; ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $phieuNhap['id']; ?>">
        <?php endif; ?>

        <h3 class="text-lg font-bold text-[#0a5c36] border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i> 1. Thông tin phiếu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mã phiếu</label>
                <input type="text" name="ma_pn" value="<?php echo $is_edit ? esc_attr($phieuNhap['ma_pn']) : esc_attr($ma_pn_du_kien); ?>" class="w-full px-4 py-3 bg-gray-200 border border-gray-200 rounded-xl font-bold text-gray-500 cursor-not-allowed outline-none" readonly>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nhà cung cấp <span class="text-red-500">*</span></label>
                <select name="ncc_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-600/20 focus:border-green-600 transition" required>
                    <option value="">-- Lựa chọn nhà cung cấp --</option>
                    <?php foreach ($nhaCungCaps as $ncc): ?>
                        <option value="<?php echo $ncc['id']; ?>" <?php if($is_edit) selected($phieuNhap['ncc_id'], $ncc['id']); ?>>
                            <?php echo esc_html($ncc['ten_ncc']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ngày nhập kho <span class="text-red-500">*</span></label>
                <input type="date" name="ngay_nhap" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 transition" value="<?php echo $is_edit ? date('Y-m-d', strtotime($phieuNhap['ngay_nhap'])) : date('Y-m-d'); ?>" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ghi chú</label>
                <input type="text" name="ghi_chu" value="<?php echo $is_edit ? esc_attr($phieuNhap['ghi_chu']) : ''; ?>" placeholder="Nhập ghi chú..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 transition">
            </div>
        </div>

        <!-- CHI TIẾT SẢN PHẨM -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#0a5c36] flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i> 2. Chi tiết mặt hàng
            </h3>
            <button type="button" id="btn-add-row" class="text-sm font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-lg hover:bg-blue-100 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Thêm dòng mặt hàng
            </button>
        </div>

        <div class="flex gap-4 px-4 py-2 bg-gray-800 text-white rounded-t-xl text-xs font-bold uppercase tracking-wider">
            <div class="flex-1">Sản phẩm <span class="text-red-400">*</span></div>
            <div class="w-32 text-center">Số lượng <span class="text-red-400">*</span></div>
            <div class="w-48 text-right">Đơn giá nhập (VNĐ) <span class="text-red-400">*</span></div>
            <div class="w-48 text-right">Thành tiền</div>
            <div class="w-12 text-center">Xóa</div>
        </div>

        <div id="product-list" class="border-x border-b border-gray-200 rounded-b-xl overflow-hidden mb-6">
            
            <?php if ($is_edit && !empty($chiTiet)): ?>
                <!-- NẾU LÀ SỬA: Lặp qua các chi tiết cũ -->
                <?php foreach ($chiTiet as $ct): ?>
                <div class="product-row flex gap-4 items-center bg-white p-4 border-b border-gray-100 transition hover:bg-gray-50">
                    <div class="flex-1">
                        <select name="san_pham_id[]" class="sp-select w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:border-green-600 focus:bg-white transition" required>
                            <option value="" data-price="0">-- Click để chọn sản phẩm --</option>
                            <?php foreach ($sanPhams as $sp): ?>
                                <option value="<?php echo $sp['id']; ?>" data-price="<?php echo isset($sp['gia_nhap']) ? $sp['gia_nhap'] : 0; ?>" <?php selected($ct['san_pham_id'], $sp['id']); ?>>
                                    [<?php echo esc_html($sp['ma_sp']); ?>] <?php echo esc_html($sp['ten_sp']); ?> (Tồn: <?php echo $sp['so_luong_ton']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="so_luong[]" class="qty-input w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-center font-bold focus:border-green-600" required min="1" value="<?php echo $ct['so_luong']; ?>">
                    </div>
                    <div class="w-48">
                        <input type="number" name="gia_nhap[]" class="price-input w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-right font-bold focus:border-green-600" required min="0" value="<?php echo $ct['gia_nhap']; ?>">
                    </div>
                    <div class="w-48 text-right font-bold text-gray-800 text-lg line-total">
                        <?php echo number_format($ct['thanh_tien']); ?> đ
                    </div>
                    <div class="w-12 text-center">
                        <button type="button" class="btn-remove-row text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Xóa dòng">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <!-- NẾU LÀ THÊM MỚI: In ra 1 dòng trống -->
                <div class="product-row flex gap-4 items-center bg-white p-4 border-b border-gray-100 transition hover:bg-gray-50">
                    <div class="flex-1">
                        <select name="san_pham_id[]" class="sp-select w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:border-green-600 focus:bg-white transition" required>
                            <option value="" data-price="0">-- Click để chọn sản phẩm --</option>
                            <?php foreach ($sanPhams as $sp): ?>
                                <option value="<?php echo $sp['id']; ?>" data-price="<?php echo isset($sp['gia_nhap']) ? $sp['gia_nhap'] : 0; ?>">
                                    [<?php echo esc_html($sp['ma_sp']); ?>] <?php echo esc_html($sp['ten_sp']); ?> (Tồn: <?php echo $sp['so_luong_ton']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="so_luong[]" class="qty-input w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-center font-bold focus:border-green-600" required min="1" value="1">
                    </div>
                    <div class="w-48">
                        <input type="number" name="gia_nhap[]" class="price-input w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-right font-bold focus:border-green-600" required min="0" placeholder="0">
                    </div>
                    <div class="w-48 text-right font-bold text-gray-800 text-lg line-total">0 đ</div>
                    <div class="w-12 text-center">
                        <button type="button" class="btn-remove-row text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Xóa dòng">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex justify-end items-center bg-green-50 p-6 rounded-xl border border-green-100 mb-8">
            <div class="text-right">
                <p class="text-sm text-green-700 font-bold uppercase mb-1">Tổng giá trị phiếu nhập</p>
                <p id="grand-total" class="text-4xl font-black text-green-800">
                    <?php echo $is_edit ? number_format($phieuNhap['tong_tien']) : '0'; ?> VNĐ
                </p>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" id="btn-submit" class="bg-[#0a5c36] hover:bg-green-800 text-white px-10 py-3.5 rounded-xl font-bold text-lg shadow-lg transition flex items-center gap-3">
                <i class="fa-solid <?php echo $is_edit ? 'fa-floppy-disk' : 'fa-check-double'; ?>"></i> 
                <?php echo $is_edit ? 'Lưu Cập Nhật' : 'Lưu & Tạo Nháp'; ?>
            </button>
        </div>
    </form>
</div>

<!-- Copy y nguyên đoạn JS của file add/edit cũ vào đây -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('product-list');
    const grandTotalEl = document.getElementById('grand-total');
    
    function formatMoney(amount) { return new Intl.NumberFormat('vi-VN').format(amount); }

    function calculateTotals() {
        let grandTotal = 0;
        const rows = productList.querySelectorAll('.product-row');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const lineTotal = qty * price;
            row.querySelector('.line-total').innerText = formatMoney(lineTotal) + ' đ';
            grandTotal += lineTotal;
        });
        grandTotalEl.innerText = formatMoney(grandTotal) + ' VNĐ';
    }

    document.getElementById('btn-add-row').addEventListener('click', function() {
        const firstRow = productList.querySelector('.product-row');
        const newRow = firstRow.cloneNode(true); 
        newRow.querySelector('.sp-select').value = '';
        newRow.querySelector('.qty-input').value = '1'; 
        newRow.querySelector('.price-input').value = '';  
        newRow.querySelector('.line-total').innerText = '0 đ';
        newRow.classList.add('bg-yellow-50');
        setTimeout(() => newRow.classList.remove('bg-yellow-50'), 1000);
        productList.appendChild(newRow);
        calculateTotals();
    });

    productList.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) calculateTotals();
    });

    productList.addEventListener('click', function(e) {
        const btnRemove = e.target.closest('.btn-remove-row');
        if (btnRemove) {
            const rows = productList.querySelectorAll('.product-row');
            if (rows.length === 1) { alert("Phiếu nhập phải có ít nhất 1 mặt hàng!"); return; }
            btnRemove.closest('.product-row').remove();
            calculateTotals(); 
        }
    });

    productList.addEventListener('change', function(e) {
        if (e.target.classList.contains('sp-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            e.target.closest('.product-row').querySelector('.price-input').value = selectedOption.getAttribute('data-price') || 0;
            calculateTotals();
        }
    });

    document.getElementById('frm-nhap-kho').addEventListener('submit', function(e) {
        let hasError = false;
        productList.querySelectorAll('.price-input').forEach(input => {
            if (parseFloat(input.value) <= 0 || input.value === '') hasError = true;
        });
        if (hasError) {
            e.preventDefault();
            alert("Vui lòng nhập đơn giá lớn hơn 0 cho tất cả sản phẩm!");
        }
    });
});
</script>