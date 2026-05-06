<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8 max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="?page=qln-nhap-hang" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-[#0a5c36] hover:border-[#0a5c36] transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tạo Phiếu Nhập Kho Mới</h2>
            <p class="text-sm text-gray-500 mt-1">Nhập nguyên vật liệu, nội thất từ nhà cung cấp vào kho</p>
        </div>
    </div>

    <form method="POST" action="?page=qln-nhap-hang-store" id="frm-nhap-kho" class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative">
        <input type="hidden" name="action_qln" value="store_phieu_nhap">

        <!-- THÔNG TIN CHUNG (Đã sửa thành 3 cột để chứa ô Ngày nhập) -->
        <h3 class="text-lg font-bold text-[#0a5c36] border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-circle-info"></i> 1. Thông tin phiếu
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nhà cung cấp <span class="text-red-500">*</span></label>
                <select name="ncc_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-600/20 focus:border-green-600 transition" required>
                    <option value="">-- Lựa chọn nhà cung cấp --</option>
                    <?php foreach ($nhaCungCaps as $ncc): ?>
                        <option value="<?php echo $ncc['id']; ?>"><?php echo esc_html($ncc['ten_ncc']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ngày nhập kho <span class="text-red-500">*</span></label>
                <input type="date" name="ngay_nhap" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-600/20 focus:border-green-600 transition" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Ghi chú phiếu nhập</label>
                <input type="text" name="ghi_chu" placeholder="Ví dụ: Nhập lô hàng tháng 5..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-green-600/20 focus:border-green-600 transition">
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

        <!-- Tiêu đề cột của bảng chi tiết -->
        <div class="flex gap-4 px-4 py-2 bg-gray-800 text-white rounded-t-xl text-xs font-bold uppercase tracking-wider">
            <div class="flex-1">Sản phẩm <span class="text-red-400">*</span></div>
            <div class="w-32 text-center">Số lượng <span class="text-red-400">*</span></div>
            <div class="w-48 text-right">Đơn giá nhập (VNĐ) <span class="text-red-400">*</span></div>
            <div class="w-48 text-right">Thành tiền</div>
            <div class="w-12 text-center">Xóa</div>
        </div>

        <!-- Khu vực chứa các dòng nhập -->
        <div id="product-list" class="border-x border-b border-gray-200 rounded-b-xl overflow-hidden mb-6">
            <!-- Dòng mẫu (Row 1) -->
            <div class="product-row flex gap-4 items-center bg-white p-4 border-b border-gray-100 transition hover:bg-gray-50">
                <div class="flex-1">
                    <select name="san_pham_id[]" class="sp-select w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:border-green-600 focus:bg-white transition" required>
                        <option value="" data-price="0">-- Click để chọn sản phẩm --</option>
                        <?php foreach ($sanPhams as $sp): ?>
                            <option value="<?php echo $sp['id']; ?>" data-ten="<?php echo esc_html($sp['ten_sp']); ?>" data-price="<?php echo isset($sp['gia_nhap']) ? $sp['gia_nhap'] : 0; ?>">
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
                <div class="w-48 text-right font-bold text-gray-800 text-lg line-total">
                    0 đ
                </div>
                <div class="w-12 text-center">
                    <button type="button" class="btn-remove-row text-gray-300 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition" title="Xóa dòng">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TỔNG CỘNG TIỀN -->
        <div class="flex justify-end items-center bg-green-50 p-6 rounded-xl border border-green-100 mb-8">
            <div class="text-right">
                <p class="text-sm text-green-700 font-bold uppercase mb-1">Tổng giá trị phiếu nhập</p>
                <p id="grand-total" class="text-4xl font-black text-green-800">0 VNĐ</p>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" id="btn-submit" class="bg-[#0a5c36] hover:bg-green-800 text-white px-10 py-3.5 rounded-xl font-bold text-lg shadow-lg shadow-green-900/20 transition transform hover:-translate-y-0.5 flex items-center gap-3">
                <i class="fa-solid fa-check-double"></i> Lưu & Nhập Kho
            </button>
        </div>
    </form>
</div>

<!-- KỊCH BẢN JAVASCRIPT XỬ LÝ ĐỘNG -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productList = document.getElementById('product-list');
    const grandTotalEl = document.getElementById('grand-total');
    
    // 1. Hàm định dạng tiền tệ Việt Nam
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }

    // 2. Hàm tính toán lại toàn bộ tiền
    function calculateTotals() {
        let grandTotal = 0;
        const rows = productList.querySelectorAll('.product-row');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const lineTotal = qty * price;
            
            // Cập nhật text thành tiền của từng dòng
            row.querySelector('.line-total').innerText = formatMoney(lineTotal) + ' đ';
            grandTotal += lineTotal;
        });
        
        // Cập nhật tổng tiền to đùng ở dưới
        grandTotalEl.innerText = formatMoney(grandTotal) + ' VNĐ';
    }

    // 3. Sự kiện thêm dòng mới
    document.getElementById('btn-add-row').addEventListener('click', function() {
        const firstRow = productList.querySelector('.product-row');
        const newRow = firstRow.cloneNode(true); 
        
        // Xóa trắng dữ liệu dòng mới
        newRow.querySelector('.sp-select').value = '';
        newRow.querySelector('.qty-input').value = '1'; 
        newRow.querySelector('.price-input').value = '';  
        newRow.querySelector('.line-total').innerText = '0 đ';
        
        // Thêm hiệu ứng nháy nền để user chú ý
        newRow.classList.add('bg-yellow-50');
        setTimeout(() => newRow.classList.remove('bg-yellow-50'), 1000);

        productList.appendChild(newRow);
        calculateTotals();
    });

    // 4. Lắng nghe sự kiện Xóa và Nhập số liệu
    productList.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
            calculateTotals();
        }
    });

    productList.addEventListener('click', function(e) {
        // Nếu click vào nút Xóa (hoặc icon thùng rác bên trong nó)
        const btnRemove = e.target.closest('.btn-remove-row');
        if (btnRemove) {
            const rows = productList.querySelectorAll('.product-row');
            // Cấm xóa nếu chỉ còn 1 dòng
            if (rows.length === 1) {
                alert("Phiếu nhập phải có ít nhất 1 mặt hàng!");
                return;
            }
            btnRemove.closest('.product-row').remove();
            calculateTotals(); // Tính lại tổng tiền sau khi xóa
        }
    });

    // 5. BẮT SỰ KIỆN CHỌN SẢN PHẨM -> TỰ ĐỘNG ĐIỀN GIÁ NHẬP
    productList.addEventListener('change', function(e) {
        if (e.target.classList.contains('sp-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            
            // Tìm ô nhập giá cùng dòng và gán giá trị
            const row = e.target.closest('.product-row');
            row.querySelector('.price-input').value = price;
            
            // Tính toán lại tổng tiền
            calculateTotals();
        }
    });

    // 6. Chặn submit form nếu tổng tiền = 0
    document.getElementById('frm-nhap-kho').addEventListener('submit', function(e) {
        let hasError = false;
        productList.querySelectorAll('.price-input').forEach(input => {
            if (parseFloat(input.value) <= 0 || input.value === '') {
                hasError = true;
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert("Vui lòng nhập đơn giá lớn hơn 0 cho tất cả sản phẩm!");
        }
    });
});
</script>