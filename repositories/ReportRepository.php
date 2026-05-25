<?php
class ReportRepository {
    private string $table_invoice;
    private string $table_invoice_detail;
    private string $table_product;
    private string $table_category;
    private string $table_customer;
    private string $table_supplier;
    private string $table_user;
    private string $table_stock_in;
    private string $table_stock_out;
    private string $table_carrier;

    public function __construct() {
        global $wpdb;
        $this->table_invoice = $wpdb->prefix . 'qln_hoa_don';
        $this->table_invoice_detail = $wpdb->prefix . 'qln_hoa_don_chi_tiet';
        $this->table_product = $wpdb->prefix . 'qln_san_pham';
        $this->table_category = $wpdb->prefix . 'qln_loai_sp';
        $this->table_customer = $wpdb->prefix . 'qln_khach_hang';
        $this->table_supplier = $wpdb->prefix . 'qln_nha_cung_cap';
        $this->table_user = $wpdb->prefix . 'qln_users';
        $this->table_stock_in = $wpdb->prefix . 'qln_phieu_nhap';
        $this->table_stock_out = $wpdb->prefix . 'qln_phieu_xuat';
        $this->table_carrier = $wpdb->prefix . 'qln_nha_van_chuyen';
    }

    public function getReportData(array $filters = []): array {
        $summary = $this->getSummary($filters);

        return [
            'summary' => $summary,
            'filters' => $filters,
            'recent_invoices' => $this->getRecentInvoices(8, $filters),
            'category_sales' => $this->getCategorySales($filters),
            'revenue_trend' => $this->getRevenueTrendByMonth($filters),
            'monthly_comparison' => $this->getMonthlyComparison($filters),
            'top_products' => $this->getTopProducts($filters),
            'top_customers' => $this->getTopCustomers($filters),
            'invoice_status_breakdown' => $this->getInvoiceStatusBreakdown($filters),
            'shipping_status_breakdown' => $this->getShippingStatusBreakdown($filters),
            'stock_alerts' => $this->getStockAlerts(),
            'supplier_purchasing' => $this->getSupplierPurchasing($filters),
            'stock_out_status_breakdown' => $this->getStockOutStatusBreakdown($filters),
            'carrier_shipping_summary' => $this->getCarrierShippingSummary($filters),
        ];
    }

    public function getSummary(array $filters = []): array {
        global $wpdb;
        [$invoiceWhere, $invoiceArgs] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        [$stockInWhere, $stockInArgs] = $this->buildDateWhere('ngay_nhap', $filters, 'AND');
        [$stockOutWhere, $stockOutArgs] = $this->buildDateWhere('ngay_xuat', $filters, 'AND');

        $totalRevenue = (float) $this->getVar("SELECT COALESCE(SUM(tong_tien), 0) FROM {$this->table_invoice} WHERE trang_thai = 'Hoàn thành' {$invoiceWhere}", $invoiceArgs);
        $stockInCost = (float) $this->getVar("SELECT COALESCE(SUM(tong_tien), 0) FROM {$this->table_stock_in} WHERE trang_thai = 'Đã nhập kho' {$stockInWhere}", $stockInArgs);
        $stockOutFees = (float) $this->getVar("SELECT COALESCE(SUM(phi_van_chuyen), 0) FROM {$this->table_stock_out} WHERE 1=1 {$stockOutWhere}", $stockOutArgs);
        $inventoryQuantity = (int) $wpdb->get_var("SELECT COALESCE(SUM(so_luong_ton), 0) FROM {$this->table_product}");
        $inventoryValue = (float) $wpdb->get_var("SELECT COALESCE(SUM(so_luong_ton * gia_nhap), 0) FROM {$this->table_product}");
        $profit = $totalRevenue - $stockInCost;

        return [
            'total_revenue' => $totalRevenue,
            'completed_invoice_count' => (int) $this->getVar("SELECT COUNT(*) FROM {$this->table_invoice} WHERE trang_thai = 'Hoàn thành' {$invoiceWhere}", $invoiceArgs),
            'invoice_count' => (int) $this->getVar("SELECT COUNT(*) FROM {$this->table_invoice} WHERE 1=1 {$invoiceWhere}", $invoiceArgs),
            'stock_in_cost' => $stockInCost,
            'stock_out_fees' => $stockOutFees,
            'inventory_quantity' => $inventoryQuantity,
            'inventory_value' => $inventoryValue,
            'gross_profit' => $profit,
            'profit_rate' => $totalRevenue > 0 ? round(($profit / $totalRevenue) * 100, 1) : 0,
            'product_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_product}"),
            'customer_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_customer}"),
            'supplier_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_supplier} WHERE trang_thai = 1"),
            'staff_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_user} WHERE trang_thai = 1"),
            'stock_in_count' => (int) $this->getVar("SELECT COUNT(*) FROM {$this->table_stock_in} WHERE 1=1 {$stockInWhere}", $stockInArgs),
            'stock_out_count' => (int) $this->getVar("SELECT COUNT(*) FROM {$this->table_stock_out} WHERE 1=1 {$stockOutWhere}", $stockOutArgs),
        ];
    }

