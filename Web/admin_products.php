<?php
require_once 'includes/common.php';

$message = '';
$edit_product = null;

// Validate Image URL - SSRF Vuln #11
if (isset($_GET['validate_image'])) {
    $image_url = $_GET['validate_image'];
    if (empty($image_url)) {
        $message = '<div class="alert alert-error">URL hình ảnh không được để trống</div>';
    } else {
        if (strpos($image_url, '.xml') !== false) {
            $xml = @simplexml_load_file($image_url);
            if ($xml === false) {
                $message = '<div class="alert alert-error">Không thể load XML từ: ' . htmlspecialchars($image_url) . '</div>';
            } else {
                $message = '<div class="alert alert-success">Validate XML thành công!</div>';
            }
        } else {
            $image_data = @file_get_contents($image_url);
            if ($image_data === false) {
                $message = '<div class="alert alert-error">Không thể load image từ: ' . htmlspecialchars($image_url) . '</div>';
            } else {
                $img = @imagecreatefromstring($image_data);
                if ($img === false) {
                    $message = '<div class="alert alert-error">Định dạng ảnh không hợp lệ</div>';
                } else {
                    $message = '<div class="alert alert-success">Validate image thành công: ' . imagesx($img) . 'x' . imagesy($img) . '</div>';
                    imagedestroy($img);
                }
            }
        }
    }
}

// Sync product data from supplier - SSRF Vuln #12 (NEW)
if (isset($_GET['sync_product'])) {
    $supplier_url = $_GET['sync_product'];
    if (empty($supplier_url)) {
        $message = '<div class="alert alert-error">URL không được để trống</div>';
    } else {
        $product_data = @file_get_contents($supplier_url);
        if ($product_data === false) {
            $message = '<div class="alert alert-error">Không thể kết nối đến URL: ' . htmlspecialchars($supplier_url) . '</div>';
        } else {
            $message = '<div class="alert alert-success">Sync thành công từ: ' . htmlspecialchars($supplier_url) . '</div>';
        }
    }
}

// Check warehouse stock API - SSRF Vuln #13 (NEW)
if (isset($_GET['check_warehouse'])) {
    $warehouse_api = $_GET['check_warehouse'];
    if (empty($warehouse_api)) {
        $message = '<div class="alert alert-error">URL warehouse không được để trống</div>';
    } else {
        $ch = curl_init($warehouse_api);
        if ($ch === false) {
            $message = '<div class="alert alert-error">URL không hợp lệ: ' . htmlspecialchars($warehouse_api) . '</div>';
        } else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $stock_info = curl_exec($ch);
            if ($stock_info === false) {
                $error = curl_error($ch);
                curl_close($ch);
                $message = '<div class="alert alert-error">Lỗi kết nối warehouse: ' . htmlspecialchars($error) . '</div>';
            } else {
                curl_close($ch);
                $message = '<div class="alert alert-success">Kiểm tra kho thành công từ: ' . htmlspecialchars($warehouse_api) . '</div>';
            }
        }
    }
}

