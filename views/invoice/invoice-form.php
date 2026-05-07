<?php if (!defined('ABSPATH')) exit; 
$isEdit = isset($invoice) && $invoice != null;
$actionUrl = $isEdit ? "?page=qln-invoices&action=update&id={$invoice->id}" : "?page=qln-invoices&action=store";
?>
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?php echo $isEdit ? 'Cập nhật Hóa Đơn' : 'Tạo Hóa Đơn Chi Tiết'; ?></h2>
        <a href="?page=qln-invoices" class="text-gray-500 hover:text-gray-800 underline">Quay lại</a>
    </div>

    <form action="<?php echo $actionUrl; ?>" method="POST" id="invoiceForm">
        <!-- Thông tin chung -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm mb-6 max-w-2xl">
            <h3 class="font-bold text-lg mb-4">Thông tin hóa đơn</h3>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Mã hóa đơn</label>
                    <input type="text" name="ma_hd" value="<?php echo $isEdit ? esc_attr($invoice->ma_hd) : ''; ?>" class="w-full px-4 py-2 border rounded-lg" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Khách hàng</label>
                    <select name="khach_hang_id" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="">-- Chọn khách hàng --</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo ($isEdit && $invoice->khach_hang_id == $c->id) ? 'selected' : ''; ?>><?php echo esc_html($c->ten_kh . ' (' . $c->ma_kh . ')'); ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Trạng thái khởi tạo</label>
                    <select name="trang_thai" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-green-600 outline-none font-bold">
                        <option value="Chờ thanh toán" <?php echo ($isEdit && $invoice->trang_thai == 'Chờ thanh toán') ? 'selected' : ''; ?>>Chờ thanh toán (Ghi nợ)</option>
                        <option value="Đã thanh toán" <?php echo ($isEdit && $invoice->trang_thai == 'Đã thanh toán') ? 'selected' : ''; ?>>Đã thanh toán (Thu tiền ngay)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-black text-gray-700 mb-2 uppercase tracking-wider">Hình thức nhận hàng</label>
                    <select name="trang_thai_giao" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-green-600 outline-none font-bold">
                        <option value="Chờ giao" <?php echo ($isEdit && $invoice->trang_thai_giao == 'Chờ giao') ? 'selected' : ''; ?>>
                            🚚 Công ty đi giao (Qua bộ phận Xuất Kho)
                        </option>
                        <option value="Tại quầy" <?php echo ($isEdit && $invoice->trang_thai_giao == 'Tại quầy') ? 'selected' : ''; ?>>
                            🏬 Nhận hàng tại quầy (Trừ kho ngay lập tức)
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Chi tiết hóa đơn -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-lg mb-4">Chi tiết sản phẩm</h3>
            <table class="w-full text-sm border-collapse" id="itemsTable">
                <thead><tr class="border-b bg-gray-50">
                    <th class="p-2 text-left">Sản phẩm</th>
                    <th class="p-2 text-left">Số lượng</th>
                    <th class="p-2 text-left">Tồn kho</th>
                    <th class="p-2 text-left">Đơn giá</th>
                    <th class="p-2 text-left">Thành tiền</th>
                    <th class="p-2 w-10"></th>
                </tr></thead>
                <tbody id="itemsBody">
                    <?php if ($isEdit && !empty($details)): ?>
                        <?php foreach ($details as $index => $item): ?>
                        <tr data-row-index="<?php echo $index; ?>">
                            <td class="p-2"><select name="products[<?php echo $index; ?>][san_pham_id]" class="product-select w-full border rounded px-2 py-1" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->gia_ban; ?>" <?php echo ($item->san_pham_id == $p->id) ? 'selected' : ''; ?>><?php echo esc_html($p->ma_sp . ' - ' . $p->ten_sp); ?></option>
                                <?php endforeach; ?>
                            </select></td>
                            <td class="p-2"><input type="number" name="products[<?php echo $index; ?>][so_luong]" class="qty w-24 border rounded px-2 py-1" value="<?php echo $item->so_luong; ?>" min="1" required></td>
                            <td class="p-2 stock-cell"><?php echo $item->so_luong; // tạm, sẽ update bằng AJAX ?></td>
                            <td class="p-2 price"><?php echo number_format($item->don_gia); ?>đ</td>
                            <td class="p-2 total"><?php echo number_format($item->thanh_tien); ?>đ</td>
                            <td class="p-2 text-center"><button type="button" class="removeRow text-red-500 hover:text-red-700">✖</button></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr data-row-index="0">
                            <td class="p-2"><select name="products[0][san_pham_id]" class="product-select w-full border rounded px-2 py-1" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->gia_ban; ?>"><?php echo esc_html($p->ma_sp . ' - ' . $p->ten_sp); ?></option>
                                <?php endforeach; ?>
                            </select></td>
                            <td class="p-2"><input type="number" name="products[0][so_luong]" class="qty w-24 border rounded px-2 py-1" value="1" min="1" required></td>
                            <td class="p-2 stock-cell">--</td>
                            <td class="p-2 price">0đ</td>
                            <td class="p-2 total">0đ</td>
                            <td class="p-2 text-center"><button type="button" class="removeRow text-red-500 hover:text-red-700">✖</button></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="5" class="p-2"><button type="button" id="addRow" class="bg-green-100 text-green-700 px-3 py-1 rounded text-sm">+ Thêm sản phẩm</button></td></tr>
                    <tr class="border-t bg-gray-50"><td colspan="4" class="p-2 text-right font-bold">Tổng cộng:</td><td class="p-2 font-bold text-red-600" id="grandTotal">0đ</td><td></td></tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-2 px-6 rounded-lg transition">Lưu hóa đơn</button>
            <a href="?page=qln-invoices" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-6 rounded-lg">Thoát</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

    // Lấy tồn kho từ AJAX
    async function fetchStock(productId) {
        if (!productId) return 0;
        const formData = new FormData();
        formData.append('action', 'qln_get_product_stock');
        formData.append('product_id', productId);
        try {
            const res = await fetch(ajaxurl, { method: 'POST', body: formData });
            const text = await res.text();
            return parseInt(text) || 0;
        } catch(e) {
            console.error('Fetch stock error', e);
            return 0;
        }
    }

    // Cập nhật tồn kho và kiểm tra số lượng
    async function updateStockCell(row) {
        const select = row.querySelector('.product-select');
        const stockCell = row.querySelector('.stock-cell');
        const qtyInput = row.querySelector('.qty');
        if (!select) return;
        const productId = select.value;
        if (!productId) {
            stockCell.innerText = '--';
            qtyInput.disabled = false;
            calculateRow(row);
            updateGrandTotal();
            return;
        }
        const stock = await fetchStock(productId);
        stockCell.innerText = stock;
        stockCell.style.color = stock <= 0 ? 'red' : 'green';
        if (stock <= 0) {
            qtyInput.disabled = true;
            qtyInput.value = 0;
            alert('Sản phẩm này đã hết hàng!');
        } else {
            qtyInput.disabled = false;
            let currentQty = parseInt(qtyInput.value) || 1;
            if (currentQty > stock) {
                qtyInput.style.border = '1px solid red';
                stockCell.innerText = `Tồn: ${stock} (không đủ)`;
                stockCell.style.color = 'red';
                alert(`Số lượng vượt quá tồn kho (${stock} cái). Đã tự động giảm xuống ${stock}.`);
                qtyInput.value = stock;
                currentQty = stock;
            } else {
                qtyInput.style.border = '';
            }
        }
        calculateRow(row);
        updateGrandTotal();
    }

    function calculateRow(row) {
        const select = row.querySelector('.product-select');
        const qty = parseInt(row.querySelector('.qty').value) || 0;
        let price = 0;
        if (select.selectedIndex > 0) {
            const option = select.options[select.selectedIndex];
            price = parseInt(option.getAttribute('data-price')) || 0;
        }
        const priceCell = row.querySelector('.price');
        const totalCell = row.querySelector('.total');
        priceCell.innerText = price.toLocaleString() + 'đ';
        const total = price * qty;
        totalCell.innerText = total.toLocaleString() + 'đ';
        return total;
    }

    function updateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const totalCell = row.querySelector('.total');
            if (totalCell) {
                let totalText = totalCell.innerText.replace(/[^\d]/g, '');
                grand += parseInt(totalText) || 0;
            }
        });
        document.getElementById('grandTotal').innerText = grand.toLocaleString() + 'đ';
    }

    function reindexRows() {
        const rows = document.querySelectorAll('#itemsBody tr');
        rows.forEach((row, idx) => {
            const selects = row.querySelectorAll('[name*="products["]');
            selects.forEach(field => {
                let name = field.getAttribute('name');
                name = name.replace(/products\[\d+\]/, `products[${idx}]`);
                field.setAttribute('name', name);
            });
            row.setAttribute('data-row-index', idx);
        });
    }

    async function attachRowEvents(row) {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.qty');
        const removeBtn = row.querySelector('.removeRow');
        if (select) {
            select.removeEventListener('change', selectChangeHandler);
            select.addEventListener('change', selectChangeHandler);
            async function selectChangeHandler() {
                const currentValue = this.value;
                if (!currentValue) {
                    await updateStockCell(row);
                    return;
                }
                // Kiểm tra trùng sản phẩm
                let isDuplicate = false;
                document.querySelectorAll('#itemsBody .product-select').forEach(otherSelect => {
                    if (otherSelect !== this && otherSelect.value === currentValue) {
                        isDuplicate = true;
                    }
                });
                if (isDuplicate) {
                    alert('Sản phẩm này đã được thêm vào hóa đơn! Vui lòng chọn sản phẩm khác hoặc gộp số lượng.');
                    this.value = '';
                    // Xóa nội dung các ô liên quan
                    row.querySelector('.stock-cell').innerText = '--';
                    row.querySelector('.price').innerText = '0đ';
                    row.querySelector('.total').innerText = '0đ';
                    updateGrandTotal();
                    return;
                }
                await updateStockCell(row);
            }
        }
        if (qty) {
            qty.removeEventListener('change', qtyChangeHandler);
            qty.addEventListener('change', qtyChangeHandler);
            function qtyChangeHandler() {
                const stockCell = row.querySelector('.stock-cell');
                const stock = parseInt(stockCell.innerText) || 0;
                let val = parseInt(this.value) || 1;
                if (val > stock && stock > 0) {
                    alert(`Số lượng vượt quá tồn kho (${stock} cái).`);
                    this.value = stock;
                    val = stock;
                }
                calculateRow(row);
                updateGrandTotal();
            }
        }
        if (removeBtn) {
            removeBtn.removeEventListener('click', removeHandler);
            removeBtn.addEventListener('click', removeHandler);
            async function removeHandler() {
                const tbody = document.getElementById('itemsBody');
                if (tbody.children.length > 1) {
                    row.remove();
                    reindexRows();
                    updateGrandTotal();
                } else {
                    // Xóa nội dung dòng đầu
                    const select = row.querySelector('.product-select');
                    const qty = row.querySelector('.qty');
                    if (select) select.selectedIndex = 0;
                    if (qty) qty.value = 1;
                    await updateStockCell(row);
                }
            }
        }
    }

    // Khởi tạo tất cả các dòng hiện tại
    async function initAllRows() {
        const rows = document.querySelectorAll('#itemsBody tr');
        for (let row of rows) {
            await attachRowEvents(row);
            await updateStockCell(row);
        }
    }

    // Thêm dòng mới
    document.getElementById('addRow').addEventListener('click', async function() {
        const tbody = document.getElementById('itemsBody');
        const newIndex = tbody.children.length;
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-row-index', newIndex);
        newRow.innerHTML = `
            <td class="p-2"><select name="products[${newIndex}][san_pham_id]" class="product-select w-full border rounded px-2 py-1" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $p): ?>
                <option value="<?php echo $p->id; ?>" data-price="<?php echo $p->gia_ban; ?>"><?php echo esc_html($p->ma_sp . ' - ' . $p->ten_sp); ?></option>
                <?php endforeach; ?>
            </select></td>
            <td class="p-2"><input type="number" name="products[${newIndex}][so_luong]" class="qty w-24 border rounded px-2 py-1" value="1" min="1" required></td>
            <td class="p-2 stock-cell">--</td>
            <td class="p-2 price">0đ</td>
            <td class="p-2 total">0đ</td>
            <td class="p-2 text-center"><button type="button" class="removeRow text-red-500 hover:text-red-700">✖</button></td>
        `;
        tbody.appendChild(newRow);
        await attachRowEvents(newRow);
        await updateStockCell(newRow);
        reindexRows();
        updateGrandTotal();
    });

    // Chạy khởi tạo
    initAllRows();
});
</script>