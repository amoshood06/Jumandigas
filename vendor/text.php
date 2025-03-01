<?php
require_once "../auth_check.php";
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

require '../db/db.php'; // Database connection

$vendor_id = $_SESSION['user_id']; // Assuming vendor is logged in

// Fetch orders for vendor
$stmt = $pdo->prepare("SELECT * FROM orders WHERE vendor_id = ? ORDER BY created_at DESC");
$stmt->execute([$vendor_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sales data for chart
$chart_stmt = $pdo->prepare("SELECT DATE(created_at) as order_date, SUM(total_price) as total_sales FROM orders WHERE vendor_id = ? GROUP BY order_date");
$chart_stmt->execute([$vendor_id]);
$sales_data = $chart_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Vendor Sales Report</h2>
    <button onclick="window.print()">Print Report</button>
    
    <canvas id="salesChart" width="400" height="200"></canvas>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Cylinder Type</th>
            <th>Exchange</th>
            <th>Amount (kg)</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['cylinder_type']) ?></td>
            <td><?= htmlspecialchars($order['exchange']) ?></td>
            <td><?= $order['amount_kg'] ?></td>
            <td><?= htmlspecialchars($order['currency']) ?><?= $order['total_price'] ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td><?= $order['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($sales_data, 'order_date')) ?>,
                datasets: [{
                    label: 'Total Sales',
                    data: <?= json_encode(array_column($sales_data, 'total_sales')) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>
