<?php
require_once 'includes/common.php';

// Optional: Check if user is admin (commented out - everyone can access)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    deleteProduct($id);
    header('Location: admin_products.php');
    exit;
}

header('Location: admin_products.php');
exit;
?>
