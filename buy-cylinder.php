<?php
session_start();
$userRole = $_SESSION['role'] ?? null; // Get user role if logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Cylinder - JumandiGas</title>
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
        .cylinder-card {
            transition: all 0.3s ease;
        }
        .cylinder-card:hover {
            transform: translateY(-5px);
        }
        
        /* WhatsApp Floating Button Styles */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 100;
        }
        
        .whatsapp-button {
            width: 60px;
            height: 60px;
            background-color: #25D366;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .whatsapp-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
        }
        
        /* Bouncing Animation */
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }
        
        .bounce {
            animation: bounce 2s infinite;
        }
        
        /* Pulse Animation for additional attention */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }
        
        .pulse {
            animation: pulse 2s infinite;
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
        <!-- Buy Cylinder Header -->
        <div class="text-center mb-16">
            <div class="inline-block bg-orange-100/80 px-4 py-2 rounded-full mb-4">
                <span class="text-primary font-medium">Buy Cylinder</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Choose Your Perfect Cylinder</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Explore our range of high-quality, safe, and durable gas cylinders. Find the perfect size for your home or business needs.
            </p>
        </div>

        <!-- Cylinders Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Cylinder Card 1 -->
            <div class="cylinder-card bg-white p-6 rounded-2xl shadow-lg">
                <img src="./asset/image/3kg-removebg-preview.png" alt="3kg Gas Cylinder" class="w-full h-48 object-contain mb-4">
                <h3 class="text-xl font-semibold mb-2">3kg Gas Cylinder</h3>
                <ul class="text-gray-600 mb-4">
                    <li><i class="fas fa-check text-primary mr-2"></i> Ideal for small households</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Portable and lightweight</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Perfect for camping trips</li>
                </ul>
                <button onclick="redirectToWhatsApp('3kg')" class="block w-full text-center bg-primary text-white py-2 rounded-full hover:bg-orange-700 transition duration-300">
                    Inquire Now
                </button>
            </div>

            <!-- Cylinder Card 2 -->
            <div class="cylinder-card bg-white p-6 rounded-2xl shadow-lg">
                <img src="./asset/image/6kg-removebg-preview.png" alt="6kg Gas Cylinder" class="w-full h-48 object-contain mb-4">
                <h3 class="text-xl font-semibold mb-2">6kg Gas Cylinder</h3>
                <ul class="text-gray-600 mb-4">
                    <li><i class="fas fa-check text-primary mr-2"></i> Suitable for medium-sized families</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Balanced size and capacity</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Easy to handle and store</li>
                </ul>
                <button onclick="redirectToWhatsApp('6kg')" class="block w-full text-center bg-primary text-white py-2 rounded-full hover:bg-orange-700 transition duration-300">
                    Inquire Now
                </button>
            </div>

            <!-- Cylinder Card 3 -->
            <div class="cylinder-card bg-white p-6 rounded-2xl shadow-lg">
                <img src="./asset/image/12.5kg-removebg-preview.png" alt="12.5kg Gas Cylinder" class="w-full h-48 object-contain mb-4">
                <h3 class="text-xl font-semibold mb-2">12.5kg Gas Cylinder</h3>
                <ul class="text-gray-600 mb-4">
                    <li><i class="fas fa-check text-primary mr-2"></i> Perfect for large families</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Ideal for frequent cooking</li>
                    <li><i class="fas fa-check text-primary mr-2"></i> Long-lasting supply</li>
                </ul>
                <button onclick="redirectToWhatsApp('12.5kg')" class="block w-full text-center bg-primary text-white py-2 rounded-full hover:bg-orange-700 transition duration-300">
                    Inquire Now
                </button>
            </div>
        </div>

        <!-- Why Choose Our Cylinders -->
        <div class="mt-20">
            <h2 class="text-3xl font-bold mb-8 text-center">Why Choose Our Cylinders?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <i class="fas fa-shield-alt text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Safety First</h3>
                    <p class="text-gray-600">All our cylinders meet the highest safety standards and undergo regular inspections.</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-medal text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Premium Quality</h3>
                    <p class="text-gray-600">Made from high-grade materials to ensure durability and long-lasting performance.</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-sync-alt text-4xl text-primary mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Easy Refills</h3>
                    <p class="text-gray-600">Convenient refill service available through our gas delivery system.</p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-16">
            <button onclick="redirectToWhatsApp('Bulk Order')" class="bg-primary text-white px-8 py-3 rounded-full hover:bg-orange-700 inline-block text-lg font-semibold">
                Contact Us for Bulk Orders
            </button>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo Column -->
                <div>
                    <img src="./asset/image/logo.png" alt="JumandiGas" class="h-12 mb-4">
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

    <!-- Floating WhatsApp Button -->
    <div class="whatsapp-float">
        <div class="whatsapp-button bounce pulse" onclick="redirectToWhatsAppSupport()">
            <i class="fab fa-whatsapp"></i>
        </div>
    </div>

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

        // WhatsApp integration function for product inquiries
        function redirectToWhatsApp(cylinderSize) {
            // Replace with your actual WhatsApp number
            const phoneNumber = "+2347018933739";
            const message = `Hello, I'm interested in buying a ${cylinderSize} gas cylinder. Please provide more information.`;
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
        
        // WhatsApp integration function for customer support
        function redirectToWhatsAppSupport() {
            // Replace with your actual WhatsApp support number (can be the same as above)
            const phoneNumber = "+2347018933739";
            const message = "Hello, I need assistance with JumandiGas products/services.";
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
        
        // Stop bouncing animation after 10 seconds to avoid annoying the user
        setTimeout(() => {
            const whatsappButton = document.querySelector('.whatsapp-button');
            whatsappButton.classList.remove('bounce');
            // Keep the pulse animation for subtle attention
        }, 10000);
    </script>
</body>
</html>