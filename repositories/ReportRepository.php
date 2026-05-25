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
    }

    public function getReportData(): array {
        $summary = $this->getSummary();

        return [
            'summary' => $summary,
            'recent_invoices' => $this->getRecentInvoices(8),
            'category_sales' => $this->getCategorySales(),
            'revenue_trend' => $this->getRevenueTrendByMonth(),
            'monthly_comparison' => $this->getMonthlyComparison(),
        ];
    }

    public function getSummary(): array {
        global $wpdb;

        $totalRevenue = (float) $wpdb->get_var("SELECT COALESCE(SUM(tong_tien), 0) FROM {$this->table_invoice} WHERE trang_thai = 'Hoàn thành'");
        $stockInCost = (float) $wpdb->get_var("SELECT COALESCE(SUM(tong_tien), 0) FROM {$this->table_stock_in} WHERE trang_thai = 'Đã nhập kho'");
        $inventoryQuantity = (int) $wpdb->get_var("SELECT COALESCE(SUM(so_luong_ton), 0) FROM {$this->table_product}");
        $inventoryValue = (float) $wpdb->get_var("SELECT COALESCE(SUM(so_luong_ton * gia_nhap), 0) FROM {$this->table_product}");
        $profit = $totalRevenue - $stockInCost;

        return [
            'total_revenue' => $totalRevenue,
            'completed_invoice_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_invoice} WHERE trang_thai = 'Hoàn thành'"),
            'invoice_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_invoice}"),
            'stock_in_cost' => $stockInCost,
            'inventory_quantity' => $inventoryQuantity,
            'inventory_value' => $inventoryValue,
            'gross_profit' => $profit,
            'profit_rate' => $totalRevenue > 0 ? round(($profit / $totalRevenue) * 100, 1) : 0,
            'product_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_product}"),
            'customer_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_customer}"),
            'supplier_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_supplier} WHERE trang_thai = 1"),
            'staff_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_user} WHERE trang_thai = 1"),
            'stock_in_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_stock_in}"),
            'stock_out_count' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_stock_out}"),
        ];
    }

    public function getRecentInvoices(int $limit = 5): array {
        global $wpdb;
        $limit = max(1, absint($limit));
        $sql = "SELECT ma_hd, tong_tien, trang_thai, ngay_tao FROM {$this->table_invoice} ORDER BY ngay_tao DESC LIMIT {$limit}";
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    public function getCategorySales(): array {
        global $wpdb;

        $detailCount = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_invoice_detail}");
        if ($detailCount > 0) {
            $sql = "SELECT COALESCE(l.ten_loai, 'Chưa phân loại') AS category, COALESCE(SUM(hdct.thanh_tien), 0) AS revenue
                    FROM {$this->table_invoice_detail} hdct
                    INNER JOIN {$this->table_invoice} hd ON hd.id = hdct.hoa_don_id AND hd.trang_thai = 'Hoàn thành'
                    LEFT JOIN {$this->table_product} sp ON sp.id = hdct.san_pham_id
                    LEFT JOIN {$this->table_category} l ON l.id_loai = sp.id_loai
                    GROUP BY l.id_loai, l.ten_loai
                    ORDER BY revenue DESC";
        } else {
            $sql = "SELECT COALESCE(l.ten_loai, 'Chưa phân loại') AS category, COALESCE(SUM(sp.so_luong_ton * sp.gia_ban), 0) AS revenue
                    FROM {$this->table_product} sp
                    LEFT JOIN {$this->table_category} l ON l.id_loai = sp.id_loai
                    GROUP BY l.id_loai, l.ten_loai
                    ORDER BY revenue DESC";
        }

        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    public function getRevenueTrendByMonth(): array {
        global $wpdb;
        $sql = "SELECT DATE_FORMAT(ngay_tao, '%Y-%m') AS month_key, DATE_FORMAT(ngay_tao, 'Tháng %m/%Y') AS label, COALESCE(SUM(tong_tien), 0) AS revenue
                FROM {$this->table_invoice}
                WHERE trang_thai = 'Hoàn thành'
                GROUP BY month_key, label
                ORDER BY month_key ASC";
        return $wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    public function getMonthlyComparison(): array {
        global $wpdb;
        $sql = "SELECT month_key, MAX(label) AS label, SUM(cost) AS cost, SUM(revenue) AS revenue
                FROM (
                    SELECT DATE_FORMAT(ngay_nhap, '%Y-%m') AS month_key, DATE_FORMAT(ngay_nhap, 'Tháng %m/%Y') AS label, COALESCE(SUM(tong_tien), 0) AS cost, 0 AS revenue
                    FROM {$this->table_stock_in}
                    WHERE trang_thai = 'Đã nhập kho'
                    GROUP BY month_key, label
                    UNION ALL
                    SELECT DATE_FORMAT(ngay_tao, '%Y-%m') AS month_key, DATE_FORMAT(ngay_tao, 'Tháng %m/%Y') AS label, 0 AS cost, COALESCE(SUM(tong_tien), 0) AS revenue
                    FROM {$this->table_invoice}
                    WHERE trang_thai = 'Hoàn thành'
                    GROUP BY month_key, label
                ) monthly
                GROUP BY month_key
                ORDER BY month_key ASC";

        $rows = $wpdb->get_results($sql, ARRAY_A) ?: [];
        foreach ($rows as &$row) {
            $row['cost'] = (float) $row['cost'];
            $row['revenue'] = (float) $row['revenue'];
            $row['profit'] = $row['revenue'] - $row['cost'];
            $row['profit_rate'] = $row['revenue'] > 0 ? round(($row['profit'] / $row['revenue']) * 100, 1) : 0;
        }

        return $rows;
    }
}
