<?php
require_once 'includes/common.php';

// Optional: Check if user is admin (commented out - everyone can access)
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit;
// }

// Demo report data
$monthly_revenue = [
    ['month' => 'T1', 'revenue' => 85000000, 'orders' => 124],
    ['month' => 'T2', 'revenue' => 92000000, 'orders' => 135],
    ['month' => 'T3', 'revenue' => 78000000, 'orders' => 98],
    ['month' => 'T4', 'revenue' => 105000000, 'orders' => 156],
    ['month' => 'T5', 'revenue' => 118000000, 'orders' => 178],
    ['month' => 'T6', 'revenue' => 125000000, 'orders' => 189],
];

$top_products = [
    ['name' => 'Laptop Gaming', 'sold' => 45, 'revenue' => 71995500],
    ['name' => 'Điện Thoại Smartphone', 'sold' => 89, 'revenue' => 44491100],
    ['name' => 'Đồng Hồ Nam Cao Cấp', 'sold' => 67, 'revenue' => 87033000],
    ['name' => 'Tai Nghe Bluetooth', 'sold' => 123, 'revenue' => 73677000],
    ['name' => 'Quần Jeans Slim Fit', 'sold' => 156, 'revenue' => 77844000],
];

$category_stats = [
    ['category' => 'Thời trang', 'sales' => 234, 'revenue' => 145000000, 'color' => '#1976d2'],
    ['category' => 'Điện tử', 'sales' => 189, 'revenue' => 285000000, 'color' => '#7b1fa2'],
    ['category' => 'Gia dụng', 'sales' => 167, 'revenue' => 98000000, 'color' => '#388e3c'],
    ['category' => 'Sách', 'sales' => 98, 'revenue' => 12000000, 'color' => '#f57c00'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        .filter-row {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-row select {
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .chart-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .table-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .table-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .report-table thead {
            background: #f8f9fa;
        }
        
        .report-table th {
            padding: 0.75rem;
            text-align: left;
            color: #666;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
        }
        
        .report-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .report-table tbody tr:hover {
            background: #fdf7eb;
        }
        
        .category-bar {
            height: 8px;
            background: linear-gradient(90deg, #e37b58 0%, #d16c4c 100%);
            border-radius: 4px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <h1><i class="fas fa-chart-line"></i> Báo cáo & Thống kê</h1>
            <div class="header-nav">
                <a href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a>
                <a href="admin_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
                <a href="admin_customers.php"><i class="fas fa-users"></i> Khách hàng</a>
                <a href="index.php"><i class="fas fa-home"></i> Về trang chủ</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="filter-row">
            <i class="fas fa-filter"></i>
            <select>
                <option>6 tháng gần nhất</option>
                <option>3 tháng gần nhất</option>
                <option>Tháng này</option>
                <option>Năm nay</option>
            </select>
            <select>
                <option>Tất cả danh mục</option>
                <option>Thời trang</option>
                <option>Điện tử</option>
                <option>Gia dụng</option>
                <option>Sách</option>
            </select>
        </div>
        
        <div class="charts-grid">
            <div class="chart-card">
                <h2><i class="fas fa-chart-bar"></i> Doanh thu theo tháng</h2>
                <canvas id="revenueChart"></canvas>
            </div>
            
            <div class="chart-card">
                <h2><i class="fas fa-chart-pie"></i> Doanh thu theo danh mục</h2>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        
        <div class="tables-grid">
            <div class="table-card">
                <h2><i class="fas fa-trophy"></i> Sản phẩm bán chạy</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>Đã bán</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_products as $index => $product): ?>
                        <tr>
                            <td><strong><?php echo $index + 1; ?></strong></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['sold']; ?> sp</td>
                            <td><strong><?php echo number_format($product['revenue']); ?> ₫</strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-card">
                <h2><i class="fas fa-tag"></i> Thống kê theo danh mục</h2>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th>Số lượng</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($category_stats as $cat): ?>
                        <tr>
                            <td>
                                <strong><?php echo $cat['category']; ?></strong>
                                <div class="category-bar" style="background: <?php echo $cat['color']; ?>; width: <?php echo ($cat['revenue'] / 3000000); ?>%;"></div>
                            </td>
                            <td><?php echo $cat['sales']; ?> sp</td>
                            <td><strong><?php echo number_format($cat['revenue']); ?> ₫</strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($monthly_revenue, 'month')); ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?php echo json_encode(array_column($monthly_revenue, 'revenue')); ?>,
                    backgroundColor: 'rgba(227, 123, 88, 0.8)',
                    borderColor: 'rgba(227, 123, 88, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000) + 'M';
                            }
                        }
                    }
                }
            }
        });
        
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($category_stats, 'category')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($category_stats, 'revenue')); ?>,
                    backgroundColor: <?php echo json_encode(array_column($category_stats, 'color')); ?>,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
