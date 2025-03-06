<?php
session_start();
require '../db/db.php'; // Include database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    die("Access denied: Not a rider.");
}

if (!isset($_SESSION['user_id'])) {
    die("Error: User ID not set in session.");
}

$user_id = $_SESSION['user_id'];
echo "<p>Debug: User ID is $user_id</p>"; // Debugging

try {
    $stmt = $pdo->prepare("SELECT o.*, u.full_name, u.phone, u.address, u.city, u.state 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.rider_id = ?");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        echo "<p>No orders found for this rider.</p>"; // Debugging
    }
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}

//var_dump($orders); // Debugging
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Rider Pending Deliveries</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
    <script>
        function deliverOrder(orderId) {
    if (!confirm("Are you sure you want to mark this order as delivered?")) return;

    fetch("deliver_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `order_id=${orderId}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") location.reload();
    });
}

       function startDelivery(orderId, trackId) {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser");
        return;
    }

    navigator.geolocation.getCurrentPosition(
        position => {
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;

            fetch("start_delivery.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `order_id=${orderId}&track_id=${trackId}&latitude=${latitude}&longitude=${longitude}`
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Debugging response
                alert(data.message);
                if (data.status === "success") {
                    location.reload();
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while starting the delivery.");
            });
        }, 
        error => {
            console.error("Geolocation error:", error);
            alert("Please enable location services to start delivery.");
        }
    );
}

    </script>
</head>
<body class="bg-[#ff6b00]">
    <!-- Header -->
    <header class="flex justify-between items-center p-4">
        <button id="menuButton" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>
        <img src="../asset/image/logos.png" alt="Jumandi Gas Logo" class="h-12 hidden lg:block">
        <div class="text-white">
            <p class="text-sm">Rider Account</p>
            <p class="text-2xl font-bold"><?= htmlspecialchars($_SESSION['user_id']); ?></p>
        </div>
        <a href="logout.php">
            <button class="bg-gray-200 px-6 py-2 rounded-full font-bold">Logout</button>
        </a>
    </header>

    <!-- Main Content -->
    <main class="bg-white rounded-t-[2rem] min-h-screen p-4 lg:p-6 lg:ml-0">
        <div class="flex gap-6 relative">
            <!-- Sidebar -->
            <div id="sidebar" class="fixed inset-y-0 left-0 lg:relative lg:block bg-white z-50 w-64 h-screen overflow-y-auto transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
                <nav class="space-y-2 px-4">
                    <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Dashboard</a>
                    <a href="rider-pending-deliveries.php" class="block p-3 hover:bg-orange-100 rounded-lg">Pending Deliveries</a>
                    <a href="rider-delivery-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                    <a href="rider-performance.php" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                    <a href="rider-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                </nav>
            </div>

            <!-- Pending Deliveries -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Pending Deliveries</h1>
                
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Track ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($order['tracking_id']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($order['full_name']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($order['phone']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars("{$order['address']}, {$order['city']}, {$order['state']}") ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full <?= $order['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium"> 
                                    <?php if ($order['status'] == 'processing'): ?>
                                        <button onclick="startDelivery('<?= htmlspecialchars($order['id']) ?>', '<?= htmlspecialchars($order['tracking_id']) ?>')" class="text-black hover:text-[#e05e00]">
                                            Start
                                        </button>
                                    <?php elseif ($order['status'] == 'moving'): ?> 
                                        <button onclick="deliverOrder('<?= htmlspecialchars($order['id']) ?>')" class="text-green-500 hover:text-green-700">
                                            Deliver
                                        </button>
                                    <?php elseif ($order['status'] == 'processing'): ?> 
                                        <button onclick="cancelRider('<?= htmlspecialchars($order['rider_id']) ?>', '<?= htmlspecialchars($order['id']) ?>')" class="text-red-500 hover:text-red-700">
                                            Cancel
                                        </button>
                                    <?php endif; ?>
                                </td>


                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
