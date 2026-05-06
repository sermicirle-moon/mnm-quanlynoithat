<?php if (!defined('ABSPATH')) exit; ?>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .notice, .updated, .error, .update-nag { display: none !important; }
    #wpfooter { display: none !important; }
    #wpbody-content { padding-bottom: 0 !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
</style>

<div class="flex bg-gray-50 rounded-xl overflow-hidden shadow-md border border-gray-200 mt-4 mr-4" style="height: calc(100vh - 70px);">
    
    <?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0 bg-white overflow-y-auto custom-scrollbar">
        <?php 
            if (isset($view_content) && file_exists($view_content)) {
                include $view_content; 
            } else {
                echo "Không tìm thấy view con!";
            }
        ?>
    </main>
</div>