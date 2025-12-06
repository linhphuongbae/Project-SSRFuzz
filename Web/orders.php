<?php
include 'includes/common.php';
include 'includes/header.php';

// Xử lý hủy đơn
if (isset($_GET['cancel']) && $_GET['cancel']) {
    echo "<div style='background:#d4edda;padding:20px;margin:20px auto;max-width:900px;border-radius:8px;border:1px solid #28a745;'>";
    echo "<strong>[SUCCESS] Đơn hàng " . htmlspecialchars($_GET['cancel']) . " đã được hủy thành công!</strong>";
    echo "</div>";
}

// Lấy filter status từ URL
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';

// Helper function to convert status to slug
function statusToSlug($status) {
    $replacements = [
        'Đã giao' => 'da-giao',
        'Đang giao' => 'dang-giao',
        'Đang xử lý' => 'dang-xu-ly'
    ];
    return isset($replacements[$status]) ? $replacements[$status] : strtolower(str_replace(' ', '-', $status));
}

// Giả lập dữ liệu đơn hàng
$orders = [
    [
        'id' => '#DH001', 
        'date' => '05/12/2025', 
        'total' => 680000, 
        'status' => 'Đã giao', 
        'items' => 3,
        'products' => [
            ['name' => 'Áo thun cotton', 'quantity' => 2, 'price' => 250000],
            ['name' => 'Quần jeans', 'quantity' => 1, 'price' => 180000]
        ],
        'customer' => ['name' => 'Nguyễn Văn A', 'phone' => '0123456789', 'address' => '123 Đường ABC, Q1, HCM']
    ],
    [
        'id' => '#DH002', 
        'date' => '04/12/2025', 
        'total' => 1500000, 
        'status' => 'Đang giao', 
        'items' => 2,
        'products' => [
            ['name' => 'Laptop Dell', 'quantity' => 1, 'price' => 1500000]
        ],
        'customer' => ['name' => 'Trần Thị B', 'phone' => '0987654321', 'address' => '456 Đường XYZ, Q3, HCM']
    ],
    [
        'id' => '#DH003', 
        'date' => '03/12/2025', 
        'total' => 450000, 
        'status' => 'Đang xử lý', 
        'items' => 1,
        'products' => [
            ['name' => 'Giày sneaker', 'quantity' => 1, 'price' => 450000]
        ],
        'customer' => ['name' => 'Lê Văn C', 'phone' => '0912345678', 'address' => '789 Đường DEF, Q5, HCM']
    ],
    [
        'id' => '#DH004', 
        'date' => '01/12/2025', 
        'total' => 2200000, 
        'status' => 'Đã giao', 
        'items' => 5,
        'products' => [
            ['name' => 'Tai nghe Sony', 'quantity' => 2, 'price' => 800000],
            ['name' => 'Chuột gaming', 'quantity' => 3, 'price' => 200000]
        ],
        'customer' => ['name' => 'Phạm Thị D', 'phone' => '0909090909', 'address' => '321 Đường GHI, Q7, HCM']
    ],
];

// Xem chi tiết đơn hàng
$viewDetail = null;
if (isset($_GET['view']) && !empty($_GET['view'])) {
    $viewId = urldecode($_GET['view']);
    foreach ($orders as $order) {
        if ($order['id'] === $viewId) {
            $viewDetail = $order;
            break;
        }
    }
    
    // Debug: log nếu không tìm thấy
    if ($viewDetail === null) {
        error_log("Order ID requested: '" . $viewId . "' - Not found in orders array");
        error_log("Available order IDs: " . implode(", ", array_column($orders, 'id')));
    }
}

// Filter orders theo status (chỉ khi không xem chi tiết)
$filteredOrders = [];
if (!$viewDetail) {
    if ($filterStatus === 'all') {
        $filteredOrders = $orders;
    } else {
        foreach ($orders as $order) {
            $orderStatusSlug = statusToSlug($order['status']);
            if ($orderStatusSlug === $filterStatus) {
                $filteredOrders[] = $order;
            }
        }
    }
}
?>

