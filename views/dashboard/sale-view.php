<?php if (!defined('ABSPATH')) exit; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex-1 overflow-y-auto p-8 bg-gray-50">
    <!-- Chào mừng -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Chào buổi sáng, <?php echo esc_html($userName); ?>!</h1>
        <p class="text-gray-500 mt-1">Hôm nay bạn có <span class="font-bold text-green-600"><?php echo $pending_orders; ?></span> đơn hàng cần xử lý và <span class="font-bold text-blue-600"><?php echo count($potential_customers); ?></span> khách hàng tiềm năng.</p>
    </div>

    <!-- Cards thống kê -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">DOANH THU THÁNG</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo qln_format_compact_money($current_month_revenue); ?>đ</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4 text-xs <?php echo $growth >= 0 ? 'text-green-600' : 'text-red-600'; ?> bg-gray-50 inline-block px-2 py-1 rounded-md">
                <i class="fa-solid fa-arrow-trend-up"></i> 12% so với tháng trước
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">SỐ ĐƠN HÀNG</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $monthly_orders; ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">Đơn hàng thành công (không hủy)</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">HOÀN THÀNH MỤC TIÊU</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $revenue_percent; ?>%</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-flag-checkered"></i>
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500">Mục tiêu doanh thu: <?php echo number_format($target_revenue); ?>đ</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500 font-medium">TRUNG BÌNH NGÀNH</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $orders_percent; ?>%</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
            </div>
            <div class="mt-4 text-xs <?php echo $orders_percent >= 68 ? 'text-green-600' : 'text-red-600'; ?>">Cao hơn TB: <?php echo abs($orders_percent - 68); ?>%</div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu 7 ngày -->
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Doanh thu cá nhân theo tuần</h3>
            <p class="text-xs text-gray-400">Dữ liệu 7 ngày gần nhất</p>
        </div>
        <canvas id="weeklyRevenueChart" height="100"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ghi chú & nhắc nhở (tĩnh) -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Ghi chú & Nhắc nhở</h3>
            <div class="space-y-4">
                <div class="border-l-4 border-blue-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800">Gọi lại cho anh Hùng</p>
                    <p class="text-sm text-gray-500">Ghi bàn giao bắt đầu ăn gì đó chi.</p>
                    <p class="text-xs text-gray-400 mt-1">14:30 - Hôm nay</p>
                </div>
                <div class="border-l-4 border-yellow-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800">Gửi catalog cho chị Lan</p>
                    <p class="text-sm text-gray-500">Mẫu mới kết hợp các trang cụ thể hơn.</p>
                    <p class="text-xs text-gray-400 mt-1">23:59 - 25/05/2026</p>
                </div>
                <div class="border-l-4 border-red-500 pl-3 py-2">
                    <p class="font-semibold text-gray-800">Check tồn kho đặt mới</p>
                    <p class="text-sm text-gray-500">Định lượng mua mới cho tháng sau.</p>
                    <p class="text-xs text-gray-400 mt-1">Cần ưu tiên</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t">
                <div class="flex gap-2">
                    <input type="text" placeholder="Thêm ghi chú mới..." class="flex-1 px-3 py-2 border rounded-lg text-sm" disabled>
                    <button class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm" disabled>+ Thêm</button>
                </div>
                <p class="text-xs text-gray-400 mt-2">Tính năng này sẽ được cập nhật sau</p>
            </div>
        </div>

        <!-- Đơn hàng cần xử lý -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Đơn hàng cần xử lý</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr><th class="p-2 text-left">Mã đơn</th><th class="p-2 text-left">Khách hàng</th><th class="p-2 text-right">Giá trị</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_list as $order): ?>
                        <tr class="border-b">
                            <td class="p-2 font-mono"><?php echo esc_html($order->ma_hd); ?></td>
                            <td class="p-2"><?php echo esc_html($order->ten_kh); ?></td>
                            <td class="p-2 text-right font-semibold"><?php echo number_format($order->tong_tien); ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pending_list)): ?>
                        <tr><td colspan="3" class="p-4 text-center text-gray-400">Không có đơn hàng cần xử lý</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <a href="?page=qln-invoices" class="text-blue-600 text-sm hover:underline">Xem tất cả →</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Khách hàng tiềm năng -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Khách hàng tiềm năng</h3>
            <div class="space-y-3">
                <?php foreach ($potential_customers as $pot): ?>
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <p class="font-medium text-gray-800"><?php echo esc_html($pot->ten_kh); ?></p>
                        <p class="text-xs text-gray-500"><?php echo esc_html($pot->email ?: $pot->sdt); ?></p>
                        <p class="text-xs text-gray-400">Đăng ký: <?php echo date('d/m/Y', strtotime($pot->ngay_tao)); ?></p>
                    </div>
                    <a href="?page=qln-customers&action=edit&id=<?php echo $pot->id; ?>" class="text-blue-500 hover:text-blue-700"><i class="fa-regular fa-message"></i> Liên hệ</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($potential_customers)): ?>
                <p class="text-gray-400 text-sm">Chưa có khách hàng tiềm năng mới.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tìm kiếm khách hàng -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-center">
            <div class="text-center">
                <i class="fa-regular fa-compass text-4xl text-gray-400 mb-3"></i>
                <h3 class="font-bold text-gray-800 mb-2">Tìm kiếm khách hàng</h3>
                <form action="<?php echo admin_url('admin.php'); ?>" method="GET" class="mt-2">
                    <input type="hidden" name="page" value="qln-customers">
                    <div class="flex gap-2">
                        <input type="text" name="search" placeholder="Nhập tên, email, SĐT..." class="flex-1 px-3 py-2 border rounded-lg text-sm">
                        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">Tìm</button>
                    </div>
                </form>
                <p class="text-xs text-gray-400 mt-3">Khám phá thêm khách hàng tiềm năng từ danh sách</p>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('weeklyRevenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($week_days); ?>,
            datasets: [{
                label: 'Doanh thu (đ)',
                data: <?php echo json_encode($daily_revenue); ?>,
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + 'đ';
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw.toLocaleString() + 'đ';
                        }
                    }
                }
            }
        }
    });
</script>