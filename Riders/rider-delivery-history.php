<?php
require_once "../auth_check.php";
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
    <title>Jumandi Gas - Rider Delivery History</title>
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

            <!-- Delivery History Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Delivery History</h1>
                
                <!-- Delivery Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Total Deliveries</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">152</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">This Month</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">37</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Average Rating</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">4.8 <span class="text-yellow-400">★★★★★</span></p>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Filter Deliveries</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <select id="dateRange" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 3 months</option>
                                <option>Custom range</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option>All</option>
                                <option>Completed</option>
                                <option>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                            <select id="sort" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                <option>Date (Newest First)</option>
                                <option>Date (Oldest First)</option>
                                <option>Customer Name</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Delivery History Table -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD123</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-20</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Alice Johnson</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2x 5kg Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="text-yellow-400">★★★★★</span> 5.0
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="viewDetails('#ORD123')">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD122</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-19</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Bob Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1x 12.5kg Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="text-yellow-400">★★★★</span>★ 4.0
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="viewDetails('#ORD122')">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD121</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-18</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Charlie Brown</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3x 3kg Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Cancelled
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        N/A
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="viewDetails('#ORD121')">View Details</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD120</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-05-17</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Diana Evans</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1x 5kg Cylinder, 1x 3kg Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="text-yellow-400">★★★★★</span> 5.0
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-[#ff6b00] hover:text-[#e05e00]" onclick="viewDetails('#ORD120')">View Details</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                        <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">152</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    1
                                </a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    2
                                </a>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    3
                                </a>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700">
                                    ...
                                </span>
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    38
                                </a>
                                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </nav>
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

        // View delivery details
        function viewDetails(orderId) {
            console.log(`Viewing details for order ${orderId}`);
            alert(`Viewing details for order ${orderId}`);
            // In a real application, you would open a modal or navigate to a details page
        }
    </script>
</body>
</html>