<?php
session_start();
require '../db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Create payment_history table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS payment_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    tx_ref VARCHAR(50) NOT NULL,
    status ENUM('successful', 'failed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
)");

// Get user ID from session (ensure user is logged in)
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not logged in");
}

// Fetch user details
$stmt = $pdo->prepare("SELECT full_name, email, country FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

// Set currency based on country
$currency = ($user['country'] == 'Nigeria') ? 'NGN' : (($user['country'] == 'Ghana') ? 'GHS' : 'USD');

// Flutterwave credentials
$public_key = "FLWPUBK-35614b38c377f9f0c86ce78c4ee9c6e0-X";
$secret_key = "FLWSECK-f361939897b2bd2eed221ca7a38542f3-1956596e20cvt-X";
$encryption_key = "f361939897b2b928ce0c84b1";

// Process payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    
    // Generate transaction reference
    $tx_ref = "TX_" . time();
    
    $payment_data = [
        "tx_ref" => $tx_ref,
        "amount" => $amount,
        "currency" => $currency,
        "redirect_url" => "http://localhost/jumandi/user/callback.php",
        "customer" => [
            "email" => $user['email'],
            "name" => $user['full_name']
        ],
        "customizations" => [
            "title" => "JumandiGas Payment",
            "description" => "Wallet Funding"
        ]
    ];
    
    // API call to Flutterwave
    $ch = curl_init("https://api.flutterwave.com/v3/payments");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $secret_key",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $response_data = json_decode($response, true);
    
    // Insert payment history into database
    $status = $response_data['status'] == 'success' ? 'successful' : 'failed';
    $stmt = $pdo->prepare("INSERT INTO payment_history (user_id, amount, currency, tx_ref, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $amount, $currency, $tx_ref, $status]);
    
    if ($status == 'successful') {
        header("Location: " . $response_data['data']['link']);
        exit;
    } else {
        echo "Payment failed. Please try again.";
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
            <p class="text-2xl font-bold"><?= htmlspecialchars($user['full_name']) ?></p>
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
                        
                        
                        <nav class="space-y-2 px-4">
    <a href="index.php" class="block p-3 bg-[#ff6b00] text-white rounded-lg">Home</a>
    <a href="user-deposit.php" class="block p-3 hover:bg-orange-100 rounded-lg">Deposit</a>
    <a href="item-tracking.php" class="block p-3 hover:bg-orange-100 rounded-lg">Track</a>
    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Buy Cylinder</a>
    
    <!-- Order Gas Dropdown -->
    <div class="relative group">
        <button class="block w-full text-left p-3 hover:bg-orange-100 rounded-lg">Order Gas</button>
        <div class="absolute hidden group-hover:block bg-white shadow-md rounded-lg mt-1 w-48">
            <a href="order-page.php" class="block p-3 hover:bg-orange-100">New Order</a>
            <a href="order-history.php" class="block p-3 hover:bg-orange-100">Order History</a>
        </div>
    </div>

    <a href="withdrawal.php" class="block p-3 hover:bg-orange-100 rounded-lg">Withdrawal</a>
    <a href="user-complaint.php" class="block p-3 hover:bg-orange-100 rounded-lg">Complain</a>
    <a href="#" class="block p-3 hover:bg-orange-100 rounded-lg">Setting</a>
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
                    <form method="POST">
                        <div class="mb-4">
                            <label for="depositAmount" class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount (₦)</label>
                            <input type="number" id="depositAmount" name="amount" min="100" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-[#ff6b00] focus:border-[#ff6b00]" required>
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
                            <tbody class="bg-white divide-y divide-gray-200">
    <?php
    require '../db/db.php'; // Include database connection

    $user_id = $_SESSION['user_id']; // Ensure user is logged in

    $limit = 5; // Number of transactions per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Count total transactions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM payment_history WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_transactions = $stmt->fetchColumn();
    $total_pages = ceil($total_transactions / $limit);

    // Fetch paginated transactions
    $stmt = $pdo->prepare("SELECT * FROM payment_history WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transactions) {
        foreach ($transactions as $transaction) {
            echo "<tr>
                    <td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($transaction['created_at']) . "</td>
                    <td class='px-6 py-4 whitespace-nowrap'>Deposit</td>
                    <td class='px-6 py-4 whitespace-nowrap'>₦" . htmlspecialchars(number_format($transaction['amount'], 2)) . "</td>
                    <td class='px-6 py-4 whitespace-nowrap'>
                        <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full " . 
                        ($transaction['status'] == 'successful' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . "'>
                        " . htmlspecialchars($transaction['status']) . "
                        </span>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='px-6 py-4 text-center text-gray-500'>No transactions found</td></tr>";
    }
    ?>
</tbody>

<!-- Pagination Controls -->
<div class="mt-4 flex justify-between">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Previous</a>
    <?php else: ?>
        <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">Previous</span>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">Next</a>
    <?php else: ?>
        <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed">Next</span>
    <?php endif; ?>
</div>



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