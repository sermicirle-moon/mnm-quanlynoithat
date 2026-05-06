<?php
if (!defined('ABSPATH')) exit;

$role_id = $_SESSION['qln_role_id'] ?? 0;
$user_name = $_SESSION['qln_user_name'] ?? 'Người dùng';
$current_page = $_GET['page'] ?? '';

// Cấu hình Menu theo Role (1: Admin, 2: Sale, 3: Warehouse)
$menus = [
    ['title' => 'Dashboard',    'slug' => 'qln-dashboard',  'icon' => 'fa-chart-pie',             'roles' => [1, 2, 3]],
    ['title' => 'Product',     'slug' => 'qln-products',   'icon' => 'fa-box',                   'roles' => [1, 2, 3]],
    ['title' => 'Customers',   'slug' => 'qln-customers',  'icon' => 'fa-users',                 'roles' => [1, 2]],
    ['title' => 'Suppliers', 'slug' => 'qln-suppliers',  'icon' => 'fa-truck-field',           'roles' => [1, 3]],
    ['title' => 'Invoices',      'slug' => 'qln-invoices',   'icon' => 'fa-file-invoice-dollar',   'roles' => [1, 2]],
    ['title' => 'Stock In',     'slug' => 'qln-nhap-hang',  'icon' => 'fa-file-import',           'roles' => [1, 3]],
    ['title' => 'Stock Out',     'slug' => 'qln-stock-out',  'icon' => 'fa-file-export',           'roles' => [1, 3]],
    ['title' => 'Staff',    'slug' => 'qln-staff',      'icon' => 'fa-user-tie',              'roles' => [1]],
    ['title' => 'Reports',      'slug' => 'qln-reports',    'icon' => 'fa-chart-line',            'roles' => [1]],
];
?>
 <aside class="w-64 bg-slate-900 text-white flex flex-col shrink-0 z-10">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center shadow-lg shadow-green-600/30">
                    <i class="fa-solid fa-couch text-sm"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">TimberFlow</span>
            </div>
        </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 custom-scrollbar">
        <ul class="space-y-2">
            <?php foreach ($menus as $menu): ?>
                <?php if (in_array($role_id, $menu['roles'])): ?>
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=' . $menu['slug']); ?>" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?php echo ($current_page === $menu['slug']) ? 'bg-green-600 text-white shadow-lg shadow-green-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                            <i class="fa-solid <?php echo $menu['icon']; ?> w-5 text-center text-lg"></i>
                            <span class="font-medium"><?php echo $menu['title']; ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="p-4 border-t border-slate-800 bg-slate-900/50">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
            <div class="p-4 border-t border-slate-800 shrink-0">
            <a href="?page=qln-logout" class="flex items-center gap-3 px-4 py-2.5 text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition duration-200">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Thoát hệ thống
            </a>
        </div>
        </div>
    </div>
</aside>