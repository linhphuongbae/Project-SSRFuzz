<?php 
include 'includes/common.php';
include 'includes/header.php';
?>

<div class="cart-page">
    <h1 class="page-title"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <div class="empty-icon"><i class="fas fa-shopping-cart"></i></div>
            <h2>Giỏ hàng trống</h2>
            <p>Bạn chưa thêm sản phẩm nào vào giỏ hàng</p>
            <a href="products.php" class="main-btn">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php 
                $products = getProducts();
                foreach ($_SESSION['cart'] as $id => $qty): 
                    $product = $products[$id];
                ?>
                    <div class="cart-item">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <div class="item-info">
                            <h3><?php echo $product['name']; ?></h3>
                            <p><?php echo $product['description']; ?></p>
                            <span class="item-price"><?php echo number_format($product['price']); ?>đ</span>
                        </div>
                        <div class="item-quantity">
                            <button onclick="updateQty(<?php echo $id; ?>, -1)">-</button>
                            <span><?php echo $qty; ?></span>
                            <button onclick="updateQty(<?php echo $id; ?>, 1)">+</button>
                        </div>
                        <div class="item-total">
                            <?php echo number_format($product['price'] * $qty); ?>đ
                        </div>
                        <a href="remove_from_cart.php?id=<?php echo $id; ?>" class="item-remove" onclick="return confirm('Xóa sản phẩm này?')"><i class="fas fa-trash"></i></a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-summary">
                <h3>Tổng đơn hàng</h3>
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?php echo number_format(getCartTotal()); ?>đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>30,000đ</span>
                </div>
                <div class="summary-row">
                    <span>Giảm giá:</span>
                    <span class="discount">-50,000đ</span>
                </div>
                <hr>
                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span><?php echo number_format(getCartTotal() + 30000 - 50000); ?>đ</span>
                </div>
                <a href="checkout.php" class="main-btn btn-checkout">Thanh toán</a>
                <a href="products.php" class="btn-continue">← Tiếp tục mua sắm</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-page {
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    font-size: 32px;
    color: #333;
    margin-bottom: 30px;
}

.empty-cart {
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

.empty-cart h2 {
    color: #333;
    margin-bottom: 10px;
}

.empty-cart p {
    color: #777;
    margin-bottom: 30px;
}

.cart-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.cart-item {
    background: white;
    padding: 20px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 20px;
    position: relative;
}

.cart-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 10px;
}

.item-info {
    flex: 1;
}

.item-info h3 {
    color: #333;
    margin-bottom: 5px;
    font-size: 18px;
}

.item-info p {
    color: #777;
    font-size: 14px;
    margin-bottom: 8px;
}

.item-price {
    color: #e37b58;
    font-weight: bold;
    font-size: 16px;
}

.item-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fdf7eb;
    padding: 8px 12px;
    border-radius: 8px;
}

.item-quantity button {
    background: #e37b58;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.item-quantity span {
    min-width: 30px;
    text-align: center;
    font-weight: bold;
}

.item-total {
    font-size: 20px;
    font-weight: bold;
    color: #e37b58;
    min-width: 120px;
    text-align: right;
}

.item-remove {
    font-size: 24px;
    text-decoration: none;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s;
}

.item-remove:hover {
    opacity: 1;
}

.cart-summary {
    background: white;
    padding: 25px;
    border-radius: 15px;
    height: fit-content;
    position: sticky;
    top: 100px;
}

.cart-summary h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 22px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    color: #555;
}

.summary-row.total {
    font-size: 22px;
    font-weight: bold;
    color: #333;
    margin-top: 15px;
}

.discount {
    color: #28a745;
}

.cart-summary hr {
    border: none;
    border-top: 2px solid #f0f0f0;
    margin: 15px 0;
}

.btn-checkout {
    width: 100%;
    margin-top: 20px;
    padding: 15px;
    font-size: 16px;
}

.btn-continue {
    display: block;
    text-align: center;
    color: #e37b58;
    text-decoration: none;
    margin-top: 15px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .cart-container {
        grid-template-columns: 1fr;
    }
    
    .cart-item {
        flex-direction: column;
        text-align: center;
    }
    
    .item-total {
        text-align: center;
    }
}
</style>

<script>
function updateQty(id, change) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id + '&change=' + change
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.removed) {
                // Reload page nếu sản phẩm bị xóa
                location.reload();
            } else {
                // Reload để cập nhật UI
                location.reload();
            }
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
    });
}
</script>

<?php include 'includes/footer.php'; ?>