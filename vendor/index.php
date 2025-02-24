<?php
require_once "../auth_check.php";
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Vendor Dashboard</title>
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
            <p class="text-2xl font-bold" id="fullname"></p>
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
                            <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Dashboard</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Inventory</a>
                            <a href="vendor-order-management.php" class="block p-3 hover:bg-orange-100 rounded-lg">Orders</a>
                            <a href="vendor-transactions.php" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="vendor-report.php" class="block p-3 hover:bg-orange-100 rounded-lg">Reports</a>
                            <a href="vendor-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Vendor Dashboard Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Vendor Dashboard</h1>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold mb-2">150</div>
                        <div class="text-sm">Total Orders</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold mb-2">₦500,000</div>
                        <div class="text-sm">Total Revenue</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold mb-2">500kg</div>
                        <div class="text-sm">Gas in Stock</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold mb-2">4.8</div>
                        <div class="text-sm">Average Rating</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6 overflow-x-auto">
                    <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
                    <div class="min-w-full">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">#1234</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">John Doe</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">₦5,000</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span></td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">2023-05-20</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">#1235</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">Jane Smith</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">₦8,000</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span></td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">2023-05-19</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">#1236</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">Bob Johnson</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">₦3,000</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span></td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm">2023-05-18</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                   
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
                                document.getElementById("fullname").innerText = data.full_name;
                            }
                        })
                        .catch(error => console.error("Error fetching balance:", error));
                    }
                </script>
</body>
</html>