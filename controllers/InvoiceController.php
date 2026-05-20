<?php
class InvoiceController {
    private $repo;
    private $detailRepo;
    private $productRepo;
    public function __construct() {
        $this->repo = new InvoiceRepository();
        $this->detailRepo = new InvoiceDetailRepository();
        $this->productRepo = new ProductRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }

        $action = $_GET['action'] ?? 'list';

        switch ($action) {
            case 'create': $this->showForm(); break;
            case 'store': $this->store(); break;
            case 'edit': $this->showForm($_GET['id']); break;
            case 'update': $this->update($_GET['id']); break;
            case 'cancel': $this->cancel($_GET['id']); break;
            case 'mark_paid': $this->markPaid($_GET['id']); break; // <-- MỚI
            case 'refund': $this->refund($_GET['id']); break;       // <-- MỚI
            case 'view': $this->view($_GET['id']); break;
            default: $this->list(); break;
        }
    }

    private function list() {
        $search = sanitize_text_field($_GET['search'] ?? '');
        $status = sanitize_text_field($_GET['status'] ?? '');
        $date_from = sanitize_text_field($_GET['date_from'] ?? '');
        $date_to = sanitize_text_field($_GET['date_to'] ?? '');
        $total_from = sanitize_text_field($_GET['total_from'] ?? '');
        $total_to = sanitize_text_field($_GET['total_to'] ?? '');
        
        $invoices = $this->repo->getAllWithFilters($search, $status, $date_from, $date_to, $total_from, $total_to);
        
        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content   = $base_view_path . 'invoice/invoice-view.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    private function showForm($id = null) {
        $invoice = null;
        $details = [];
        if ($id) {
            $invoice = $this->repo->getById($id);
            if (!$invoice) {
                $_SESSION['qln_error'] = "Hóa đơn không tồn tại!";
                wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
            }
            if ($invoice->trang_thai == 'Đã hủy' || $invoice->trang_thai == 'Hoàn tiền' || $invoice->trang_thai == 'Đã thanh toán') {
                $_SESSION['qln_error'] = "Hóa đơn đã " . $invoice->trang_thai . ", không thể sửa đổi nội dung!";
                wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
            }
            $details = $this->detailRepo->getByInvoiceId($id);
        }
        $customers = (new CustomerRepository())->getAll();
        $products = $this->productRepo->getAvailableProducts();

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content   = $base_view_path . 'invoice/invoice-form.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    // Kiểm tra tồn kho (Giữ nguyên logic cực kỳ chặt chẽ của bạn)
    private function checkStockAvailability($invoice_id, $new_details) {
        $errors = [];
        $seen = [];
        $new_qty_map = [];
        foreach ($new_details as $item) {
            $pid = intval($item['san_pham_id']);
            $qty = intval($item['so_luong']);
            if (!isset($new_qty_map[$pid])) $new_qty_map[$pid] = 0;
            $new_qty_map[$pid] += $qty;
            if ($pid <= 0 || $qty <= 0) continue;
            if (isset($seen[$pid])) {
                $product = $this->productRepo->getById($pid);
                $errors[] = "Sản phẩm \"{$product->ten_sp}\" bị trùng. Vui lòng gộp dòng.";
            }
            $seen[$pid] = true;
        }
        if (!empty($errors)) return ['valid' => false, 'errors' => $errors];

        $old_qty_map = [];
        if ($invoice_id) {
            $old_details = $this->detailRepo->getByInvoiceId($invoice_id);
            foreach ($old_details as $old) {
                $old_qty_map[$old->san_pham_id] = $old->so_luong;
            }
        }

        foreach ($new_qty_map as $pid => $new_qty) {
            $stock = $this->productRepo->getStock($pid);
            $old_qty = isset($old_qty_map[$pid]) ? $old_qty_map[$pid] : 0;
            $needed = $new_qty - $old_qty; 
            if ($needed > 0 && $stock < $needed) {
                $product = $this->productRepo->getById($pid);
                $errors[] = "Sản phẩm \"{$product->ten_sp}\" chỉ còn {$stock} cái trong kho.";
            }
        }
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    private function updateInventory($invoice_id, $new_details) {
        $old_details = $this->detailRepo->getByInvoiceId($invoice_id);
        $old_map = [];
        foreach ($old_details as $old) {
            $old_map[$old->san_pham_id] = $old->so_luong;
        }
        $new_map = [];
        foreach ($new_details as $item) {
            $pid = $item['san_pham_id'];
            $qty = $item['so_luong'];
            if (!isset($new_map[$pid])) $new_map[$pid] = 0;
            $new_map[$pid] += $qty;
        }

        foreach ($old_map as $pid => $old_qty) {
            if (!isset($new_map[$pid])) {
                $this->productRepo->increaseStock($pid, $old_qty); 
            }
        }

        $all_pids = array_unique(array_merge(array_keys($old_map), array_keys($new_map)));
        foreach ($all_pids as $pid) {
            $old_qty = $old_map[$pid] ?? 0;
            $new_qty = $new_map[$pid] ?? 0;
            $diff = $new_qty - $old_qty;
            if ($diff > 0) {
                $this->productRepo->decreaseStock($pid, $diff);
            } elseif ($diff < 0) {
                $this->productRepo->increaseStock($pid, -$diff);
            }
        }
    }

    private function returnInventory($invoice_id) {
        $old_details = $this->detailRepo->getByInvoiceId($invoice_id);
        foreach ($old_details as $item) {
            $this->productRepo->increaseStock($item->san_pham_id, $item->so_luong);
        }
    }

    private function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $products_input = $_POST['products'] ?? [];
        $new_details = [];
        foreach ($products_input as $item) {
            if (empty($item['san_pham_id']) || empty($item['so_luong'])) continue;
            $new_details[] = ['san_pham_id' => intval($item['san_pham_id']), 'so_luong' => intval($item['so_luong'])];
        }

        $hinh_thuc_giao = sanitize_text_field($_POST['trang_thai_giao'] ?? 'Chờ giao');

        // Check tồn kho nếu lấy hàng tại quầy
        if ($hinh_thuc_giao === 'Tại quầy') {
            $check = $this->checkStockAvailability(null, $new_details);
            if (!$check['valid']) {
                $_SESSION['qln_error'] = implode('<br>', $check['errors']);
                wp_redirect(admin_url('admin.php?page=qln-invoices&action=create')); exit;
            }
        }

        $data = [
            'ma_hd'           => sanitize_text_field($_POST['ma_hd']),
            'khach_hang_id'   => intval($_POST['khach_hang_id']),
            'trang_thai'      => sanitize_text_field($_POST['trang_thai']),
            'trang_thai_giao' => $hinh_thuc_giao,
            'ngay_tao'        => date('Y-m-d H:i:s'),
            'tong_tien'       => 0
        ];
        
        if ($this->repo->create($data)) {
            $invoice_id = $this->repo->getLastInsertId();
            $this->saveInvoiceDetails($invoice_id, $new_details);
            $this->repo->updateTotalAmount($invoice_id); 
            
            // FIX LỖI: Trừ kho trực tiếp cho đơn "Tại quầy"
            if ($hinh_thuc_giao === 'Tại quầy') {
                foreach ($new_details as $item) {
                    $this->productRepo->decreaseStock($item['san_pham_id'], $item['so_luong']);
                }
            }
             // *** CẬP NHẬT KHÁCH HÀNG NẾU TRẠNG THÁI LÀ "ĐÃ THANH TOÁN" ***
            if ($data['trang_thai'] === 'Đã thanh toán') {
                $invoice = $this->repo->getById($invoice_id); // lấy lại để có tổng tiền
                $customerRepo = new CustomerRepository();
                $customerRepo->updateTotalSpent($invoice->khach_hang_id, $invoice->tong_tien, true);
                $customerRepo->updateOrderCount($invoice->khach_hang_id, true);
            }

            $_SESSION['qln_success'] = "Thêm hóa đơn thành công!";
        }else{
            $_SESSION['qln_error'] = "Thêm hóa đơn thất bại!";
        }
        wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
    }

    private function saveInvoiceDetails($invoice_id, $products_array) {
        foreach ($products_array as $item) {
            $product = $this->productRepo->getById($item['san_pham_id']);
            if (!$product) continue;
            
            $so_luong = intval($item['so_luong']);
            $don_gia = $product->gia_ban;
            $thanh_tien = $so_luong * $don_gia; // TÍNH TOÁN THÀNH TIỀN

            $this->detailRepo->create([
                'hoa_don_id'   => $invoice_id,
                'san_pham_id'  => $item['san_pham_id'],
                'so_luong'     => $so_luong,
                'don_gia'      => $don_gia,
                'thanh_tien'   => $thanh_tien // LƯU VÀO DATABASE
            ]);
        }
    }

    private function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $old_invoice = $this->repo->getById($id);
        $old_status = $old_invoice->trang_thai;
        $new_status = sanitize_text_field($_POST['trang_thai']);
        $products_input = $_POST['products'] ?? [];
        $new_details = [];

        foreach ($products_input as $item) {
            if (empty($item['san_pham_id']) || empty($item['so_luong'])) continue;
            $new_details[] = [
                'san_pham_id' => intval($item['san_pham_id']),
                'so_luong'    => intval($item['so_luong'])
            ];
        }

        // Logic bảo vệ: Nếu trạng thái là Hủy/Hoàn tiền thì trả hàng và không cho sửa chi tiết nữa
        if ($new_status === 'Đã hủy' || $new_status === 'Hoàn tiền') {
            $this->returnInventory($id);
            $this->repo->update($id, ['trang_thai' => $new_status]);
            $_SESSION['qln_success'] = "Hóa đơn đã được chuyển trạng thái và hoàn tồn kho.";
            wp_redirect(admin_url('admin.php?page=qln-invoices')); 
            exit;
        }

        $this->detailRepo->deleteByInvoiceId($id); // Xóa để lưu mới
        $this->saveInvoiceDetails($id, $new_details);
        $this->repo->updateTotalAmount($id); // ĐÃ MỞ KHÓA: Đảm bảo tổng tiền khớp với sản phẩm hiện tại
        $this->updateInventory($id, $new_details);
        $this->repo->update($id, ['trang_thai' => $new_status]); // Cập nhật trạng thái sau cùng

        //  Nếu chuyển từ Chờ thanh toán sang Đã thanh toán, cộng dồn khách hàng ***
        if ($old_status === 'Chờ thanh toán' && $new_status === 'Đã thanh toán') {
            $invoice = $this->repo->getById($id);
            $customerRepo = new CustomerRepository();
            $customerRepo->updateTotalSpent($invoice->khach_hang_id, $invoice->tong_tien, true);
            $customerRepo->updateOrderCount($invoice->khach_hang_id, true);
        }

        $_SESSION['qln_success'] = "Cập nhật hóa đơn thành công!";
        wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
    }

    // --- CÁC HÀM XỬ LÝ TRẠNG THÁI NHANH ---

    private function markPaid($id) {
        $invoice = $this->repo->getById($id);
        if ($invoice && $invoice->trang_thai === 'Chờ thanh toán') {
            $this->repo->update($id, ['trang_thai' => 'Đã thanh toán']);

            // Cập nhật thống kê khách hàng
            $customerRepo = new CustomerRepository();
            $customerRepo->updateTotalSpent($invoice->khach_hang_id, $invoice->tong_tien, true);
            $customerRepo->updateOrderCount($invoice->khach_hang_id, true);
            $_SESSION['qln_success'] = "Hóa đơn đã được ghi nhận thanh toán! Doanh thu đã được cộng.";
        }
        wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
    }

    private function cancel($id) {
        $invoice = $this->repo->getById($id);
        if ($invoice && $invoice->trang_thai === 'Chờ thanh toán') {
            // Cập nhật trạng thái
            $this->repo->update($id, ['trang_thai' => 'Đã hủy', 'trang_thai_giao' => 'Đã hủy']);
            
            // Nếu đơn này trước đó đã lấy hàng tại quầy thì phải HOÀN KHO
            if ($invoice->trang_thai_giao === 'Tại quầy') {
                $this->returnInventory($id);
                $_SESSION['qln_success'] = "Đã hủy hóa đơn và hoàn trả hàng về kho.";
            } else {
                $_SESSION['qln_success'] = "Đã hủy hóa đơn thành công.";
            }
        } else {
            $_SESSION['qln_error'] = "Chỉ có thể hủy hóa đơn đang Chờ thanh toán.";
        }
        wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
    }

    private function refund($id) {
        $invoice = $this->repo->getById($id);
        if ($invoice && $invoice->trang_thai === 'Đã thanh toán') {
            $this->repo->update($id, ['trang_thai' => 'Hoàn tiền']);
            $customerRepo = new CustomerRepository();
            $customerRepo->updateTotalSpent($invoice->khach_hang_id, $invoice->tong_tien, false);
            $customerRepo->updateOrderCount($invoice->khach_hang_id, false);

            // Nếu đơn này khách đã mang hàng về (Tại quầy) thì thu hồi hàng vào kho
            if ($invoice->trang_thai_giao === 'Tại quầy') {
                $this->returnInventory($id); 
                $this->repo->update($id, ['trang_thai_giao' => 'Đã hoàn trả']);
                $_SESSION['qln_success'] = "Đã hoàn tiền & thu hồi hàng về kho.";
            } else {
                $_SESSION['qln_success'] = "Đã hoàn tiền thành công (Hàng chưa xuất nên không cần hoàn kho).";
            }
        }
        wp_redirect(admin_url('admin.php?page=qln-invoices')); exit;
    }

    private function view($id) {
        $invoice = $this->repo->getById($id);
        $details = $this->detailRepo->getByInvoiceId($id);
        $customers = (new CustomerRepository())->getAll();
        
        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'invoice/invoice-view-detail.php';
        include $base_view_path . 'layout/masterlayout.php';
    }
}