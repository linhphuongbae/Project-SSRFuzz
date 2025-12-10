<?php
include 'includes/common.php';

// Optional: Check admin authentication (commented out - everyone can access)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// Handle export report
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sales_report_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    fputcsv($output, ['Tháng', 'Doanh thu', 'Đơn hàng', 'Khách hàng']);
    
    $months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'];
    $revenues = [85000000, 92000000, 78000000, 105000000, 118000000, 125000000];
    $orders = [124, 135, 98, 156, 178, 189];
    $customers = [45, 52, 38, 67, 73, 89];
    
    for ($i = 0; $i < count($months); $i++) {
        fputcsv($output, [
            $months[$i],
            number_format($revenues[$i]) . ' đ',
            $orders[$i],
            $customers[$i]
        ]);
    }
    
    fclose($output);
    exit;
}

$products = getAllProducts();
$totalProducts = count($products);
$totalOrders = 156; // Giả lập
$totalRevenue = 125000000; // Giả lập
$totalCustomers = 89; // Giả lập
$newOrders = 12; // Đơn hàng mới
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MyShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .dashboard {
            min-height: 100vh;
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            font-size: 28px;
            font-weight: 900;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
        }

        .top-bar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .top-bar-right a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .top-bar-right a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #fafbfc 100%);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            border-color: #e37b58;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #e37b58, #f39c12, #e37b58);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -20px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(227,123,88,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translateY(-50%);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            font-size: 48px;
            background: linear-gradient(135deg, #e37b58, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
        }

        .stat-value {
            font-size: 42px;
            font-weight: 900;
            background: linear-gradient(135deg, #333 0%, #555 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .stat-label {
            color: #888;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-change {
            margin-top: 15px;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .stat-change.up {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .stat-change.down {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        /* Sections */
        .section {
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 35px;
            border: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .section:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 3px solid #f5f7fa;
        }

        .section-title {
            font-size: 24px;
            font-weight: 800;
            color: #333;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #e37b58;
        }

        .section-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #e37b58 0%, #d16c4c 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(227, 123, 88, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(227, 123, 88, 0.45);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ebef 100%);
            color: #333;
            border: 2px solid #dfe3e8;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #e8ebef 0%, #dfe3e8 100%);
            border-color: #e37b58;
            transform: translateY(-2px);
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #fdf7eb;
        }

        .data-table th {
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        .data-table tbody tr:hover {
            background: #f9f9f9;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .product-name {
            font-weight: 600;
            color: #333;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Charts placeholder */
        .chart-placeholder {
            background: linear-gradient(135deg, #fdf7eb 0%, #fff 100%);
            height: 300px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 18px;
            font-weight: 600;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .quick-action-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 35px 25px;
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e37b58, #f39c12);
            transform: scaleX(0);
            transition: transform 0.4s;
        }

        .quick-action-card:hover::before {
            transform: scaleX(1);
        }

        .quick-action-card:hover {
            border-color: #e37b58;
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 35px rgba(227, 123, 88, 0.2);
        }

        .quick-action-icon {
            font-size: 52px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #e37b58, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s;
        }

        .quick-action-card:hover .quick-action-icon {
            transform: scale(1.15) rotate(5deg);
        }

        .quick-action-title {
            font-weight: 700;
            color: #333;
            font-size: 16px;
            letter-spacing: 0.3px;
        }

        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section {
                padding: 20px;
            }

            .data-table {
                font-size: 13px;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <div class="logo"><i class="fas fa-store"></i> MyShop</div>
                <div class="page-title">Admin Dashboard</div>
            </div>
            <div class="top-bar-right">
                <a href="index.php"><i class="fas fa-home"></i> Trang chủ</a>
                <a href="admin_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
                <a href="admin_customers.php"><i class="fas fa-users"></i> Khách hàng</a>
                <a href="admin_reports.php"><i class="fas fa-chart-bar"></i> Báo cáo</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="container">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo number_format($totalRevenue/1000000, 1); ?>tr</div>
                            <div class="stat-label">Doanh thu</div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +12.5% so với tháng trước</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $totalOrders; ?></div>
                            <div class="stat-label">Đơn hàng</div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    </div>
                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +8.3% so với tháng trước</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $totalCustomers; ?></div>
                            <div class="stat-label">Khách hàng</div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-user-friends"></i></div>
                    </div>
                    <div class="stat-change up"><i class="fas fa-arrow-up"></i> +15.2% khách hàng mới</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-value"><?php echo $totalProducts; ?></div>
                            <div class="stat-label">Sản phẩm</div>
                        </div>
                        <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                    </div>
                    <div class="stat-change down"><i class="fas fa-arrow-down"></i> 2 sản phẩm hết hàng</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">Thao tác nhanh</h2>
                </div>
                <div class="quick-actions">
                    <div class="quick-action-card" onclick="window.location.href='admin_products.php'">
                        <div class="quick-action-icon"><i class="fas fa-plus"></i></div>
                        <div class="quick-action-title">Thêm sản phẩm</div>
                    </div>
                    <div class="quick-action-card" onclick="window.location.href='admin_products.php'">
                        <div class="quick-action-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="quick-action-title">Xem đơn hàng</div>
                    </div>
                    <div class="quick-action-card" onclick="window.location.href='admin_customers.php'">
                        <div class="quick-action-icon"><i class="fas fa-users"></i></div>
                        <div class="quick-action-title">Quản lý khách hàng</div>
                    </div>
                    <div class="quick-action-card" onclick="window.location.href='admin_reports.php'">
                        <div class="quick-action-icon"><i class="fas fa-chart-bar"></i></div>
                        <div class="quick-action-title">Báo cáo</div>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">Sản phẩm mới nhất</h2>
                    <div class="section-actions">
                        <a href="products.php" class="btn btn-primary">Xem tất cả</a>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentProducts = array_slice($products, 0, 8);
                        foreach ($recentProducts as $product): 
                            $categoryNames = [
                                'fashion' => 'Thời trang',
                                'electronics' => 'Điện tử',
                                'home' => 'Gia dụng',
                                'books' => 'Sách'
                            ];
                        ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="<?php echo $product['image']; ?>" alt="" class="product-img">
                                    <span class="product-name"><?php echo $product['name']; ?></span>
                                </div>
                            </td>
                            <td><?php echo $categoryNames[$product['category']] ?? 'Khác'; ?></td>
                            <td><strong><?php echo number_format($product['price']); ?>đ</strong></td>
                            <td><span class="badge badge-success">Còn hàng</span></td>
                            <td>
                                <div class="actions">
                                    <button class="action-btn" title="Sửa" onclick="alert('Chức năng sửa')"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn" title="Xóa" onclick="confirm('Xóa sản phẩm?')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Revenue Chart -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-chart-line"></i> Doanh thu theo tháng</h2>
                    <div class="section-actions">
                        <a href="dashboard.php?export=csv" class="btn btn-primary">
                            <i class="fas fa-download"></i> Xuất báo cáo CSV
                        </a>
                        <a href="admin_reports.php" class="btn btn-secondary">
                            <i class="fas fa-chart-bar"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
                <div style="position: relative; height: 400px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:8px;">
                    <p style="color:#999; font-size:16px;">📊 Biểu đồ doanh thu (Chart disabled)</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
