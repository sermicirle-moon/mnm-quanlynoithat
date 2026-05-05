<?php if(isset($_SESSION['qln_success'])): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['qln_error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - TimberFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-6">

    <script src="https://cdn.tailwindcss.com"></script>

<div class="flex items-center justify-center bg-gray-50" style="min-height: calc(100vh - 50px); margin: -10px -20px 0 -20px;">
    <div class="bg-white rounded-2xl shadow-xl flex w-full max-w-5xl h-[600px] overflow-hidden m-6">
        
        <div class="w-1/2 bg-gradient-to-br from-orange-400 via-amber-600 to-slate-900 p-10 flex flex-col justify-end relative text-white">
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    </div>
                    <span class="font-bold text-xl">TimberFlow</span>
                </div>
                <p class="text-white/80 text-sm max-w-xs leading-relaxed">Quản lý chuỗi cung ứng và sản xuất nội thất tinh hoa với độ chính xác tuyệt đối.</p>
            </div>
            <div class="flex gap-8 border-t border-white/20 pt-6">
                <div>
                    <h4 class="font-bold text-xl">1.2k+</h4>
                    <p class="text-white/60 text-xs mt-1">Dự án hoàn thành</p>
                </div>
                <div>
                    <h4 class="font-bold text-xl">98%</h4>
                    <p class="text-white/60 text-xs mt-1">Đúng hạn giao hàng</p>
                </div>
            </div>
        </div>

        <div class="w-1/2 p-12 flex flex-col justify-center relative">
            <h2 class="text-2xl font-semibold text-gray-800">Chào mừng trở lại</h2>
            <p class="text-sm text-gray-500 mt-2 mb-8">Vui lòng đăng nhập để quản lý hệ thống TimberFlow.</p>

            <?php if(isset($_SESSION['qln_success'])): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-medium border border-green-200"><?php echo $_SESSION['qln_success']; unset($_SESSION['qln_success']); ?></div>
            <?php endif; ?>

            <?php if(isset($_SESSION['qln_error'])): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-medium border border-red-200"><?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">
                <input type="hidden" name="qln_action" value="login">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Địa chỉ Email</label>
                    <input type="email" name="email" placeholder="admin@timberflow.vn" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-green-700 focus:ring-1 focus:ring-green-700 transition" required>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Mật khẩu</label>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-green-700 focus:ring-1 focus:ring-green-700 transition" required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="rounded border-gray-300 text-green-700 focus:ring-green-700">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="w-full bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-3 rounded-lg transition duration-200 flex justify-center items-center gap-2">
                    Đăng nhập 
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-8">Chưa có tài khoản? <a href="?page=qln-register" class="text-green-700 font-medium hover:underline">Đăng ký ngay</a></p>
        </div>
    </div>
</div>
</body>
</html>