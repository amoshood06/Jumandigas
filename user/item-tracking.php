<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - JumandiGas</title>
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
        .tracking-line {
            height: 2px;
            background-color: #e5e7eb;
        }
        .tracking-dot {
            width: 20px;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 50%;
        }
        .tracking-dot.active {
            background-color: #FF6B00;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
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
                    <a href="order-page.php" class="text-black hover:text-primary">Order Gas</a>
                    <a href="item-tracking.php" class="text-black hover:text-primary font-semibold">Track Order</a>
                    <a href="#" class="text-black hover:text-primary">My Account</a>
                    <a href="logout.php" class="bg-primary text-white px-8 py-2 rounded-full hover:bg-orange-700">Logout</a>
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
                <a href="order-page.php" class="text-black hover:text-primary">Order Gas</a>
                <a href="item-tracking.php" class="text-black hover:text-primary font-semibold">Track Order</a>
                <a href="#" class="text-black hover:text-primary">My Account</a>
                <a href="logout.php" class="bg-primary text-white px-6 py-2 rounded-full text-center hover:bg-orange-700">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <!-- Track Order Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Track Your Order</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Stay updated on the status of your gas cylinder delivery. Enter your order number to track its progress.
            </p>
        </div>

        <!-- Order Tracking Form -->
        <div class="max-w-md mx-auto mb-12">
            <form id="trackingForm" class="bg-white p-6 rounded-2xl shadow-lg">
                <div class="mb-4">
                    <label for="orderNumber" class="block text-gray-700 mb-2">Order Number</label>
                    <input type="text" id="orderNumber" name="orderNumber" placeholder="Enter your order number" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded-full hover:bg-orange-700 transition duration-300">
                    Track Order
                </button>
            </form>
        </div>

        <!-- Order Details and Tracking (Initially Hidden) -->
        <div id="orderDetails" class="bg-white p-6 rounded-2xl shadow-lg mb-8 hidden">
            <h2 class="text-2xl font-semibold mb-4">Order #<span id="displayOrderNumber"></span></h2>
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="font-semibold">Estimated Delivery:</p>
                    <p id="estimatedDelivery">July 16, 2023, 2:00 PM - 4:00 PM</p>
                </div>
                <div>
                    <p class="font-semibold">Order Status:</p>
                    <p id="orderStatus" class="text-green-600">In Transit</p>
                </div>
            </div>
            <div class="mb-6">
                <p class="font-semibold mb-2">Order Items:</p>
                <ul id="orderItems" class="list-disc pl-5">
                    <li>1x 12.5kg Gas Cylinder</li>
                    <li>1x Gas Regulator</li>
                </ul>
            </div>
            <div class="mb-6">
                <p class="font-semibold mb-2">Delivery Address:</p>
                <p id="deliveryAddress">123 Main St, Lekki, Lagos</p>
            </div>
            <div id="map" class="w-full h-64 rounded-lg mt-4"></div>
        </div>

        <!-- Tracking Timeline (Initially Hidden) -->
        <div id="trackingTimeline" class="bg-white p-6 rounded-2xl shadow-lg hidden">
            <h3 class="text-xl font-semibold mb-4">Tracking Timeline</h3>
            <div class="relative">
                <div class="tracking-line absolute left-2 top-2 bottom-2"></div>
                <div class="space-y-8">
                    <div class="flex items-center">
                        <div class="tracking-dot active z-10"></div>
                        <div class="ml-4">
                            <p class="font-semibold">Order Placed</p>
                            <p class="text-sm text-gray-500">July 15, 2023, 10:30 AM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="tracking-dot active z-10"></div>
                        <div class="ml-4">
                            <p class="font-semibold">Order Confirmed</p>
                            <p class="text-sm text-gray-500">July 15, 2023, 11:00 AM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="tracking-dot active z-10"></div>
                        <div class="ml-4">
                            <p class="font-semibold">Out for Delivery</p>
                            <p class="text-sm text-gray-500">July 16, 2023, 9:00 AM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="tracking-dot z-10"></div>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-400">Delivered</p>
                            <p class="text-sm text-gray-500">Estimated: July 16, 2023, 2:00 PM - 4:00 PM</p>
                        </div>
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

        // Order tracking form submission (mock)
        document.getElementById('trackingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const orderNumber = document.getElementById('orderNumber').value;
            
            // Show order details and tracking timeline
            document.getElementById('orderDetails').classList.remove('hidden');
            document.getElementById('trackingTimeline').classList.remove('hidden');
            
            // Update order number in the details section
            document.getElementById('displayOrderNumber').textContent = orderNumber;
            
            // Scroll to order details
            document.getElementById('orderDetails').scrollIntoView({ behavior: 'smooth' });

            updateMap(6.5244, 3.3792); // Replace with actual coordinates from your backend
        });

    // Google Maps functionality
    let map;
    let marker;

    function initMap() {
        // Default coordinates (you can set this to a default location)
        const defaultLocation = { lat: 6.5244, lng: 3.3792 }; // Lagos, Nigeria

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 13,
        });

        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            title: "Delivery Location"
        });
    }

    function updateMap(lat, lng) {
        const newPosition = new google.maps.LatLng(lat, lng);
        map.setCenter(newPosition);
        marker.setPosition(newPosition);
    }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let inactivityTime = 0; // Time in seconds

    function resetTimer() {
        inactivityTime = 0; // Reset the timer when user interacts
    }

    // Listen for user activity
    $(document).on('mousemove keypress click scroll', function () {
        resetTimer();
    });

    // Check inactivity every second
    setInterval(function () {
        inactivityTime++;
        if (inactivityTime >= 300) { // 300 seconds = 5 minutes
            autoLogout();
        }
    }, 1000);

    function autoLogout() {
        $.post("logout.php", { ajax: true }, function (response) {
            let data = JSON.parse(response);
            if (data.status === "success") {
                alert(data.message);
                window.location.href = "../login.php"; // Redirect to login page
            }
        });
    }
</script>

</body>
</html>