<div class="orders-page">
    <?php if ($viewDetail !== null): ?>
        <!-- Chi tiết đơn hàng -->
        <div class="order-detail-page">
            <div style="margin-bottom:20px;">
                <a href="orders.php" style="color:#e37b58;text-decoration:none;font-weight:600;"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
            </div>
            
            <div class="order-detail-card">
                <div class="detail-header">
                    <h2>Chi tiết đơn hàng <?php echo $viewDetail['id']; ?></h2>
                    <span class="order-status status-<?php echo statusToSlug($viewDetail['status']); ?>">
                        <?php echo $viewDetail['status']; ?>
                    </span>
                </div>
                
                <div class="detail-section">
                    <h3><i class="fas fa-box"></i> Thông tin đơn hàng</h3>
                    <p><strong>Ngày đặt:</strong> <?php echo $viewDetail['date']; ?></p>
                    <p><strong>Tổng tiền:</strong> <span style="color:#e37b58;font-size:20px;font-weight:bold;"><?php echo number_format($viewDetail['total']); ?>đ</span></p>
                </div>
                
                <div class="detail-section">
                    <h3><i class="fas fa-user"></i> Thông tin người nhận</h3>
                    <p><strong>Họ tên:</strong> <?php echo $viewDetail['customer']['name']; ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo $viewDetail['customer']['phone']; ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo $viewDetail['customer']['address']; ?></p>
                </div>
                
                <div class="detail-section">
                    <h3><i class="fas fa-shopping-bag"></i> Sản phẩm</h3>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viewDetail['products'] as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo $product['quantity']; ?></td>
                                    <td><?php echo number_format($product['price']); ?>đ</td>
                                    <td><strong><?php echo number_format($product['price'] * $product['quantity']); ?>đ</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right;"><strong>Tổng cộng:</strong></td>
                                <td><strong style="color:#e37b58;font-size:18px;"><?php echo number_format($viewDetail['total']); ?>đ</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php if ($viewDetail['status'] === 'Đang xử lý'): ?>
                    <div class="detail-actions">
                        <a href="orders.php?cancel=<?php echo $viewDetail['id']; ?>" class="btn-cancel-order" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"><i class="fas fa-times-circle"></i> Hủy đơn hàng</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php elseif (isset($_GET['view'])): ?>
        <!-- Không tìm thấy đơn hàng -->
        <div style="background:#f8d7da;padding:20px;border-radius:8px;border:1px solid #f5c6cb;text-align:center;">
            <h3 style="color:#721c24;"><i class="fas fa-exclamation-triangle"></i> Không tìm thấy đơn hàng</h3>
            <p style="color:#721c24;">Mã đơn hàng "<?php echo htmlspecialchars($_GET['view']); ?>" không tồn tại.</p>
            <p style="color:#721c24;font-size:12px;">DEBUG: Tìm kiếm ID = '<?php echo htmlspecialchars(urldecode($_GET['view'])); ?>'</p>
            <p style="color:#721c24;font-size:12px;">Danh sách ID có sẵn: 
                <?php 
                $ids = array_column($orders, 'id');
                foreach($ids as $id) {
                    echo "'" . htmlspecialchars($id) . "' ";
                }
                ?>
            </p>
            <a href="orders.php" class="main-btn" style="margin-top:15px;display:inline-block;"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
        </div>
    <?php else: ?>
        <!-- Danh sách đơn hàng -->
        <div class="orders-header">
        <h1><i class="fas fa-clipboard-list"></i> Đơn hàng của tôi</h1>
        <div class="orders-filter">
            <a href="orders.php?status=all" class="filter-btn <?php echo $filterStatus === 'all' ? 'active' : ''; ?>">Tất cả</a>
            <a href="orders.php?status=dang-xu-ly" class="filter-btn <?php echo $filterStatus === 'dang-xu-ly' ? 'active' : ''; ?>">Đang xử lý</a>
            <a href="orders.php?status=dang-giao" class="filter-btn <?php echo $filterStatus === 'dang-giao' ? 'active' : ''; ?>">Đang giao</a>
            <a href="orders.php?status=da-giao" class="filter-btn <?php echo $filterStatus === 'da-giao' ? 'active' : ''; ?>">Đã giao</a>
        </div>
    </div>
    
    <div class="orders-list">
        <?php if (count($filteredOrders) > 0): ?>
            <?php foreach ($filteredOrders as $order): ?>
            <div class="order-card">
                <div class="order-header-row">
                    <div class="order-id">
                        <strong><?php echo $order['id']; ?></strong>
                        <span class="order-date"><?php echo $order['date']; ?></span>
                    </div>
                    <span class="order-status status-<?php echo statusToSlug($order['status']); ?>">
                        <?php echo $order['status']; ?>
                    </span>
                </div>
                
                <div class="order-body">
                    <div class="order-info">
                        <p><i class="fas fa-box"></i> Số lượng: <strong><?php echo $order['items']; ?></strong> sản phẩm</p>
                        <p><i class="fas fa-dollar-sign"></i> Tổng tiền: <strong class="order-total"><?php echo number_format($order['total']); ?>đ</strong></p>
                    </div>
                    <div class="order-actions">
                        <a href="orders.php?view=<?php echo urlencode($order['id']); ?>" class="btn-view-detail"><i class="fas fa-eye"></i> Xem chi tiết</a>
                        <?php if ($order['status'] === 'Đang xử lý'): ?>
                            <a href="orders.php?cancel=<?php echo urlencode($order['id']); ?>" class="btn-cancel" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')"><i class="fas fa-times"></i> Hủy đơn</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-orders">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <h3>Không có đơn hàng nào</h3>
                <p>Chưa có đơn hàng nào với trạng thái này.</p>
                <a href="orders.php?status=all" class="main-btn">Xem tất cả đơn hàng</a>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.orders-page {
    max-width: 900px;
    margin: 0 auto;
}

