<?php
include 'includes/common.php';
if (isset($_GET['id'])) {
    removeFromCart($_GET['id']);
    header("Location: cart.php");
    exit;
}
?>