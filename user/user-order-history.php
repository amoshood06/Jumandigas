<?php 
session_start();
require '../db/db.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jumandi Gas - Order History</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    @media (max-width: 1023px) {
        .sidebar-open { transform: translateX(0); }
        .sidebar-closed { transform: translateX(-100%); }
    }
</style>
</head>
<body class="bg-[#ff6b00]">

<!-- Header -->
<header class="flex justify-between items-center p-4">
    <button id="menuButton" class="lg:hidden text-white p-2">
        <i class="fas fa-bars"></i>
    </button>

    <img src="../asset/image/logos.png" alt="Jumandi Gas Logo" class="h-12 hidden lg:block">

    <div class="text-white">
            <p class="text-sm">Wallet</p>
            <p class="text-2xl font-bold">
            <span id="currencySymbol"></span> 
            <span id="currentBalance">0.00</span>
            </p>
            
        </div>
        <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        fetchBalance();
                    });

                    function fetchBalance() {
                        fetch("fetch_balance.php") // Create a new file to get balance
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById("currentBalance").innerText = data.balance;
                                document.getElementById("currencySymbol").innerText = data.currency;
                            }
                        })
                        .catch(error => console.error("Error fetching balance:", error));
                    }
                </script>
    <a href="logout.php">
        <button class="bg-gray-200 px-6 py-2 rounded-full font-bold">Logout</button>
    </a>
</header>

<!-- Main Content -->
<main class="bg-white rounded-t-[2rem] min-h-screen p-4 lg:p-6 lg:ml-0">
    <div class="flex gap-6 relative">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 lg:relative lg:block bg-white z-50 w-64 h-screen overflow-y-auto transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full">
                <div class="lg:hidden flex justify-between items-center p-4 border-b">
                    <img src="../asset/image/logo.png" alt="Jumandi Gas Logo" class="h-8">
                    <button id="closeMenu" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto">
                    
                    
                    <nav class="space-y-2 px-4">
    <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Home</a>
    <a href="user-deposit.php" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
    <a href="item-tracking.php" class="block p-3 hover:bg-orange-100 rounded-lg">Track</a>
    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
    <!-- Order Gas Dropdown -->
    <div class="relative group">
        <button class="block w-full text-left p-3 hover:bg-orange-100 rounded-lg">Order Gas</button>
        <div class="absolute hidden group-hover:block bg-white shadow-md rounded-lg mt-1 w-48">
            <a href="order-page.php" class="block p-3 hover:bg-orange-100">New Order</a>
            <a href="order-history.php" class="block p-3 hover:bg-orange-100">Order History</a>
        </div>
    </div>
    
    <a href="withdrawal.php" class="block p-3 hover:bg-orange-100 rounded-lg">Withdrawal</a>
    <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Setting</a>
</nav>

                </div>
            </div>
        </div>

        <!-- Overlay for mobile -->
        <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

        <!-- Order History Content -->
        <div class="flex-1 w-full">
            <h1 class="text-2xl font-bold mb-6">My Orders</h1>
            
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Vendor</th>
                            <th class="px-6 py-3 text-left">Amount (Kg)</th>
                            <th class="px-6 py-3 text-left">Total Price</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="px-6 py-4"><?= htmlspecialchars($order['id']); ?></td>
                            <td class="px-6 py-4">Vendor<?= htmlspecialchars($order['vendor_id']); ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($order['amount_kg']); ?> Kg</td>
                            <td class="px-6 py-4"><?= htmlspecialchars($order['total_price']); ?> NGN</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs font-semibold rounded-full <?= ($order['status'] === 'pending') ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?= ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($order['status'] === 'pending'): ?>
                                    <button onclick="cancelOrder(<?= $order['id']; ?>)" class="text-red-600 hover:text-red-900">Cancel</button>
                                <?php else: ?>
                                    <span class="text-gray-500">N/A</span>
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

    function cancelOrder(orderId) {
        if (confirm("Are you sure you want to cancel this order?")) {
            fetch('cancel_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_id=' + orderId
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }
    }
</script>
</body>
</html>