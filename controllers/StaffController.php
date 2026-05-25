<?php
class StaffController {
    private $repo;

    public function __construct() {
        $this->repo = new UserRepository();
    }

    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }

        $action = sanitize_key($_GET['action'] ?? 'list');

        switch ($action) {
            case 'create': $this->showForm(); break;
            case 'store': $this->store(); break;
            case 'edit': $this->showForm((int) ($_GET['id'] ?? 0)); break;
            case 'update': $this->update((int) ($_GET['id'] ?? 0)); break;
            case 'delete': $this->delete((int) ($_GET['id'] ?? 0)); break;
            case 'export': $this->exportCsv(); break;
            case 'pdf': $this->exportPdf(); break;
            default: $this->list(); break;
        }
    }

    private function list() {
        $filters = $this->getFilters();
        $limit = 5;
        $current_page = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
        $offset = ($current_page - 1) * $limit;
        $staff = $this->repo->getAllPaginated($limit, $offset, $filters);
        $stats = $this->repo->getStats();
        $total_items = $this->repo->getTotalCount($filters);
        $total_pages = max(1, (int) ceil($total_items / $limit));
        $url_params = $this->buildUrlParams($filters);

        $base_view_path = plugin_dir_path(__FILE__) . '../views/staff/';
        $view_content = $base_view_path . 'staff-view.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function showForm($id = null) {
        $staffMember = $id ? $this->repo->getById($id) : null;
        $base_view_path = plugin_dir_path(__FILE__) . '../views/staff/';
        $view_content = $base_view_path . 'staff-form.php';
        include plugin_dir_path(__FILE__) . '../views/layout/masterlayout.php';
    }

    private function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_redirect(admin_url('admin.php?page=qln-staff')); exit;
        }

        $data = $this->getStaffPostData(true);
        $this->repo->createStaff($data);
        wp_redirect(admin_url('admin.php?page=qln-staff'));
        exit;
    }

    private function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_redirect(admin_url('admin.php?page=qln-staff')); exit;
        }

        $data = $this->getStaffPostData(false);
        $this->repo->updateStaff($id, $data);
        wp_redirect(admin_url('admin.php?page=qln-staff'));
        exit;
    }

    private function delete($id) {
        if ($id > 0) {
            $this->repo->deleteStaff($id);
        }
        wp_redirect(admin_url('admin.php?page=qln-staff'));
        exit;
    }

    private function exportCsv() {
        $filters = $this->getFilters();
        $staff = $this->repo->getAllWithFilters($filters);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="timberflow-staff-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        if ($output === false) exit;

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['ID', 'Họ tên', 'Email', 'SĐT', 'Quê quán', 'Mã vai trò', 'Vai trò', 'Trạng thái', 'Ngày tạo']);
        foreach ($staff as $member) {
            fputcsv($output, [$member->id, $member->ho_ten, $member->email, $member->sdt, $member->que_quan, $member->role_id, $member->getRoleName(), $member->getTrangThaiText(), $member->ngay_tao]);
        }

        fclose($output);
        exit;
    }

    private function exportPdf() {
        $filters = $this->getFilters();
        $staff = $this->repo->getAllPaginated(10000, 0, $filters);
        $filename = $this->buildPdfFilename('staff');
        $title = 'Báo cáo nhân viên';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"><title><?php echo esc_html($filename); ?></title>
        <style>body{font-family:Arial,sans-serif;color:#0f172a;padding:24px}h1{font-size:24px;margin-bottom:4px}.meta{color:#64748b;margin-bottom:20px}.actions{display:flex;gap:8px;margin-bottom:16px}button{border:1px solid #cbd5e1;background:#fff;border-radius:8px;padding:8px 12px;font-weight:700;cursor:pointer}table{width:100%;border-collapse:collapse;font-size:12px}th,td{border:1px solid #e2e8f0;padding:8px;text-align:left}th{background:#f1f5f9}@page{margin:12mm}@media print{body{padding:0}.actions{display:none}}</style>
        </head><body><div class="actions"><button onclick="window.print()">In / Lưu PDF</button><button onclick="window.close()">Đóng</button></div><h1><?php echo esc_html($title); ?></h1><p class="meta">Tên file gợi ý: <?php echo esc_html($filename); ?>.pdf</p><p class="meta">Tổng: <?php echo esc_html((string) count($staff)); ?> nhân viên - Ngày xuất: <?php echo esc_html(date_i18n('d/m/Y H:i')); ?></p><table><thead><tr><th>ID</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Quê quán</th><th>Vai trò</th><th>Trạng thái</th></tr></thead><tbody>
        <?php foreach ($staff as $member): ?><tr><td><?php echo esc_html((string) $member->id); ?></td><td><?php echo esc_html($member->ho_ten); ?></td><td><?php echo esc_html($member->email); ?></td><td><?php echo esc_html($member->sdt); ?></td><td><?php echo esc_html($member->que_quan); ?></td><td><?php echo esc_html($member->getRoleName()); ?></td><td><?php echo esc_html($member->getTrangThaiText()); ?></td></tr><?php endforeach; ?>
        </tbody></table></body></html>
        <?php
        exit;
    }

    private function buildPdfFilename($module) {
        $username = sanitize_file_name((string) ($_SESSION['qln_user_name'] ?? 'admin'));
        return $username . '_' . $module . '_' . date_i18n('Y-m-d_H-i-s');
    }

    private function getFilters() {
        return [
            'search' => sanitize_text_field($_GET['search'] ?? ''),
            'role_id' => sanitize_text_field($_GET['role_id'] ?? ''),
            'trang_thai' => sanitize_text_field($_GET['trang_thai'] ?? ''),
        ];
    }

    private function buildUrlParams($filters) {
        $params = '';
        foreach ($filters as $key => $value) {
            if ($value !== '') {
                $params .= '&' . $key . '=' . urlencode($value);
            }
        }
        return $params;
    }

    private function getStaffPostData($isCreate) {
        $data = [
            'email' => sanitize_email($_POST['email'] ?? ''),
            'ho_ten' => sanitize_text_field($_POST['ho_ten'] ?? ''),
            'que_quan' => sanitize_text_field($_POST['que_quan'] ?? ''),
            'sdt' => sanitize_text_field($_POST['sdt'] ?? ''),
            'role_id' => (int) ($_POST['role_id'] ?? 2),
            'trang_thai' => (int) ($_POST['trang_thai'] ?? 1),
        ];

        if ($isCreate || !empty($_POST['mat_khau'])) {
            $data['mat_khau'] = password_hash((string) ($_POST['mat_khau'] ?? '123456'), PASSWORD_DEFAULT);
        }

        return $data;
    }
}
