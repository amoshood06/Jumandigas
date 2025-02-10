<?php
session_start();
$userRole = $_SESSION['role'] ?? null; // Get user role if logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JumandiGas</title>
    <link rel="shortcut icon" href="./asset/image/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .mobile-nav {
            transition: transform 0.3s ease-in-out;
        }
        .mobile-nav.hidden {
            transform: translateX(-100%);
        }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Header -->
        <header class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-2">
                <img src="./asset/image/logo.png" alt="City Logo" class="w-20 h-10 object-contain">
            </div>

            <nav class="hidden md:flex items-center gap-8">
                <a href="#" class="font-medium hover:text-orange-500">Home</a>
                <a href="#" class="font-medium hover:text-orange-500">Service</a>
                <a href="#" class="font-medium hover:text-orange-500">Shop</a>
                
                <?php if (!$userRole): ?>
                    <!-- Show Register & Login when user is NOT logged in -->
                    <a href="register.php" class="font-medium hover:text-orange-500">Register</a>
                    <a href="login.php" class="font-medium hover:text-orange-500">Login</a>
                <?php else: ?>
                    <!-- Show Dashboard when user is logged in -->
                    <?php 
                        $dashboardUrl = '#';
                        if ($userRole == 'vendor') {
                            $dashboardUrl = './vendor/index.php';
                        } elseif ($userRole == 'user') {
                            $dashboardUrl = './user/index.php';
                        } elseif ($userRole == 'rider') {
                            $dashboardUrl = './rider/index.php';
                        }
                    ?>
                    <a href="<?= $dashboardUrl ?>" class="font-medium hover:text-orange-500">Dashboard</a>
                <?php endif; ?>
            </nav>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="search" class="w-5 h-5"></i>
                    <span class="hidden md:inline">Search</span>
                </div>
                <div class="relative">
                    <i data-lucide="shopping-basket" class="w-5 h-5"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">2</span>
                </div>
                <button id="mobile-menu-button" class="md:hidden">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </header>

        <!-- Mobile Navigation -->
        <div id="mobile-nav" class="mobile-nav fixed top-0 left-0 w-64 h-full bg-white shadow-lg z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="p-4">
                <button id="close-mobile-menu" class="mb-4">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
                <nav class="flex flex-col gap-4">
                    <a href="#" class="font-medium hover:text-orange-500">Home</a>
                    <a href="#" class="font-medium hover:text-orange-500">Service</a>
                    <a href="#" class="font-medium hover:text-orange-500">Shop</a>
                    <?php if (!$userRole): ?>
                        <!-- Show Register & Login when user is NOT logged in -->
                    <a href="#" class="font-medium hover:text-orange-500">Register</a>
                    <a href="#" class="font-medium hover:text-orange-500">Login</a>
                    <?php else: ?>
                    <!-- Show Dashboard when user is logged in -->
                    <?php 
                        $dashboardUrl = '#';
                        if ($userRole == 'vendor') {
                            $dashboardUrl = './vendor/index.php';
                        } elseif ($userRole == 'user') {
                            $dashboardUrl = './user/index.php';
                        } elseif ($userRole == 'rider') {
                            $dashboardUrl = './rider/index.php';
                        }
                    ?>
                    <a href="<?= $dashboardUrl ?>" class="font-medium hover:text-orange-500">Dashboard</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-orange-100 px-4 py-2 rounded-full">
                    <span class="text-orange-600">Bike Delivery</span>
                    <span class="text-xl text-orange-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bike"><circle cx="18.5" cy="17.5" r="3.5"/><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="15" cy="5" r="1"/><path d="M12 17.5V14l-3-3 4-3 2 3h2"/></svg></span>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold leading-tight">
                    The Fastest Delivery<br>
                    in <span class="text-orange-500">Your City</span>
                </h1>

                <p class="text-gray-600 max-w-md">
                At JumandiGas, we bring you a seamless and stress-free way to order and receive cooking gas at your doorstep. No more unexpected gas shortages or long refill queues—just a fast, safe, and convenient delivery service that keeps your kitchen running.
                </p>

                <div class="flex items-center gap-4">
                    <button class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors">
                        Book Now
                    </button>
                    <button class="flex items-center gap-2 text-gray-600">
                        Order Process
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Food Cards Grid -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Burger Card -->
                <div class="bg-gray-50 p-4 rounded-xl relative">
                    <img src="./asset/image/pngwing.com (24).png" alt="Burger" class="w-24 h-24 mx-auto mb-4 object-contain">
                    <h3 class="font-semibold">Gas - 1kg</h3>
                    <p class="text-sm text-gray-500">Refill</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-semibold">₦2500.25</span>
                        <div class="flex gap-2">
                            <button class="p-2 bg-gray-900 text-white rounded-full">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                            <button class="p-2 bg-gray-200 rounded-full">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pizza Card -->
                <div class="bg-gray-50 p-4 rounded-xl relative">
                    <img src="./asset/image/2kg-removebg-preview.png" alt="Pizza" class="w-24 h-24 mx-auto mb-4 object-contain">
                    <h3 class="font-semibold">Gas - 2kg</h3>
                    <p class="text-sm text-gray-500">Refill</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-semibold">₦3500.10</span>
                        <div class="flex gap-2">
                            <button class="p-2 bg-gray-900 text-white rounded-full">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                            <button class="p-2 bg-gray-200 rounded-full">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cake Card -->
                <div class="bg-gray-50 p-4 rounded-xl relative">
                    <img src="./asset/image/3kg-removebg-preview.png" alt="Cake" class="w-24 h-24 mx-auto mb-4 object-contain">
                    <h3 class="font-semibold">Gas - 3kg</h3>
                    <p class="text-sm text-gray-500">Refill</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-semibold">₦6500.10</span>
                        <div class="flex gap-2">
                            <button class="p-2 bg-gray-900 text-white rounded-full">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                            <button class="p-2 bg-gray-200 rounded-full">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Food Dish Card -->
                <div class="bg-gray-50 p-4 rounded-xl relative">
                    <img src="./asset/image/5kg-removebg-preview.png" alt="Food Dish" class="w-24 h-24 mx-auto mb-4 object-contain">
                    <h3 class="font-semibold">Gas - 5kg</h3>
                    <p class="text-sm text-gray-500">Refill</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-semibold">₦7600.10</span>
                        <div class="flex gap-2">
                            <button class="p-2 bg-gray-900 text-white rounded-full">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                            <button class="p-2 bg-gray-200 rounded-full">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="button w-full">
                    <button class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors">
                        More
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Products -->
         <div class="w-full flex item-center gap-6 p-4">
         <h1 class="font-semibold">Buy Cylinder</h1> <span class="text-black">★</span>
         </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Fresh Orange -->
            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl">
                <img src="./asset/image/1kg-removebg-preview.png" alt="Fresh Orange" class="w-24 h-24 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-medium">Cylinder - 1kg</h3>
                    <div class="flex items-center gap-1 text-sm">
                        <span class="text-yellow-400">★</span>
                        <span>5.8</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-bold">₦10,000</span>
                        <button class="p-2 bg-red-500 text-white rounded-full">
                            <i data-lucide="shopping-basket" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Melting Cheese -->
            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl">
                <img src="./asset/image/2kg-removebg-preview.png" alt="Melting Cheese" class="w-24 h-24 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-medium">Cylinder - 2kg</h3>
                    <div class="flex items-center gap-1 text-sm">
                        <span class="text-yellow-400">★</span>
                        <span>5.8</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-bold">₦12,000</span>
                        <button class="p-2 bg-red-500 text-white rounded-full">
                            <i data-lucide="shopping-basket" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fresh Pomegranate -->
            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl">
                <img src="./asset/image/3kg-removebg-preview.png" alt="Fresh Pomegranate" class="w-24 h-24 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-medium">Cylinder - 3kg</h3>
                    <div class="flex items-center gap-1 text-sm">
                        <span class="text-yellow-400">★</span>
                        <span>5.8</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="font-bold">₦14,000</span>
                        <button class="p-2 bg-red-500 text-white rounded-full">
                            <i data-lucide="shopping-basket" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="button w-full flex justify-center item-center gap-6 p-4">
                    <button class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-colors">
                        More
                    </button>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');
        const mobileNav = document.getElementById('mobile-nav');

        mobileMenuButton.addEventListener('click', () => {
            mobileNav.classList.remove('-translate-x-full');
        });

        closeMobileMenuButton.addEventListener('click', () => {
            mobileNav.classList.add('-translate-x-full');
        });
    </script>
</body>
</html>
