<?php include 'includes/common.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
// Kiểm tra nếu có ID sản phẩm
if (isset($_GET['id'])) {
    $product = getProductById($_GET['id']);
    
    if (!$product) {
        echo "<div style='padding: 40px; text-align: center;'>";
        echo "<h2>Không tìm thấy sản phẩm!</h2>";
        echo "<a href='products.php' class='main-btn'>Quay lại danh sách</a>";
        echo "</div>";
        include 'includes/footer.php';
        exit;
    }
?>

<style>
.product-detail-wrapper {
    background: linear-gradient(135deg, #fdf7eb 0%, #fff 100%);
    padding: 60px 0;
}

.product-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.breadcrumb {
    display: flex;
    gap: 10px;
    color: #999;
    font-size: 14px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: #e37b58;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.product-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    background: white;
    padding: 50px;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
}

.product-gallery {
    position: relative;
}

.main-image-wrapper {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, #fdf7eb 0%, #fff 100%);
    margin-bottom: 20px;
}

.main-image-wrapper img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.main-image-wrapper:hover img {
    transform: scale(1.08);
}

.image-badge {
    position: absolute;
    top: 25px;
    right: 25px;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 4px 15px rgba(227, 123, 88, 0.4);
}

.thumbnail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.thumbnail {
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
}

.thumbnail:hover {
    border-color: #e37b58;
    transform: translateY(-3px);
}

.thumbnail img {
    width: 100%;
    height: 100px;
    object-fit: cover;
}

.product-info h1 {
    color: #222;
    font-size: 40px;
    margin-bottom: 20px;
    font-weight: 800;
    line-height: 1.2;
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 2px solid #f0f0f0;
}

.rating-stars {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stars {
    color: #ffc107;
    font-size: 20px;
    letter-spacing: 2px;
}

.rating-text {
    color: #666;
    font-weight: 600;
}

.sold-count {
    color: #999;
    font-size: 14px;
}

.product-price-box {
    background: linear-gradient(135deg, #fff5f0 0%, #fdf7eb 100%);
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 30px;
    border: 2px solid #e37b5820;
}

.current-price {
    color: #e37b58;
    font-size: 48px;
    font-weight: 900;
    display: block;
    margin-bottom: 10px;
}

.price-details {
    display: flex;
    align-items: center;
    gap: 15px;
}

.original-price {
    color: #999;
    font-size: 20px;
    text-decoration: line-through;
}

.discount-tag {
    background: #e37b58;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
}

.product-description {
    color: #666;
    line-height: 1.9;
    margin-bottom: 35px;
    font-size: 16px;
}

.product-specs {
    background: #f8f8f8;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.product-specs h3 {
    color: #333;
    font-size: 16px;
    margin-bottom: 15px;
    font-weight: 700;
}

.spec-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.spec-item:last-child {
    border-bottom: none;
}

.spec-label {
    color: #666;
    font-weight: 500;
}

.spec-value {
    color: #333;
    font-weight: 600;
}

.quantity-section {
    margin-bottom: 30px;
}

.quantity-label {
    display: block;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    font-size: 16px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    width: fit-content;
    background: white;
}

.quantity-controls button {
    background: #f5f5f5;
    border: none;
    padding: 15px 22px;
    cursor: pointer;
    font-size: 20px;
    font-weight: 700;
    color: #333;
    transition: all 0.3s;
}

.quantity-controls button:hover {
    background: #e37b58;
    color: white;
}

.quantity-controls input {
    border: none;
    width: 70px;
    text-align: center;
    font-size: 18px;
    font-weight: 700;
    color: #333;
    padding: 15px 0;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.btn-add-to-cart {
    flex: 2;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    border: none;
    padding: 18px 30px;
    font-size: 18px;
    font-weight: 700;
    border-radius: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: all 0.3s;
    box-shadow: 0 6px 20px rgba(227, 123, 88, 0.35);
}

.btn-add-to-cart:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(227, 123, 88, 0.45);
}

.btn-buy-now {
    flex: 1;
    background: white;
    color: #e37b58;
    border: 3px solid #e37b58;
    padding: 18px 30px;
    font-size: 18px;
    font-weight: 700;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-buy-now:hover {
    background: #e37b58;
    color: white;
}

.product-guarantees {
    background: linear-gradient(135deg, #f8f8f8 0%, #fff 100%);
    padding: 25px;
    border-radius: 16px;
    border-left: 5px solid #e37b58;
}

.product-guarantees h3 {
    color: #333;
    margin-bottom: 18px;
    font-size: 18px;
    font-weight: 700;
}

.guarantee-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    color: #555;
    font-size: 15px;
}

.guarantee-item:last-child {
    margin-bottom: 0;
}

.guarantee-icon {
    color: #e37b58;
    font-size: 20px;
}

@media (max-width: 968px) {
    .product-detail-grid {
        grid-template-columns: 1fr;
        gap: 40px;
        padding: 30px 25px;
    }
    
    .main-image-wrapper img {
        height: 400px;
    }
    
    .product-info h1 {
        font-size: 32px;
    }
    
    .current-price {
        font-size: 38px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<!-- Chi tiết sản phẩm -->
<div class="product-detail-wrapper">
    <div class="product-detail-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Trang chủ</a>
            <span>›</span>
            <a href="products.php">Sản phẩm</a>
            <span>›</span>
            <span><?php echo $product['name']; ?></span>
        </div>
        
        <div class="product-detail-grid">
            <!-- Hình ảnh sản phẩm -->
            <div class="product-gallery">
                <div class="main-image-wrapper">
                    <span class="image-badge">HOT SALE</span>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" id="mainImage">
                </div>
                
                <div class="thumbnail-grid">
                    <div class="thumbnail">
                        <img src="<?php echo $product['image']; ?>" alt="Ảnh 1">
                    </div>
                    <div class="thumbnail">
                        <img src="<?php echo str_replace('e37b58', 'd16c4c', $product['image']); ?>" alt="Ảnh 2">
                    </div>
                    <div class="thumbnail">
                        <img src="<?php echo str_replace('e37b58', 'c85a3e', $product['image']); ?>" alt="Ảnh 3">
                    </div>
                    <div class="thumbnail">
                        <img src="<?php echo str_replace('e37b58', 'b54830', $product['image']); ?>" alt="Ảnh 4">
                    </div>
                </div>
            </div>
            
            <!-- Thông tin sản phẩm -->
            <div class="product-info">
                <h1><?php echo $product['name']; ?></h1>
                
                <div class="product-meta">
                    <div class="rating-stars">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span class="rating-text">4.9/5</span>
                    </div>
                    <span class="sold-count">| Đã bán: 2.5k+</span>
                </div>
                
                <div class="product-price-box">
                    <span class="current-price"><?php echo number_format($product['price']); ?>đ</span>
                    <div class="price-details">
                        <span class="original-price"><?php echo number_format($product['price'] * 1.3); ?>đ</span>
                        <span class="discount-tag">-23%</span>
                    </div>
                </div>
                
                <p class="product-description">
                    <?php echo $product['description']; ?>
                    <br><br>
                    Sản phẩm được nhập khẩu chính hãng, đảm bảo chất lượng cao cấp. Thiết kế hiện đại, phù hợp với mọi lứa tuổi và phong cách.
                </p>
                
                <div class="product-specs">
                    <h3>Thông số kỹ thuật</h3>
                    <div class="spec-item">
                        <span class="spec-label">Thương hiệu</span>
                        <span class="spec-value">Premium Brand</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Xuất xứ</span>
                        <span class="spec-value">Việt Nam / Nhập khẩu</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Bảo hành</span>
                        <span class="spec-value">12 tháng</span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Tình trạng</span>
                        <span class="spec-value" style="color: #27ae60;">Còn hàng</span>
                    </div>
                </div>
                
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="quantity-section">
                        <label class="quantity-label">Số lượng:</label>
                        <div class="quantity-controls">
                            <button type="button" onclick="decreaseQty()">−</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" readonly>
                            <button type="button" onclick="increaseQty()">+</button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" class="btn-add-to-cart">
                            <span><i class="fas fa-shopping-cart"></i></span>
                            <span>Thêm vào giỏ hàng</span>
                        </button>
                        <button type="button" class="btn-buy-now">Mua ngay</button>
                    </div>
                </form>
                
                <div class="product-guarantees">
                    <h3>Cam kết của chúng tôi</h3>
                    <div class="guarantee-item">
                        <span class="guarantee-icon">✔</span>
                        <span>Giao hàng siêu tốc 2-3 ngày toàn quốc</span>
                    </div>
                    <div class="guarantee-item">
                        <span class="guarantee-icon">✔</span>
                        <span>Miễn phí vận chuyển đơn từ 500.000đ</span>
                    </div>
                    <div class="guarantee-item">
                        <span class="guarantee-icon">✔</span>
                        <span>Đổi trả dễ dàng trong 7 ngày nếu lỗi</span>
                    </div>
                    <div class="guarantee-item">
                        <span class="guarantee-icon">✔</span>
                        <span>Thanh toán khi nhận hàng (COD)</span>
                    </div>
                    <div class="guarantee-item">
                        <span class="guarantee-icon">✔</span>
                        <span>Hỗ trợ khách hàng 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function increaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) < 99) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Thumbnail click
document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', function() {
        const img = this.querySelector('img');
        document.getElementById('mainImage').src = img.src;
    });
});
</script>

<?php
} else {
    // Hiển thị danh sách sản phẩm
    $products = getAllProducts();
    
    // Lấy filter parameters
    $category = $_GET['category'] ?? 'all';
    $priceRange = $_GET['price'] ?? 'all';
    $sort = $_GET['sort'] ?? 'newest';
    $search = $_GET['search'] ?? '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 12; // Số sản phẩm trên mỗi trang
    
    // Filter products
    $filteredProducts = $products;
    
    // Search filter
    if ($search) {
        $filteredProducts = array_filter($filteredProducts, function($p) use ($search) {
            return stripos($p['name'], $search) !== false || 
                   stripos($p['description'], $search) !== false;
        });
    }
    
    // Category filter
    if ($category != 'all') {
        $filteredProducts = array_filter($filteredProducts, function($p) use ($category) {
            return isset($p['category']) && $p['category'] == $category;
        });
    }
    
    // Price filter
    if ($priceRange != 'all') {
        $filteredProducts = array_filter($filteredProducts, function($p) use ($priceRange) {
            $price = $p['price'];
            switch($priceRange) {
                case 'under200': return $price < 200000;
                case '200to500': return $price >= 200000 && $price < 500000;
                case '500to1m': return $price >= 500000 && $price < 1000000;
                case 'over1m': return $price >= 1000000;
                default: return true;
            }
        });
    }
    
    // Sort products
    usort($filteredProducts, function($a, $b) use ($sort) {
        switch($sort) {
            case 'price_asc': return $a['price'] - $b['price'];
            case 'price_desc': return $b['price'] - $a['price'];
            case 'name': return strcmp($a['name'], $b['name']);
            default: return 0;
        }
    });
    
    // Tính toán phân trang
    $totalProducts = count($filteredProducts);
    $totalPages = ceil($totalProducts / $perPage);
    $page = min($page, max(1, $totalPages)); // Đảm bảo page hợp lệ
    $offset = ($page - 1) * $perPage;
    
    // Lấy sản phẩm cho trang hiện tại
    $currentPageProducts = array_slice($filteredProducts, $offset, $perPage);
    
    // Tạo URL với params hiện tại
    function buildUrl($params) {
        $current = $_GET;
        $merged = array_merge($current, $params);
        return 'products.php?' . http_build_query($merged);
    }
?>

<style>
.products-page {
    background: linear-gradient(180deg, #fdf7eb 0%, #fff 40%);
    min-height: 100vh;
    padding: 50px 0;
}

.products-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 60px;
}

.page-header h1 {
    font-size: 56px;
    color: #222;
    margin-bottom: 18px;
    font-weight: 900;
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle {
    color: #666;
    font-size: 20px;
    font-weight: 500;
}

.filters-section {
    background: white;
    padding: 30px 35px;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    margin-bottom: 50px;
}

.filters-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.filters-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    flex: 1;
}

.filter-dropdown {
    position: relative;
}

.filter-select {
    padding: 14px 45px 14px 20px;
    border: 2px solid #e8e8e8;
    border-radius: 12px;
    background: white;
    font-size: 15px;
    color: #333;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 600;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23333' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
}

.filter-select:hover {
    border-color: #e37b58;
    box-shadow: 0 2px 8px rgba(227, 123, 88, 0.15);
}

.filter-select:focus {
    outline: none;
    border-color: #e37b58;
    box-shadow: 0 0 0 4px rgba(227, 123, 88, 0.1);
}

.search-box {
    display: flex;
    align-items: center;
    background: #f8f8f8;
    border-radius: 12px;
    padding: 6px 8px 6px 18px;
    border: 2px solid transparent;
    transition: all 0.3s;
    min-width: 280px;
}

.search-box:focus-within {
    background: white;
    border-color: #e37b58;
    box-shadow: 0 0 0 4px rgba(227, 123, 88, 0.1);
}

.search-box input {
    border: none;
    background: none;
    font-size: 15px;
    flex: 1;
    padding: 8px;
    outline: none;
    font-weight: 500;
}

.search-box button {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s;
}

.search-box button:hover {
    transform: scale(1.05);
}

.results-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
}

