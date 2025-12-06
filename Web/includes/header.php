<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Bán Hàng Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php"><i class="fas fa-store"></i> SSRFuzzShop</a>
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
