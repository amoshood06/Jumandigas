<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - Vendor Reports</title>
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
                            <a href="#" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Reports</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Reports Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Vendor Reports</h1>
                
                <!-- Report Filters -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Generate Report</h2>
                    <form id="reportForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="reportType" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                                <select id="reportType" name="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                    <option value="sales">Sales Report</option>
                                    <option value="inventory">Inventory Report</option>
                                    <option value="customers">Customer Report</option>
                                </select>
                            </div>
                            <div>
                                <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                <select id="dateRange" name="dateRange" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]">
                                    <option value="last7days">Last 7 Days</option>
                                    <option value="last30days">Last 30 Days</option>
                                    <option value="lastMonth">Last Month</option>
                                    <option value="lastYear">Last Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-offset-2">
                                Generate Report
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Report Summary -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Report Summary</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">Total Sales</h3>
                            <p class="text-2xl font-bold text-gray-900">₦1,234,567</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                            <p class="text-2xl font-bold text-gray-900">256</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500">Average Order Value</h3>
                            <p class="text-2xl font-bold text-gray-900">₦4,823</p>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Sales Trend</h2>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>

                <!-- Top Products Table -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Top Selling Products</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Sold</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">5kg Gas Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">150</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦600,000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">12.5kg Gas Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">100</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦800,000</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">3kg Gas Cylinder</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">75</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦187,500</td>
                                </tr>
                            </tbody>
                        </table>
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

        // Handle report form submission
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // In a real application, you would generate the report based on the selected options
            console.log('Generating report...');
            alert('Report generated successfully!');
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [1200000, 1900000, 300000, 500000, 2000000, 3000000],
                    borderColor: 'rgb(255, 107, 0)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return '₦' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>