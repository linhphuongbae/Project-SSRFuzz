<?php
include 'includes/common.php';
include 'includes/header.php';
?>

<!-- Hero Banner with Modern Design -->
<section class="hero-banner">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-fire"></i>
                <span>Ưu đãi đặc biệt</span>
            </div>
            <h1 class="hero-title">
                Mua sắm thông minh<br/>
                <span class="hero-highlight">Tiết kiệm mỗi ngày</span>
            </h1>
            <p class="hero-subtitle">
                Hàng ngàn sản phẩm chính hãng với giá tốt nhất. Giao hàng nhanh toàn quốc, đổi trả dễ dàng.
            </p>
            <div class="hero-cta">
                <a href="products.php" class="btn-hero-primary">
                    Khám phá ngay
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="products.php?sort=deals" class="btn-hero-secondary">
                    Xem ưu đãi
                </a>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="hero-image">
                <div class="feature-badge badge-1">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Giao hàng nhanh</span>
                </div>
                <div class="feature-badge badge-2">
                    <i class="fas fa-shield-check"></i>
                    <span>Hàng chính hãng</span>
                </div>
                <div class="feature-badge badge-3">
                    <i class="fas fa-percent"></i>
                    <span>Giảm đến 50%</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <h2 class="section-title">Danh mục sản phẩm</h2>
    <div class="categories-grid">
        <div class="category-card">
            <div class="category-icon"><i class="fas fa-tshirt"></i></div>
            <h3>Thời trang</h3>
            <a href="products.php?cat=fashion">Xem ngay →</a>
        </div>
        <div class="category-card">
            <div class="category-icon"><i class="fas fa-mobile-alt"></i></div>
            <h3>Điện tử</h3>
            <a href="products.php?cat=electronics">Xem ngay →</a>
        </div>
        <div class="category-card">
            <div class="category-icon"><i class="fas fa-home"></i></div>
            <h3>Gia dụng</h3>
            <a href="products.php?cat=home">Xem ngay →</a>
        </div>
        <div class="category-card">
            <div class="category-icon"><i class="fas fa-book"></i></div>
            <h3>Sách</h3>
            <a href="products.php?cat=books">Xem ngay →</a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <h2 class="section-title">Sản phẩm nổi bật</h2>
    <div class="products-grid">
        <?php 
        $products = getProducts();
        $count = 0;
        foreach ($products as $id => $product): 
            if ($count >= 8) break;
            $count++;
        ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="product-badge">Mới</div>
                </div>
                <div class="product-info">
                    <h3><?php echo $product['name']; ?></h3>
                    <p class="product-desc">
                        <?php 
                        $desc = $product['description'] ?? 'Sản phẩm chất lượng cao';
                        if (function_exists('mb_substr')) {
                            echo mb_substr($desc, 0, 50, 'UTF-8');
                        } else {
                            // Cắt an toàn với UTF-8 không cần mbstring
                            $shortened = '';
                            $length = 0;
                            for ($i = 0; $i < strlen($desc) && $length < 50; $i++) {
                                $char = $desc[$i];
                                $shortened .= $char;
                                // Chỉ đếm ký tự đầu của UTF-8 sequence
                                if ((ord($char) & 0xC0) != 0x80) {
                                    $length++;
                                }
                            }
                            echo $shortened;
                        }
                        ?>...
                    </p>
                    <div class="product-footer">
                        <span class="product-price"><?php echo number_format($product['price']); ?>đ</span>
                        <a href="product.php?id=<?php echo $id; ?>" class="btn-view">Xem</a>
                        <a href="add_to_cart.php?id=<?php echo $id; ?>" class="btn-cart">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="section-footer">
        <a href="products.php" class="main-btn">Xem tất cả sản phẩm</a>
    </div>
</section>

<!-- Features -->
<section class="features-section">
    <div class="feature-item">
        <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="#e37b58">
                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
            </svg>
        </div>
        <h3>Giao hàng nhanh</h3>
        <p>Miễn phí ship đơn từ 200k</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="#e37b58">
                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
            </svg>
        </div>
        <h3>Thanh toán linh hoạt</h3>
        <p>COD, chuyển khoản, thẻ</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="#e37b58">
                <path d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8c-.45-.83-.7-1.79-.7-2.8 0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"/>
            </svg>
        </div>
        <h3>Đổi trả dễ dàng</h3>
        <p>7 ngày đổi trả miễn phí</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="#e37b58">
                <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
            </svg>
        </div>
        <h3>Ưu đãi hấp dẫn</h3>
        <p>Khuyến mãi mỗi ngày</p>
    </div>
</section>

<style>
/* Hero Banner */
.hero-banner {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    border-radius: 20px;
    padding: 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 50px;
    color: white;
}

.hero-content h1 {
    font-size: 48px;
    margin-bottom: 15px;
}

.hero-content p {
    font-size: 18px;
    margin-bottom: 25px;
    opacity: 0.95;
}

.hero-btn {
    background: white;
    color: #e37b58;
    font-size: 16px;
}

.hero-btn:hover {
    background: #fdf7eb;
}

.hero-image {
    font-size: 120px;
}

/* Categories */
.categories-section {
    margin: 50px 0;
}

.section-title {
    font-size: 32px;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}

.category-card {
    background: linear-gradient(135deg, #ffffff 0%, #fdf7eb 100%);
    padding: 40px 30px;
    border-radius: 20px;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #f0f0f0;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(227,123,88,0.05) 0%, rgba(209,108,76,0.05) 100%);
    opacity: 0;
    transition: opacity 0.4s;
}

.category-card:hover {
    border-color: #e37b58;
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 15px 35px rgba(227,123,88,0.25);
}

.category-card:hover::before {
    opacity: 1;
}

.category-icon {
    font-size: 72px;
    margin-bottom: 20px;
    color: #e37b58;
    transition: all 0.4s;
    display: inline-block;
}

.category-card:hover .category-icon {
    transform: scale(1.15) rotate(5deg);
}

.category-icon i {
    display: block;
}

.category-card h3 {
    color: #333;
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.category-card a {
    color: #e37b58;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    display: inline-block;
    padding: 8px 20px;
    border-radius: 20px;
    background: rgba(227,123,88,0.1);
    transition: all 0.3s;
    position: relative;
    z-index: 1;
}

.category-card:hover a {
    background: #e37b58;
    color: white;
    transform: translateX(5px);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s;
    border: 1px solid #f0f0f0;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f8f8;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #e37b58;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.product-info {
    padding: 20px;
}

.product-info h3 {
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
}

.product-desc {
    font-size: 13px;
    color: #777;
    margin-bottom: 15px;
}

.product-footer {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-price {
    font-size: 18px;
    font-weight: bold;
    color: #e37b58;
    flex: 1;
}

.btn-view, .btn-cart {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-view {
    background: #fdf7eb;
    color: #e37b58;
    border: 1px solid #e37b58;
}

.btn-cart {
    background: #e37b58;
    color: white;
}

.btn-view:hover, .btn-cart:hover {
    transform: scale(1.05);
}

.section-footer {
    text-align: center;
}

/* Features */
.features-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    margin: 50px 0;
}

.feature-item {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
}

.feature-icon {
    font-size: 50px;
    margin-bottom: 15px;
}

.feature-item h3 {
    color: #333;
    margin-bottom: 8px;
}

.feature-item p {
    color: #777;
    font-size: 14px;
}

@media (max-width: 768px) {
    .hero-banner {
        flex-direction: column;
        text-align: center;
        padding: 40px 30px;
    }
    
    .hero-content h1 {
        font-size: 32px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
}
</style>

<?php include 'includes/footer.php'; ?>