<?php
class AuthController {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qln_action'])) {
            if ($_POST['qln_action'] === 'login') {
                $this->login();
            } elseif ($_POST['qln_action'] === 'register') {
                $this->register();
            }
        }
        
        // Xử lý đăng xuất
        if (isset($_GET['page']) && $_GET['page'] == 'qln-logout') {
            unset($_SESSION['qln_user_id']);
            unset($_SESSION['qln_user_name']);
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }
    }

    private function login() {
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $user = $this->userRepo->findByEmail($email);

        if ($user && password_verify($password, $user->mat_khau)) {
            // Lưu session
            $_SESSION['qln_user_id'] = $user->id;
            $_SESSION['qln_user_name'] = $user->ho_ten;
            $_SESSION['qln_role_id'] = $user->role_id;
            
            wp_redirect(admin_url('admin.php?page=qln-dashboard'));
            exit;
        } else {
            $_SESSION['qln_error'] = "Email hoặc mật khẩu không đúng!";
        }
    }

    private function register() {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa pass
        $que_quan = sanitize_text_field($_POST['que_quan']);
        if ($this->userRepo->findByEmail($email)) {
            $_SESSION['qln_error'] = "Email này đã được sử dụng!";
            return;
        }

        $newUser = new User([
            'email' => $email,
            'mat_khau' => $password,
            'ho_ten' => $name,
            'que_quan' => $que_quan,
            'role_id' => 2 // NV
        ]);

        if ($this->userRepo->create($newUser)) {
            $_SESSION['qln_success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            wp_redirect(admin_url('admin.php?page=qln-login'));
            exit;
        }
    }
}