.results-count {
    color: #666;
    font-size: 16px;
    font-weight: 600;
}

.results-count strong {
    color: #e37b58;
    font-size: 20px;
    font-weight: 800;
}

.view-toggle {
    display: flex;
    gap: 8px;
    background: #f5f5f5;
    padding: 6px;
    border-radius: 10px;
}

.view-toggle button {
    background: none;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s;
}

.view-toggle button.active {
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 35px;
    margin-bottom: 60px;
}

.product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 6px 25px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}

.product-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #fdf7eb 0%, #fff 100%);
    height: 320px;
}

.product-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.product-card:hover .product-image-container img {
    transform: scale(1.15) rotate(2deg);
}

.product-labels {
    position: absolute;
    top: 18px;
    left: 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.label-tag {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(227, 123, 88, 0.4);
}

.label-discount {
    background: #27ae60;
}

.wishlist-btn {
    position: absolute;
    top: 18px;
    right: 18px;
    background: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 20px;
}

.wishlist-btn:hover {
    background: #e37b58;
    transform: scale(1.15);
    color: white;
}

.quick-view-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: 20px;
    transform: translateY(100%);
    transition: transform 0.4s;
}

.product-card:hover .quick-view-overlay {
    transform: translateY(0);
}

.quick-view-btn {
    background: white;
    color: #e37b58;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s;
}

