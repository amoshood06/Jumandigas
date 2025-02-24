<?php
// Start the session before any output
// Include authentication check
require_once "../auth_check.php";

// Check if the user is logged in and their role is 'user'
if ($_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

function isLoggedIn() {
    // Check if the session has the user_id set (user is logged in)
    return isset($_SESSION['user_id']);
}

function getUserLocation() {
    // Assuming user country and state are stored in session after login
    if (isset($_SESSION['country']) && isset($_SESSION['state'])) {
        return [
            'country' => $_SESSION['country'],
            'state' => $_SESSION['state']
        ];
    }
    return null; // Return null if location information is not available
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas Dashboard</title>
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
            <span id="currencySymbol">₦</span> 
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
                        <div class="flex flex-col items-center my-8">
                            <img src="/placeholder.svg?height=100&width=100" alt="Profile" class="rounded-full w-24 h-24 mb-4">
                        </div>
                        
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Home</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
                            <a href="order-page.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas</a>
                            <a href="user-order-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas History</a>
                            <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Setting</a>
                        </nav>

                        <!-- Quick Action Items - visible only on mobile -->
                        <div class="lg:hidden mt-8 px-4">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <a href="#" class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
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

            <!-- Dashboard Content -->
            <div class="flex-1">
                <!-- Action Cards - visible on all devices -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">⬇️</div>
                        <div class="text-sm">Deposit</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">🚚</div>
                        <div class="text-sm">Orders Gas</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="mb-2">🛒</div>
                        <div class="text-sm">Buy Cylinder</div>
                    </div>
                    <div class="bg-[#ff6b00] p-4 rounded-lg text-white text-center">
                        <div class="text-2xl font-bold">5</div>
                        <div class="text-sm">Total Orders</div>
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="bg-gray-200 rounded-lg overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#ff6b00] text-white">
                            <tr>
                                <th class="p-2 lg:p-4 text-left">Type</th>
                                <th class="p-2 lg:p-4 text-left">Amount</th>
                                <th class="p-2 lg:p-4 text-left hidden sm:table-cell">Transaction id</th>
                                <th class="p-2 lg:p-4 text-left hidden md:table-cell">Date / Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-300">
                                <td class="p-2 lg:p-4">Deposit</td>
                                <td class="p-2 lg:p-4">N5000</td>
                                <td class="p-2 lg:p-4 hidden sm:table-cell">TNX129309E</td>
                                <td class="p-2 lg:p-4 hidden md:table-cell">May, 17/12:00AM</td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="p-2 lg:p-4">1kg</td>
                                <td class="p-2 lg:p-4">N5000</td>
                                <td class="p-2 lg:p-4 hidden sm:table-cell">TNX129309E</td>
                                <td class="p-2 lg:p-4 hidden md:table-cell">May, 17/12:00AM</td>
                            </tr>
                            <tr class="border-b border-gray-300">
                                <td class="p-2 lg:p-4">5kg</td>
                                <td class="p-2 lg:p-4">N5000</td>
                                <td class="p-2 lg:p-4 hidden sm:table-cell">TNX129309E</td>
                                <td class="p-2 lg:p-4 hidden md:table-cell">May, 17/12:00AM</td>
                            </tr>
                            <tr>
                                <td class="p-2 lg:p-4">Cylinder - 1kg</td>
                                <td class="p-2 lg:p-4">N5000</td>
                                <td class="p-2 lg:p-4 hidden sm:table-cell">TNX129309E</td>
                                <td class="p-2 lg:p-4 hidden md:table-cell">May, 17/12:00AM</td>
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
    </script>
</body>
</html>

