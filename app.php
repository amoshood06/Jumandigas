<?php
session_start();
$userRole = $_SESSION['role'] ?? null; // Get user role if logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JumandiGas - Download Our Apps</title>
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
        .app-card {
            transition: all 0.3s ease;
        }
        .app-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <!-- Fixed Header -->
    <header class="bg-orange-50 border-b sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="/" class="flex-shrink-0">
                    <div class="h-8 w-32 bg-primary rounded-md flex items-center justify-center text-white font-bold">
                        JumandiGas
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-black hover:text-primary">Home</a>
                    <a href="./user/order-page.php" class="text-black hover:text-primary">Order Gas</a>
                    <a href="buy-cylinder.php" class="text-black hover:text-primary">Buy Cylinder</a>
                        <?php if (!$userRole): ?>
                            <a href="register.php" class="text-black hover:text-primary">Register</a>
                            <a href="login.php" class="bg-primary text-white px-8 py-2 rounded-full hover:bg-orange-700">Login</a>
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
            <a href="index.php" class="text-black hover:text-primary">Home</a>
                    <a href="./user/order-page.php" class="text-black hover:text-primary">Order Gas</a>
                    <a href="buy-cylinder.php" class="text-black hover:text-primary">Buy Cylinder</a>
                        <?php if (!$userRole): ?>
                            <a href="register.php" class="text-black hover:text-primary">Register</a>
                            <a href="login.php" class="bg-primary text-white px-8 py-2 rounded-full hover:bg-orange-700">Login</a>
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
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-12">
        <!-- App Links Header -->
        <div class="text-center mb-16">
            <div class="inline-block bg-orange-100/80 px-4 py-2 rounded-full mb-4">
                <span class="text-primary font-medium">Our Mobile Apps</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Choose Your App</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Download the JumandiGas app that fits your needs. Whether you're a customer, vendor, or delivery rider,
                we have a specialized app for you.
            </p>
        </div>

        <!-- App Cards -->
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- User App -->
            <div class="app-card bg-white p-8 rounded-2xl shadow-lg">
                <div class="h-16 w-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-users text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-center">User App</h3>
                <p class="text-gray-600 mb-6 text-center">
                    Order gas, track deliveries, and manage your account with our user-friendly customer app.
                </p>
                <div class="space-y-3">
                    <a href="https://mega.nz/file/bHABCR5I#GAS-HJn9WAXI_AfU6bxYL4ATJSHV2d3DgRvN2fDisfM" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-apple text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">Download on the</p>
                            <p class="text-sm font-semibold">App Store</p>
                        </div>
                    </a>
                    <a href="https://mega.nz/file/bHABCR5I#GAS-HJn9WAXI_AfU6bxYL4ATJSHV2d3DgRvN2fDisfM" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-google-play text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">GET IT ON</p>
                            <p class="text-sm font-semibold">Google Play</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Vendor App -->
            <div class="app-card bg-white p-8 rounded-2xl shadow-lg">
                <div class="h-16 w-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-store text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-center">Vendor App</h3>
                <p class="text-gray-600 mb-6 text-center">
                    Manage inventory, process orders, and track sales with our comprehensive vendor dashboard.
                </p>
                <div class="space-y-3">
                    <a href="https://apps.apple.com" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-apple text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">Download on the</p>
                            <p class="text-sm font-semibold">App Store</p>
                        </div>
                    </a>
                    <a href="https://play.google.com" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-google-play text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">GET IT ON</p>
                            <p class="text-sm font-semibold">Google Play</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Rider App -->
            <div class="app-card bg-white p-8 rounded-2xl shadow-lg">
                <div class="h-16 w-16 bg-orange-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-motorcycle text-primary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4 text-center">Rider App</h3>
                <p class="text-gray-600 mb-6 text-center">
                    Accept deliveries, navigate routes, and manage your earnings with our rider-focused app.
                </p>
                <div class="space-y-3">
                    <a href="https://apps.apple.com" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-apple text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">Download on the</p>
                            <p class="text-sm font-semibold">App Store</p>
                        </div>
                    </a>
                    <a href="https://play.google.com" class="flex items-center justify-center bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors w-full">
                        <i class="fab fa-google-play text-2xl mr-2"></i>
                        <div class="text-left">
                            <p class="text-xs">GET IT ON</p>
                            <p class="text-sm font-semibold">Google Play</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-24">
            <h2 class="text-3xl font-bold mb-12 text-center">App Features</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-download text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Easy Installation</h3>
                    <p class="text-gray-600">Download and set up in minutes with our simple onboarding process.</p>
                </div>
                <div class="text-center">
                    <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-lock text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Secure Transactions</h3>
                    <p class="text-gray-600">All payments and personal data are protected with industry-standard encryption.</p>
                </div>
                <div class="text-center">
                    <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-bolt text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Real-time Updates</h3>
                    <p class="text-gray-600">Get instant notifications about your orders, deliveries, and account activity.</p>
                </div>
                <div class="text-center">
                    <div class="h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-headset text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Our customer service team is always available to help with any issues.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-orange-100 rounded-2xl p-8 md:p-12 mt-24 text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to get started?</h2>
            <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
                Download the JumandiGas app today and experience the convenience of gas delivery at your fingertips.
            </p>
            <a href="#" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-orange-600 transition-colors inline-block text-lg font-semibold">
                Download Now
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo Column -->
                <div>
                    <div class="h-12 w-32 bg-white rounded-md flex items-center justify-center text-primary font-bold mb-4">
                        JumandiGas
                    </div>
                    <p class="text-sm">
                        Your trusted partner for reliable gas delivery services.
                    </p>
                </div>

                <!-- Company Column -->
                <div>
                    <h3 class="font-semibold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">About us</a></li>
                        <li><a href="#" class="hover:underline">FAQs</a></li>
                        <li><a href="#" class="hover:underline">Customer Stories</a></li>
                    </ul>
                </div>

                <!-- Account Column -->
                <div>
                    <h3 class="font-semibold mb-4">Account</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:underline">Customers</a></li>
                        <li><a href="#" class="hover:underline">Vendors</a></li>
                        <li><a href="#" class="hover:underline">Riders</a></li>
                    </ul>
                </div>

                <!-- App Column -->
                <div>
                    <h3 class="font-semibold mb-4">App</h3>
                    <div class="space-y-4">
                        <a href="#" class="inline-block">
                            <button class="bg-black text-white px-6 py-2 rounded-lg flex items-center hover:bg-gray-800 transition-colors">
                                <i class="fab fa-apple text-2xl mr-2"></i>
                                <div class="text-left">
                                    <p class="text-xs">Download on the</p>
                                    <p class="text-sm font-semibold">App Store</p>
                                </div>
                            </button>
                        </a>
                        <a href="#" class="inline-block">
                            <button class="bg-black text-white px-6 py-2 rounded-lg flex items-center hover:bg-gray-800 transition-colors">
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
        const overlay = document.createElement('div');
        
        overlay.classList.add('fixed', 'inset-0', 'bg-black', 'opacity-0', 'pointer-events-none', 'z-40');
        overlay.style.transition = 'opacity 0.3s ease-in-out';
        document.body.appendChild(overlay);

        function openMobileMenu() {
            mobileMenu.classList.add('active');
            overlay.classList.add('opacity-50');
            overlay.classList.remove('pointer-events-none');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileMenu.classList.remove('active');
            overlay.classList.remove('opacity-50');
            overlay.classList.add('pointer-events-none');
            document.body.style.overflow = '';
        }

        mobileMenuButton.addEventListener('click', openMobileMenu);
        closeMenuButton.addEventListener('click', closeMobileMenu);
        overlay.addEventListener('click', closeMobileMenu);

        // Close menu when window is resized to desktop size
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) { // md breakpoint
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>