<?php
require_once "../auth_check.php"; // Ensure user is authenticated
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
                        <h2 class="text-2xl font-bold text-center mb-6">Order Management</h2>

                        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="py-2 px-4">Order ID</th>
                                    <th class="py-2 px-4">Customer Details</th>
                                    <th class="py-2 px-4">Items</th>
                                    <th class="py-2 px-4">Total Price</th>
                                    <th class="py-2 px-4">Status</th>
                                    <th class="py-2 px-4">Action</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table">
                                <!-- Orders will be loaded here dynamically -->
                            </tbody>
                        </table>
                        </div>

                        <script>
                        function fetchOrders() {
                            fetch('fetch_orders.php')
                            .then(response => response.json())
                            .then(orders => {
                                const tableBody = document.getElementById("orders-table");
                                tableBody.innerHTML = "";

                                orders.forEach(order => {
                                    tableBody.innerHTML += `
                                        <tr class="border-b">
                                            <td class="py-2 px-4 text-center">${order.id}</td>
                                            <td class="py-2 px-4">
                                                <strong>${order.full_name}</strong> <br>
                                                ${order.address} <br>
                                                📞 ${order.telephone}
                                            </td>
                                            <td class="py-2 px-4">${order.items}</td>
                                            <td class="py-2 px-4">₦${order.total}</td>
                                            <td class="py-2 px-4">
                                                <span class="px-2 py-1 rounded text-white ${order.status === 'Pending' ? 'bg-yellow-500' : (order.status === 'Accepted' ? 'bg-green-500' : 'bg-red-500')}">
                                                    ${order.status}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4">
                                                ${order.status === 'Pending' ? `
                                                    <button onclick="handleOrder(${order.id}, 'accept')" class="bg-green-500 text-white px-3 py-1 rounded">Accept</button>
                                                    <button onclick="handleOrder(${order.id}, 'reject')" class="bg-red-500 text-white px-3 py-1 rounded ml-2">Reject</button>
                                                ` : `<span class="text-gray-500">${order.status}</span>`}
                                            </td>
                                        </tr>
                                    `;
                                });
                            });
                        }

                        function handleOrder(orderId, action) {
                            fetch('update_order.php', {
                                method: 'POST',
                                body: new URLSearchParams({
                                    order_id: orderId,
                                    status: action === 'accept' ? 'Accepted' : 'Rejected'
                                }),
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                            })
                            .then(response => response.json())
                            .then(data => {
                                alert(data.message);
                                fetchOrders(); // Reload orders after update
                            })
                            .catch(error => console.error('Error:', error));
                        }

                        document.addEventListener("DOMContentLoaded", fetchOrders);
                        </script>
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