<?php
// Start the session (if not already started)
session_start();

// Check if user is logged in and is a rider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    // Redirect to login page if not logged in or not a rider
    header("Location: ../login.php");
    exit();
}

// Include database connection
require_once '../db/db.php';

// Get rider ID from session
$rider_id = $_SESSION['user_id'];

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process profile information update
    if (isset($_POST['update_profile'])) {
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $fullName = $firstName . ' ' . $lastName;
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        // Validate inputs
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
            $error_message = "All fields are required";
        } else {
            try {
                // Check if email already exists for another user
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $rider_id]);
                if ($stmt->rowCount() > 0) {
                    $error_message = "Email already in use by another account";
                } else {
                    // Update rider information
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET full_name = ?, email = ?, phone = ?
                        WHERE id = ? AND role = 'rider'
                    ");
                    
                    $stmt->execute([$fullName, $email, $phone, $rider_id]);
                    
                    $success_message = "Profile information updated successfully";
                }
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
                $stmt->execute([$rider_id]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($currentPassword, $user['password'])) {
                    // Hash the new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    // Update password
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $rider_id]);
                    
                    $success_message = "Password updated successfully";
                } else {
                    $error_message = "Current password is incorrect";
                }
            } catch (PDOException $e) {
                $error_message = "Error updating password: " . $e->getMessage();
            }
        }
    }
}

// Fetch rider information from database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'rider'");
    $stmt->execute([$rider_id]);
    $rider = $stmt->fetch();
    
    if (!$rider) {
        // Rider not found or not a rider
        header("Location: login.php");
        exit();
    }
    
    // Get rider's name parts
    $name_parts = explode(' ', $rider['full_name'], 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
    
} catch (PDOException $e) {
    die("Error fetching rider data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Rider Settings</title>
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
                            <a href="rider-pending-deliveries.php" class="block p-3 hover:bg-orange-100 rounded-lg">Pending Deliveries</a>
                            <a href="rider-delivery-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                            <a href="rider-performance.php" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                            <a href="rider_profile.php" class="block p-3 hover:bg-orange-100 rounded-lg">Profile</a>
                            <a href="rider-settings.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Settings Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Settings</h1>
                
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
                
                <!-- Profile Information -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
                    <form id="profileForm" method="POST" action="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($first_name); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($last_name); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($rider['email']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($rider['phone']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" name="update_profile" class="px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-opacity-50">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Security -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Account Security</h2>
                    <form id="securityForm" method="POST" action="">
                        <div class="space-y-4">
                            <div>
                                <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input type="password" id="currentPassword" name="currentPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                            <div>
                                <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" id="newPassword" name="newPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                            <div>
                                <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" name="change_password" class="px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-opacity-50">
                                Change Password
                            </button>
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