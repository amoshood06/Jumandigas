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
    <title>Jumandi Gas - Vendor Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
    <style>
        @media (max-width: 1023px) {
            .sidebar-open {
                transform: translateX(0);
            }
            .sidebar-closed {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body class="bg-[#ff6b00]">
    <!-- Header -->
    <header class="flex justify-between items-center p-4">
        <!-- Mobile Menu Button -->
        <button id="menuButton" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Logo - hidden on mobile, visible on desktop -->
        <img src="../asset/image/logos.png" alt="Jumandi Gas Logo" class="h-12 hidden lg:block">

        <div class="text-white">
            <p class="text-sm">Vendor Account</p>
            <p class="text-2xl font-bold">Vendor Name</p>
        </div>
        <a href="logout.php">
            <button class="bg-gray-200 px-6 py-2 rounded-full font-bold">Logout</button>
        </a>
    </header>

    <!-- Main Content -->
    <main class="bg-white rounded-t-[2rem] min-h-screen p-4 lg:p-6 lg:ml-0">
        <div class="flex gap-6 relative">
            <!-- Sidebar - Mobile Responsive -->
            <div id="sidebar" class="fixed inset-y-0 left-0 lg:relative lg:block bg-white z-50 w-64 h-screen overflow-y-auto transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
                <div class="flex flex-col h-full">
                    <!-- Mobile nav header -->
                    <div class="lg:hidden flex justify-between items-center p-4 border-b">
                        <img src="../asset/image/logo.png" alt="Jumandi Gas Logo" class="h-8">
                        <button id="closeMenu" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex-grow overflow-y-auto">
                        <!-- <div class="flex flex-col items-center my-8">
                            <img src="/placeholder.svg?height=100&width=100" alt="Vendor Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">Vendor Name</h2>
                        </div> -->
                        
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="withdrawal.php" class="block p-3 hover:bg-orange-100 rounded-lg">Withdrawal</a>
                            <a href="vendor-order-management.php" class="block p-3 hover:bg-orange-100 rounded-lg">Orders</a>
                            <a href="vendor-transactions.php" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="vendor-report.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Reports</a>
                            <a href="vendor-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Reports Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Vendor Reports</h1>
                
                <!-- Report Filters -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
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
                
                
            </div>
        </div>
    </main>

    <script>
        // Mobile menu functionality
        const menuButton = document.getElementById('menuButton');
        const closeButton = document.getElementById('closeMenu');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function toggleMenu() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        menuButton.addEventListener('click', toggleMenu);
        closeButton.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        // Close menu when clicking a link (mobile)
        const navLinks = sidebar.getElementsByTagName('a');
        for (const link of navLinks) {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) { // lg breakpoint
                    toggleMenu();
                }
            });
        }

        // Handle resize events
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { // lg breakpoint
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Handle report form submission
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real application, you would generate the report based on the selected options
            console.log('Generating report...');
            alert('Report generated successfully!');
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [1200000, 1900000, 300000, 500000, 2000000, 3000000],
                    borderColor: 'rgb(255, 107, 0)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>