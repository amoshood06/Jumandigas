<?php 
session_start();
require '../db/db.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch vendors in the same location
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'vendor' AND country = ? AND state = ? AND city = ?");
$stmt->execute([$user['country'], $user['state'], $user['city']]);
$vendors = $stmt->fetchAll();

// Fetch gas price per kg
$stmt = $pdo->prepare("SELECT price FROM locations WHERE country = ? AND state = ?");
$stmt->execute([$user['country'], $user['state']]);
$location = $stmt->fetch();
$price_per_kg = $location ? $location['price'] : 0; // Renamed variable

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cylinder_type = $_POST['cylinder_type'];
    $exchange = $_POST['exchange'];
    $amount_kg = $_POST['amount_kg'];
    $vendor_id = $_POST['vendor_id'];
    $total_price = $amount_kg * $price_per_kg; // Correct variable
    $currency = $user['currency'];

    // Check if user has enough balance
    if ($user['balance'] >= $total_price) {
        // Deduct from user balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$total_price, $user_id]);

        // Generate tracking ID
        $tracking_id = "TRK" . strtoupper(uniqid());

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, vendor_id, cylinder_type, exchange, amount_kg, total_price, currency, tracking_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $vendor_id, $cylinder_type, $exchange, $amount_kg, $total_price, $currency, $tracking_id]);

        echo "<script>alert('Order placed! Tracking ID: $tracking_id'); window.location='user-order-history.php';</script>";
    } else {
        echo "<script>alert('Insufficient balance!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Order Page</title>
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
            <p class="text-sm">Wallet</p>
            <p class="text-2xl font-bold">N20,000</p>
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
                <div class="flex flex-col items-center my-8">
                    <img src="/placeholder.svg?height=100&width=100" alt="Profile" class="rounded-full w-24 h-24 mb-4">
                </div>
                
                <nav class="space-y-2 px-4">
                    <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Home</a>
                    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
                    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
                    <a href="order-page.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas</a>
                    <a href="order-page.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas History</a>
                    <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
                    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Setting</a>
                </nav>

                <!-- Quick Action Items - visible only on mobile -->
                <div class="lg:hidden mt-8 px-4">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                            <div class="mb-2">⬇️</div>
                            <div>Deposit</div>
                        </a>
                        <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                            <div class="mb-2">🚚</div>
                            <div>Orders Gas</div>
                        </a>
                        <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                            <div class="mb-2">🛒</div>
                            <div>Buy Cylinder</div>
                        </a>
                        <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                            <div class="text-2xl font-bold">5</div>
                            <div>Total Orders</div>
                        </a>
                    </div>
                </div>

                <!-- Sidebar content (same as in your original HTML) -->
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Order Page Content -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold mb-6">Order Gas</h1>
                <form method="POST">
                <!-- Gas Options -->
                <div class="flex flex-col gap-2">
                    <!-- 1kg Gas Option -->
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold">Your Cylinder</h3>
                        </div>
                       <select type="text" name="cylinder_type" required class="w-full p-2 border rounded mb-2">
                        <option value="">Select cylinder</option>
                        <option value="1kg">1kg</option>
                        <option value="3kg">3kg</option>
                        <option value="4kg">4kg</option>
                       </select>
                    </div>
                    
                    <!-- exchange Option -->
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold">Cylinder Exchange</h3>
                        </div>
                       <select type="text" name="exchange" required class="w-full p-2 border rounded mb-2">
                        <option value="">Select</option>
                        <option value="exchange">Exchange</option>
                        <option value="pick return">Pick Return</option>
                       </select>
                    </div>

                    <!-- amount of kg -->
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold">Amount of kg</h3>
                        </div>
                        
                        <input type="number" name="amount_kg" id="amount_kg" required class="w-full p-2 border rounded mb-2" oninput="calculateTotal()">
                        
                    </div>

                        <!-- amount of kg -->
                    <div class="flex flex-col p-4 bg-white rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold">Select Vendor:</h3>
                        </div>
                        <select name="vendor_id" required class="w-full p-2 border rounded mb-2">
                            <?php foreach ($vendors as $vendor) { ?>
                                <option value="<?= $vendor['id'] ?>"><?= $vendor['full_name'] ?> (<?= $vendor['city'] ?>, <?= $vendor['state'] ?>)</option>
                            <?php } ?>
                        </select>
                        
                    </div>
                    
                </div>

                <!-- Order Summary -->
                <div class="mt-8 bg-[#ff6b00] text-white p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <span>Price per Kg:</span>
                        <span id="price"><?= $price_per_kg ?></span> <?= $user['currency'] ?>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span>Total Cost:</span>
                        <span id="total_price">0</span> <?= $user['currency'] ?>
                    </div>
                    <button type="submit" class="w-full bg-white text-[#ff6b00] py-2 rounded-lg font-semibold flex items-center justify-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Place Order
                    </button>
                </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        function calculateTotal() {
            let amount = document.getElementById("amount_kg").value;
            let price = document.getElementById("price").innerText;
            document.getElementById("total_price").innerText = (amount * price).toFixed(2);
        }
    </script>
    <script>
        // Mobile menu functionality (same as in your original HTML)
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