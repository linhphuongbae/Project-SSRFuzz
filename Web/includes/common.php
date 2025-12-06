<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Data sản phẩm giả (id => [name, price, description, image])
function getProducts() {
    return [
        1 => ['id' => 1, 'name' => 'Áo Thun Cotton Nam', 'price' => 199000, 'description' => 'Áo thun cotton 100% cao cấp, thoáng mát, thấm hút mồ hôi tốt.', 'image' => 'images/products/ao-thun.jpg', 'category' => 'fashion'],
        2 => ['id' => 2, 'name' => 'Quần Jeans Slim Fit', 'price' => 499000, 'description' => 'Quần jeans cao cấp, form dáng chuẩn, co giãn 4 chiều.', 'image' => 'images/products/quan-jeans.jpg', 'category' => 'fashion'],
        3 => ['id' => 3, 'name' => 'Giày Sneaker Thể Thao', 'price' => 799000, 'description' => 'Giày sneaker phong cách, êm ái, phù hợp vận động.', 'image' => 'images/products/giay-sneaker.jpg', 'category' => 'fashion'],
        4 => ['id' => 4, 'name' => 'Túi Xách Da Thời Trang', 'price' => 350000, 'description' => 'Túi xách da PU cao cấp, nhiều ngăn tiện lợi.', 'image' => 'images/products/tui-xach.jpg', 'category' => 'fashion'],
        5 => ['id' => 5, 'name' => 'Mũ Lưỡi Trai Snapback', 'price' => 149000, 'description' => 'Mũ lưỡi trai thể thao, chống nắng hiệu quả.', 'image' => 'images/products/mu.jpg', 'category' => 'fashion'],
        6 => ['id' => 6, 'name' => 'Đồng Hồ Nam Cao Cấp', 'price' => 1299000, 'description' => 'Đồng hồ nam sang trọng, chống nước 3ATM.', 'image' => 'images/products/dong-ho.jpg', 'category' => 'electronics'],
        7 => ['id' => 7, 'name' => 'Balo Laptop 15.6 inch', 'price' => 459000, 'description' => 'Balo laptop chống sốc, nhiều ngăn tiện lợi.', 'image' => 'images/products/balo.jpg', 'category' => 'fashion'],
        8 => ['id' => 8, 'name' => 'Ví Da Nam Cao Cấp', 'price' => 249000, 'description' => 'Ví da thật 100%, thiết kế sang trọng, bền đẹp.', 'image' => 'images/products/vi-da.jpg', 'category' => 'fashion'],
        9 => ['id' => 9, 'name' => 'Áo Khoác Dù Nam', 'price' => 399000, 'description' => 'Áo khoác dù chống nước, chống gió tốt.', 'image' => 'images/products/ao-khoac.jpg', 'category' => 'fashion'],
        10 => ['id' => 10, 'name' => 'Quần Short Thể Thao', 'price' => 189000, 'description' => 'Quần short thoáng mát, phù hợp tập luyện.', 'image' => 'images/products/quan-short.jpg', 'category' => 'fashion'],
        11 => ['id' => 11, 'name' => 'Tai Nghe Bluetooth', 'price' => 599000, 'description' => 'Tai nghe bluetooth 5.0, âm thanh chất lượng cao.', 'image' => 'images/products/tai-nghe.jpg', 'category' => 'electronics'],
        12 => ['id' => 12, 'name' => 'Điện Thoại Smartphone', 'price' => 4999000, 'description' => 'Smartphone cao cấp, camera 48MP, RAM 8GB.', 'image' => 'images/products/smartphone.jpg', 'category' => 'electronics'],
        13 => ['id' => 13, 'name' => 'Laptop Gaming', 'price' => 15999000, 'description' => 'Laptop gaming RTX 3060, i7 Gen 11, RAM 16GB.', 'image' => 'images/products/laptop.jpg', 'category' => 'electronics'],
        14 => ['id' => 14, 'name' => 'Chuột Không Dây', 'price' => 299000, 'description' => 'Chuột gaming không dây, DPI cao, pin 6 tháng.', 'image' => 'images/products/chuot.jpg', 'category' => 'electronics'],
        15 => ['id' => 15, 'name' => 'Bàn Phím Cơ RGB', 'price' => 899000, 'description' => 'Bàn phím cơ RGB, switch blue, anti-ghosting.', 'image' => 'images/products/ban-phim.jpg', 'category' => 'electronics'],
        16 => ['id' => 16, 'name' => 'Nồi Cơm Điện', 'price' => 799000, 'description' => 'Nồi cơm điện 1.8L, lòng chống dính cao cấp.', 'image' => 'images/products/noi-com.jpg', 'category' => 'home'],
        17 => ['id' => 17, 'name' => 'Bình Giữ Nhiệt', 'price' => 259000, 'description' => 'Bình giữ nhiệt inox 304, giữ nhiệt 24h.', 'image' => 'images/products/binh-nuoc.jpg', 'category' => 'home'],
        18 => ['id' => 18, 'name' => 'Máy Xay Sinh Tố', 'price' => 459000, 'description' => 'Máy xay sinh tố đa năng, công suất 500W.', 'image' => 'images/products/may-xay.jpg', 'category' => 'home'],
        19 => ['id' => 19, 'name' => 'Chăn Ga Gối Đệm', 'price' => 699000, 'description' => 'Bộ chăn ga gối cotton 100%, mát mịn.', 'image' => 'images/products/chan-ga.jpg', 'category' => 'home'],
        20 => ['id' => 20, 'name' => 'Đèn Ngủ Thông Minh', 'price' => 199000, 'description' => 'Đèn ngủ điều khiển từ xa, 16 triệu màu.', 'image' => 'images/products/den-ngu.jpg', 'category' => 'home'],
        21 => ['id' => 21, 'name' => 'Sách Kỹ Năng Sống', 'price' => 89000, 'description' => 'Cuốn sách hay về kỹ năng mềm, phát triển bản thân.', 'image' => 'images/products/sach-ky-nang.jpg', 'category' => 'books'],
        22 => ['id' => 22, 'name' => 'Truyện Tiểu Thuyết', 'price' => 129000, 'description' => 'Tiểu thuyết hay, cốt truyện hấp dẫn.', 'image' => 'images/products/truyen.jpg', 'category' => 'books'],
        23 => ['id' => 23, 'name' => 'Vở Ghi Chú A5', 'price' => 45000, 'description' => 'Vở ghi chú cao cấp, giấy dày mịn.', 'image' => 'images/products/vo.jpg', 'category' => 'books'],
        24 => ['id' => 24, 'name' => 'Bút Ký Cao Cấp', 'price' => 159000, 'description' => 'Bút ký sang trọng, viết mượt, bền đẹp.', 'image' => 'images/products/but.jpg', 'category' => 'books'],
    ];
}

