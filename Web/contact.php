<?php
include 'includes/common.php';

// SSRF Vuln #5: Download attachment from URL (simple)
if (isset($_POST['attachment_url'])) {
    $url = $_POST['attachment_url'];
    readfile($url);
    exit;
}

// SSRF Vuln #6: Verify captcha (simple)
if (isset($_POST['verify_captcha'])) {
    $verify_url = $_POST['verify_captcha'];
    $response_lines = file($verify_url);
    echo "<pre>" . implode('', $response_lines) . "</pre>";
    exit;
}

// SSRF Vuln #7: Webhook (simple curl POST)
if (isset($_POST['webhook'])) {
    $webhook_url = $_POST['webhook'];
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=test');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    exit;
}

include 'includes/header.php';

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $messageSent = true;
}
?>

<div class="contact-page">
    <div class="contact-hero">
        <h1>Liên hệ với chúng tôi</h1>
        <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
    </div>
    
    <?php if ($messageSent): ?>
        <div class="message-success">
            <div class="success-icon"><i class="fas fa-check-circle"></i></div>
            <h2>Gửi tin nhắn thành công!</h2>
            <p>Cảm ơn bạn đã liên hệ. Chúng tôi sẽ phản hồi trong vòng 24h.</p>
            <a href="contact.php" class="main-btn">Gửi tin nhắn khác</a>
        </div>
    <?php else: ?>
        <div class="contact-container">
            <div class="contact-info">
                <h2>Thông tin liên hệ</h2>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#e37b58">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Địa chỉ</h3>
                        <p>123 Đường ABC, Quận 1, TP.HCM</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#e37b58">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Email</h3>
                        <p>support@myshop.vn</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#e37b58">
                            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Hotline</h3>
                        <p>1900 xxxx (Miễn phí)</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#e37b58">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                    <div>
                        <h3>Giờ làm việc</h3>
                        <p>T2 - CN: 8:00 - 22:00</p>
                    </div>
                </div>
                
                <div class="social-links">
                    <h3>Theo dõi chúng tôi</h3>
                    <div class="social-icons">
                        <a href="#" title="Facebook" style="background:#1877f2;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" title="Instagram" style="background:linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <a href="#" title="Twitter" style="background:#1da1f2;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" title="Youtube" style="background:#ff0000;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-container">
                <h2>Gửi tin nhắn</h2>
                <form method="POST" class="contact-form">
                    <div class="form-group">
                        <label>Họ và tên *</label>
                        <input type="text" name="name" required placeholder="Nhập họ tên của bạn">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" placeholder="0123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Chủ đề *</label>
                        <select name="subject" required>
                            <option value="">Chọn chủ đề</option>
                            <option>Hỏi về sản phẩm</option>
                            <option>Hỏi về đơn hàng</option>
                            <option>Khiếu nại</option>
                            <option>Góp ý</option>
                            <option>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nội dung *</label>
                        <textarea name="message" required rows="6" placeholder="Nhập nội dung tin nhắn..."></textarea>
                    </div>
                    
                    <!-- Hidden SSRF test parameters for fuzzing -->
                    <input type="hidden" name="attachment_url" value="">
                    <input type="hidden" name="verify_captcha" value="">
                    <input type="hidden" name="webhook" value="">
                    
                    <button type="submit" class="main-btn btn-send"><i class="fas fa-paper-plane"></i> Gửi tin nhắn</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.contact-page {
    max-width: 1200px;
    margin: 0 auto;
}

.contact-hero {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #e37b58, #d16c4c);
    border-radius: 20px;
    color: white;
}

.contact-hero h1 {
    font-size: 42px;
    margin-bottom: 10px;
}

.contact-hero p {
    font-size: 18px;
    opacity: 0.95;
}

.message-success {
    background: white;
    padding: 60px;
    border-radius: 20px;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 80px;
    margin-bottom: 20px;
}

.message-success h2 {
    color: #28a745;
    margin-bottom: 15px;
}

.message-success p {
    color: #777;
    margin-bottom: 30px;
}

.contact-container {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 40px;
}

.contact-info {
    background: white;
    padding: 30px;
    border-radius: 20px;
    height: fit-content;
}

.contact-info h2 {
    color: #333;
    margin-bottom: 25px;
    font-size: 24px;
}

.info-item {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-of-type {
    border-bottom: none;
}

.info-icon {
    font-size: 32px;
    width: 50px;
    height: 50px;
    background: #fdf7eb;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-item h3 {
    color: #333;
    margin-bottom: 5px;
    font-size: 16px;
}

.info-item p {
    color: #777;
    font-size: 14px;
}

.social-links {
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
}

.social-links h3 {
    color: #333;
    margin-bottom: 15px;
}

.social-icons {
    display: flex;
    gap: 12px;
}

.social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

.social-icons a:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

.contact-form-container {
    background: white;
    padding: 30px;
    border-radius: 20px;
}

.contact-form-container h2 {
    color: #333;
    margin-bottom: 25px;
    font-size: 24px;
}

.contact-form .form-group {
    margin-bottom: 20px;
}

.contact-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.contact-form label {
    display: block;
    color: #555;
    font-weight: 600;
    margin-bottom: 8px;
}

.contact-form input,
.contact-form select,
.contact-form textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
}

.contact-form input:focus,
.contact-form select:focus,
.contact-form textarea:focus {
    border-color: #e37b58;
}

.contact-form textarea {
    resize: vertical;
    font-family: inherit;
}

.btn-send {
    width: 100%;
    padding: 15px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
    
    .contact-form .form-row {
        grid-template-columns: 1fr;
    }
    
    .contact-hero h1 {
        font-size: 28px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>