.orders-header {
    margin-bottom: 30px;
}

.orders-header h1 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
}

.orders-filter {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 20px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    color: #555;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.filter-btn:hover,
.filter-btn.active {
    border-color: #e37b58;
    background: #e37b58;
    color: white;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.order-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 16px rgba(227,123,88,0.15);
    transform: translateY(-2px);
}

.order-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f5f5f5;
}

.order-id {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.order-id strong {
    font-size: 18px;
    color: #333;
}

.order-date {
    font-size: 14px;
    color: #777;
}

.order-status {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

.status-da-giao {
    background: #d4edda;
    color: #28a745;
}

.status-dang-giao {
    background: #fff3cd;
    color: #856404;
}

.status-dang-xu-ly {
    background: #cce5ff;
    color: #004085;
}

.order-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-info p {
    margin: 8px 0;
    color: #555;
}

.order-total {
    color: #e37b58;
    font-size: 18px;
}

.order-actions {
    display: flex;
    gap: 10px;
}

.btn-view-detail,
.btn-cancel {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-view-detail {
    background: #e37b58;
    color: white;
}

.btn-view-detail:hover {
    background: #d16c4c;
}

.btn-cancel {
    background: white;
    color: #dc3545;
    border: 2px solid #dc3545;
}

.btn-cancel:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .order-body {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .order-actions {
        width: 100%;
    }
    
    .btn-view-detail,
    .btn-cancel {
        flex: 1;
        text-align: center;
    }
}

/* Order Detail Styles */
.order-detail-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 0 auto;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 3px solid #f5f5f5;
}

.detail-header h2 {
    color: #333;
    margin: 0;
}

.detail-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #fdf7eb;
    border-radius: 10px;
}

.detail-section h3 {
    color: #e37b58;
    margin-bottom: 15px;
    font-size: 18px;
}

.detail-section p {
    margin: 10px 0;
    color: #555;
    line-height: 1.6;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.products-table th {
    background: #e37b58;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.products-table td {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.products-table tbody tr:hover {
    background: white;
}

.products-table tfoot tr {
    background: #f8f9fa;
    font-weight: bold;
}

.detail-actions {
    text-align: center;
    margin-top: 30px;
}

.btn-cancel-order {
    padding: 12px 30px;
    background: white;
    color: #dc3545;
    border: 2px solid #dc3545;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s;
}

.btn-cancel-order:hover {
    background: #dc3545;
    color: white;
}

.empty-orders {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
}

.empty-orders .empty-icon {
    font-size: 80px;
    color: #ccc;
    margin-bottom: 20px;
}

.empty-orders h3 {
    color: #333;
    font-size: 24px;
    margin-bottom: 10px;
}

.empty-orders p {
    color: #777;
    margin-bottom: 20px;
}

.empty-orders .main-btn {
    display: inline-block;
    padding: 12px 30px;
    background: #e37b58;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.empty-orders .main-btn:hover {
    background: #d16c4c;
    transform: translateY(-2px);
}
</style>

<?php include 'includes/footer.php'; ?>^ < s c r i p t 
