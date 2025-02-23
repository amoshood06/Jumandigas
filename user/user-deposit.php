<?php
session_start();
require '../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate user session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $amount = $_POST['depositAmount'];
    $payment_method = $_POST['paymentMethod'];

    // Validate input
    if ($amount < 100) {
        echo json_encode(["status" => "error", "message" => "Minimum deposit amount is ₦100"]);
        exit;
    }

    try {
        // Insert deposit record
        $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, payment_method, status) VALUES (?, ?, ?, 'successful')");
        $stmt->execute([$user_id, $amount, $payment_method]);

        // Update user balance
        $updateBalance = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $updateBalance->execute([$amount, $user_id]);

        // Fetch updated balance and currency
        $balanceQuery = $pdo->prepare("SELECT balance, currency FROM users WHERE id = ?");
        $balanceQuery->execute([$user_id]);
        $user = $balanceQuery->fetch(PDO::FETCH_ASSOC);

        // Return response
        echo json_encode([
            "status" => "success", 
            "message" => "Deposit successful",
            "balance" => number_format($user['balance'], 2), 
            "currency" => $user['currency']
        ]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jumandi Gas - User Deposit</title>
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
        <!-- Mobile Menu Button -->
        <button id="menuButton" class="lg:hidden text-white p-2">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Logo - hidden on mobile, visible on desktop -->
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
                            <img src="/placeholder.svg?height=100&width=100" alt="User Profile" class="rounded-full w-24 h-24 mb-4">
                            <h2 class="text-xl font-semibold">John Doe</h2>
                            <p class="text-sm text-gray-500">User ID: U12345</p>
                        </div>
                        
                        <nav class="space-y-2 px-4">
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Dashboard</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Order Gas</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Order History</a>
                            <a href="#" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Deposit</a>
                            <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Overlay for mobile -->
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Deposit Content -->
            <div class="flex-1 w-full">
                <h1 class="text-2xl font-bold mb-6">Deposit Funds</h1>
                
                <!-- Account Balance --> 
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-2">Current Balance</h2>
                    <p class="text-3xl font-bold text-[#ff6b00]">
                        <span id="currencySymbol">₦</span> 
                        <span id="currentBalance">0.00</span>
                    </p>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        fetchBalance();
                    });

                    function fetchBalance() {
                        fetch("fetch_balance.php") // Create a new file to get balance
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById("currentBalance").innerText = data.balance;
                                document.getElementById("currencySymbol").innerText = data.currency;
                            }
                        })
                        .catch(error => console.error("Error fetching balance:", error));
                    }
                </script>


                <!-- Deposit Form -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Make a Deposit</h2>
                    <form id="depositForm">
                        <div class="mb-4">
                            <label for="depositAmount" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (₦)</label>
                            <input type="number" id="depositAmount" name="depositAmount" min="100" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" required>
                        </div>
                        <div class="mb-4">
                            <label for="paymentMethod" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="paymentMethod" name="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" required>
                                <option value="">Select a payment method</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="ussd">USSD</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-[#ff6b00] text-white rounded-md hover:bg-[#e05e00] focus:outline-none focus:ring-2 focus:ring-[#ff6b00] focus:ring-opacity-50">
                            Proceed to Payment
                        </button>
                    </form>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Transactions</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="transactionTableBody">
                                <!-- Transaction rows will be dynamically added here -->
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

    </script>
    <script>
document.getElementById('depositForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch('deposit.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Deposit successful!");
            updateBalance(parseFloat(formData.get('depositAmount')));
            addTransaction(formData.get('depositAmount'), 'Deposit');
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

// Update balance
function updateBalance(amount) {
    const balanceElement = document.getElementById('currentBalance');
    let currentBalance = parseFloat(balanceElement.textContent.replace(',', ''));
    currentBalance += amount;
    balanceElement.textContent = currentBalance.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Add transaction to the table
function addTransaction(amount, type) {
    const tableBody = document.getElementById('transactionTableBody');
    const newRow = tableBody.insertRow(0);
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date().toLocaleDateString()}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${type}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦${parseFloat(amount).toFixed(2)}</td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                Successful
            </span>
        </td>
    `;
}
</script>

</body>
</html>