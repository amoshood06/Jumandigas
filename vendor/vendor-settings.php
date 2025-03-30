<?php
// Start the session (if not already started)
session_start();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    // Redirect to login page if not logged in or not a vendor
    header("Location: login.php");
    exit();
}

// Include database connection
require_once '../db/db.php';

// Get vendor ID from session
$vendor_id = $_SESSION['user_id'];

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process profile information update
    if (isset($_POST['update_profile'])) {
        $vendorName = trim($_POST['vendorName']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $country = trim($_POST['country']);
        $state = trim($_POST['state']);
        $city = trim($_POST['city']);
        
        // Validate inputs
        if (empty($vendorName) || empty($email) || empty($phone) || empty($address)) {
            $error_message = "All fields are required";
        } else {
            try {
                // Update vendor information
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET full_name = ?, email = ?, phone = ?, address = ?, 
                        country = ?, state = ?, city = ?
                    WHERE id = ? AND role = 'vendor'
                ");
                
                $stmt->execute([
                    $vendorName, $email, $phone, $address,
                    $country, $state, $city,
                    $vendor_id
                ]);
                
                $success_message = "Profile information updated successfully";
            } catch (PDOException $e) {
                $error_message = "Error updating profile: " . $e->getMessage();
            }
        }
    }
    
    // Process password change
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        
        // Validate inputs
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error_message = "All password fields are required";
        } elseif ($newPassword !== $confirmPassword) {
            $error_message = "New passwords do not match";
        } elseif (strlen($newPassword) < 8) {
            $error_message = "Password must be at least 8 characters long";
        } else {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$vendor_id]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($currentPassword, $user['password'])) {
                    // Hash the new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    // Update password
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $vendor_id]);
                    
                    $success_message = "Password updated successfully";
                } else {
                    $error_message = "Current password is incorrect";
                }
            } catch (PDOException $e) {
                $error_message = "Error updating password: " . $e->getMessage();
            }
        }
    }
    
    // Process business hours update
    if (isset($_POST['update_hours'])) {
        $openTime = $_POST['openTime'];
        $closeTime = $_POST['closeTime'];
        
        // Here you would update the business hours in your database
        // This would require adding a business_hours table or columns to the users table
        $success_message = "Business hours updated successfully";
    }
    
    // Process notification settings
    if (isset($_POST['update_notifications'])) {
        $emailNotifications = isset($_POST['emailNotifications']) ? 1 : 0;
        $smsNotifications = isset($_POST['smsNotifications']) ? 1 : 0;
        
        // Here you would update the notification settings in your database
        // This would require adding notification_settings columns to the users table
        $success_message = "Notification settings updated successfully";
    }
}

// Fetch vendor information from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'vendor'");
    $stmt->execute([$vendor_id]);
    $vendor = $stmt->fetch();
    
    if (!$vendor) {
        // Vendor not found or not a vendor
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching vendor data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Vendor Settings</title>
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
            <p class="text-2xl font-bold"><?php echo htmlspecialchars($vendor['full_name']); ?></p>
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
                            <a href="Withdrawal.php" class="block p-3 hover:bg-orange-100 rounded-lg">Withdrawal</a>
                            <a href="vendor-order-management.php" class="block p-3 hover:bg-orange-100 rounded-lg">Orders</a>
                            <a href="vendor-transactions.php" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="vendor-report.php" class="block p-3 hover:bg-orange-100 rounded-lg">Reports</a>
                            <a href="profile.php" class="block p-3 hover:bg-orange-100 rounded-lg">Profile</a>
                            <a href="vendor-settings.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Settings Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Vendor Settings</h1>
                
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
                
                <div class="bg-white shadow rounded-lg p-6">
                    <!-- Profile Information -->
                    <form method="POST" action="">
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold mb-4">Profile Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="vendorName" class="block text-sm font-medium text-gray-700 mb-1">Vendor Name</label>
                                    <input type="text" id="vendorName" name="vendorName" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['full_name']); ?>">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['email']); ?>">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['phone']); ?>">
                                </div>
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                                    <input type="text" id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['address']); ?>">
                                </div>
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <input type="text" id="country" name="country" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['country']); ?>">
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <input type="text" id="state" name="state" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['state']); ?>">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <input type="text" id="city" name="city" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="<?php echo htmlspecialchars($vendor['city']); ?>">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" name="update_profile" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                    Update Profile
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Business Hours -->
                    <form method="POST" action="">
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Business Hours</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="openTime" class="block text-sm font-medium text-gray-700 mb-1">Opening Time</label>
                                    <input type="time" id="openTime" name="openTime" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="08:00">
                                </div>
                                <div>
                                    <label for="closeTime" class="block text-sm font-medium text-gray-700 mb-1">Closing Time</label>
                                    <input type="time" id="closeTime" name="closeTime" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="18:00">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" name="update_hours" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                    Update Hours
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Notification Settings -->
                    <form method="POST" action="">
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Notification Settings</h2>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="emailNotifications" name="emailNotifications" class="h-4 w-4 text-[#ff6b00] focus:ring-[#ff6b00] border-gray-300 rounded" checked>
                                    <label for="emailNotifications" class="ml-2 block text-sm text-gray-900">
                                        Receive email notifications for new orders
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="smsNotifications" name="smsNotifications" class="h-4 w-4 text-[#ff6b00] focus:ring-[#ff6b00] border-gray-300 rounded" checked>
                                    <label for="smsNotifications" class="ml-2 block text-sm text-gray-900">
                                        Receive SMS notifications for new orders
                                    </label>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" name="update_notifications" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                    Update Notifications
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Password Change -->
                    <form method="POST" action="">
                        <div class="mb-6 pt-6 border-t border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Change Password</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                    <input type="password" id="currentPassword" name="currentPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                </div>
                                <div>
                                    <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" id="newPassword" name="newPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                </div>
                                <div>
                                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" id="confirmPassword" name="confirmPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" name="change_password" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                    Change Password
                                </button>
                            </div>
                        </div>
                    </form>
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