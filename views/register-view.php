<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - TimberFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-6">
    <script src="https://cdn.tailwindcss.com"></script>

<div class="flex items-center justify-center bg-gray-50" style="min-height: calc(100vh - 50px); margin: -10px -20px 0 -20px;">
    <div class="bg-white rounded-2xl shadow-xl flex w-full max-w-6xl h-[750px] overflow-hidden m-6">
        
        <div class="w-2/5 bg-[#0a5c36] p-10 flex flex-col justify-center text-white">
            <h1 class="text-3xl font-bold mb-4">Gia nhập TimberFlow</h1>
            <p class="text-white/80 leading-relaxed">Khởi tạo tài khoản để bắt đầu quản lý quy trình sản xuất và chuỗi cung ứng nội thất chuyên nghiệp.</p>
            <div class="mt-8 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="bg-white/10 p-2 rounded-full">✓</span>
                    <span>Quản lý kho hàng thông minh</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-white/10 p-2 rounded-full">✓</span>
                    <span>Báo cáo hiệu suất thời gian thực</span>
                </div>
            </div>
        </div>

        <div class="w-3/5 p-12 overflow-y-auto bg-white">
            <h2 class="text-2xl font-semibold text-gray-800">Đăng ký tài khoản</h2>
            <p class="text-sm text-gray-500 mt-1 mb-6">Vui lòng điền đầy đủ các thông tin nhân sự bên dưới.</p>

            <?php if(isset($_SESSION['qln_error'])): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm font-medium border border-red-200">
                    <?php echo $_SESSION['qln_error']; unset($_SESSION['qln_error']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-2 gap-4">
                <input type="hidden" name="qln_action" value="register">
                
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Họ và tên nhân viên</label>
                    <input type="text" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Địa chỉ Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Quê quán</label>
                    <input type="text" name="que_quan" placeholder="Ví dụ: Hà Nội" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Chức vụ</label>
                    <select name="chuc_vu" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                        <option value="Nhân viên kinh doanh">Nhân viên kinh doanh</option>
                        <option value="Quản lý kho">Quản lý kho</option>
                        
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Mật khẩu</label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-green-700 outline-none transition" required>
                </div>

                <div class="col-span-2 mt-4">
                    <button type="submit" class="w-full bg-[#0a5c36] hover:bg-green-800 text-white font-medium py-3 rounded-lg shadow-lg shadow-green-900/20 transition">
                        Hoàn tất đăng ký thành viên
                    </button>
                    <p class="text-center text-sm text-gray-500 mt-6">Đã có tài khoản? <a href="?page=qln-login" class="text-green-700 font-medium hover:underline">Đăng nhập ngay</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>