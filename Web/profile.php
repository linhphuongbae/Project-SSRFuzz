<?php
include 'includes/common.php';

// Update avatar from URL
if (isset($_POST['avatar_url']) && $_POST['avatar_url']) {
    $url = $_POST['avatar_url'];
    $info = @getimagesize($url);
    if ($info) {
        echo "<div style='background:#d4edda;padding:20px;margin:20px;border-radius:8px;border:1px solid #28a745;'>";
        echo "<strong>[SUCCESS] Avatar loaded successfully!</strong><br>";
        echo "Image Type: " . $info['mime'] . "<br>";
        echo "Dimensions: " . $info[0] . "x" . $info[1] . "<br>";
        $image_data = @file_get_contents($url);
        if ($image_data) {
            echo "Size: " . strlen($image_data) . " bytes";
        }
        echo "</div>";
    } else {
        echo "<div style='background:#f8d7da;padding:20px;margin:20px;border-radius:8px;border:1px solid #dc3545;'>";
        echo "<strong>[ERROR] Failed to load avatar from URL</strong>";
        echo "</div>";
    }
}

// Import profile settings from external source
if (isset($_POST['import_json']) && $_POST['import_json']) {
    $json_url = $_POST['import_json'];
    $json_data = @file_get_contents($json_url);
    if ($json_data) {
        $profile_data = json_decode($json_data, true);
        echo "<div style='background:#cfe2ff;padding:20px;margin:20px;border-radius:8px;border:1px solid #0d6efd;'>";
        echo "<strong>[IMPORT] Profile data imported:</strong><br>";
        echo "<pre>" . htmlspecialchars(print_r($profile_data, true)) . "</pre>";
        echo "</div>";
    }
}

include 'includes/header.php';
?>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <div class="avatar-img">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="white">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <button class="btn-change-avatar">Đổi ảnh</button>
            </div>
            <div class="profile-menu">
                <a href="#" class="menu-item active">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </span>
                    <span>Thông tin cá nhân</span>
                </a>
                <a href="orders.php" class="menu-item">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </span>
                    <span>Đơn hàng</span>
                </a>
                <a href="#" class="menu-item">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </span>
                    <span>Yêu thích</span>
                </a>
                <a href="#" class="menu-item">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                    </span>
                    <span>Thông báo</span>
                </a>
                <a href="#" class="menu-item">
                    <span class="menu-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                        </svg>
                    </span>
                    <span>Cài đặt</span>
                </a>
            </div>
        </div>
        
        <div class="profile-main">
            <div class="profile-header">
                <h1>Thông tin cá nhân</h1>
                <button class="btn-edit"><i class="fas fa-edit"></i> Chỉnh sửa</button>
            </div>
            
            <div class="profile-content">
                <div class="info-section">
                    <h3>Thông tin cơ bản</h3>
                    <div class="info-grid">
                        <div class="info-field">
                            <label>Họ và tên</label>
                            <p>Nguyễn Văn A</p>
                        </div>
                        <div class="info-field">
                            <label>Email</label>
                            <p>nguyenvana@email.com</p>
                        </div>
                        <div class="info-field">
                            <label>Số điện thoại</label>
                            <p>0123 456 789</p>
                        </div>
                        <div class="info-field">
                            <label>Ngày sinh</label>
                            <p>01/01/1990</p>
                        </div>
                        <div class="info-field full-width">
                            <label>Địa chỉ</label>
                            <p>123 Đường ABC, Quận 1, TP. Hồ Chí Minh</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>Thống kê</h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#e3f2fd;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#2196f3">
                                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>24</h4>
                                <p>Đơn hàng</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#e8f5e9;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#4caf50">
                                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>5,600,000đ</h4>
                                <p>Tổng chi tiêu</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#fff3e0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#ff9800">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>Gold</h4>
                                <p>Hạng thành viên</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#fce4ec;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#e91e63">
                                    <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>1,250</h4>
                                <p>Điểm tích lũy</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="info-section">
                    <h3>Cập nhật ảnh đại diện</h3>
                    <form method="POST" style="margin-top:15px;">
                        <div class="form-group">
                            <label>URL ảnh đại diện</label>
                            <input type="text" name="avatar_url" placeholder="https://example.com/avatar.jpg" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:10px;">
                            <button type="submit" class="main-btn">Cập nhật ảnh</button>
                        </div>
                    </form>
                </div>
                
                <div class="info-section">
                    <h3>Địa chỉ giao hàng</h3>
                    <div class="address-list">
                        <div class="address-card">
                            <div class="address-header">
                                <strong>Địa chỉ mặc định</strong>
                                <span class="badge-default">Mặc định</span>
                            </div>
                            <p>Nguyễn Văn A</p>
                            <p>0123 456 789</p>
                            <p>123 Đường ABC, Quận 1, TP. Hồ Chí Minh</p>
                            <div class="address-actions">
                                <a href="#">Sửa</a>
                                <a href="#">Xóa</a>
                            </div>
                        </div>
                        <button class="btn-add-address">+ Thêm địa chỉ mới</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-page {
    max-width: 1200px;
    margin: 0 auto;
}

.profile-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
}

