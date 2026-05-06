<?php if (!defined('ABSPATH')) exit; ?>

<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
<?php endif; ?>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Hóa Đơn</h1>
            <p class="text-sm text-gray-500 mt-1">Theo dõi và quản lý các giao dịch bán hàng gỗ và nội thất.</p>
        </div>
        <a href="?page=qln-invoices&action=create" 
           class="bg-[#0a5c36] hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition">
            + Thêm Hóa Đơn
        </a>
    </div>

    <!-- 4 cards thống kê -->
    <?php
        // Tính toán thống kê từ $invoices
        $total_invoices = count($invoices);
        $total_revenue = 0;
        $pending_count = 0;
        $cancelled_count = 0;
        foreach ($invoices as $inv) {
            if ($inv->trang_thai !== 'Đã hủy') {
                $total_revenue += $inv->tong_tien;
            }
            if ($inv->trang_thai == 'Chờ thanh toán') $pending_count++;
            if ($inv->trang_thai == 'Đã hủy') $cancelled_count++;
        }
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">TỔNG HÓA ĐƠN</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $total_invoices; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">DOANH THU</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-1"><?php echo qln_format_compact_money($total_revenue); ?>đ</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">CHỜ THANH TOÁN</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-1"><?php echo $pending_count; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">ĐÃ HỦY</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-1"><?php echo $cancelled_count; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc nâng cao -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="hidden" name="page" value="qln-invoices">
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tìm kiếm</label>
                <input type="text" name="search" placeholder="Mã HĐ / Tên KH" value="<?php echo esc_attr($_GET['search'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="">-- Tất cả --</option>
                    <option value="Đang xử lý" <?php echo ($_GET['status'] ?? '') == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                    <option value="Chờ thanh toán" <?php echo ($_GET['status'] ?? '') == 'Chờ thanh toán' ? 'selected' : ''; ?>>Chờ thanh toán</option>
                    <option value="Đã thanh toán" <?php echo ($_GET['status'] ?? '') == 'Đã thanh toán' ? 'selected' : ''; ?>>Đã thanh toán</option>
                    <option value="Đã hủy" <?php echo ($_GET['status'] ?? '') == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Từ ngày</label>
                <input type="date" name="date_from" value="<?php echo esc_attr($_GET['date_from'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Đến ngày</label>
                <input type="date" name="date_to" value="<?php echo esc_attr($_GET['date_to'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tổng tiền (khoảng)</label>
                <div class="flex gap-2">
                    <input type="number" name="total_from" placeholder="Từ" value="<?php echo esc_attr($_GET['total_from'] ?? ''); ?>" class="w-1/2 px-3 py-2 border rounded-lg text-sm">
                    <input type="number" name="total_to" placeholder="Đến" value="<?php echo esc_attr($_GET['total_to'] ?? ''); ?>" class="w-1/2 px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>

            <div class="md:col-span-5 flex gap-2 justify-end mt-2">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm">Lọc dữ liệu</button>
                <a href="?page=qln-invoices" class="bg-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm">Xóa lọc</a>
            </div>
        </form>
    </div>

    <!-- Bảng hóa đơn -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">MÃ HÓA ĐƠN</th>
                        <th class="px-6 py-4">KHÁCH HÀNG</th>
                        <th class="px-6 py-4">NGÀY LẬP</th>
                        <th class="px-6 py-4">TỔNG TIỀN (VNĐ)</th>
                        <th class="px-6 py-4">TRẠNG THÁI</th>
                        <th class="px-6 py-4 text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Không tìm thấy hóa đơn nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-blue-600"><?php echo esc_html($inv->ma_hd); ?></td>
                            <td class="px-6 py-4 text-gray-800 font-medium"><?php echo esc_html($inv->ten_kh); ?></td>
                            <td class="px-6 py-4 text-gray-500"><?php echo date('d/m/Y', strtotime($inv->ngay_tao)); ?></td>
                            <td class="px-6 py-4 font-bold text-gray-800"><?php echo number_format($inv->tong_tien); ?>đ</td>
                            <td class="px-6 py-4">
                                <?php 
                                    $statusClass = 'bg-gray-100 text-gray-700';
                                    if ($inv->trang_thai == 'Đã thanh toán') $statusClass = 'bg-green-100 text-green-700';
                                    elseif ($inv->trang_thai == 'Chờ thanh toán') $statusClass = 'bg-yellow-100 text-yellow-700';
                                    elseif ($inv->trang_thai == 'Đang xử lý') $statusClass = 'bg-blue-100 text-blue-700';
                                    elseif ($inv->trang_thai == 'Đã hủy') $statusClass = 'bg-red-100 text-red-700';
                                ?>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase <?php echo $statusClass; ?>">
                                    <?php echo esc_html($inv->trang_thai); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                             <!--Sửa thêm cái đã xuất ở đây nhé.-->
                            <?php if ($inv->trang_thai == 'Đã hủy' || $inv->trang_thai == 'Đã thanh toán'): ?>
                                    <a href="?page=qln-invoices&action=view&id=<?php echo $inv->id; ?>" class="text-gray-400 text-xs" title="Xem">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?page=qln-invoices&action=edit&id=<?php echo $inv->id; ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="?page=qln-invoices&action=cancel&id=<?php echo $inv->id; ?>" 
                                       onclick="return confirm('Bạn có chắc muốn hủy hóa đơn này? Sản phẩm sẽ được hoàn lại kho.')"
                                       class="text-red-600 hover:text-red-800 mx-1" title="Hủy">
                                        <i class="fa-solid fa-ban"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>