<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'rider') {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Rider Dashboard</title>
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
            <p class="text-2xl font-bold">John Doe</p>
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
                        <!-- <div class="flex flex-col items-center my-8">
                            <img src="/placeholder.svg?height=100&width=100" alt="Rider Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">John Doe</h2>
                            <p class="text-sm text-gray-500">Rider ID: R12345</p>
                        </div> -->
                        
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Dashboard</a>
                            <a href="rider-pending-deliveries.php" class="block p-3 hover:bg-orange-100 rounded-lg">Pending Deliveries</a>
                            <a href="rider-delivery-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                            <a href="rider-performance.php" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                            <a href="rider-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Dashboard Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Rider Dashboard</h1>
                
                <!-- Overview Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Today's Deliveries</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">8</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Pending Pickups</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">3</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Total Earnings</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">₦12,500</p>
                    </div>
                </div>

                <!-- Pending Deliveries Section -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Pending Deliveries</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Alice Johnson</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">123 Main St, Lagos</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending Pickup
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="startDelivery('#ORD001')">Start Delivery</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Bob Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">456 Elm St, Lagos</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            En Route
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="completeDelivery('#ORD002')">Complete Delivery</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Map Section (Simulated) -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Delivery Route</h2>
                    <div class="bg-gray-200 h-64 rounded-lg flex items-center justify-center">
                        <p class="text-gray-500">Map visualization would be displayed here</p>
                    </div>
                </div>

                <!-- Delivery History Section -->
                <div class="bg-white p-4 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-4">Recent Deliveries</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD003</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Charlie Brown</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD004</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Diana Evans</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-19</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

        // Simulated delivery actions
        function startDelivery(orderId) {
            console.log(`Starting delivery for order ${orderId}`);
            alert(`Delivery started for order ${orderId}`);
            // In a real application, you would update the order status and refresh the table
        }

        function completeDelivery(orderId) {
            console.log(`Completing delivery for order ${orderId}`);
            alert(`Delivery completed for order ${orderId}`);
            // In a real application, you would update the order status and refresh the table
        }
    </script>
</body>
</html>