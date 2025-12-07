<?php
include 'includes/common.php';

$id = $_GET['id'] ?? 0;
$product = getProductById($id);

if (!$product) {
    include 'includes/header.php';
    echo "<p class='alert alert-danger'>Sản phẩm không tồn tại.</p>";
    include 'includes/footer.php';
    exit;
}

include 'includes/header.php';
?>
<div class="product-detail" style="display:flex; gap:32px; flex-wrap:wrap; justify-content:center;">
    <div style="flex:1; min-width:260px; max-width:400px;">
        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width:100%; border-radius:12px; border:1px solid #e37b58;">
    </div>
    <div style="flex:2; min-width:260px; max-width:500px;">
        <h1 class="main-color"><?php echo $product['name']; ?></h1>
        <p><?php echo $product['description']; ?></p>
        <h3 class="main-color">Giá: <?php echo number_format($product['price']); ?> VND</h3>
        <form action="add_to_cart.php" method="POST" style="margin-top:16px;">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" class="main-btn">Thêm vào giỏ</button>
        </form>
        <a href="products.php" class="main-btn" style="background:#fff; color:#e37b58; border:1px solid #e37b58; margin-top:12px;">Quay lại danh sách</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>