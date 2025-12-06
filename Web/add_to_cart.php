<?php
include 'includes/common.php';

// Xử lý cả GET và POST
$product_id = null;
$quantity = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? $_POST['id'] ?? null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
} elseif (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
}

if ($product_id && $quantity > 0) {
    addToCart($product_id, $quantity);
    header("Location: cart.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>