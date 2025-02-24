<?php
session_start();
$userRole = $_SESSION['role'] ?? null; // Get user role if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - JumandiGas</title>
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
        .testimonial-card {
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
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
                    <img src="./asset/image/logo.png" alt="JumandiGas" class="h-8">
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-black hover:text-primary">Home</a>
                    <a href="./user/order-page.php" class="text-black hover:text-primary">Order Gas</a>
                    <a href="buy-cylinder.php" class="text-black hover:text-primary">Buy Cylinder</a>
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
        <!-- Testimonials Header -->
        <div class="text-center mb-16">
            <div class="inline-block bg-orange-100/80 px-4 py-2 rounded-full mb-4">
                <span class="text-primary font-medium">Testimonials</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">What Our Customers Say</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Discover why thousands of customers trust JumandiGas for their cooking gas needs. Read their experiences and stories.
            </p>
        </div>

        <!-- Featured Testimonial -->
        <div class="bg-white rounded-3xl p-8 mb-16 shadow-lg">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div class="space-y-6">
                    <div class="text-primary">
                        <i class="fas fa-quote-left text-4xl"></i>
                    </div>
                    <p class="text-2xl font-medium leading-relaxed">
                        "JumandiGas has revolutionized how I get my cooking gas. The service is prompt, professional, and their safety standards are impressive. I never have to worry about running out of gas anymore!"
                    </p>
                    <div class="flex items-center gap-4">
                        <img src="./asset/image/test1.jpg?height=60&width=60" alt="Sarah Johnson" class="w-16 h-16 rounded-full object-cover">
                        <div>
                            <h3 class="font-semibold text-lg">Sarah Johnson</h3>
                            <p class="text-gray-600">Regular Customer</p>
                        </div>
                    </div>
                </div>
                <div class="relative h-[400px] rounded-2xl overflow-hidden">
                    <img src="./asset/image/test1.jpg?height=400&width=500" alt="Customer using JumandiGas service" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Testimonials Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Testimonial Card 1 -->
            <div class="testimonial-card bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex items-center gap-4 mb-4">
                    <img src="/placeholder.svg?height=50&width=50" alt="John Doe" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold">John Doe</h3>
                        <p class="text-sm text-gray-600">Lagos, Nigeria</p>
                    </div>
                </div>
                <p class="text-gray-700">
                    "The delivery is always on time, and the staff is very professional. Great service!"
                </p>
                <div class="mt-4 text-primary">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Testimonial Card 2 -->
            <div class="testimonial-card bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex items-center gap-4 mb-4">
                    <img src="/placeholder.svg?height=50&width=50" alt="Mary Smith" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold">Mary Smith</h3>
                        <p class="text-sm text-gray-600">Ikeja, Lagos</p>
                    </div>
                </div>
                <p class="text-gray-700">
                    "The app is so easy to use, and the delivery is always quick. Highly recommended!"
                </p>
                <div class="mt-4 text-primary">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Testimonial Card 3 -->
            <div class="testimonial-card bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex items-center gap-4 mb-4">
                    <img src="/placeholder.svg?height=50&width=50" alt="David Wilson" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h3 class="font-semibold">David Wilson</h3>
                        <p class="text-sm text-gray-600">Lekki, Lagos</p>
                    </div>
                </div>
                <p class="text-gray-700">
                    "Safe, reliable, and convenient. JumandiGas has made my life easier!"
                </p>
                <div class="mt-4 text-primary">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-16">
            <a href="#" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-orange-700 inline-block">
                Share Your Experience
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo Column -->
                <div>
                    <img src="./asset/image/logos.png" alt="JumandiGas" class="h-12 mb-4">
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