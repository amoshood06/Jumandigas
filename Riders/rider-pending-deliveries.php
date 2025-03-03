<?php
require '../db/db.php'; // Database connection

try {
    // Fetch today's deliveries
    $stmt = $pdo->prepare("SELECT o.*, u.full_name, u.country, u.phone, u.state, u.city, o.tracking_id, o.status, o.rider_id 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE DATE(o.created_at) = CURDATE()");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
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
        function startDelivery(orderId, trackId) {
            if (!navigator.geolocation) {
                alert("Geolocation is not supported by your browser");
                return;
            }

            navigator.geolocation.getCurrentPosition(position => {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                fetch("start_delivery.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `order_id=${orderId}&track_id=${trackId}&latitude=${latitude}&longitude=${longitude}`
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === "success") location.reload();
                });
            }, () => {
                alert("Please enable location services to start delivery.");
            });
        }

        function cancelRider(riderId, orderId) {
            if (!confirm("Are you sure you want to cancel? A new rider will be assigned.")) return;

            fetch("cancel_rider.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `rider_id=${riderId}&order_id=${orderId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === "success") location.reload();
            });
        }
    </script>
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
            <p class="text-sm">Rider Account</p>
            <p class="text-2xl font-bold">John Doe</p>
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
                            <img src="/placeholder.svg?height=100&width=100" alt="Rider Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">John Doe</h2>
                            <p class="text-sm text-gray-500">Rider ID: R12345</p>
                        </div> -->
                        
                        <nav class="space-y-2 px-4">
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="#" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Pending Deliveries</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Pending Deliveries Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Pending Deliveries</h1>
                
                <!-- Delivery Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Pending Pickups</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">3</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">En Route</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">2</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Completed Today</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">5</p>
                    </div>
                </div>

                <!-- Delivery List -->
                <!-- Delivery List -->
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
                                <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars("{$order['city']}, {$order['state']}, {$order['country']}") ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 inline-flex text-xs font-semibold rounded-full <?= $order['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <?php if ($order['status'] == 'Pending'): ?>
                                        <button onclick="startDelivery('<?= $order['id'] ?>', '<?= $order['track_id'] ?>')" class="text-[#ff6b00] hover:text-[#e05e00]">Start</button>
                                    <?php elseif ($order['status'] == 'En Route'): ?>
                                        <button onclick="cancelRider('<?= $order['rider_id'] ?>', '<?= $order['id'] ?>')" class="text-red-500 hover:text-red-700">Cancel</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
       
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

    </script>
</body>
</html>