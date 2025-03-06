<?php
session_start();
require '../db/db.php'; // Include database connection

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
$public_key = "FLWPUBK_TEST-451af96faf638c6cd745dbe69aac4ecc-X";
$secret_key = "FLWSECK_TEST-a22f5960b5fa2b75adaf0b1e41057101-X";
$encryption_key = "FLWSECK_TEST41f93707d268";

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
    <title>Payment</title>
</head>
<body>
    <h2>Make a Payment</h2>
    <form method="POST">
        <input type="number" name="amount" placeholder="Amount" required><br>
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
