<?php 
session_start();
require '../db/db.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - User Complaints</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        <button id="menuButton" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>
        <img src="../asset/image/logos.png" alt="Jumandi Gas Logo" class="h-12 hidden lg:block">
        <div class="text-white">
            <p class="text-sm">User Account</p>
            <p class="text-2xl font-bold">John Doe</p>
        </div>
        <a href="logout.php">
            <button class="bg-gray-200 px-6 py-2 rounded-full font-bold">Logout</button>
        </a>
    </header>

    <!-- Main Content -->
    <main class="bg-white rounded-t-[2rem] min-h-screen p-4 lg:p-6 lg:ml-0">
        <div class="flex gap-6 relative">
            <!-- Sidebar -->
            <div id="sidebar" class="fixed inset-y-0 left-0 lg:relative lg:block bg-white z-50 w-64 h-screen overflow-y-auto transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
                <div class="flex flex-col h-full">
                    <div class="lg:hidden flex justify-between items-center p-4 border-b">
                        <img src="../asset/image/logo.png" alt="Jumandi Gas Logo" class="h-8">
                        <button id="closeMenu" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex-grow overflow-y-auto">
                        <nav class="space-y-2 px-4">
                            <a href="index.php" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
                            <a href="order-page.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas</a>
                            <a href="user-order-history.php" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas History</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
                            <a href="user-complaint.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Complaints</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Complaint Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Complaints</h1>

                <!-- Complaint Form -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Submit a Complaint</h2>
                    <form id="complaintForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Complaint Type</label>
                            <select id="complaintType" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-[#ff6b00]">
                                <option value="">Select a complaint type</option>
                                <option value="delivery">Delivery Issue</option>
                                <option value="product">Product Quality</option>
                                <option value="service">Customer Service</option>
                                <option value="billing">Billing Problem</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="complaintDescription" required rows="4" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-[#ff6b00]"></textarea>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00]">
                            Submit Complaint
                        </button>
                    </form>
                </div>

                <!-- Complaint History -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Complaint History</h2>
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="border px-4 py-2">Date</th>
                                <th class="border px-4 py-2">Type</th>
                                <th class="border px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody id="complaintTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
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

        document.getElementById('complaintForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const type = document.getElementById('complaintType').value;
            const description = document.getElementById('complaintDescription').value;
            const date = new Date().toLocaleDateString();
            const tableBody = document.getElementById('complaintTableBody');

            const newRow = tableBody.insertRow();
            newRow.innerHTML = `
                <td class="border px-4 py-2">${date}</td>
                <td class="border px-4 py-2">${type}</td>
                <td class="border px-4 py-2 text-yellow-600">Pending</td>
            `;

            alert('Complaint submitted successfully!');
            this.reset();
        });
    </script>
</body>
</html>
