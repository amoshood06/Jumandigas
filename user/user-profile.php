<?php
// Start session and include authentication
require_once "../auth_check.php";
require '../db/db.php'; // Include database connection

// Check if the user is logged in and their role is 'user'
if ($_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Initialize message variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $state = trim($_POST['state']);
    $city = trim($_POST['city']);
    
    // Validate inputs
    if (empty($full_name) || empty($email) || empty($phone)) {
        $error_message = "Name, email, and phone are required fields.";
    } else {
        try {
            // Check if email already exists for another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->rowCount() > 0) {
                $error_message = "Email already in use by another account.";
            } else {
                // Check if phone already exists for another user
                $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
                $stmt->execute([$phone, $user_id]);
                if ($stmt->rowCount() > 0) {
                    $error_message = "Phone number already in use by another account.";
                } else {
                    // Update user profile
                    $stmt = $pdo->prepare("UPDATE users SET 
                        full_name = ?, 
                        email = ?, 
                        phone = ?, 
                        address = ?, 
                        state = ?, 
                        city = ?, 
                        updated_at = NOW() 
                        WHERE id = ?");
                    
                    $stmt->execute([
                        $full_name,
                        $email,
                        $phone,
                        $address,
                        $state,
                        $city,
                        $user_id
                    ]);
                    
                    $success_message = "Profile updated successfully!";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Error updating profile: " . $e->getMessage();
        }
    }
}

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Error fetching user data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Jumandi Gas</title>
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
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Home</a>
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
                            <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
                            <a href="user-profile.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Profile</a>
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

            <!-- Profile Content -->
            <div class="flex-1">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-2xl font-bold mb-6">My Profile</h1>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <form method="POST" action="">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed" readonly>
                                    <p class="text-xs text-gray-500 mt-1">Country cannot be changed</p>
                                </div>
                                
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                    <textarea id="address" name="address" rows="3" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="px-6 py-3 bg-[#ff6b00] text-white font-medium rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Change Password Section -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold mb-4">Change Password</h2>
                        <div class="bg-white shadow-md rounded-lg p-6">
                            <form action="change-password.php" method="POST">
                                <div class="space-y-4">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                        <input type="password" id="current_password" name="current_password" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                    
                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                        <input type="password" id="new_password" name="new_password" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                    
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
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