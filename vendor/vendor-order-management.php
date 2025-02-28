<?php
require_once "../auth_check.php"; // Ensure user is authenticated
require_once "../db/db.php"; // Include the database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];

// Fetch orders for this vendor
$sql = "SELECT o.id, o.amount_kg, o.total_price, o.currency, o.cylinder_type, o.tracking_id, 
               o.status, o.vendor_id, o.rider_id, 
               u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        WHERE o.vendor_id = :vendor_id AND u.role = 'user'";

$stmt = $pdo->prepare($sql); // Ensure $pdo is defined in db.php
$stmt->execute(['vendor_id' => $vendor_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available riders in the same country, state, and city as the vendor
$sql_riders = "SELECT id, full_name FROM users 
               WHERE role = 'rider' 
               AND country = (SELECT country FROM users WHERE id = :vendor_id) 
               AND state = (SELECT state FROM users WHERE id = :vendor_id) 
               AND city = (SELECT city FROM users WHERE id = :vendor_id)";
               
$stmt_riders = $pdo->prepare($sql_riders);
$stmt_riders->execute(['vendor_id' => $vendor_id]);
$riders = $stmt_riders->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Vendor Order Management</title>
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
                                            
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="vendor-order-management.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Orders</a>
                            <a href="vendor-transactions.php" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="vendor-report.php" class="block p-3 hover:bg-orange-100 rounded-lg">Reports</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Order Management Content -->
            <div class="flex-1 w-full">
                        <h2 class="text-2xl font-bold text-center mb-6">Order Management</h2>

                        <?php if (isset($_GET['message'])) { ?>
            <p class="text-green-600"><?php echo $_GET['message']; ?></p>
        <?php } elseif (isset($_GET['error'])) { ?>
            <p class="text-red-600"><?php echo $_GET['error']; ?></p>
        <?php } ?>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($orders as $order) { ?>
        <div class="bg-white shadow-md rounded-lg p-4 border">
            <h3 class="text-lg font-bold mb-2">Order #<?php echo $order['id']; ?></h3>
            <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?> (<?php echo $order['customer_phone']; ?>)</p>
            <p><strong>Address:</strong> <?php echo $order['customer_address']; ?></p>
            <p><strong>Amount:</strong> <?php echo $order['amount_kg']; ?> KG</p>
            <p><strong>Total Price:</strong> <?php echo $order['total_price']; ?> <?php echo $order['currency']; ?></p>
            <p><strong>Cylinder Type:</strong> <?php echo $order['cylinder_type']; ?></p>
            <p><strong>Tracking ID:</strong> <?php echo $order['tracking_id']; ?></p>
            <p class="font-bold text-<?php echo ($order['status'] == 'pending') ? 'yellow-500' : (($order['status'] == 'processing') ? 'blue-500' : 'green-500'); ?>">
                <strong>Status:</strong> <?php echo ucfirst($order['status']); ?>
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                <?php if ($order['status'] == 'pending') { ?>
                    <form action="accept_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Accept</button>
                    </form>
                    <form action="reject_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Reject</button>
                    </form>
                <?php } elseif ($order['status'] == 'processing' && $order['rider_id'] == NULL) { ?>
                    <form action="assign_rider.php" method="POST" class="w-full">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="rider_id" class="border px-2 py-1 rounded w-full">
                            <option value="">Select Rider</option>
                            <?php foreach ($riders as $rider) { ?>
                                <option value="<?php echo $rider['id']; ?>"><?php echo $rider['full_name']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 mt-2 rounded w-full">Assign Rider</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
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