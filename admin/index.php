<?php
require_once "auth_admin.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Mobile Sidebar */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .overlay {
            display: none;
        }
        .overlay.show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Overlay -->
    <div id="overlay" class="overlay fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-64 bg-[#ff6b00] text-white p-4 z-50 sidebar md:relative md:translate-x-0 hidden md:block">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold">Jumandi Gas</h2>
                <button id="closeSidebar" class="md:hidden text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li><a href="#" class="block py-2 px-4 rounded bg-orange-700">Dashboard</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Orders</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Customers</a></li>
                    <li><a href="locations.php" class="block py-2 px-4 rounded hover:bg-orange-700">Gas Price</a></li>
                    <li><a href="bike.php" class="block py-2 px-4 rounded hover:bg-orange-700">Rider Price</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Settings</a></li>
                    <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-orange-700">Login</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-0">
            <!-- Top Navigation -->
            <header class="bg-white shadow p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <button id="openSidebar" class="md:hidden mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold">Dashboard</h1>
                </div>
            </header>

            <!-- Dashboard Content -->
            <main class="p-6 bg-gray-100">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-600 text-sm">Gas in Stock</h3>
                            <p class="text-2xl font-bold mt-1">10,000 kg</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-gas-pump text-[#ff6b00] text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-600 text-sm">Total Orders</h3>
                            <p class="text-2xl font-bold mt-1">5,678</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-[#ff6b00] text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-600 text-sm">Total Users</h3>
                            <p class="text-2xl font-bold mt-1">2,345</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-users text-[#ff6b00] text-xl"></i>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-600 text-sm">Cylinder Orders</h3>
                            <p class="text-2xl font-bold mt-1">2,234</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-flask text-[#ff6b00] text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="mt-8 bg-white rounded-lg shadow-sm overflow-hidden">
                    <h3 class="text-lg font-semibold p-4 bg-gray-50 border-b">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px]">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Table rows remain the same -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');

        function toggleSidebar(show) {
            sidebar.classList.toggle('open', show);
            overlay.classList.toggle('show', show);
            sidebar.classList.toggle('hidden', !show);
        }

        openSidebarBtn.addEventListener('click', () => toggleSidebar(true));
        closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));
        overlay.addEventListener('click', () => toggleSidebar(false));
    </script>
</body>
</html>