    public function getRecentInvoices(int $limit = 5, array $filters = []): array {
        $limit = max(1, absint($limit));
        [$where, $args] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        return $this->getResults("SELECT ma_hd, tong_tien, trang_thai, ngay_tao FROM {$this->table_invoice} WHERE 1=1 {$where} ORDER BY ngay_tao DESC LIMIT {$limit}", $args);
    }

    public function getCategorySales(array $filters = []): array {
        global $wpdb;
        [$where, $args] = $this->buildDateWhere('hd.ngay_tao', $filters, 'AND');
        $detailCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_invoice_detail}");

        if ($detailCount > 0) {
            return $this->getResults("SELECT COALESCE(l.ten_loai, 'Chưa phân loại') AS category, COALESCE(SUM(hdct.thanh_tien), 0) AS revenue
                    FROM {$this->table_invoice_detail} hdct
                    INNER JOIN {$this->table_invoice} hd ON hd.id = hdct.hoa_don_id AND hd.trang_thai = 'Hoàn thành' {$where}
                    LEFT JOIN {$this->table_product} sp ON sp.id = hdct.san_pham_id
                    LEFT JOIN {$this->table_category} l ON l.id_loai = sp.id_loai
                    GROUP BY l.id_loai, l.ten_loai
                    ORDER BY revenue DESC", $args);
        }

        return $wpdb->get_results("SELECT COALESCE(l.ten_loai, 'Chưa phân loại') AS category, COALESCE(SUM(sp.so_luong_ton * sp.gia_ban), 0) AS revenue
                    FROM {$this->table_product} sp
                    LEFT JOIN {$this->table_category} l ON l.id_loai = sp.id_loai
                    GROUP BY l.id_loai, l.ten_loai
                    ORDER BY revenue DESC", ARRAY_A) ?: [];
    }

    public function getRevenueTrendByMonth(array $filters = []): array {
        [$where, $args] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        return $this->getResults("SELECT DATE_FORMAT(ngay_tao, '%Y-%m') AS month_key, DATE_FORMAT(ngay_tao, 'Tháng %m/%Y') AS label, COALESCE(SUM(tong_tien), 0) AS revenue
                FROM {$this->table_invoice}
                WHERE trang_thai = 'Hoàn thành' {$where}
                GROUP BY month_key, label
                ORDER BY month_key ASC", $args);
    }

    public function getMonthlyComparison(array $filters = []): array {
        [$stockInWhere, $stockInArgs] = $this->buildDateWhere('ngay_nhap', $filters, 'AND');
        [$invoiceWhere, $invoiceArgs] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        $args = array_merge($stockInArgs, $invoiceArgs);
        $rows = $this->getResults("SELECT month_key, MAX(label) AS label, SUM(cost) AS cost, SUM(revenue) AS revenue
                FROM (
                    SELECT DATE_FORMAT(ngay_nhap, '%Y-%m') AS month_key, DATE_FORMAT(ngay_nhap, 'Tháng %m/%Y') AS label, COALESCE(SUM(tong_tien), 0) AS cost, 0 AS revenue
                    FROM {$this->table_stock_in}
                    WHERE trang_thai = 'Đã nhập kho' {$stockInWhere}
                    GROUP BY month_key, label
                    UNION ALL
                    SELECT DATE_FORMAT(ngay_tao, '%Y-%m') AS month_key, DATE_FORMAT(ngay_tao, 'Tháng %m/%Y') AS label, 0 AS cost, COALESCE(SUM(tong_tien), 0) AS revenue
                    FROM {$this->table_invoice}
                    WHERE trang_thai = 'Hoàn thành' {$invoiceWhere}
                    GROUP BY month_key, label
                ) monthly
                GROUP BY month_key
                ORDER BY month_key ASC", $args);

        foreach ($rows as &$row) {
            $row['cost'] = (float) $row['cost'];
            $row['revenue'] = (float) $row['revenue'];
            $row['profit'] = $row['revenue'] - $row['cost'];
            $row['profit_rate'] = $row['revenue'] > 0 ? round(($row['profit'] / $row['revenue']) * 100, 1) : 0;
        }

        return $rows;
    }

    public function getTopProducts(array $filters = [], int $limit = 8): array {
        global $wpdb;
        $limit = max(1, absint($limit));
        [$where, $args] = $this->buildDateWhere('hd.ngay_tao', $filters, 'AND');
        $detailCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_invoice_detail}");

        if ($detailCount > 0) {
            return $this->getResults("SELECT sp.ma_sp, sp.ten_sp, COALESCE(SUM(hdct.so_luong), 0) AS quantity_sold, COALESCE(SUM(hdct.thanh_tien), 0) AS revenue, COUNT(DISTINCT hd.id) AS order_count
                    FROM {$this->table_invoice_detail} hdct
                    INNER JOIN {$this->table_invoice} hd ON hd.id = hdct.hoa_don_id AND hd.trang_thai = 'Hoàn thành' {$where}
                    LEFT JOIN {$this->table_product} sp ON sp.id = hdct.san_pham_id
                    GROUP BY sp.id, sp.ma_sp, sp.ten_sp
                    ORDER BY revenue DESC, quantity_sold DESC
                    LIMIT {$limit}", $args);
        }

        return $wpdb->get_results("SELECT ma_sp, ten_sp, GREATEST(0, 30 - so_luong_ton) AS quantity_sold, (GREATEST(0, 30 - so_luong_ton) * gia_ban) AS revenue, 0 AS order_count
                FROM {$this->table_product}
                ORDER BY revenue DESC
                LIMIT {$limit}", ARRAY_A) ?: [];
    }

    public function getTopCustomers(array $filters = [], int $limit = 8): array {
        [$where, $args] = $this->buildDateWhere('hd.ngay_tao', $filters, 'AND');
        return $this->getResults("SELECT kh.ma_kh, kh.ten_kh, kh.sdt, COUNT(hd.id) AS invoice_count, COALESCE(SUM(hd.tong_tien), 0) AS total_spent, MAX(hd.ngay_tao) AS latest_invoice
                FROM {$this->table_invoice} hd
                LEFT JOIN {$this->table_customer} kh ON kh.id = hd.khach_hang_id
                WHERE hd.trang_thai = 'Hoàn thành' {$where}
                GROUP BY kh.id, kh.ma_kh, kh.ten_kh, kh.sdt
                ORDER BY total_spent DESC, invoice_count DESC
                LIMIT " . max(1, absint($limit)), $args);
    }

    public function getInvoiceStatusBreakdown(array $filters = []): array {
        [$where, $args] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        return $this->getResults("SELECT COALESCE(trang_thai, 'Không rõ') AS status, COUNT(*) AS count, COALESCE(SUM(tong_tien), 0) AS amount
                FROM {$this->table_invoice}
                WHERE 1=1 {$where}
                GROUP BY trang_thai
                ORDER BY count DESC, amount DESC", $args);
    }

    public function getShippingStatusBreakdown(array $filters = []): array {
        [$where, $args] = $this->buildDateWhere('ngay_tao', $filters, 'AND');
        return $this->getResults("SELECT COALESCE(trang_thai_giao, 'Không rõ') AS status, COUNT(*) AS count, COALESCE(SUM(tong_tien), 0) AS amount
                FROM {$this->table_invoice}
                WHERE 1=1 {$where}
                GROUP BY trang_thai_giao
                ORDER BY count DESC, amount DESC", $args);
    }

    public function getStockAlerts(int $threshold = 5): array {
        global $wpdb;
        $threshold = max(0, absint($threshold));
        return $wpdb->get_results($wpdb->prepare("SELECT sp.ma_sp, sp.ten_sp, COALESCE(l.ten_loai, 'Chưa phân loại') AS category, sp.so_luong_ton, sp.gia_nhap, (sp.so_luong_ton * sp.gia_nhap) AS inventory_value, sp.trang_thai
                FROM {$this->table_product} sp
                LEFT JOIN {$this->table_category} l ON l.id_loai = sp.id_loai
                WHERE sp.so_luong_ton <= %d
                ORDER BY sp.so_luong_ton ASC, inventory_value DESC", $threshold), ARRAY_A) ?: [];
    }

    public function getSupplierPurchasing(array $filters = [], int $limit = 8): array {
        [$where, $args] = $this->buildDateWhere('pn.ngay_nhap', $filters, 'AND');
        return $this->getResults("SELECT ncc.ma_ncc, ncc.ten_ncc, COUNT(pn.id) AS receipt_count, COALESCE(SUM(pn.tong_tien), 0) AS total_purchase, MAX(pn.ngay_nhap) AS latest_purchase
                FROM {$this->table_stock_in} pn
                LEFT JOIN {$this->table_supplier} ncc ON ncc.id = pn.ncc_id
                WHERE pn.trang_thai = 'Đã nhập kho' {$where}
                GROUP BY ncc.id, ncc.ma_ncc, ncc.ten_ncc
                ORDER BY total_purchase DESC, receipt_count DESC
                LIMIT " . max(1, absint($limit)), $args);
    }

    public function getStockOutStatusBreakdown(array $filters = []): array {
        [$where, $args] = $this->buildDateWhere('ngay_xuat', $filters, 'AND');
        return $this->getResults("SELECT COALESCE(trang_thai, 'Không rõ') AS status, COUNT(*) AS count, COALESCE(SUM(phi_van_chuyen), 0) AS shipping_fee
                FROM {$this->table_stock_out}
                WHERE 1=1 {$where}
                GROUP BY trang_thai
                ORDER BY count DESC, shipping_fee DESC", $args);
    }

    public function getCarrierShippingSummary(array $filters = [], int $limit = 8): array {
        [$where, $args] = $this->buildDateWhere('px.ngay_xuat', $filters, 'AND');
        return $this->getResults("SELECT nvc.ten_nvc, nvc.loai_hinh, COUNT(px.id) AS shipment_count, COALESCE(SUM(px.phi_van_chuyen), 0) AS shipping_fee, SUM(CASE WHEN px.trang_thai = 'Đã giao' THEN 1 ELSE 0 END) AS delivered_count
                FROM {$this->table_stock_out} px
                LEFT JOIN {$this->table_carrier} nvc ON nvc.id = px.nvc_id
                WHERE 1=1 {$where}
                GROUP BY nvc.id, nvc.ten_nvc, nvc.loai_hinh
                ORDER BY shipment_count DESC, shipping_fee DESC
                LIMIT " . max(1, absint($limit)), $args);
    }

    private function buildDateWhere(string $column, array $filters, string $prefix = 'AND'): array {
        $conditions = [];
        $args = [];
        if (!empty($filters['from_date'])) {
            $conditions[] = "{$column} >= %s";
            $args[] = $filters['from_date'] . ' 00:00:00';
        }
        if (!empty($filters['to_date'])) {
            $conditions[] = "{$column} <= %s";
            $args[] = $filters['to_date'] . ' 23:59:59';
        }

        return [empty($conditions) ? '' : ' ' . $prefix . ' ' . implode(' AND ', $conditions), $args];
    }

    private function getResults(string $sql, array $args = []): array {
        global $wpdb;
        if (!empty($args)) {
            $sql = $wpdb->prepare($sql, ...$args);
        }
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    private function getVar(string $sql, array $args = []) {
        global $wpdb;
        if (!empty($args)) {
            $sql = $wpdb->prepare($sql, ...$args);
        }
        return $wpdb->get_var($sql);
    }
}