// Thêm vào giỏ hàng
function addToCart($id, $qty = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
}

// Xóa khỏi giỏ
function removeFromCart($id) {
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
}

// Tính tổng tiền
function getCartTotal() {
    $total = 0;
    $products = getProducts();
    foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
        if (isset($products[$id])) {
            $total += $products[$id]['price'] * $qty;
        }
    }
    return $total;
}

// Admin functions - Thêm sản phẩm mới
function addProduct($name, $price, $category, $description, $image, $stock = 0) {
    if (!isset($_SESSION['custom_products'])) {
        $_SESSION['custom_products'] = [];
    }
    
    // Tìm ID lớn nhất hiện tại
    $allProducts = getAllProducts();
    $maxId = 0;
    foreach ($allProducts as $p) {
        if ($p['id'] > $maxId) {
            $maxId = $p['id'];
        }
    }
    
    $newId = $maxId + 1;
    $_SESSION['custom_products'][$newId] = [
        'id' => $newId,
        'name' => $name,
        'price' => $price,
        'category' => $category,
        'description' => $description,
        'image' => $image ?: 'https://via.placeholder.com/400x400/e37b58/fff?text=No+Image',
        'stock' => $stock
    ];
    
    return $newId;
}

// Cập nhật sản phẩm
function updateProduct($id, $name, $price, $category, $description, $stock, $image) {
    if (!isset($_SESSION['custom_products'])) {
        $_SESSION['custom_products'] = [];
    }
    
    $_SESSION['custom_products'][$id] = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'category' => $category,
        'description' => $description,
        'image' => $image ?: 'https://via.placeholder.com/400x400/e37b58/fff?text=No+Image',
        'stock' => $stock
    ];
}

// Xóa sản phẩm
function deleteProduct($id) {
    if (isset($_SESSION['custom_products'][$id])) {
        unset($_SESSION['custom_products'][$id]);
        return true;
    }
    return false;
}

// Cập nhật hàm getAllProducts để bao gồm sản phẩm custom
function getAllProducts() {
    $baseProducts = getProducts();
    $customProducts = $_SESSION['custom_products'] ?? [];
    
    // Merge và sắp xếp theo ID
    $allProducts = array_merge($baseProducts, $customProducts);
    
    // Thêm stock mặc định cho sản phẩm cũ nếu chưa có
    foreach ($allProducts as &$product) {
        if (!isset($product['stock'])) {
            $product['stock'] = rand(10, 100); // Random stock cho demo
        }
    }
    
    return $allProducts;
}

// Cập nhật hàm getProductById để lấy cả sản phẩm custom
function getProductById($id) {
    $allProducts = getAllProducts();
    return $allProducts[$id] ?? null;
}
?>