.quick-view-btn:hover {
    background: #e37b58;
    color: white;
}

.product-card-body {
    padding: 28px;
}

.product-category-tag {
    color: #999;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 10px;
    font-weight: 700;
}

.product-card-title {
    color: #222;
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 14px;
    height: 52px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3;
}

.product-card-title:hover {
    color: #e37b58;
}

.product-rating-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rating-stars-small {
    color: #ffc107;
    font-size: 16px;
}

.rating-value {
    color: #666;
    font-size: 14px;
    font-weight: 700;
}

.sold-badge {
    background: #f0f0f0;
    color: #666;
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
}

.product-price-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 20px;
}

.product-current-price {
    color: #e37b58;
    font-size: 30px;
    font-weight: 900;
}

.product-old-price {
    color: #999;
    font-size: 16px;
    text-decoration: line-through;
}

.add-to-cart-button {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    border: none;
    padding: 16px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 12px;
    cursor: pointer;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(227, 123, 88, 0.3);
}

.add-to-cart-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(227, 123, 88, 0.45);
}

.pagination-container {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 60px;
}

.pagination-btn {
    padding: 14px 22px;
    border: 2px solid #e8e8e8;
    background: white;
    color: #666;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    transition: all 0.3s;
    font-size: 15px;
    text-decoration: none;
    display: inline-block;
}

