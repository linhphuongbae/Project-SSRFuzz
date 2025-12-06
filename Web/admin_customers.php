<?php
require_once 'includes/common.php';

// Optional: Check if user is admin (commented out - everyone can access)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// Get filter and search params
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Demo customer data
$customers = [
    ['id' => 1, 'name' => 'Nguyễn Văn A', 'email' => 'nguyenvana@email.com', 'phone' => '0901234567', 'orders' => 5, 'total_spent' => 2500000, 'status' => 'active', 'joined' => '2024-01-15'],
    ['id' => 2, 'name' => 'Trần Thị B', 'email' => 'tranthib@email.com', 'phone' => '0912345678', 'orders' => 12, 'total_spent' => 8900000, 'status' => 'vip', 'joined' => '2023-11-20'],
    ['id' => 3, 'name' => 'Lê Văn C', 'email' => 'levanc@email.com', 'phone' => '0923456789', 'orders' => 3, 'total_spent' => 1200000, 'status' => 'active', 'joined' => '2024-05-10'],
    ['id' => 4, 'name' => 'Phạm Thị D', 'email' => 'phamthid@email.com', 'phone' => '0934567890', 'orders' => 8, 'total_spent' => 4500000, 'status' => 'active', 'joined' => '2024-02-28'],
    ['id' => 5, 'name' => 'Hoàng Văn E', 'email' => 'hoangvane@email.com', 'phone' => '0945678901', 'orders' => 1, 'total_spent' => 350000, 'status' => 'new', 'joined' => '2024-11-01'],
    ['id' => 6, 'name' => 'Võ Thị F', 'email' => 'vothif@email.com', 'phone' => '0956789012', 'orders' => 0, 'total_spent' => 0, 'status' => 'inactive', 'joined' => '2024-10-15'],
];

// Filter customers
$filteredCustomers = $customers;

// Apply status filter
if ($statusFilter) {
    $filteredCustomers = array_filter($filteredCustomers, function($c) use ($statusFilter) {
        return $c['status'] === $statusFilter;
    });
}

// Apply search
if ($searchQuery) {
    $filteredCustomers = array_filter($filteredCustomers, function($c) use ($searchQuery) {
        $search = strtolower($searchQuery);
        return stripos($c['name'], $search) !== false ||
               stripos($c['email'], $search) !== false ||
               stripos($c['phone'], $search) !== false;
    });
}

$stats = [
    'total' => count($customers),
    'active' => count(array_filter($customers, fn($c) => $c['status'] === 'active')),
    'vip' => count(array_filter($customers, fn($c) => $c['status'] === 'vip')),
    'new' => count(array_filter($customers, fn($c) => $c['status'] === 'new'))
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng - Admin</title>
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
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card.total {
            color: #e37b58;
        }
        
        .stat-card.active {
            color: #28a745;
        }
        
        .stat-card.vip {
            color: #ffc107;
        }
        
        .stat-card.new {
            color: #17a2b8;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.875rem;
        }
        
        .customers-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .customers-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .search-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .search-bar input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .search-bar select {
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .btn-search {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(227, 123, 88, 0.4);
        }
        
        .customers-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .customers-table thead {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
        }
        
        .customers-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 500;
        }
        
        .customers-table td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .customers-table tbody tr:hover {
            background: #fdf7eb;
        }
        
        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .customer-details h4 {
            margin: 0;
            color: #333;
        }
        
        .customer-details p {
            margin: 0.25rem 0 0 0;
            color: #666;
            font-size: 0.875rem;
        }
        
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-vip {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-new {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
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
        
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        
        .btn-view:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .btn-email {
            background: #6c757d;
            color: white;
        }
        
        .btn-email:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <h1><i class="fas fa-users"></i> Quản lý khách hàng</h1>
            <div class="header-nav">
                <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="admin_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
                <a href="admin_reports.php"><i class="fas fa-chart-line"></i> Báo cáo</a>
                <a href="index.php"><i class="fas fa-home"></i> Về trang chủ</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="stats-row">
            <div class="stat-card total">
                <i class="fas fa-users"></i>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Tổng khách hàng</div>
            </div>
            <div class="stat-card active">
                <i class="fas fa-user-check"></i>
                <div class="stat-value"><?php echo $stats['active']; ?></div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-card vip">
                <i class="fas fa-crown"></i>
                <div class="stat-value"><?php echo $stats['vip']; ?></div>
                <div class="stat-label">Khách VIP</div>
            </div>
            <div class="stat-card new">
                <i class="fas fa-user-plus"></i>
                <div class="stat-value"><?php echo $stats['new']; ?></div>
                <div class="stat-label">Khách mới</div>
            </div>
        </div>
        
        <div class="customers-card">
            <h2>
                <span><i class="fas fa-list"></i> Danh sách khách hàng</span>
            </h2>
            
            <form method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Tìm kiếm theo tên, email, số điện thoại..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Đang hoạt động</option>
                    <option value="vip" <?php echo $statusFilter === 'vip' ? 'selected' : ''; ?>>VIP</option>
                    <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>Mới</option>
                    <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Không hoạt động</option>
                </select>
                <button type="submit" class="btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
            </form>
            </div>
            
            <table class="customers-table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Liên hệ</th>
                        <th>Đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($filteredCustomers) > 0): ?>
                        <?php foreach ($filteredCustomers as $customer): ?>
                        <tr>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        <?php 
                                        // Lấy ký tự đầu tiên của tên (hỗ trợ tiếng Việt)
                                        $name = $customer['name'];
                                        $firstChar = function_exists('mb_substr') ? mb_substr($name, 0, 1, 'UTF-8') : substr($name, 0, 1);
                                        echo htmlspecialchars($firstChar);
                                        ?>
                                    </div>
                                    <div class="customer-details">
                                        <h4><?php echo htmlspecialchars($customer['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($customer['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td><strong><?php echo $customer['orders']; ?></strong> đơn</td>
                            <td><strong><?php echo number_format($customer['total_spent']); ?> ₫</strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $customer['status']; ?>">
                                    <?php 
                                    $status_labels = [
                                        'active' => 'Hoạt động',
                                        'vip' => 'VIP',
                                        'new' => 'Mới',
                                        'inactive' => 'Không hoạt động'
                                    ];
                                    echo $status_labels[$customer['status']];
                                    ?>
                                </span>
                            </td>
                        <td><?php echo date('d/m/Y', strtotime($customer['joined'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-sm btn-view" onclick="viewCustomer(<?php echo $customer['id']; ?>)">
                                    <i class="fas fa-eye"></i> Xem
                                </button>
                                <button class="btn-sm btn-email" onclick="emailCustomer('<?php echo $customer['email']; ?>')">
                                    <i class="fas fa-envelope"></i> Email
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: #999;">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            <p style="font-size: 1.1rem;">Không tìm thấy khách hàng nào</p>
                            <?php if ($searchQuery || $statusFilter): ?>
                                <a href="admin_customers.php" style="color: #e37b58; margin-top: 1rem; display: inline-block;">Xóa bộ lọc</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function viewCustomer(id) {
            alert('Xem chi tiết khách hàng #' + id + '\n(Chức năng đang phát triển)');
        }
        
        function emailCustomer(email) {
            window.location.href = 'mailto:' + email;
        }
    </script>
</body>
</html>
