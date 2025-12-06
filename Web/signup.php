<?php
include 'includes/common.php';

// Xử lý đăng ký (giả lập)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Giả lập đăng ký thành công
    if ($name && $email && $password) {
        $_SESSION['user'] = [
            'email' => $email,
            'name' => $name,
            'role' => 'user'
        ];
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - SSRFuzzShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Arial, sans-serif;
}

.signup-page {
    min-height: 100vh;
    display: flex;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    position: relative;
    overflow: hidden;
}

.signup-page::before {
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

.signup-page::after {
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

.signup-container {
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

.signup-left {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: white;
    position: relative;
}

.signup-left::before {
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

.signup-left h1 {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.signup-left p {
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
    content: '✔';
    background: rgba(255, 255, 255, 0.2);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.signup-right {
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.signup-header {
    margin-bottom: 40px;
}

.signup-header h2 {
    font-size: 32px;
    color: #1a1a1a;
    margin-bottom: 10px;
    font-weight: 700;
}

.signup-header p {
    color: #666;
    font-size: 15px;
}

.signup-form {
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
    font-size: 20px;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.terms {
    margin-bottom: 25px;
}

.terms label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    color: #666;
    font-size: 14px;
}

.terms input[type="checkbox"] {
    margin-top: 3px;
    cursor: pointer;
}

.terms a {
    color: #e37b58;
    text-decoration: none;
}

.terms a:hover {
    text-decoration: underline;
}

.signup-btn {
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

.signup-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(227, 123, 88, 0.5);
}

.signup-btn:active {
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

.login-link {
    text-align: center;
    color: #666;
    font-size: 15px;
}

.login-link a {
    color: #e37b58;
    text-decoration: none;
    font-weight: 700;
}

.login-link a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .signup-container {
        grid-template-columns: 1fr;
        margin: 20px;
    }
    
    .signup-left {
        display: none;
    }
    
    .signup-right {
        padding: 40px 30px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="signup-page">
    <div class="signup-container">
        <!-- Left side - Brand & Features -->
        <div class="signup-left">
            <div class="brand-logo">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h1>Tham Gia<br/>SSRFuzzShop</h1>
            <p>Đăng ký ngay để trải nghiệm mua sắm tuyệt vời</p>
            
            <ul class="features-list">
                <li>Miễn phí vận chuyển đơn hàng đầu tiên</li>
                <li>Ưu đãi độc quyền cho thành viên mới</li>
                <li>Tích điểm đổi quà hấp dẫn</li>
                <li>Hỗ trợ tận tình 24/7</li>
            </ul>
        </div>
        
        <!-- Right side - Signup Form -->
        <div class="signup-right">
            <div class="signup-header">
                <h2>Tạo tài khoản mới</h2>
                <p>Chỉ mất 2 phút để bắt đầu</p>
            </div>
        
        <form method="POST" class="signup-form">
            <div class="form-group">
                <label for="name">Họ và tên</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input type="text" id="name" name="name" placeholder="Nguyễn Văn A" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon">@</span>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm">Confirm</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="confirm" name="confirm" placeholder="••••••••" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-phone"></i></span>
                    <input type="tel" id="phone" name="phone" placeholder="0123456789">
                </div>
            </div>
            
            <div class="terms">
                <label>
                    <input type="checkbox" required>
                    <span>Tôi đồng ý với <a href="#">Điều khoản</a> và <a href="#">Chính sách</a></span>
                </label>
            </div>
            
            <button type="submit" class="signup-btn">Đăng ký</button>
        </form>
        
        <div class="divider">
            <span>Hoặc đăng ký với</span>
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
        
        <div class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
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
