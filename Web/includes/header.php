<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Bán Hàng Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <svg width="40" height="40" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Hexagon Background -->
                        <path d="M50 5 L90 27.5 L90 72.5 L50 95 L10 72.5 L10 27.5 Z" fill="url(#logo-gradient)" stroke="#d16c4c" stroke-width="3"/>
                        <defs>
                            <linearGradient id="logo-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#e37b58;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#d16c4c;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <!-- Shopping Bag Icon -->
                        <path d="M35 35 L65 35 L60 70 L40 70 Z" fill="white" opacity="0.9"/>
                        <circle cx="50" cy="28" r="8" fill="white" opacity="0.9"/>
                        <path d="M42 35 Q42 25, 50 25 Q58 25, 58 35" stroke="white" stroke-width="3" fill="none"/>
                        <!-- Sparkle -->
                        <circle cx="70" cy="20" r="3" fill="#fdf7eb"/>
                        <circle cx="78" cy="30" r="2" fill="#fdf7eb"/>
                    </svg>
                    <span class="logo-text">SSRFuzzShop</span>
                </a>
            </div>
            
            <div class="nav-search">
                <form action="products.php" method="GET" style="display: flex; align-items: center; width: 100%;">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." style="flex: 1;">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Trang chủ</a>
                <a href="products.php" class="nav-link">Sản phẩm</a>
                <a href="orders.php" class="nav-link">Đơn hàng</a>
                <a href="contact.php" class="nav-link">Liên hệ</a>
            </div>
            
            <div class="nav-actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="profile.php" class="nav-icon" title="<?php echo $_SESSION['user']['name']; ?>">
                        <i class="fas fa-user"></i>
                    </a>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="dashboard.php" class="nav-icon" title="Admin Dashboard">
                            <i class="fas fa-cog"></i>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="nav-icon" title="Đăng xuất">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="nav-icon" title="Đăng nhập">
                        <i class="fas fa-user-circle"></i>
                    </a>
                <?php endif; ?>
                <a href="cart.php" class="nav-icon cart-icon" title="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>
    
    <main class="main-wrapper">