/* Sidebar */
.profile-sidebar {
    background: white;
    border-radius: 20px;
    padding: 25px;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.profile-avatar {
    text-align: center;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.avatar-img {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #e37b58, #d16c4c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    margin: 0 auto 15px;
}

.btn-change-avatar {
    background: #fdf7eb;
    color: #e37b58;
    border: 1px solid #e37b58;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
}

.profile-menu {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border-radius: 10px;
    text-decoration: none;
    color: #555;
    transition: all 0.3s;
}

.menu-item:hover,
.menu-item.active {
    background: #fdf7eb;
    color: #e37b58;
}

.menu-icon {
    font-size: 20px;
}

/* Main Content */
.profile-main {
    background: white;
    border-radius: 20px;
    padding: 30px;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.profile-header h1 {
    color: #333;
    font-size: 28px;
}

.btn-edit {
    background: #e37b58;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}

.info-section {
    margin-bottom: 35px;
}

.info-section h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.info-field {
    background: #fdf7eb;
    padding: 15px;
    border-radius: 10px;
}

.info-field.full-width {
    grid-column: 1 / -1;
}

.info-field label {
    display: block;
    color: #777;
    font-size: 13px;
    margin-bottom: 5px;
}

.info-field p {
    color: #333;
    font-weight: 600;
    font-size: 15px;
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.stat-card {
    background: #fdf7eb;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.stat-icon {
    font-size: 32px;
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-info h4 {
    color: #e37b58;
    font-size: 18px;
    margin-bottom: 3px;
}

.stat-info p {
    color: #777;
    font-size: 13px;
}

/* Address */
.address-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.address-card {
    background: #fdf7eb;
    padding: 20px;
    border-radius: 12px;
}

.address-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.badge-default {
    background: #e37b58;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
}

.address-card p {
    color: #555;
    margin: 5px 0;
    font-size: 14px;
}

.address-actions {
    margin-top: 12px;
    display: flex;
    gap: 15px;
}

.address-actions a {
    color: #e37b58;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.btn-add-address {
    background: white;
    border: 2px dashed #e37b58;
    color: #e37b58;
    padding: 15px;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
}

@media (max-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Button đổi ảnh avatar
    const btnChangeAvatar = document.querySelector('.btn-change-avatar');
    if (btnChangeAvatar) {
        btnChangeAvatar.addEventListener('click', function() {
            document.querySelector('input[name="avatar_url"]').focus();
            document.querySelector('input[name="avatar_url"]').scrollIntoView({behavior: 'smooth', block: 'center'});
        });
    }
    
    // Button chỉnh sửa thông tin
    const btnEdit = document.querySelector('.btn-edit');
    if (btnEdit) {
        let isEditing = false;
        btnEdit.addEventListener('click', function() {
            const infoFields = document.querySelectorAll('.info-field p');
            
            if (isEditing) {
                // Lưu thông tin
                infoFields.forEach(field => {
                    const input = field.querySelector('input');
                    if (input) {
                        field.textContent = input.value;
                    }
                });
                this.textContent = '✎ Chỉnh sửa';
                isEditing = false;
                alert('Đã lưu thông tin thành công!');
            } else {
                // Chế độ chỉnh sửa
                infoFields.forEach(field => {
                    const currentText = field.textContent.trim();
                    field.innerHTML = '<input type="text" value="' + currentText + '" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; font-size:14px;">';
                });
                this.textContent = '� Lưu';
                isEditing = true;
            }
        });
    }
    
    // Sửa/xóa địa chỉ
    document.addEventListener('click', function(e) {
        if (e.target.matches('.address-actions a')) {
            e.preventDefault();
            const action = e.target.textContent;
            
            if (action === 'Sửa') {
                const addressCard = e.target.closest('.address-card');
                const addressText = addressCard.querySelectorAll('p');
                const name = prompt('Nhập tên người nhận:', addressText[0].textContent);
                if (name) addressText[0].textContent = name;
                
                const phone = prompt('Nhập số điện thoại:', addressText[1].textContent);
                if (phone) addressText[1].textContent = phone;
                
                const address = prompt('Nhập địa chỉ:', addressText[2].textContent);
                if (address) addressText[2].textContent = address;
            } else if (action === 'Xóa') {
                if (confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
                    e.target.closest('.address-card').remove();
                    alert('Đã xóa địa chỉ!');
                }
            }
        }
    });
    
    // Thêm địa chỉ mới
    const btnAddAddress = document.querySelector('.btn-add-address');
    if (btnAddAddress) {
        btnAddAddress.addEventListener('click', function() {
            const name = prompt('Nhập tên người nhận:');
            if (!name) return;
            
            const phone = prompt('Nhập số điện thoại:');
            if (!phone) return;
            
            const address = prompt('Nhập địa chỉ đầy đủ:');
            if (!address) return;
            
            const newAddressCard = document.createElement('div');
            newAddressCard.className = 'address-card';
            newAddressCard.innerHTML = `
                <div class="address-header">
                    <strong>Địa chỉ mới</strong>
                </div>
                <p>${name}</p>
                <p>${phone}</p>
                <p>${address}</p>
                <div class="address-actions">
                    <a href="#">Sửa</a>
                    <a href="#">Xóa</a>
                </div>
            `;
            
            document.querySelector('.address-list').insertBefore(newAddressCard, this);
            alert('Đã thêm địa chỉ mới!');
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
