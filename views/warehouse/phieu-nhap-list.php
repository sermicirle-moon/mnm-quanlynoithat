<?php if (!defined('ABSPATH')) exit; ?>
<div class="p-8">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Quản lý Nhập hàng</h2>
            <p class="text-gray-500 text-sm mt-1">Theo dõi và quản lý các phiếu nhập kho.</p>
        </div>
        <a href="?page=qln-nhap-hang-add" class="bg-[#0a5c36] hover:bg-green-800 text-white px-5 py-2.5 rounded-lg font-medium shadow-md transition flex items-center gap-2">
            + Tạo phiếu nhập mới
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tổng phiếu</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo str_pad($stats['total_phieu'] ?? 0, 2, '0', STR_PAD_LEFT); ?></h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Giá trị nhập kho</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['total_value'] ?? 0); ?>đ</h3>
        </div>
    </div>

    <!-- BỘ LỌC NÂNG CAO & TÌM KIẾM -->
    <form method="GET" class="mb-6 bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <input type="hidden" name="page" value="qln-nhap-hang">
        
        <!-- Hàng 1: Tìm nhanh, Trạng thái, Sắp xếp -->
        <div class="flex gap-4 mb-4">
            <div class="flex-1 relative">
                <input type="text" name="search_pn" value="<?php echo esc_attr($filters['search'] ?? ''); ?>" placeholder="Tìm mã phiếu hoặc ghi chú..." class="w-full pl-11 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-[#0a5c36] transition outline-none">
            </div>

            <div class="w-48">
                <select name="status_pn" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-[#0a5c36] transition outline-none">
                    <option value="">- Tất cả trạng thái -</option>
                    <option value="Chờ xử lý" <?php selected($filters['status'] ?? '', 'Chờ xử lý'); ?>>Chờ xử lý</option>
                    <option value="Đã nhập kho" <?php selected($filters['status'] ?? '', 'Đã nhập kho'); ?>>Đã nhập kho</option>
                    <option value="Đã hủy" <?php selected($filters['status'] ?? '', 'Đã hủy'); ?>>Đã hủy</option>
                </select>
            </div>

            <div class="w-56">
                <select name="sort_order" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-[#0a5c36] transition outline-none font-medium text-gray-700">
                    <option value="id_desc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'id_desc'); ?>>Mới tạo nhất (Mặc định)</option>
                    <option value="id_asc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'id_asc'); ?>>Tạo cũ nhất</option>
                    <option value="tong_tien_desc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'tong_tien_desc'); ?>>Giá trị: Từ cao đến thấp</option>
                    <option value="tong_tien_asc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'tong_tien_asc'); ?>>Giá trị: Từ thấp đến cao</option>
                    <option value="ngay_nhap_desc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'ngay_nhap_desc'); ?>>Ngày nhập: Gần đây nhất</option>
                    <option value="ngay_nhap_asc" <?php selected(isset($_GET['sort_order']) ? $_GET['sort_order'] : '', 'ngay_nhap_asc'); ?>>Ngày nhập: Xa nhất</option>
                </select>
            </div>

            <button type="submit" class="bg-gray-800 hover:bg-black text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-filter"></i> Áp dụng
            </button>
            
            <?php if(!empty(array_filter($filters))): ?>
                <a href="?page=qln-nhap-hang" class="flex items-center text-gray-400 hover:text-red-500 font-medium px-2 transition" title="Xóa toàn bộ lọc">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            <?php endif; ?>
        </div>

        <!-- Hàng 2: Lọc Nâng Cao (Thời gian, Nhà cung cấp, Giá tiền) -->
        <div class="grid grid-cols-5 gap-4 pt-4 border-t border-gray-100">
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Từ ngày</label>
                <input type="date" name="tu_ngay" value="<?php echo esc_attr($filters['tu_ngay'] ?? ''); ?>" class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-[#0a5c36] outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Đến ngày</label>
                <input type="date" name="den_ngay" value="<?php echo esc_attr($filters['den_ngay'] ?? ''); ?>" class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-[#0a5c36] outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Nhà cung cấp</label>
                <select name="ncc_id" class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-[#0a5c36] outline-none">
                    <option value="">- Tất cả -</option>
                    <?php if(!empty($nhaCungCaps)): foreach ($nhaCungCaps as $ncc): ?>
                        <option value="<?php echo $ncc['id']; ?>" <?php selected($filters['ncc_id'] ?? '', $ncc['id']); ?>><?php echo esc_html($ncc['ten_ncc']); ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Tổng tiền (Từ)</label>
                <input type="number" name="min_price" value="<?php echo esc_attr($filters['min_price'] ?? ''); ?>" placeholder="VNĐ" class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-[#0a5c36] outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Tổng tiền (Đến)</label>
                <input type="number" name="max_price" value="<?php echo esc_attr($filters['max_price'] ?? ''); ?>" placeholder="VNĐ" class="w-full px-3 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-md focus:border-[#0a5c36] outline-none">
            </div>
        </div>
    </form>

    <!-- Table Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left border-collapse">
                <thead class="text-[11px] uppercase font-bold text-gray-400 tracking-wider border-b">
                    <tr>
                        <th class="px-4 py-3">Mã phiếu</th>
                        <th class="px-4 py-3">Nhà cung cấp</th>
                        <th class="px-4 py-3">Ngày nhập</th>
                        <th class="px-4 py-3">Người tạo</th>
                        <th class="px-4 py-3">Tổng tiền</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    <?php if(!empty($phieuNhaps)): foreach ($phieuNhaps as $pn): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 font-bold text-green-700"><?php echo esc_html($pn['ma_pn']); ?></td>
                        <td class="px-4 py-4 font-bold text-gray-800"><?php echo esc_html($pn['ten_ncc']); ?></td>
                        
                        <td class="px-4 py-4 text-gray-600 font-medium">
                            <span class="text-xs text-gray-400 mr-1"><?php echo date('H:i', strtotime($pn['ngay_nhap'])); ?> |</span>
                            <?php echo date('d/m/Y', strtotime($pn['ngay_nhap'])); ?>
                        </td>

                        <td class="px-4 py-4 text-gray-600"><?php echo esc_html($pn['nguoi_tao']); ?></td>
                        <td class="px-4 py-4 font-bold text-gray-800"><?php echo number_format($pn['tong_tien']); ?> đ</td>
                        
                        <td class="px-4 py-4">
                            <?php 
                                $status = $pn['trang_thai'];
                                if ($status === 'Đã nhập kho' || $status === 'Hoàn tất') {
                                    $bg_color = 'bg-green-100 text-green-700';
                                } elseif ($status === 'Đã hủy') {
                                    $bg_color = 'bg-red-100 text-red-700';
                                } else {
                                    $bg_color = 'bg-yellow-100 text-yellow-700';
                                }
                            ?>
                            <span class="<?php echo $bg_color; ?> px-3 py-1 rounded-md text-[11px] font-bold whitespace-nowrap">
                                <?php echo esc_html($status); ?>
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            <?php if ($pn['trang_thai'] === 'Chờ duyệt' || $pn['trang_thai'] === 'Chờ xử lý'): ?>
                                <div class="flex items-center justify-center gap-2">
                                    <a href="?page=qln-nhap-hang-approve&id=<?php echo $pn['id']; ?>" onclick="return confirm('Bạn xác nhận duyệt và cộng hàng vào kho?');" class="text-green-600 hover:bg-green-50 px-2 py-1.5 rounded transition" title="Duyệt phiếu vào kho">
                                        <i class="fa-solid fa-check-double"></i>
                                    </a>
                                    <a href="?page=qln-nhap-hang-edit&id=<?php echo $pn['id']; ?>" class="text-blue-600 hover:bg-blue-50 px-2 py-1.5 rounded transition" title="Sửa phiếu">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="?page=qln-nhap-hang-delete&id=<?php echo $pn['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa/hủy phiếu nhập này?');" class="text-red-600 hover:bg-red-50 px-2 py-1.5 rounded transition" title="Hủy phiếu">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="?page=qln-nhap-hang-view&id=<?php echo $pn['id']; ?>" class="text-gray-500 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="p-4 text-center text-gray-500">Không tìm thấy phiếu nhập nào phù hợp.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- THANH PHÂN TRANG KÈM BIẾN LỌC ($url_params) -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <div class="p-4 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500 bg-gray-50">
            <p>Hiển thị trang <b class="text-gray-800"><?php echo $trang_hien_tai; ?></b> trên tổng số <?php echo $total_pages; ?> trang (Tổng: <?php echo $total_items; ?> phiếu)</p>
            <div class="flex gap-1">
                <!-- Nút Lùi -->
                <?php if ($trang_hien_tai > 1): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $trang_hien_tai - 1; ?><?php echo $url_params ?? ''; ?>" class="w-8 h-8 flex items-center justify-center border border-gray-200 bg-white rounded hover:bg-gray-100 transition"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <!-- Các trang 1, 2, 3... -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $i; ?><?php echo $url_params ?? ''; ?>" class="w-8 h-8 flex items-center justify-center border <?php echo ($i == $trang_hien_tai) ? 'border-[#0a5c36] bg-[#0a5c36] text-white font-bold' : 'border-gray-200 bg-white hover:bg-gray-100 font-bold text-gray-600'; ?> rounded transition shadow-sm"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <!-- Nút Tiến -->
                <?php if ($trang_hien_tai < $total_pages): ?>
                    <a href="?page=qln-nhap-hang&paged=<?php echo $trang_hien_tai + 1; ?><?php echo $url_params ?? ''; ?>" class="w-8 h-8 flex items-center justify-center border border-gray-200 bg-white rounded hover:bg-gray-100 transition"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>