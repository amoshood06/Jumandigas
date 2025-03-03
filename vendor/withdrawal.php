<?php
require_once "../auth_check.php"; // Ensure user is authenticated
require_once "../db/db.php"; // Include the database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}

// Fetch recent withdrawals for the logged-in user
$vendor_id = $_SESSION['user_id'] ?? null;
$withdrawals = [];

if ($vendor_id) {
    $stmt = $pdo->prepare("SELECT amount, status, created_at FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$vendor_id]);
    $withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Withdrawal - JumandiGas</title>
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
                    <a href="index.php" class="text-black hover:text-primary">Dashboard</a>
                    <a href="vendor-order-management.php" class="text-black hover:text-primary">Orders</a>
                    <a href="withdrawal.php" class="text-black hover:text-primary font-semibold">Withdrawals</a>
                    <a href="vendor-settings.php" class="text-black hover:text-primary">Settings</a>
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
                <a href="index.php" class="text-black hover:text-primary">Dashboard</a>
                <a href="vendor-order-management.php" class="text-black hover:text-primary">Orders</a>
                <a href="withdrawal.php" class="text-black hover:text-primary font-semibold">Withdrawals</a>
                <a href="vendor-settings.php" class="text-black hover:text-primary">Settings</a>
                <a href="logout.php" class="bg-primary text-white px-6 py-2 rounded-full text-center hover:bg-orange-700">Logout</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12">
        <!-- Withdrawal Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Vendor Withdrawal</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Manage your earnings and request withdrawals securely through our platform.
            </p>
        </div>

        <!-- Balance and Withdrawal Form -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Balance Card -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Your Balance</h2>
                <div class="text-4xl font-bold text-primary mb-4">
                <span id="currencySymbol"></span> 
            <span id="currentBalance">0.00</span>
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

                <p class="text-gray-600 mb-4">Last updated: <span id="lastUpdated">--</span></p>
                    <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-300 transition duration-300" onclick="updateBalance()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh Balance
                    </button>



                <script>
                    function updateBalance() {
                        fetch('fetch_balance.php')
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === "success") {
                                    document.getElementById("balanceAmount").textContent = `${data.currency} ${data.balance}`;
                                    document.getElementById("fullName").textContent = data.full_name;
                                    document.getElementById("lastUpdated").textContent = new Date().toLocaleString();
                                } else {
                                    alert("Error: " + data.message);
                                }
                            })
                            .catch(error => console.error('Error fetching balance:', error));
                    }
                </script>

            </div>



<div class="bg-white p-6 rounded-2xl shadow-lg">
    <h2 class="text-2xl font-semibold mb-4">Request Withdrawal</h2>
    <form id="withdrawalForm">
        <div class="mb-4">
            <label for="amount" class="block text-gray-700 mb-2" >Amount</label>
            <input type="number" id="amount" name="amount" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
        </div>
        <div class="mb-4">
            <label for="bank" class="block text-gray-700 mb-2">Bank</label>
            <select id="bank" name="bank" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
                <option value="">Select your bank</option>
                <option value="access">Access Bank</option>
                <option value="gtb">Guaranty Trust Bank</option>
                <option value="zenith">Zenith Bank</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="accountNumber" class="block text-gray-700 mb-2">Account Number</label>
            <input type="text" id="accountNumber" name="accountNumber" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
        </div>
        <button type="submit" class="w-full bg-primary text-white py-2 rounded-full hover:bg-orange-700 transition duration-300">
            Request Withdrawal
        </button>
    </form>
</div>

<!-- Recent Withdrawals -->
<div class="mt-12 w-full">
    <h2 class="text-2xl font-semibold mb-4">Recent Withdrawals</h2>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($withdrawals)): ?>
                    <?php foreach ($withdrawals as $withdrawal): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date("Y-m-d", strtotime($withdrawal['created_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">₦<?= number_format($withdrawal['amount'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?= $withdrawal['status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($withdrawal['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No recent withdrawals</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById("withdrawalForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("withdraw.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === "success") {
            location.reload();
        }
    })
    .catch(error => console.error("Error:", error));
});
</script>

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

        // Update balance functionality (mock)
        function updateBalance() {
            
        }

        // Form submission (mock)
        document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const amount = document.getElementById('amount').value;
            const bank = document.getElementById('bank').value;
            const accountNumber = document.getElementById('accountNumber').value;

            if (amount && bank && accountNumber) {
                alert(`Withdrawal request submitted:\nAmount: ₦${amount}\nBank: ${bank}\nAccount Number: ${accountNumber}`);
                this.reset();
            } else {
                alert('Please fill in all fields.');
            }
        });
    </script>
</body>
</html>