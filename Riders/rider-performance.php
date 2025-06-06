<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Rider Performance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <p class="text-sm">Rider Account</p>
            <p class="text-2xl font-bold">John Doe</p>
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
                        <!-- <div class="flex flex-col items-center my-8">
                            <img src="/placeholder.svg?height=100&width=100" alt="Rider Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">John Doe</h2>
                            <p class="text-sm text-gray-500">Rider ID: R12345</p>
                        </div> -->
                        
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Dashboard</a>
                            <a href="rider-pending-deliveries.php" class="block p-3 hover:bg-orange-100 rounded-lg">Pending Deliveries</a>
                            <a href="rider-delivery-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Delivery History</a>
                            <a href="rider-performance.php" class="block p-3 hover:bg-orange-100 rounded-lg">My Performance</a>
                            <a href="rider-settings.php" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Performance Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">My Performance</h1>
                
                <!-- Performance Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Overall Rating</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">4.8 <span class="text-yellow-400">★★★★★</span></p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">On-Time Deliveries</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">98%</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-2">Customer Satisfaction</h3>
                        <p class="text-2xl font-bold text-[#ff6b00]">96%</p>
                    </div>
                </div>

                <!-- Performance Charts -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Deliveries Chart -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Deliveries (Last 7 Days)</h3>
                        <canvas id="deliveriesChart"></canvas>
                    </div>
                    <!-- Ratings Chart -->
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Ratings Distribution</h3>
                        <canvas id="ratingsChart"></canvas>
                    </div>
                </div>

                <!-- Achievements -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <h2 class="text-lg font-semibold mb-4">Achievements</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-award text-4xl text-yellow-400 mb-2"></i>
                            <p class="text-sm font-semibold">Top Performer</p>
                            <p class="text-xs text-gray-500">This Month</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <i class="fas fa-clock text-4xl text-blue-500 mb-2"></i>
                            <p class="text-sm font-semibold">Always On Time</p>
                            <p class="text-xs text-gray-500">30 Days Streak</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-4xl text-green-500 mb-2"></i>
                            <p class="text-sm font-semibold">Customer Favorite</p>
                            <p class="text-xs text-gray-500">50+ 5-Star Ratings</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <i class="fas fa-truck text-4xl text-purple-500 mb-2"></i>
                            <p class="text-sm font-semibold">Delivery Expert</p>
                            <p class="text-xs text-gray-500">500+ Deliveries</p>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold">Detailed Metrics</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-semibold mb-2">Average Delivery Time</h3>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-green-600 h-2.5 rounded-full" style="width: 85%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">28 mins</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold mb-2">Order Acceptance Rate</h3>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 92%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">92%</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold mb-2">Customer Feedback Response</h3>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 78%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">78%</span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold mb-2">Delivery Accuracy</h3>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-purple-600 h-2.5 rounded-full" style="width: 99%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500">99%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Feedback -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b">
                        <h2 class="text-lg font-semibold">Recent Customer Feedback</h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="/placeholder.svg?height=40&width=40" alt="Customer">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Alice Johnson</p>
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">★★★★★</span>
                                        <span class="ml-1 text-sm text-gray-500">2 days ago</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-700">Very prompt and professional delivery. The rider was courteous and helpful.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="/placeholder.svg?height=40&width=40" alt="Customer">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Bob Smith</p>
                                    <div class="flex items-center">
                                        <span class="text-yellow-400">★★★★</span>★
                                        <span class="ml-1 text-sm text-gray-500">4 days ago</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-700">Good service, but arrived a bit later than expected. Overall satisfied.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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

        // Deliveries Chart
        const deliveriesCtx = document.getElementById('deliveriesChart').getContext('2d');
        new Chart(deliveriesCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Deliveries',
                    data: [12, 19, 15, 17, 14, 13, 18],
                    backgroundColor: '#ff6b00',
                    borderColor: '#ff6b00',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Ratings Chart
        const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
        new Chart(ratingsCtx, {
            type: 'pie',
            data: {
                labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
                datasets: [{
                    data: [70, 20, 5, 3, 2],
                    backgroundColor: [
                        '#4CAF50',
                        '#8BC34A',
                        '#FFC107',
                        '#FF9800',
                        '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>