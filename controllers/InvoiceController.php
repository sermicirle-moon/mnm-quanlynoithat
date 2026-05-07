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
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }

        $action = $_GET['action'] ?? 'list';

        switch ($action) {
            case 'create':
                $this->showForm();
                break;
            case 'store':
                $this->store();
                break;
            case 'edit':
                $this->showForm($_GET['id']);
                break;
            case 'update':
                $this->update($_GET['id']);
                break;
            case 'cancel':
                $this->cancel($_GET['id']);
                break;
            case 'view':
                $this->view($_GET['id']);
                break;
            default:
                $this->list();
                break;
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
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }
        if ($invoice->trang_thai == 'Đã hủy' || $invoice->trang_thai == 'Đã thanh toán') {
            $_SESSION['qln_error'] = "Hóa đơn đã " . $invoice->trang_thai . ", không thể sửa!";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }
        
        if ($id) {
            $invoice = $this->repo->getById($id);
            if (!$invoice) {
                $_SESSION['qln_error'] = "Hóa đơn không tồn tại!";
                wp_redirect(admin_url('admin.php?page=qln-invoices'));
                exit;
            }
            $details = $this->detailRepo->getByInvoiceId($id);
        }
        }
        $customers = (new CustomerRepository())->getAll();
        $products = $this->productRepo->getAvailableProducts();

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content   = $base_view_path . 'invoice/invoice-form.php';
        include $base_view_path . 'layout/masterlayout.php';
    }

    /**
     * Kiểm tra tồn kho cho các sản phẩm trong mảng chi tiết mới
     * Có tính đến chi tiết cũ (nếu là update) để trừ lượng đã tồn tại
     * @param int|null $invoice_id (null khi thêm mới)
     * @param array $new_details (dạng [['san_pham_id'=>x, 'so_luong'=>y], ...])
     * @return array ['valid'=>true/false, 'errors'=>[]]
     */
    private function checkStockAvailability($invoice_id, $new_details) {
        $errors = [];
        $seen = [];
        // Nhóm số lượng mới theo sản phẩm
        $new_qty_map = [];
        foreach ($new_details as $item) {
            $pid = intval($item['san_pham_id']);
            $qty = intval($item['so_luong']);
            if (!isset($new_qty_map[$pid])) $new_qty_map[$pid] = 0;
            $new_qty_map[$pid] += $qty;
            if ($pid <= 0 || $qty <= 0) continue;
            if (isset($seen[$pid])) {
                $product = $this->productRepo->getById($pid);
                $errors[] = "Sản phẩm \"{$product->ten_sp}\" đã được thêm nhiều lần. Vui lòng gộp số lượng thành một dòng.";
            }
            $seen[$pid] = true;
        }
        if (!empty($errors)) {
            return ['valid' => false, 'errors' => $errors];
        }
            

        // Lấy số lượng cũ đã dùng (nếu có)
        $old_qty_map = [];
        if ($invoice_id) {
            $old_details = $this->detailRepo->getByInvoiceId($invoice_id);
            foreach ($old_details as $old) {
                $old_qty_map[$old->san_pham_id] = $old->so_luong;
            }
        }

        // Kiểm tra từng sản phẩm
        foreach ($new_qty_map as $pid => $new_qty) {
            $stock = $this->productRepo->getStock($pid);
            $old_qty = isset($old_qty_map[$pid]) ? $old_qty_map[$pid] : 0;
            $needed = $new_qty - $old_qty; // lượng thực tế cần lấy từ kho (âm nếu trả về)
            if ($needed > 0 && $stock < $needed) {
                $product = $this->productRepo->getById($pid);
                $errors[] = "Sản phẩm \"{$product->ten_sp}\" chỉ còn {$stock} cái, nhưng bạn đặt thêm {$needed} cái.";
            }
        }
        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Cập nhật tồn kho dựa trên chênh lệch giữa chi tiết cũ và mới
     * @param int $invoice_id
     * @param array $new_details
     */
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

        // Xử lý các sản phẩm bị xóa hoàn toàn trong chi tiết mới
        foreach ($old_map as $pid => $old_qty) {
            if (!isset($new_map[$pid])) {
                $this->productRepo->increaseStock($pid, $old_qty); // trả lại toàn bộ
            }
        }

        // Xử lý các sản phẩm có thay đổi
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

    /**
     * Hoàn lại toàn bộ số lượng của hóa đơn (khi hủy hoặc xóa)
     */
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
            $new_details[] = [
                'san_pham_id' => intval($item['san_pham_id']),
                'so_luong'    => intval($item['so_luong'])
            ];
        }
        foreach ($new_details as $item) {
            $product = $this->productRepo->getById($item['san_pham_id']);
            if (!$product || $product->trang_thai == 'Không bán') {
                $_SESSION['qln_error'] = "Sản phẩm '{$product->ten_sp}' không được phép bán!";
                wp_redirect(admin_url('admin.php?page=qln-invoices&action=create'));
                exit;
            }
        }

        // Kiểm tra tồn kho
        $check = $this->checkStockAvailability(null, $new_details);
        if (!$check['valid']) {
            $_SESSION['qln_error'] = implode('<br>', $check['errors']);
            wp_redirect(admin_url('admin.php?page=qln-invoices&action=create'));
            exit;
        }

        // Lưu hóa đơn
        $data = [
            'ma_hd'          => sanitize_text_field($_POST['ma_hd']),
            'khach_hang_id'  => intval($_POST['khach_hang_id']),
            'trang_thai'     => sanitize_text_field($_POST['trang_thai']),
            'ngay_tao'       => date('Y-m-d H:i:s'),
            'tong_tien'      => 0
        ];
        $result = $this->repo->create($data);
        if (!$result) {
            $_SESSION['qln_error'] = "Thêm hóa đơn thất bại!";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }
        $invoice_id = $this->repo->getLastInsertId();

        // Lưu chi tiết
        $this->saveInvoiceDetails($invoice_id, $new_details);

        // Cập nhật tổng tiền và trừ kho
        // $this->repo->updateTotalAmount($invoice_id);
        $this->updateInventory($invoice_id, $new_details);

        $_SESSION['qln_success'] = "Thêm hóa đơn thành công!";
        wp_redirect(admin_url('admin.php?page=qln-invoices'));
        exit;
    }



    private function saveInvoiceDetails($invoice_id, $products_array) {
        foreach ($products_array as $item) {
            if (empty($item['san_pham_id']) || empty($item['so_luong'])) continue;
            $san_pham_id = intval($item['san_pham_id']);
            $so_luong = intval($item['so_luong']);

            // Lấy giá bán hiện tại của sản phẩm
            $product = $this->productRepo->getById($san_pham_id);
            if (!$product) continue;
            $don_gia = $product->gia_ban;

            $this->detailRepo->create([
                'hoa_don_id'   => $invoice_id,
                'san_pham_id'  => $san_pham_id,
                'so_luong'     => $so_luong,
                'don_gia'      => $don_gia
            ]);
        }
    }

    private function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $old_status = $this->repo->getStatus($id);
        $new_status = sanitize_text_field($_POST['trang_thai']);

        // Cập nhật thông tin chung
        $data = [
            'ma_hd'          => sanitize_text_field($_POST['ma_hd']),
            'khach_hang_id'  => intval($_POST['khach_hang_id']),
            'trang_thai'     => $new_status
        ];
        $this->repo->update($id, $data);

        // Lấy chi tiết cũ và mới
        $old_details = $this->detailRepo->getByInvoiceId($id);
        $products_input = $_POST['products'] ?? [];
        $new_details = [];
        foreach ($products_input as $item) {
            if (empty($item['san_pham_id']) || empty($item['so_luong'])) continue;
            $new_details[] = [
                'san_pham_id' => intval($item['san_pham_id']),
                'so_luong'    => intval($item['so_luong'])
            ];
        }
        foreach ($new_details as $item) {
            $product = $this->productRepo->getById($item['san_pham_id']);
            if (!$product || $product->trang_thai == 'Không bán') {
                $_SESSION['qln_error'] = "Sản phẩm '{$product->ten_sp}' không được phép bán!";
                wp_redirect(admin_url('admin.php?page=qln-invoices&action=create'));
                exit;
            }
        }
        // Nếu hủy hóa đơn (chuyển từ trạng thái khác thành "Đã hủy")
        if ($old_status !== 'Đã hủy' && $new_status === 'Đã hủy') {
            error_log("Attempting to cancel invoice $id");
            // Không cần lưu chi tiết mới vì đã hủy
            $_SESSION['qln_success'] = "Hóa đơn đã được hủy, số lượng sản phẩm đã được hoàn lại kho.";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }

        // Nếu không phải hủy, tiến hành kiểm tra tồn kho (dựa trên chênh lệch)
        $check = $this->checkStockAvailability($id, $new_details);
        if (!$check['valid']) {
            $_SESSION['qln_error'] = implode('<br>', $check['errors']);
            wp_redirect(admin_url("admin.php?page=qln-invoices&action=edit&id={$id}"));
            exit;
        }

        // Xóa chi tiết cũ và lưu chi tiết mới
        $this->detailRepo->deleteByInvoiceId($id);
        $this->saveInvoiceDetails($id, $new_details);

        // Cập nhật tổng tiền và điều chỉnh tồn kho
        // $this->repo->updateTotalAmount($id);
        $this->updateInventory($id, $new_details);

        $_SESSION['qln_success'] = "Cập nhật hóa đơn thành công!";
        wp_redirect(admin_url('admin.php?page=qln-invoices'));
        exit;
    }

    private function cancel($id) {
        global $wpdb;

        $invoice = $this->repo->getById($id);
        if (!$invoice) {
            $_SESSION['qln_error'] = "Hóa đơn không tồn tại!";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }

        // Kiểm tra nếu đã hủy rồi
        if ($invoice->trang_thai === 'Đã hủy') {
            $_SESSION['qln_error'] = "Hóa đơn này đã được hủy trước đó.";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }

        // Cập nhật trạng thái hóa đơn
        $result = $this->repo->update($id, ['trang_thai' => 'Đã hủy']);

        if ($result === false) {
            $_SESSION['qln_error'] = "Lỗi cập nhật trạng thái hóa đơn: " . $wpdb->last_error;
        } elseif ($result == 0) {
            $_SESSION['qln_error'] = "Không có thay đổi nào (có thể trạng thái đã là 'Đã hủy' từ trước).";
        } else {
            $_SESSION['qln_success'] = "Hóa đơn đã được hủy. Tồn kho đã được hoàn trả.";
        }
        error_log("Cancel invoice $id, result = " . var_export($result, true));
        error_log("Last error: " . $wpdb->last_error);
        wp_redirect(admin_url('admin.php?page=qln-invoices'));
        exit;
    }
    private function view($id) {
        $invoice = $this->repo->getById($id);
        if (!$invoice) {
            $_SESSION['qln_error'] = "Hóa đơn không tồn tại!";
            wp_redirect(admin_url('admin.php?page=qln-invoices'));
            exit;
        }
        $details = $this->detailRepo->getByInvoiceId($id);
        $customers = (new CustomerRepository())->getAll();
        $products = $this->productRepo->getAll();

        $base_view_path = plugin_dir_path(__FILE__) . '../views/';
        $view_content = $base_view_path . 'invoice/invoice-view-detail.php';
        include $base_view_path . 'layout/masterlayout.php';
    }
}