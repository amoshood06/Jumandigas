<?php 
require_once "../auth_check.php";
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

require '../db/db.php'; // Ensure database connection

// Get filter values from AJAX request
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$whereClause = "";
$params = [];

if ($startDate && $endDate) {
    $whereClause = "WHERE order_date BETWEEN ? AND ?";
    $params = [$startDate, $endDate];
}

// Ensure 'order_price' exists, or update to the correct column in the database
$salesQuery = "SELECT SUM(order_price) AS total_sales, COUNT(id) AS total_orders FROM orders $whereClause";
$salesStmt = $pdo->prepare($salesQuery);
$salesStmt->execute($params);
$salesData = $salesStmt->fetch();
$totalSales = $salesData['total_sales'] ?? 0;
$totalOrders = $salesData['total_orders'] ?? 0;

// Fetch top-selling products
$productsQuery = "SELECT product_name, SUM(quantity) AS total_quantity FROM order_items 
                  JOIN products ON order_items.product_id = products.id 
                  $whereClause 
                  GROUP BY product_name 
                  ORDER BY total_quantity DESC 
                  LIMIT 5";
$productsStmt = $pdo->prepare($productsQuery);
$productsStmt->execute($params);
$topProducts = $productsStmt->fetchAll();

// Fetch sales chart data
$chartQuery = "SELECT DATE(order_date) AS date, SUM(order_price) AS sales FROM orders 
               $whereClause 
               GROUP BY DATE(order_date) 
               ORDER BY order_date";
$chartStmt = $pdo->prepare($chartQuery);
$chartStmt->execute($params);
$chartData = $chartStmt->fetchAll();

// Return JSON response for AJAX calls
if (isset($_GET['ajax'])) {
    echo json_encode([
        'total_sales' => $totalSales,
        'total_orders' => $totalOrders,
        'top_products' => $topProducts,
        'chart_data' => $chartData
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Reports - Jumandi Gas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#ff6b00]">
    <header class="flex justify-between items-center p-4">
        <button id="menuButton" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>
        <img src="../asset/image/logos.png" alt="Logo" class="h-12 hidden lg:block">
        <div class="text-white">
            <p class="text-sm">Vendor Account</p>
            <p class="text-2xl font-bold">Vendor Name</p>
        </div>
        <a href="logout.php">
            <button class="bg-gray-200 px-6 py-2 rounded-full font-bold">Logout</button>
        </a>
    </header>

    <main class="bg-white rounded-t-[2rem] min-h-screen p-4 lg:p-6">
        <h1 class="text-2xl font-bold mb-6">Vendor Reports</h1>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Generate Report</h2>
            <form id="reportForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Date Range</label>
                        <input type="date" id="startDate" class="border p-2 rounded w-full">
                        <input type="date" id="endDate" class="border p-2 rounded w-full mt-2">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md">Generate Report</button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Report Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500">Total Sales</h3>
                    <p id="totalSales" class="text-2xl font-bold text-gray-900">₦0</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                    <p id="totalOrders" class="text-2xl font-bold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Sales Trend</h2>
            <canvas id="salesChart"></canvas>
        </div>
    </main>

    <script>
        document.getElementById('reportForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            let startDate = document.getElementById('startDate').value;
            let endDate = document.getElementById('endDate').value;
            
            fetch(`vendor-report.php?ajax=1&start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalSales').innerText = '₦' + (data.total_sales || 0);
                    document.getElementById('totalOrders').innerText = data.total_orders || 0;
                    updateChart(data.chart_data);
                })
                .catch(error => console.error('Error fetching data:', error));
        });

        function updateChart(data) {
            let ctx = document.getElementById('salesChart').getContext('2d');
            let labels = data.map(entry => entry.date);
            let salesData = data.map(entry => entry.sales);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sales',
                        data: salesData,
                        borderColor: '#ff6b00',
                        backgroundColor: 'rgba(255, 107, 0, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                }
            });
        }
    </script>
</body>
</html>
