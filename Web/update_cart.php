<?php
session_start();

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['change'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    exit;
}

$productId = $_POST['id'];
$change = (int)$_POST['change'];

if (!isset($_SESSION['cart'][$productId])) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng']);
    exit;
}

// Update số lượng
$_SESSION['cart'][$productId] += $change;

// Nếu số lượng <= 0, xóa sản phẩm
if ($_SESSION['cart'][$productId] <= 0) {
    unset($_SESSION['cart'][$productId]);
    echo json_encode([
        'success' => true, 
        'removed' => true,
        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
    ]);
    exit;
}

// Tính lại tổng tiền
include 'includes/common.php';
$total = getCartTotal();

echo json_encode([
    'success' => true,
    'quantity' => $_SESSION['cart'][$productId],
    'total' => number_format($total),
    'finalTotal' => number_format($total + 30000 - 50000)
]);
?>
