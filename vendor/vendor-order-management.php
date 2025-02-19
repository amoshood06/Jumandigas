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
                        <div class="flex flex-col items-center my-8">
                            <img src="/placeholder.svg?height=100&width=100" alt="Vendor Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">Vendor Name</h2>
                        </div>
                        
                        <nav class="space-y-2 px-4">
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Inventory</a>
                            <a href="#" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Orders</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Reports</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Order Management Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Order Management</h1>
                
                <!-- Order Filters -->
                <div class="mb-6">
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-[#ff6b00] text-white px-4 py-2 rounded-lg text-sm">All Orders</button>
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Pending</button>
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Accepted</button>
                        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Rejected</button>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#1001</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">2x 5kg, 1x 3kg</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">₦10,500</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-green-600 hover:text-green-900 mr-3" onclick="handleOrder('1001', 'accept')">Accept</button>
                                    <button class="text-red-600 hover:text-red-900" onclick="handleOrder('1001', 'reject')">Reject</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#1002</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">Jane Smith</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">1x 12.5kg</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">₦8,000</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-green-600 hover:text-green-900 mr-3" onclick="handleOrder('1002', 'accept')">Accept</button>
                                    <button class="text-red-600 hover:text-red-900" onclick="handleOrder('1002', 'reject')">Reject</button>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#1003</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">Bob Johnson</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">3x 1kg</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">₦3,000</td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepted</span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                    <span class="text-gray-500">Accepted</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center gap-4 mt-6">
                    <button class="bg-[#ff6b00] text-white px-4 py-2 rounded-lg text-sm">←</button>
                    <button class="bg-[#ff6b00] text-white px-4 py-2 rounded-lg text-sm">→</button>
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

        // Handle order acceptance or rejection
        function handleOrder(orderId, action) {
            // In a real application, this would make an API call to update the order status
            console.log(`Order ${orderId} ${action}ed`);
            
            // For demonstration, we'll update the UI
            const row = document.querySelector(`tr:has(td:first-child:contains(#${orderId}))`);
            if (row) {
                const statusCell = row.querySelector('td:nth-child(5)');
                const actionsCell = row.querySelector('td:last-child');
                
                if (action === 'accept') {
                    statusCell.innerHTML = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepted</span>';
                    actionsCell.innerHTML = '<span class="text-gray-500">Accepted</span>';
                } else if (action === 'reject') {
                    statusCell.innerHTML = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
                    actionsCell.innerHTML = '<span class="text-gray-500">Rejected</span>';
                }
            }
        }
    </script>
</body>
</html>