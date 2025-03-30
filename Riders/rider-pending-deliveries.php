<?php
// Start the session (if not already started)
session_start();

// Check if user is logged in and is a rider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    // Redirect to login page if not logged in or not a rider
    header("Location: login.php");
    exit();
}

// Include database connection
require_once '../db/db.php';

// Get rider ID
$rider_id = $_SESSION['user_id'];

// Fetch pending deliveries for this rider
try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.full_name as customer_name, u.address as customer_address, u.phone as customer_phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.rider_id = ? AND o.status = 'moving'
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$rider_id]);
    $pending_deliveries = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching pending deliveries: " . $e->getMessage());
}

// Fetch rider information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'rider'");
    $stmt->execute([$rider_id]);
    $rider = $stmt->fetch();
    
    if (!$rider) {
        // Rider not found or not a rider
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching rider data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Pending Deliveries</title>
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
            <p class="text-2xl font-bold"><?php echo htmlspecialchars($rider['full_name']); ?></p>
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
                        <nav class="space-y-2 px-4 mt-6">
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="rider-pending-deliveries.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Pending Deliveries</a>
                            <a href="rider-delivery-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                            <a href="rider-performance.php" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                            <a href="rider_profile.php" class="block p-3 hover:bg-orange-100 rounded-lg">Profile</a>
                            <a href="rider-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Pending Deliveries Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Pending Deliveries</h1>
                
                <?php if (empty($pending_deliveries)): ?>
                <div class="bg-white shadow rounded-lg p-8 text-center">
                    <div class="text-gray-400 text-5xl mb-4">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h2 class="text-xl font-semibold mb-2">No Pending Deliveries</h2>
                    <p class="text-gray-500">You don't have any pending deliveries at the moment.</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($pending_deliveries as $delivery): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-lg font-semibold">Order #<?php echo htmlspecialchars($delivery['id']); ?></h2>
                                <p class="text-sm text-gray-500">Tracking ID: <?php echo htmlspecialchars($delivery['tracking_id']); ?></p>
                            </div>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                <?php echo ucfirst(htmlspecialchars($delivery['status'])); ?>
                            </span>
                        </div>
                        
                        <div class="space-y-3 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Customer</p>
                                <p class="font-medium"><?php echo htmlspecialchars($delivery['customer_name']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Delivery Address</p>
                                <p class="font-medium"><?php echo htmlspecialchars($delivery['customer_address']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium"><?php echo htmlspecialchars($delivery['customer_phone']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Order Details</p>
                                <p class="font-medium">
                                    <?php echo htmlspecialchars($delivery['amount_kg']); ?> x <?php echo htmlspecialchars($delivery['cylinder_type']); ?> 
                                    (<?php echo htmlspecialchars($delivery['exchange']); ?>)
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="font-medium text-lg">
                                    <?php echo htmlspecialchars($delivery['currency']); ?> <?php echo number_format($delivery['total_price'], 2); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="tel:<?php echo htmlspecialchars($delivery['customer_phone']); ?>" class="flex-1 px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 text-center">
                                <i class="fas fa-phone mr-2"></i> Call Customer
                            </a>
                            <form action="update_delivery.php" method="post" class="flex-1">
                                <input type="hidden" name="order_id" value="<?php echo $delivery['id']; ?>">
                                <input type="hidden" name="action" value="complete">
                                <button type="submit" class="w-full px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00]">
                                    <i class="fas fa-check mr-2"></i> Mark as Delivered
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
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