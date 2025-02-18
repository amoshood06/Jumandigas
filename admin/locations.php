<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Mobile Sidebar */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .overlay {
            display: none;
        }
        .overlay.show {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Overlay -->
    <div id="overlay" class="overlay fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-64 bg-[#ff6b00] text-white p-4 z-50 sidebar md:relative md:translate-x-0 hidden md:block">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold">Jumandi Gas</h2>
                <button id="closeSidebar" class="md:hidden text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <nav>
                <ul class="space-y-2">
                    <li><a href="#" class="block py-2 px-4 rounded bg-orange-700">Dashboard</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Orders</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Customers</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Location</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Settings</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-0">
            <!-- Top Navigation -->
            <header class="bg-white shadow p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <button id="openSidebar" class="md:hidden mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold">Manage Locations</h1>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-bold mb-6">Add New Location</h2>
                    <form>
                        <!-- Country Dropdown -->
                        <div class="mb-4">
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select id="country" name="country" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                                <option value="">Select Country</option>
                                <option value="Nigeria">Nigeria</option>
                                <option value="Ghana">Ghana</option>
                            </select>
                        </div>

                        <!-- State Dropdown (Updated) -->
                        <div class="mb-4">
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <select id="state" name="state" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                                <option value="">Select State</option>
                            </select>
                        </div>

                         <!-- Gas Dropdown (Updated) -->
                         <div class="mb-4">
                            <label for="gas" class="block text-sm font-medium text-gray-700 mb-1">Gas kg</label>
                            <select id="gas" name="gas" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                                <option value="">Select Gas kg</option>
                                <option value="1kg">1 kg</option>
                                <option value="2kg">2 kg</option>
                            </select>
                        </div>
                        <!-- Specific price -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="text" id="price" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" placeholder="Enter specific gas" required>
                        </div>

                        <!-- Currency Input (Updated) -->
                        <div class="mb-6">
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <input type="text" id="currency" name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" readonly>
                        </div>

                        <button type="submit" class="w-full bg-[#ff6b00] text-white py-2 px-4 rounded-md hover:bg-[#e55f00] transition duration-300">Add Location</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for Sidebar Toggle & Location Selection -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');
        const countrySelect = document.getElementById("country");
        const stateSelect = document.getElementById("state");
        const currencyInput = document.getElementById("currency");

        function toggleSidebar(show) {
            sidebar.classList.toggle('open', show);
            overlay.classList.toggle('show', show);
            sidebar.classList.toggle('hidden', !show);
        }

        openSidebarBtn.addEventListener('click', () => toggleSidebar(true));
        closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));
        overlay.addEventListener('click', () => toggleSidebar(false));

        const locations = {
            "Nigeria": {
                states: ["Lagos", "Abuja", "Kano", "Rivers", "Oyo", "Kaduna", "Enugu"],
                currency: "NGN (₦)"
            },
            "Ghana": {
                states: ["Greater Accra", "Ashanti", "Central", "Northern", "Western", "Volta", "Eastern"],
                currency: "GHS (₵)"
            }
        };

        countrySelect.addEventListener("change", function () {
            const selectedCountry = countrySelect.value;
            stateSelect.innerHTML = "<option value=''>Select State</option>";

            if (selectedCountry && locations[selectedCountry]) {
                locations[selectedCountry].states.forEach(state => {
                    const option = document.createElement("option");
                    option.value = state;
                    option.textContent = state;
                    stateSelect.appendChild(option);
                });

                currencyInput.value = locations[selectedCountry].currency;
            } else {
                currencyInput.value = "";
            }
        });
    </script>
</body>
</html>
