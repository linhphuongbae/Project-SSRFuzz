<?php 
include 'includes/common.php';

// Xử lý thanh toán
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $orderSuccess = true;
    $orderData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'total' => getCartTotal()
    ];
    
    // Lưu đơn hàng vào session để hiển thị
    $_SESSION['last_order'] = $orderData;
    unset($_SESSION['cart']);
}

include 'includes/header.php';
?>

<div class="checkout-page">
    <?php if ($orderSuccess): ?>
        <!-- Order Success -->
        <div class="order-success">
            <div class="success-icon"><i class="fas fa-check-circle"></i></div>
            <h1>Đặt hàng thành công!</h1>
            <p>Cảm ơn bạn đã mua hàng tại MyShop</p>
            <div class="order-details">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['email']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['phone']); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['address']); ?></p>
                <p><strong>Tổng tiền:</strong> <span class="price"><?php echo number_format($_SESSION['last_order']['total']); ?>đ</span></p>
            </div>
            <div class="success-actions">
                <a href="products.php" class="main-btn">Tiếp tục mua sắm</a>
                <a href="orders.php" class="btn-secondary">Xem đơn hàng</a>
            </div>
        </div>
    <?php elseif (empty($_SESSION['cart'])): ?>
        <!-- Empty Cart -->
        <div class="empty-checkout">
            <div class="empty-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="#ccc">
                    <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </div>
            <h2>Giỏ hàng trống</h2>
            <p>Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán</p>
            <a href="products.php" class="main-btn">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <!-- Checkout Form -->
        <div class="checkout-container">
            <div class="checkout-form-section">
                <h1>Thông tin thanh toán</h1>
                <form method="POST" class="checkout-form">
                    <div class="form-group">
                        <label>Họ và tên *</label>
                        <input type="text" name="name" required placeholder="Nguyễn Văn A">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required placeholder="email@example.com">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại *</label>
                            <input type="tel" name="phone" required placeholder="0123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng *</label>
                        <textarea name="address" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea name="note" placeholder="Ghi chú về đơn hàng (tuỳ chọn)"></textarea>
                    </div>
                    
                    <h3>Phương thức thanh toán</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="cod" checked>
                            <span><i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="bank">
                            <span><i class="fas fa-university"></i> Chuyển khoản ngân hàng</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card">
                            <span><i class="fas fa-credit-card"></i> Thanh toán thẻ</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="main-btn btn-submit">Đặt hàng ngay</button>
                </form>
            </div>
            
            <div class="checkout-summary">
                <h3>Đơn hàng của bạn</h3>
                <div class="summary-items">
                    <?php 
                    $products = getProducts();
                    foreach ($_SESSION['cart'] as $id => $qty): 
                        $product = $products[$id];
                    ?>
                        <div class="summary-item">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            <div class="item-details">
                                <h4><?php echo $product['name']; ?></h4>
                                <p>Số lượng: <?php echo $qty; ?></p>
                            </div>
                            <span class="item-price"><?php echo number_format($product['price'] * $qty); ?>đ</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="total-row">
                        <span>Tạm tính:</span>
                        <span><?php echo number_format(getCartTotal()); ?>đ</span>
                    </div>
                    <div class="total-row">
                        <span>Phí vận chuyển:</span>
                        <span>30,000đ</span>
                    </div>
                    <div class="total-row">
                        <span>Giảm giá:</span>
                        <span class="discount">-50,000đ</span>
                    </div>
                    <hr>
                    <div class="total-row final">
                        <span>Tổng cộng:</span>
                        <span><?php echo number_format(getCartTotal() + 30000 - 50000); ?>đ</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.checkout-page {
    max-width: 1200px;
    margin: 0 auto;
}

/* Order Success */
.order-success {
    background: white;
    padding: 60px;
    border-radius: 20px;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.success-icon {
    font-size: 100px;
    margin-bottom: 20px;
}

.order-success h1 {
    color: #28a745;
    margin-bottom: 10px;
}

.order-success > p {
    color: #777;
    margin-bottom: 30px;
}

.order-details {
    background: #fdf7eb;
    padding: 25px;
    border-radius: 15px;
    text-align: left;
    margin: 30px 0;
}

.order-details h3 {
    color: #333;
    margin-bottom: 15px;
}

.order-details p {
    margin: 10px 0;
    color: #555;
}

.order-details .price {
    color: #e37b58;
    font-weight: bold;
    font-size: 20px;
}

.success-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-secondary {
    background: white;
    color: #e37b58;
    border: 2px solid #e37b58;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #fdf7eb;
}

/* Empty Checkout */
.empty-checkout {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 20px;
}

.empty-icon {
    font-size: 100px;
    margin-bottom: 20px;
    opacity: 0.5;
}

/* Checkout Container */
.checkout-container {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 30px;
}

.checkout-form-section {
    background: white;
    padding: 30px;
    border-radius: 20px;
}

.checkout-form-section h1 {
    color: #333;
    margin-bottom: 25px;
}

.checkout-form h3 {
    color: #333;
    margin: 25px 0 15px;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    display: block;
    color: #555;
    font-weight: 600;
    margin-bottom: 8px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #e37b58;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 30px;
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.payment-option:hover {
    border-color: #e37b58;
    background: #fdf7eb;
}

.payment-option input {
    margin-right: 12px;
}

.btn-submit {
    width: 100%;
    padding: 15px;
    font-size: 16px;
}

/* Checkout Summary */
.checkout-summary {
    background: white;
    padding: 25px;
    border-radius: 20px;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.checkout-summary h3 {
    color: #333;
    margin-bottom: 20px;
}

.summary-items {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 20px;
}

.summary-item {
    display: flex;
    gap: 12px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.summary-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    font-size: 14px;
    color: #333;
    margin-bottom: 5px;
}

.item-details p {
    font-size: 12px;
    color: #777;
}

.item-price {
    font-weight: bold;
    color: #e37b58;
}

.summary-totals {
    margin-top: 20px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    color: #555;
}

.total-row.final {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-top: 15px;
}

.discount {
    color: #28a745;
}

.summary-totals hr {
    border: none;
    border-top: 2px solid #f0f0f0;
    margin: 15px 0;
}

@media (max-width: 768px) {
    .checkout-container {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>