.pagination-btn:hover {
    border-color: #e37b58;
    color: #e37b58;
    transform: translateY(-2px);
}

.pagination-btn.active {
    background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
    color: white;
    border-color: #e37b58;
}

.pagination-dots {
    padding: 14px 10px;
    color: #999;
    font-weight: 700;
}

.empty-results {
    text-align: center;
    padding: 100px 20px;
}

.empty-results h2 {
    font-size: 36px;
    color: #333;
    margin-bottom: 20px;
}

.empty-results p {
    color: #666;
    font-size: 18px;
    margin-bottom: 30px;
}

@media (max-width: 968px) {
    .page-header h1 {
        font-size: 38px;
    }
    
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filters-group {
        width: 100%;
    }
    
    .filter-select {
        flex: 1;
        min-width: 120px;
    }
    
    .search-box {
        width: 100%;
        min-width: unset;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 20px;
    }
    
    .product-card-body {
        padding: 18px;
    }
    
    .product-card-title {
        font-size: 16px;
        height: 42px;
    }
    
    .product-current-price {
        font-size: 22px;
    }
}
</style>

<!-- Danh sách sản phẩm -->
<div class="products-page">
    <div class="products-container">
        <div class="page-header">
            <h1>Khám Phá Sản Phẩm</h1>
            <p class="page-subtitle">Hơn <?php echo count($products); ?>+ sản phẩm chất lượng cao đang chờ bạn khám phá</p>
        </div>
        
        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="products.php">
                <div class="filters-row">
                    <div class="filters-group">
                        <div class="filter-dropdown">
                            <select class="filter-select" name="category" onchange="this.form.submit()">
                                <option value="all" <?php echo $category == 'all' ? 'selected' : ''; ?>>Tất cả danh mục</option>
                                <option value="fashion" <?php echo $category == 'fashion' ? 'selected' : ''; ?>>Thời trang</option>
                                <option value="electronics" <?php echo $category == 'electronics' ? 'selected' : ''; ?>>Điện tử</option>
                                <option value="home" <?php echo $category == 'home' ? 'selected' : ''; ?>>Gia dụng</option>
                                <option value="books" <?php echo $category == 'books' ? 'selected' : ''; ?>>Sách & Văn phòng</option>
                            </select>
                        </div>
                        
                        <div class="filter-dropdown">
                            <select class="filter-select" name="price" onchange="this.form.submit()">
                                <option value="all" <?php echo $priceRange == 'all' ? 'selected' : ''; ?>>Khoảng giá</option>
                                <option value="under200" <?php echo $priceRange == 'under200' ? 'selected' : ''; ?>>Dưới 200k</option>
                                <option value="200to500" <?php echo $priceRange == '200to500' ? 'selected' : ''; ?>>200k - 500k</option>
                                <option value="500to1m" <?php echo $priceRange == '500to1m' ? 'selected' : ''; ?>>500k - 1 triệu</option>
                                <option value="over1m" <?php echo $priceRange == 'over1m' ? 'selected' : ''; ?>>Trên 1 triệu</option>
                            </select>
                        </div>
                        
                        <div class="filter-dropdown">
                            <select class="filter-select" name="sort" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá: Thấp → Cao</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá: Cao → Thấp</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên: A → Z</option>
                            </select>
                        </div>
                        
                        <input type="hidden" name="page" value="1">
                        
                        <div class="results-count" style="margin-left: auto;">
                            Tìm thấy <strong><?php echo $totalProducts; ?></strong> sản phẩm
                            <?php if ($totalPages > 1): ?>
                            (Trang <?php echo $page; ?>/<?php echo $totalPages; ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="results-bar">
            <div></div>
            <div class="view-toggle">
                <button class="active">▦</button>
                <button>≡</button>
            </div>
        </div>
        
        <?php if (count($filteredProducts) > 0): ?>
        <!-- Grid sản phẩm -->
        <div class="products-grid">
            <?php foreach ($currentPageProducts as $product): ?>
            <div class="product-card">
                <div class="product-image-container">
                    <a href="products.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-labels">
                            <span class="label-tag">NEW</span>
                            <span class="label-tag label-discount">-23%</span>
                        </div>
                        <div class="wishlist-btn">♡</div>
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <div class="quick-view-overlay">
                            <button class="quick-view-btn"><i class="fas fa-eye"></i> Xem nhanh</button>
                        </div>
                    </a>
                </div>
                <div class="product-card-body">
                    <a href="products.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-category-tag">
                            <?php 
                            $categoryNames = [
                                'fashion' => 'Thời trang',
                                'electronics' => 'Điện tử',
                                'home' => 'Gia dụng',
                                'books' => 'Sách'
                            ];
                            echo $categoryNames[$product['category']] ?? 'Sản phẩm';
                            ?>
                        </div>
                        <h3 class="product-card-title"><?php echo $product['name']; ?></h3>
                        <div class="product-rating-row">
                            <div class="rating-display">
                                <span class="rating-stars-small">⭐⭐⭐⭐⭐</span>
                                <span class="rating-value">4.9</span>
                            </div>
                            <span class="sold-badge">Đã bán 2.5k</span>
                        </div>
                        <div class="product-price-row">
                            <span class="product-current-price"><?php echo number_format($product['price']); ?>đ</span>
                            <span class="product-old-price"><?php echo number_format($product['price'] * 1.3); ?>đ</span>
                        </div>
                    </a>
                    <form action="add_to_cart.php" method="POST" style="margin: 0;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="add-to-cart-button">
                            <span><i class="fas fa-shopping-cart"></i></span>
                            <span>Thêm vào giỏ</span>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <?php if ($page > 1): ?>
            <a href="<?php echo buildUrl(['page' => $page - 1]); ?>" class="pagination-btn">← Trước</a>
            <?php else: ?>
            <button class="pagination-btn" disabled style="opacity: 0.5; cursor: not-allowed;">← Trước</button>
            <?php endif; ?>
            
            <?php
            // Hiển thị các trang
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            // Nút trang đầu
            if ($startPage > 1) {
                echo '<a href="' . buildUrl(['page' => 1]) . '" class="pagination-btn">1</a>';
                if ($startPage > 2) {
                    echo '<span class="pagination-dots">...</span>';
                }
            }
            
            // Các trang ở giữa
            for ($i = $startPage; $i <= $endPage; $i++) {
                if ($i == $page) {
                    echo '<button class="pagination-btn active">' . $i . '</button>';
                } else {
                    echo '<a href="' . buildUrl(['page' => $i]) . '" class="pagination-btn">' . $i . '</a>';
                }
            }
            
            // Nút trang cuối
            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) {
                    echo '<span class="pagination-dots">...</span>';
                }
                echo '<a href="' . buildUrl(['page' => $totalPages]) . '" class="pagination-btn">' . $totalPages . '</a>';
            }
            ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="<?php echo buildUrl(['page' => $page + 1]); ?>" class="pagination-btn">Sau →</a>
            <?php else: ?>
            <button class="pagination-btn" disabled style="opacity: 0.5; cursor: not-allowed;">Sau →</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-results">
            <h2>Không tìm thấy sản phẩm</h2>
            <p>Xin lỗi, chúng tôi không tìm thấy sản phẩm nào phù hợp với tiêu chí của bạn</p>
            <a href="products.php" class="main-btn">Xem tất cả sản phẩm</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php } ?>

<?php include 'includes/footer.php'; ?>

