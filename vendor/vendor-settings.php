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
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Orders</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Transactions</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Reports</a>
                            <a href="#" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Settings Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Vendor Settings</h1>
                
                <div class="bg-white shadow rounded-lg p-6">
                    <form id="settingsForm">
                        <!-- Profile Information -->
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold mb-4">Profile Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="vendorName" class="block text-sm font-medium text-gray-700 mb-1">Vendor Name</label>
                                    <input type="text" id="vendorName" name="vendorName" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="Jumandi Gas Vendor">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="vendor@jumandigas.com">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="+234 123 456 7890">
                                </div>
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                                    <input type="text" id="address" name="address" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" value="123 Gas Street, Lagos, Nigeria">
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="mb-6">
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
                        </div>

                        <!-- Notification Settings -->
                        <div class="mb-6">
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
                        </div>

                        <!-- Password Change -->
                        <div class="mb-6">
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
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                Save Changes
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

        // Handle form submission
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real application, you would send this data to your server
            console.log('Settings form submitted');
            
            // Simulate a successful save
            alert('Settings saved successfully!');
        });
    </script>
</body>
</html>