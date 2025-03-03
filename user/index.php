<?php
// Start session and include authentication
require_once "../auth_check.php";
require '../db/db.php'; // Include database connection

// Check if the user is logged in and their role is 'user'
if ($_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// Pagination variables
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total records count
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch orders with pagination (Fixing the LIMIT and OFFSET issue)
$query = "SELECT cylinder_type AS Type, total_price AS Amount, tracking_id AS `Transaction ID`, created_at AS `Date / Time`
          FROM orders 
          WHERE user_id = ? 
          ORDER BY created_at DESC 
          LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]); // Only binding user_id
$orders = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Logo - hidden on mobile, visible on desktop -->
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
            <!-- Sidebar - Mobile Responsive -->
            <div id="sidebar" class="fixed inset-y-0 left-0 lg:relative lg:block bg-white z-50 w-64 h-screen overflow-y-auto transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
                <div class="flex flex-col h-full">
                    <!-- Mobile nav header -->
                    <div class="lg:hidden flex justify-between items-center p-4 border-b">
                        <img src="../asset/image/logo.png" alt="Jumandi Gas Logo" class="h-8">
                        <button id="closeMenu" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
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


                        <!-- Quick Action Items - visible only on mobile -->
                        <div class="lg:hidden mt-8 px-4">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="user-deposit.php" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                                    <div class="mb-2">⬇️</div>
                                    <div>Deposit</div>
                                </a>
                                <a href="order-page.php" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                                    <div class="mb-2">🚚</div>
                                    <div>Orders Gas</div>
                                </a>
                                <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                                    <div class="mb-2">🛒</div>
                                    <div>Buy Cylinder</div>
                                </a>
                                <a href="user-order-history.php" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                                    <div class="text-2xl font-bold">5</div>
                                    <div>Total Orders</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Dashboard Content -->
            <div class="flex-1">
                <!-- Action Cards - visible on all devices -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">⬇️</div>
                        <div class="text-sm">Deposit</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">🚚</div>
                        <div class="text-sm">Orders Gas</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">🛒</div>
                        <div class="text-sm">Buy Cylinder</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold">5</div>
                        <div class="text-sm">Total Orders</div>
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="bg-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full">
        <thead class="bg-[#ff6b00] text-white">
            <tr>
                <th class="p-2 lg:p-4 text-left">Type</th>
                <th class="p-2 lg:p-4 text-left">Amount</th>
                <th class="p-2 lg:p-4 text-left hidden sm:table-cell">Transaction ID</th>
                <th class="p-2 lg:p-4 text-left hidden md:table-cell">Date / Time</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order) { ?>
            <tr class="border-b border-gray-300">
                <td class="p-2 lg:p-4"><?= htmlspecialchars($order['Type']) ?></td>
                <td class="p-2 lg:p-4"><?= htmlspecialchars($order['Amount']) ?></td>
                <td class="p-2 lg:p-4 hidden sm:table-cell"><?= htmlspecialchars($order['Transaction ID']) ?></td>
                <td class="p-2 lg:p-4 hidden md:table-cell"><?= htmlspecialchars($order['Date / Time']) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
                <!-- Pagination Controls -->
                <div class="flex justify-center gap-4 mt-6">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="bg-[#ff6b00] text-white px-4 py-2 rounded-lg text-sm">← Prev</a>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="bg-[#ff6b00] text-white px-4 py-2 rounded-lg text-sm">Next →</a>
                    <?php endif; ?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let inactivityTime = 0; // Time in seconds

    function resetTimer() {
        inactivityTime = 0; // Reset the timer when user interacts
    }

    // Listen for user activity
    $(document).on('mousemove keypress click scroll', function () {
        resetTimer();
    });

    // Check inactivity every second
    setInterval(function () {
        inactivityTime++;
        if (inactivityTime >= 300) { // 300 seconds = 5 minutes
            autoLogout();
        }
    }, 1000);

    function autoLogout() {
        $.post("logout.php", { ajax: true }, function (response) {
            let data = JSON.parse(response);
            if (data.status === "success") {
                alert(data.message);
                window.location.href = "../login.php"; // Redirect to login page
            }
        });
    }
</script>

</body>
</html>

