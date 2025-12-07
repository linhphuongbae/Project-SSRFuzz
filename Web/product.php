
<?php
include 'includes/common.php';

// SSRF Vuln #1: Load image from URL (simple, like TaintInfer sample)
if (isset($_GET['load_image'])) {
    $image_url = $_GET['load_image'];
    $image_content = file_get_contents($image_url);
    header('Content-Type: image/jpeg');
    echo $image_content;
    exit;
}

// SSRF Vuln #2: Check API (simple curl)
if (isset($_GET['check_api'])) {
    $api_url = $_GET['check_api'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    exit;
}

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
        
        <!-- Load image from custom URL -->
        <div style="margin-top:20px; background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #dee2e6;">
            <strong>🖼️ Load Image from URL:</strong>
            <form method="GET" style="margin-top:10px;">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="text" name="load_image" placeholder="Enter image URL" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <button type="submit" style="margin-top:8px; padding:8px 16px; background:#667eea; color:white; border:none; border-radius:4px; cursor:pointer;">Load Image</button>
            </form>
        </div>
        
        <!-- Check stock availability -->
        <div style="margin-top:15px; background:#f8f9fa; padding:15px; border-radius:8px; border:1px solid #dee2e6;">
            <strong>📊 Check Stock API:</strong>
            <form method="GET" style="margin-top:10px;">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="text" name="check_api" placeholder="API endpoint URL" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <button type="submit" style="margin-top:8px; padding:8px 16px; background:#17a2b8; color:white; border:none; border-radius:4px; cursor:pointer;">Check Stock</button>
            </form>
        </div>
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
<?php include 'footer.php'; ?>