// Optional: Check if user is admin (commented out - everyone can access)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);
    
    // Validate
    if (empty($name) || $price <= 0 || empty($category)) {
        $message = '<div class="alert alert-error">Vui lòng điền đầy đủ thông tin!</div>';
    } else {
        if ($product_id) {
            // Update existing product
            updateProduct($product_id, $name, $price, $category, $description, $stock, $image_url);
            $message = '<div class="alert alert-success">Cập nhật sản phẩm thành công!</div>';
        } else {
            // Add new product
            addProduct($name, $price, $category, $description, $image_url, $stock);
            $message = '<div class="alert alert-success">Thêm sản phẩm thành công!</div>';
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $edit_product = getProductById($_GET['edit']);
}

$all_products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fdf7eb 0%, #fff 100%);
            min-height: 100vh;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-content h1 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .header-nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            animation: slideDown 0.3s ease;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .form-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }
        
        .form-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #e37b58;
            box-shadow: 0 0 0 3px rgba(227, 123, 88, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .image-preview {
            margin-top: 1rem;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(227, 123, 88, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .products-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .products-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table thead {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
        }
        
        .products-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }
        
        .products-table td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .products-table tbody tr:hover {
            background: #fdf7eb;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-fashion {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-electronics {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-home {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .badge-books {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .stock-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stock-in {
            color: #28a745;
        }
        
        .stock-low {
            color: #ffc107;
        }
        
        .stock-out {
            color: #dc3545;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .btn-edit {
            background: #17a2b8;
            color: white;
        }
        
        .btn-edit:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card.total {
            color: #e37b58;
        }
        
        .stat-card.fashion {
            color: #1976d2;
        }
        
        .stat-card.electronics {
            color: #7b1fa2;
        }
        
        .stat-card.home {
            color: #388e3c;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <h1><i class="fas fa-box"></i> Quản lý sản phẩm</h1>
            <div class="header-nav">
                <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="admin_customers.php"><i class="fas fa-users"></i> Khách hàng</a>
                <a href="admin_reports.php"><i class="fas fa-chart-line"></i> Báo cáo</a>
                <a href="index.php"><i class="fas fa-home"></i> Về trang chủ</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php echo $message; ?>
        
        <div class="stats-row">
            <div class="stat-card total">
                <i class="fas fa-box"></i>
                <div class="stat-value"><?php echo count($all_products); ?></div>
                <div class="stat-label">Tổng sản phẩm</div>
            </div>
            <div class="stat-card fashion">
                <i class="fas fa-tshirt"></i>
                <div class="stat-value"><?php echo count(array_filter($all_products, function($p) { return $p['category'] === 'fashion'; })); ?></div>
                <div class="stat-label">Thời trang</div>
            </div>
            <div class="stat-card electronics">
                <i class="fas fa-laptop"></i>
                <div class="stat-value"><?php echo count(array_filter($all_products, function($p) { return $p['category'] === 'electronics'; })); ?></div>
                <div class="stat-label">Điện tử</div>
            </div>
            <div class="stat-card home">
                <i class="fas fa-home"></i>
                <div class="stat-value"><?php echo count(array_filter($all_products, function($p) { return $p['category'] === 'home'; })); ?></div>
                <div class="stat-label">Gia dụng</div>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="form-card">
                <h2>
                    <i class="fas fa-<?php echo $edit_product ? 'edit' : 'plus-circle'; ?>"></i>
                    <?php echo $edit_product ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới'; ?>
                </h2>
                
                <form method="POST" id="productForm">
                    <?php if ($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Tên sản phẩm *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo $edit_product['name'] ?? ''; ?>"
                               placeholder="Nhập tên sản phẩm">
                    </div>
                    
                    <div class="form-group">
                        <label for="price"><i class="fas fa-dollar-sign"></i> Giá (VNĐ) *</label>
                        <input type="number" id="price" name="price" required min="0" step="1000"
                               value="<?php echo $edit_product['price'] ?? ''; ?>"
                               placeholder="Nhập giá sản phẩm">
                    </div>
                    
                    <div class="form-group">
                        <label for="stock"><i class="fas fa-warehouse"></i> Số lượng *</label>
                        <input type="number" id="stock" name="stock" required min="0"
                               value="<?php echo $edit_product['stock'] ?? 0; ?>"
                               placeholder="Nhập số lượng">
                    </div>
                    
                    <div class="form-group">
                        <label for="category"><i class="fas fa-list"></i> Danh mục *</label>
                        <select id="category" name="category" required>
                            <option value="">Chọn danh mục</option>
                            <option value="fashion" <?php echo ($edit_product['category'] ?? '') === 'fashion' ? 'selected' : ''; ?>>Thời trang</option>
                            <option value="electronics" <?php echo ($edit_product['category'] ?? '') === 'electronics' ? 'selected' : ''; ?>>Điện tử</option>
                            <option value="home" <?php echo ($edit_product['category'] ?? '') === 'home' ? 'selected' : ''; ?>>Gia dụng</option>
                            <option value="books" <?php echo ($edit_product['category'] ?? '') === 'books' ? 'selected' : ''; ?>>Sách</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
                        <textarea id="description" name="description" 
                                  placeholder="Nhập mô tả sản phẩm"><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_url"><i class="fas fa-image"></i> URL hình ảnh</label>
                        <input type="url" id="image_url" name="image_url"
                               value="<?php echo $edit_product['image'] ?? ''; ?>"
                               placeholder="https://example.com/image.jpg"
                               onchange="previewImage(this.value)">
                        <div class="image-preview" id="imagePreview">
                            <?php if ($edit_product && $edit_product['image']): ?>
                                <img src="<?php echo $edit_product['image']; ?>" alt="Preview">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Advanced Import Tools -->
                    <div style="background:#f8f9fa; padding:15px; border-radius:8px; margin:15px 0; border:1px solid #dee2e6;">
                        <strong>Công cụ nâng cao:</strong>
                        
                        <div class="form-group" style="margin-top:10px;">
                            <label>Validate Image URL</label>
                            <div style="display:flex; gap:8px;">
                                <input type="text" id="validate_url" placeholder="https://cdn.example.com/product.jpg" style="flex:1;">
                                <a href="#" onclick="window.location.href='?validate_image=' + document.getElementById('validate_url').value; return false;" class="btn btn-secondary" style="white-space:nowrap;">✓ Validate</a>
                            </div>
                            <small style="display:block; color:#6c757d; margin-top:5px;">Kiểm tra URL hình ảnh hợp lệ (hỗ trợ .jpg, .png, .xml)</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Sync từ Nhà Cung Cấp (GET)</label>
                            <div style="display:flex; gap:8px;">
                                <input type="text" id="sync_url" placeholder="https://shopee.vn/api/product/123" style="flex:1;">
                                <a href="#" onclick="window.location.href='?sync_product=' + document.getElementById('sync_url').value; return false;" class="btn btn-secondary" style="white-space:nowrap;">🔄 Sync</a>
                            </div>
                            <small style="display:block; color:#6c757d; margin-top:5px;">Đồng bộ dữ liệu từ Shopee/Lazada/1688</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Kiểm Tra Kho Hàng (GET)</label>
                            <div style="display:flex; gap:8px;">
                                <input type="text" id="warehouse_url" placeholder="https://api.warehouse.com/stock/123" style="flex:1;">
                                <a href="#" onclick="window.location.href='?check_warehouse=' + document.getElementById('warehouse_url').value; return false;" class="btn btn-secondary" style="white-space:nowrap;">📦 Check</a>
                            </div>
                            <small style="display:block; color:#6c757d; margin-top:5px;">Kiểm tra tồn kho qua API warehouse</small>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?php echo $edit_product ? 'Cập nhật' : 'Thêm mới'; ?>
                        </button>
                        <?php if ($edit_product): ?>
                            <a href="admin_products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="products-card">
                <h2><i class="fas fa-list"></i> Danh sách sản phẩm</h2>
                
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_products as $product): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
                            </td>
                            <td><strong><?php echo $product['name']; ?></strong></td>
                            <td>
                                <span class="badge badge-<?php echo $product['category']; ?>">
                                    <?php 
                                    $categories = [
                                        'fashion' => 'Thời trang',
                                        'electronics' => 'Điện tử',
                                        'home' => 'Gia dụng',
                                        'books' => 'Sách'
                                    ];
                                    echo $categories[$product['category']];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo number_format($product['price']); ?> ₫</td>
                            <td>
                                <div class="stock-status">
                                    <?php 
                                    $stock = $product['stock'] ?? 0;
                                    if ($stock > 10) {
                                        echo '<i class="fas fa-check-circle stock-in"></i> ' . $stock;
                                    } elseif ($stock > 0) {
                                        echo '<i class="fas fa-exclamation-triangle stock-low"></i> ' . $stock;
                                    } else {
                                        echo '<i class="fas fa-times-circle stock-out"></i> Hết hàng';
                                    }
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit=<?php echo $product['id']; ?>" class="btn-sm btn-edit">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <button onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" 
                                            class="btn-sm btn-delete">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(url) {
            const preview = document.getElementById('imagePreview');
            if (url) {
                preview.innerHTML = `<img src="${url}" alt="Preview" onerror="this.src='https://via.placeholder.com/200x200/e37b58/ffffff?text=Invalid+URL'">`;
            } else {
                preview.innerHTML = '';
            }
        }
        
        function deleteProduct(id, name) {
            if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${name}"?`)) {
                window.location.href = `delete_product.php?id=${id}`;
            }
        }
    </script>
</body>
</html>
