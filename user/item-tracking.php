<?php
// Start session and include authentication
require_once "../auth_check.php";
require '../db/db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tracking_result = null;
$error_message = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['track_id'])) {
    $track_id = trim($_POST['track_id']);
    
    if (empty($track_id)) {
        $error_message = "Please enter a tracking ID";
    } else {
        try {
            // First check in orders table
            $stmt = $pdo->prepare("SELECT o.*, 
                                  u_vendor.full_name AS vendor_name,
                                  u_rider.full_name AS rider_name
                                  FROM orders o
                                  LEFT JOIN users u_vendor ON o.vendor_id = u_vendor.id
                                  LEFT JOIN users u_rider ON o.rider_id = u_rider.id
                                  WHERE o.tracking_id = ?");
            $stmt->execute([$track_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($order) {
                // Get rider location if available
                $rider_location = null;
                if ($order['rider_id']) {
                    $stmt = $pdo->prepare("SELECT * FROM riders WHERE track_id = ? ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$track_id]);
                    $rider_location = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                
                $tracking_result = [
                    'order' => $order,
                    'rider_location' => $rider_location
                ];
            } else {
                $error_message = "No order found with this tracking ID";
            }
        } catch (PDOException $e) {
            $error_message = "Error retrieving tracking information: " . $e->getMessage();
        }
    }
}

// Function to get status text and class
function getStatusInfo($status) {
    switch ($status) {
        case 'pending':
            return ['text' => 'Order Pending', 'class' => 'bg-yellow-100 text-yellow-800'];
        case 'processing':
            return ['text' => 'Processing', 'class' => 'bg-blue-100 text-blue-800'];
        case 'moving':
            return ['text' => 'On the Way', 'class' => 'bg-indigo-100 text-indigo-800'];
        case 'delivered':
            return ['text' => 'Delivered', 'class' => 'bg-green-100 text-green-800'];
        case 'canceled':
            return ['text' => 'Canceled', 'class' => 'bg-red-100 text-red-800'];
        default:
            return ['text' => 'Unknown', 'class' => 'bg-gray-100 text-gray-800'];
    }
}

// Function to get step completion status
function getStepStatus($orderStatus, $step) {
    $steps = [
        'pending' => 1,
        'processing' => 2,
        'moving' => 3,
        'delivered' => 4
    ];
    
    $currentStep = isset($steps[$orderStatus]) ? $steps[$orderStatus] : 0;
    $stepNumber = $steps[$step] ?? 0;
    
    if ($orderStatus === 'canceled') {
        return 'canceled';
    } elseif ($stepNumber < $currentStep) {
        return 'completed';
    } elseif ($stepNumber === $currentStep) {
        return 'active';
    } else {
        return 'pending';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Jumandi Gas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media (max-width: 1023px) {
            .sidebar-open {
                transform: translateX(0);
            }
            .sidebar-closed {
                transform: translateX(-100%);
            }
        }
        .tracking-step {
            position: relative;
        }
        .tracking-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 2.5rem;
            left: 1.25rem;
            height: calc(100% - 2.5rem);
            width: 2px;
            background-color: #e5e7eb;
        }
        .tracking-step.completed:not(:last-child)::after {
            background-color: #FF6B00;
        }
        .tracking-step.canceled:not(:last-child)::after {
            background-color: #EF4444;
        }
        .step-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e5e7eb;
            color: #6B7280;
        }
        .step-icon.completed {
            background-color: #FF6B00;
            color: white;
        }
        .step-icon.active {
            background-color: #FF6B00;
            color: white;
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.2);
        }
        .step-icon.canceled {
            background-color: #EF4444;
            color: white;
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
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Home</a>
                            <a href="user-deposit.php" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
                            <a href="item-tracking.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Track</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
                            
                            <!-- Order Gas Dropdown -->
                            <div class="relative group">
                                <button class="block w-full text-left p-3 hover:bg-orange-100 rounded-lg">Order Gas</button>
                                <div class="absolute hidden group-hover:block bg-white shadow-md rounded-lg mt-1 w-48">
                                    <a href="order-page.php" class="block p-3 hover:bg-orange-100">New Order</a>
                                    <a href="order-history.php" class="block p-3 hover:bg-orange-100">Order History</a>
                                </div>
                            </div>
                            <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
                            <a href="user-profile.php" class="block p-3 hover:bg-orange-100 rounded-lg">Profile</a>
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

            <!-- Tracking Content -->
            <div class="flex-1">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-2xl font-bold mb-6">Track Your Order</h1>
                    
                    <!-- Tracking Form -->
                    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                        <form method="POST" action="">
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="flex-1">
                                    <label for="track_id" class="block text-sm font-medium text-gray-700 mb-1">Tracking ID</label>
                                    <input type="text" id="track_id" name="track_id" placeholder="Enter your tracking ID" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                <div class="self-end">
                                    <button type="submit" class="px-6 py-2 bg-[#ff6b00] text-white font-medium rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        Track Order
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($tracking_result): ?>
                        <!-- Order Details -->
                        <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-semibold mb-4">Order Details</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-gray-600 mb-1">Tracking ID</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['tracking_id']); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 mb-1">Order Date</p>
                                    <p class="font-medium"><?php echo date('F j, Y, g:i a', strtotime($tracking_result['order']['created_at'])); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 mb-1">Cylinder Type</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['cylinder_type']); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 mb-1">Amount</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['amount_kg']); ?> kg</p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 mb-1">Total Price</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['currency'] . ' ' . number_format($tracking_result['order']['total_price'], 2)); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-600 mb-1">Status</p>
                                    <?php $statusInfo = getStatusInfo($tracking_result['order']['status']); ?>
                                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full <?php echo $statusInfo['class']; ?>">
                                        <?php echo $statusInfo['text']; ?>
                                    </span>
                                </div>
                                
                                <?php if ($tracking_result['order']['vendor_name']): ?>
                                <div>
                                    <p class="text-gray-600 mb-1">Vendor</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['vendor_name']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($tracking_result['order']['rider_name']): ?>
                                <div>
                                    <p class="text-gray-600 mb-1">Delivery Person</p>
                                    <p class="font-medium"><?php echo htmlspecialchars($tracking_result['order']['rider_name']); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Order Timeline -->
                        <div class="bg-white shadow-md rounded-lg p-6">
                            <h2 class="text-xl font-semibold mb-6">Order Timeline</h2>
                            
                            <div class="space-y-8 pl-8">
                                <?php 
                                $steps = [
                                    'pending' => ['icon' => 'fa-receipt', 'title' => 'Order Placed', 'description' => 'Your order has been received'],
                                    'processing' => ['icon' => 'fa-box', 'title' => 'Order Processing', 'description' => 'Your order is being prepared'],
                                    'moving' => ['icon' => 'fa-truck', 'title' => 'On the Way', 'description' => 'Your order is on the way to you'],
                                    'delivered' => ['icon' => 'fa-check-circle', 'title' => 'Delivered', 'description' => 'Your order has been delivered']
                                ];
                                
                                foreach ($steps as $step => $info):
                                    $stepStatus = getStepStatus($tracking_result['order']['status'], $step);
                                ?>
                                <div class="tracking-step <?php echo $stepStatus; ?>">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-4">
                                            <div class="step-icon <?php echo $stepStatus; ?>">
                                                <i class="fas <?php echo $info['icon']; ?>"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="font-medium"><?php echo $info['title']; ?></h3>
                                            <p class="text-gray-600"><?php echo $info['description']; ?></p>
                                            
                                            <?php if ($stepStatus === 'completed' || $stepStatus === 'active'): ?>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <?php 
                                                    if ($step === 'pending') {
                                                        echo date('F j, Y, g:i a', strtotime($tracking_result['order']['created_at']));
                                                    } elseif ($step === 'delivered' && $tracking_result['order']['status'] === 'delivered') {
                                                        echo date('F j, Y, g:i a', strtotime($tracking_result['order']['updated_at']));
                                                    } elseif ($step === 'moving' && $tracking_result['rider_location']) {
                                                        echo date('F j, Y, g:i a', strtotime($tracking_result['rider_location']['created_at']));
                                                    }
                                                    ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <?php if ($step === 'moving' && $stepStatus === 'active' && $tracking_result['rider_location']): ?>
                                                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                                    <p class="text-sm">
                                                        <span class="font-medium">Rider:</span> 
                                                        <?php echo htmlspecialchars($tracking_result['order']['rider_name']); ?>
                                                    </p>
                                                    <p class="text-sm mt-1">
                                                        <span class="font-medium">Last updated:</span> 
                                                        <?php echo date('F j, Y, g:i a', strtotime($tracking_result['rider_location']['created_at'])); ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if ($tracking_result['order']['status'] === 'canceled'): ?>
                                <div class="tracking-step canceled">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-4">
                                            <div class="step-icon canceled">
                                                <i class="fas fa-times"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="font-medium">Order Canceled</h3>
                                            <p class="text-gray-600">This order has been canceled</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                <?php echo date('F j, Y, g:i a', strtotime($tracking_result['order']['updated_at'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Instructions when no tracking ID is entered -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                            <div class="mb-4">
                                <i class="fas fa-search text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium mb-2">Track Your Order</h3>
                            <p class="text-gray-600 mb-4">Enter your tracking ID above to see the status and details of your order.</p>
                            <p class="text-sm text-gray-500">You can find your tracking ID in your order confirmation email or in your order history.</p>
                        </div>
                    <?php endif; ?>
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