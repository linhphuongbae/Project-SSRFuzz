<?php
include 'includes/common.php';

// Xử lý đăng nhập (giả lập - không check database thật)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Giả lập đăng nhập thành công
    if ($email && $password) {
        $_SESSION['user'] = [
            'email' => $email,
            'name' => 'Khách hàng',
            'role' => 'user'
        ];
        
        // Nếu admin thì chuyển về dashboard
        if ($email === 'admin@myshop.vn') {
            $_SESSION['user']['role'] = 'admin';
            $_SESSION['user']['name'] = 'Admin';
            header('Location: dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - SSRFuzzShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Arial, sans-serif;
}

.login-page {
    min-height: 100vh;
    display: flex;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    position: relative;
    overflow: hidden;
}

.login-page::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    top: -250px;
    right: -100px;
    animation: float 6s ease-in-out infinite;
}

.login-page::after {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    bottom: -150px;
    left: -100px;
    animation: float 8s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.login-container {
    margin: auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: white;
    border-radius: 24px;
    overflow: hidden;
    max-width: 1000px;
    width: 90%;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.login-left {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
    position: relative;
}

.login-left::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="150" cy="100" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="100" cy="150" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.5;
}

.brand-logo {
    font-size: 48px;
    margin-bottom: 20px;
}

.login-left h1 {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.login-left p {
    font-size: 18px;
    opacity: 0.95;
    line-height: 1.6;
    margin-bottom: 30px;
}

.features-list {
    list-style: none;
    margin-top: 30px;
}

.features-list li {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    font-size: 16px;
}

.features-list li::before {
    content: '';
    background: rgba(255, 255, 255, 0.2);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
}

.features-list li:nth-child(1)::before { content: '\f291'; } /* fas fa-shopping-bag */
.features-list li:nth-child(2)::before { content: '\f02b'; } /* fas fa-tag */
.features-list li:nth-child(3)::before { content: '\f0d1'; } /* fas fa-truck */
.features-list li:nth-child(4)::before { content: '\f4ad'; } /* fas fa-headset */

.login-right {
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-header {
    margin-bottom: 40px;
}

.login-header h2 {
    font-size: 32px;
    color: #1a1a1a;
    margin-bottom: 10px;
    font-weight: 700;
}

.login-header p {
    color: #666;
    font-size: 15px;
}

.login-form {
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group label i {
    color: #e37b58;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 16px;
}

.form-group input {
    width: 100%;
    padding: 16px 18px 16px 50px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s;
    background: #f8f9fa;
}

.form-group input:focus {
    outline: none;
    border-color: #e37b58;
    background: white;
    box-shadow: 0 0 0 4px rgba(227, 123, 88, 0.1);
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
}

.remember-me input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.remember-me label {
    color: #666;
    font-size: 14px;
    cursor: pointer;
}

.forgot-password a {
    color: #e37b58;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.forgot-password a:hover {
    text-decoration: underline;
}

.login-btn {
    width: 100%;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    border: none;
    padding: 18px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(227, 123, 88, 0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(227, 123, 88, 0.5);
}

.login-btn:active {
    transform: translateY(0);
}

.divider {
    text-align: center;
    margin: 30px 0;
    position: relative;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e8e8e8;
}

.divider span {
    background: white;
    padding: 0 15px;
    color: #999;
    font-size: 14px;
    position: relative;
    z-index: 1;
}

.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 30px;
}

.social-btn {
    padding: 14px;
    border: 2px solid #e8e8e8;
    background: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #333;
}

.social-btn svg {
    width: 20px;
    height: 20px;
}

.social-btn:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.google-btn:hover {
    border-color: #4285F4;
}

.facebook-btn:hover {
    border-color: #1877F2;
}

.signup-link {
    text-align: center;
    color: #666;
    font-size: 15px;
}

.signup-link a {
    color: #e37b58;
    text-decoration: none;
    font-weight: 700;
}

.signup-link a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .login-container {
        grid-template-columns: 1fr;
        margin: 20px;
    }
    
    .login-left {
        display: none;
    }
    
    .login-right {
        padding: 40px 30px;
    }
}
</style>

<div class="login-page">
    <div class="login-container">
        <!-- Left side - Brand & Features -->
        <div class="login-left">
            <div class="brand-logo">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h1>SSRFuzzShop<br/>E-Commerce</h1>
            <p>Nền tảng mua sắm trực tuyến hàng đầu với hàng ngàn sản phẩm chất lượng</p>
            
            <ul class="features-list">
                <li>Sản phẩm đa dạng & chất lượng</li>
                <li>Giá cả cạnh tranh nhất thị trường</li>
                <li>Giao hàng nhanh chóng toàn quốc</li>
                <li>Hỗ trợ khách hàng 24/7</li>
            </ul>
        </div>
        
        <!-- Right side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h2>Chào mừng trở lại!</h2>
                <p>Đăng nhập để tiếp tục mua sắm</p>
            </div>
            
            <div class="demo-info" style="background: linear-gradient(135deg, #e3f2fd 0%, #f0f7ff 100%); border-left: 4px solid #2196f3; padding: 18px; margin-bottom: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(33, 150, 243, 0.1);">
                <h4 style="color: #1976d2; margin-bottom: 12px; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle"></i> Demo Mode - Hướng dẫn đăng nhập
                </h4>
                <p style="color: #555; font-size: 13px; margin: 6px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-user" style="color: #2196f3; width: 16px;"></i>
                    <strong>User:</strong> Nhập email bất kỳ
                </p>
                <p style="color: #555; font-size: 13px; margin: 6px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-user-shield" style="color: #2196f3; width: 16px;"></i>
                    <strong>Admin:</strong> admin@myshop.vn
                </p>
                <p style="color: #555; font-size: 13px; margin: 6px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-key" style="color: #2196f3; width: 16px;"></i>
                    <strong>Password:</strong> Nhập gì cũng được
                </p>
            </div>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-at"></i></span>
                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-key"></i></span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#">Quên mật khẩu?</a>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Đăng nhập</button>
            </form>
            
            <div class="divider">
                <span>Hoặc đăng nhập với</span>
            </div>
            
            <div class="social-login">
                <button type="button" class="social-btn google-btn">
                    <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z"/>
                        <path fill="#34A853" d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z"/>
                        <path fill="#FBBC05" d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z"/>
                        <path fill="#EA4335" d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.428 0 9.003 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z"/>
                    </svg>
                    Google
                </button>
                <button type="button" class="social-btn facebook-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </button>
            </div>
            
            <div class="signup-link">
                Chưa có tài khoản? <a href="signup.php">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>

<!-- Back to Home -->
<a href="index.php" style="position: fixed; top: 20px; left: 20px; color: white; text-decoration: none; font-size: 16px; font-weight: 600; z-index: 1000; background: rgba(0,0,0,0.2); padding: 12px 20px; border-radius: 10px; backdrop-filter: blur(10px); transition: all 0.3s;">
    <i class="fas fa-arrow-left"></i> Về trang chủ
</a>

</body>
</html>
