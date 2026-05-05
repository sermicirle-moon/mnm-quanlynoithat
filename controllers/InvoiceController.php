<?php
class InvoiceController {
    public function index() {
        if (!isset($_SESSION['qln_user_id'])) {
            wp_redirect(admin_url('admin.php?page=qln-login')); exit;
        }
        $repo = new InvoiceRepository();
        $invoices = $repo->getAll();
        include plugin_dir_path(__FILE__) . '../views/NVKD/invoice-view.php';
    }
}