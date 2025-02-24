<?php
session_start();
$userRole = $_SESSION['role'] ?? null; // Get user role if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - JumandiGas</title>
    <link rel="shortcut icon" href="./asset/image/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF6B00',
                    }
                }
            }
        }
    </script>
    <style>
        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .mobile-menu.active {
            transform: translateX(0);
        }
    </style>
</head>
<body class="bg-orange-50 pt-16">
    <!-- Fixed Header -->
    <header class="fixed top-0 left-0 right-0 bg-orange-50 border-b z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="#" class="flex-shrink-0">
                <img src="./asset/image/logo.png" alt="City Logo" class="w-20 h-10 object-contain">
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-black hover:text-primary">Home</a>
                    <a href="#" class="text-black hover:text-primary">Order Gas</a>
                    <a href="#" class="text-black hover:text-primary">Buy Cylinder</a>
                    <?php if (!$userRole): ?>
                            <a href="#" class="text-black hover:text-primary">Register</a>
                            <a href="#" class="bg-primary text-white px-8 py-2 rounded-full hover:bg-orange-700">Login</a>
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
                        <a href="<?= $dashboardUrl ?>" class="bg-primary text-white px-8 py-2 rounded-full hover:bg-orange-700">Dashboard</a>
                    <?php endif; ?>
                </nav>

                <!-- Mobile menu button -->
                <button class="md:hidden text-gray-600 hover:text-gray-900" id="mobile-menu-button">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-menu fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50 md:hidden">
        <div class="p-4">
            <button class="mb-4 text-gray-600 hover:text-gray-900" id="close-menu-button">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="flex flex-col space-y-4">
                <a href="#" class="text-black hover:text-primary">Home</a>
                <a href="#" class="text-black hover:text-primary">Order Gas</a>
                <a href="#" class="text-black hover:text-primary">Buy Cylinder</a>
                <?php if (!$userRole): ?>
                    <a href="register.php" class="text-black hover:text-primary">Register</a>
                    <a href="login.php" class="bg-primary text-white px-6 py-2 rounded-full text-center hover:bg-orange-700">Login</a>
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
                    <a href="<?= $dashboardUrl ?>" class="bg-primary text-white px-6 py-2 rounded-full text-center hover:bg-orange-700">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-2 gap-12 items-start">
            <!-- Left Column -->
            <div class="space-y-6">
                <div class="inline-block bg-orange-100/80 px-4 py-2 rounded-full">
                    <span class="text-primary font-medium">About us</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold">
                    About us
                </h1>

                <p class="text-gray-700 text-lg max-w-lg">
                    At JumandiGas, we bring you a seamless and stress-free way to order and receive cooking gas at your doorstep. No more unexpected gas shortages or long refill queues—just a fast, safe, and convenient delivery service that keeps your kitchen running.
                </p>

                <a href="#" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-orange-700 inline-block">
                    Book Now
                </a>

                <!-- Industrial Images -->
                <div class="grid grid-cols-2 gap-4 mt-8">
                    <div class="rounded-2xl overflow-hidden">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/About%20us-TOgOVk1fW3D07SVapgaXGpL7IlegWD.png" alt="Gas Storage Facility" class="w-full h-48 object-cover">
                    </div>
                    <div class="rounded-2xl overflow-hidden">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/About%20us-TOgOVk1fW3D07SVapgaXGpL7IlegWD.png" alt="Gas Storage Tanks" class="w-full h-48 object-cover">
                    </div>
                </div>
            </div>

            <!-- Right Column - Map -->
            <div class="bg-[#F5F5F5] rounded-2xl overflow-hidden">
                <div class="relative w-full h-[600px]">
                    <img src="./asset/image/map.png" alt="Service Area Map" class="w-full h-full object-cover">
                    <!-- Location Labels -->
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white px-4 py-2 rounded-full text-sm text-gray-600">
                        Click on any live location to order from restaurants near you
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo Column -->
                <div>
                <img src="./asset/image/logos.png" alt="City Logo" class="w-20 h-10 object-contain">
                </div>

                <!-- Company Column -->
                <div>
                    <h3 class="font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="about-us.php" class="hover:underline">About us</a></li>
                        <li><a href="faqs.php" class="hover:underline">FAQs</a></li>
                        <li><a href="testimonials.php" class="hover:underline">Customer Stories</a></li>
                    </ul>
                </div>

                <!-- Account Column -->
                <div>
                    <h3 class="font-semibold mb-4">Account</h3>
                    <ul class="space-y-2">
                        <li><a href="./user/index.php" class="hover:underline">Customers</a></li>
                        <li><a href="./vendor/index.php" class="hover:underline">Vendors</a></li>
                        <li><a href="./Riders/index.php" class="hover:underline">Riders</a></li>
                    </ul>
                </div>

                <!-- App Column -->
                <div>
                    <h3 class="font-semibold mb-4">App</h3>
                    <div class="space-y-4">
                        <a href="#" class="inline-block">
                            <button class="bg-black text-white px-6 py-2 rounded-lg flex items-center hover:bg-gray-800 transition duration-300">
                                <i class="fab fa-apple text-2xl mr-2"></i>
                                <div class="text-left">
                                    <p class="text-xs">Download on the</p>
                                    <p class="text-sm font-semibold">App Store</p>
                                </div>
                            </button>
                        </a>
                        <a href="#" class="inline-block">
                            <button class="bg-black text-white px-6 py-2 rounded-lg flex items-center hover:bg-gray-800 transition duration-300">
                                <i class="fab fa-google-play text-2xl mr-2"></i>
                                <div class="text-left">
                                    <p class="text-xs">GET IT ON</p>
                                    <p class="text-sm font-semibold">Google Play</p>
                                </div>
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center text-sm">
                <p>© All Rights Reserved. 2025, Jumandi Gas.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu functionality
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMenuButton = document.getElementById('close-menu-button');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.add('active');
        });

        closeMenuButton.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                mobileMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>