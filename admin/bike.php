<?php
require_once "auth_admin.php";  // Admin authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../asset/toast/toastr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Overlay for mobile sidebar -->
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
                    <li><a href="index" class="block py-2 px-4 rounded bg-orange-700">Dashboard</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Orders</a></li>
                    <li><a href="" class="block py-2 px-4 rounded hover:bg-orange-700">Customers</a></li>
                    <li><a href="location.php" class="block py-2 px-4 rounded hover:bg-orange-700">Location</a></li>
                    <li><a href="bike.php" class="block py-2 px-4 rounded hover:bg-orange-700">Bike Price</a></li>
                    <li><a href="#" class="block py-2 px-4 rounded hover:bg-orange-700">Settings</a></li>
                    <li><a href="logout.php" class="block py-2 px-4 rounded hover:bg-orange-700">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:ml-0">
            <header class="bg-white shadow p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <button id="openSidebar" class="md:hidden mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold">Manage Bike Price</h1>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6"> 
    <h2 class="text-2xl font-bold mb-6">Add New City Price</h2>
    <form id="registerForm" method="POST" enctype="multipart/form-data">
        
        <!-- Country Dropdown -->
        <div class="mb-4">
            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <select id="country" name="country" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                <option value="">Select Country</option>
                <option value="Nigeria">Nigeria</option>
                <option value="Ghana">Ghana</option>
            </select>
        </div>

        <!-- State Dropdown -->
        <div class="mb-4">
            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
            <select id="state" name="state" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                <option value="">Select State</option>
            </select>
        </div>

        <!-- City Dropdown (Updated) -->
        <div class="mb-4">
            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
            <select id="city" name="city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
                <option value="">Select City</option>
            </select>
        </div>

        <!-- Price Input -->
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
            <input type="number" step="0.01" id="price" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" required>
        </div>

        <!-- Currency Input -->
        <div class="mb-6">
            <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
            <input type="text" id="currency" name="currency" maxlength="10" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#ff6b00]" readonly>
        </div>

        <button type="submit" class="w-full bg-[#ff6b00] text-white py-2 px-4 rounded-md hover:bg-[#e55f00] transition duration-300">Add City</button>
    </form>
</div>

            </main>
        </div>
    </div>
    <script src="../asset/toast/jquery-3.7.1.min.js"></script>
    <script src="../asset/toast/toastr.min.js"></script>
    <!-- JavaScript for dynamic state and currency -->
    <script>
    // JavaScript for dynamic country, state, and city selection
    const locations = {
        "Nigeria": {
            states: {
                "Lagos": ["Ikeja", "Surulere", "Lekki"],
                "Abuja": ["Garki", "Maitama", "Wuse"],
                "Kano": ["Fagge", "Gwale", "Nasarawa"],
                "Rivers": ["Port Harcourt", "Obio-Akpor", "Eleme"]
            },
            currency: "NGN (₦)"
        },
        "Ghana": {
            states: {
                "Greater Accra": ["Accra", "Tema", "Madina"],
                "Ashanti": ["Kumasi", "Obuasi", "Ejisu"],
                "Central": ["Cape Coast", "Kasoa", "Winneba"]
            },
            currency: "GHS (₵)"
        }
    };

    // Elements
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    const citySelect = document.getElementById('city');
    const currencyInput = document.getElementById('currency');

    // Handle country selection
    countrySelect.addEventListener('change', function () {
        const selectedCountry = this.value;
        stateSelect.innerHTML = "<option value=''>Select State</option>";
        citySelect.innerHTML = "<option value=''>Select City</option>"; // Reset city dropdown

        if (locations[selectedCountry]) {
            Object.keys(locations[selectedCountry].states).forEach(state => {
                const option = document.createElement('option');
                option.value = state;
                option.textContent = state;
                stateSelect.appendChild(option);
            });
            currencyInput.value = locations[selectedCountry].currency;
        } else {
            currencyInput.value = '';
        }
    });

    // Handle state selection
    stateSelect.addEventListener('change', function () {
        const selectedCountry = countrySelect.value;
        const selectedState = this.value;
        citySelect.innerHTML = "<option value=''>Select City</option>";

        if (locations[selectedCountry] && locations[selectedCountry].states[selectedState]) {
            locations[selectedCountry].states[selectedState].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    });

// Mobile sidebar toggle
const openSidebarBtn = document.getElementById('openSidebar');
const closeSidebarBtn = document.getElementById('closeSidebar');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');

// Open sidebar
openSidebarBtn.addEventListener('click', function () {
    sidebar.classList.remove('hidden');
    overlay.classList.remove('hidden');
});

// Close sidebar when close button or overlay is clicked
closeSidebarBtn.addEventListener('click', function () {
    sidebar.classList.add('hidden');
    overlay.classList.add('hidden');
});

// Close sidebar if overlay is clicked
overlay.addEventListener('click', function () {
    sidebar.classList.add('hidden');
    overlay.classList.add('hidden');
});

// Handle form submission
document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('upload_bike.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            window.location.href = 'index.php';  // Redirect to a dashboard or location list page
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

    </script>
</body>
</html>
