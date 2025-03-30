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
} catch (PDOException $e) {
    die("Error fetching rider data: " . $e->getMessage());
}

// Fetch rider's delivery statistics
try {
    // Total deliveries
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_deliveries FROM orders WHERE rider_id = ?");
    $stmt->execute([$rider_id]);
    $total_deliveries = $stmt->fetch()['total_deliveries'];
    
    // Completed deliveries
    $stmt = $pdo->prepare("SELECT COUNT(*) as completed_deliveries FROM orders WHERE rider_id = ? AND status = 'delivered'");
    $stmt->execute([$rider_id]);
    $completed_deliveries = $stmt->fetch()['completed_deliveries'];
    
    // Pending deliveries
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_deliveries FROM orders WHERE rider_id = ? AND status = 'moving'");
    $stmt->execute([$rider_id]);
    $pending_deliveries = $stmt->fetch()['pending_deliveries'];
    
    // Calculate completion rate
    $completion_rate = ($total_deliveries > 0) ? round(($completed_deliveries / $total_deliveries) * 100) : 0;
    
} catch (PDOException $e) {
    $total_deliveries = 0;
    $completed_deliveries = 0;
    $pending_deliveries = 0;
    $completion_rate = 0;
}

// Get rider's name parts
$name_parts = explode(' ', $rider['full_name'], 2);
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Rider Profile</title>
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
                            <a href="rider_profile.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Profile</a>
                            <a href="rider-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Profile Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Rider Profile</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Profile Card -->
                    <div class="bg-white shadow rounded-lg p-6 col-span-2">
                        <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-4xl">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($rider['full_name']); ?></h2>
                                <p class="text-gray-500 mb-4"><?php echo htmlspecialchars($rider['email']); ?></p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Phone</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($rider['phone']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Address</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($rider['address']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">City</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($rider['city']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">State</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($rider['state']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Country</p>
                                        <p class="font-medium"><?php echo htmlspecialchars($rider['country']); ?></p>
                                    </div>
                                </div>
                                
                                <a href="rider-settings.php" class="inline-block px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00]">
                                    Edit Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Account Status</h3>
                        <div class="flex items-center mb-4">
                            <div class="w-4 h-4 rounded-full <?php echo $rider['online'] ? 'bg-green-500' : 'bg-gray-400'; ?> mr-2"></div>
                            <p class="font-medium">
                                <?php echo $rider['online'] ? 'Online' : 'Offline'; ?>
                            </p>
                        </div>
                        
                        <form action="update_status.php" method="post" class="mt-4">
                            <input type="hidden" name="status" value="<?php echo $rider['online'] ? '0' : '1'; ?>">
                            <button type="submit" class="w-full px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00]">
                                <?php echo $rider['online'] ? 'Go Offline' : 'Go Online'; ?>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Total Deliveries</h3>
                        <p class="text-3xl font-bold"><?php echo $total_deliveries; ?></p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Completed</h3>
                        <p class="text-3xl font-bold"><?php echo $completed_deliveries; ?></p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Pending</h3>
                        <p class="text-3xl font-bold"><?php echo $pending_deliveries; ?></p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Completion Rate</h3>
                        <p class="text-3xl font-bold"><?php echo $completion_rate; ?>%</p>
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