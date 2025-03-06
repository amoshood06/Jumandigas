<?php
session_start();
require '../db/db.php'; // Include database connection

// Check if request is from Flutterwave (JSON) or manual GET request
$payload = file_get_contents('php://input');
$response = json_decode($payload, true);

if (!$response || !isset($response['data'])) {
    // Fallback to GET parameters for testing
    if (isset($_GET['tx_ref']) && isset($_GET['status'])) {
        $tx_ref = $_GET['tx_ref'];
        $status = $_GET['status'];
    } else {
        die("Invalid response");
    }
} else {
    $tx_ref = $response['data']['tx_ref'];
    $status = $response['data']['status'];
    $amount = $response['data']['amount'];
    $currency = $response['data']['currency'];
}

// Fetch user ID from payment history
$stmt = $pdo->prepare("SELECT user_id FROM payment_history WHERE tx_ref = ?");
$stmt->execute([$tx_ref]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found for this transaction");
}

$user_id = $user['user_id'];

// Update payment history status
$stmt = $pdo->prepare("UPDATE payment_history SET status = ? WHERE tx_ref = ?");
$stmt->execute([$status, $tx_ref]);

if ($status == 'successful') {
    // Update user balance
    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);
    
    // Redirect to payment page after success
    header("Location: user-deposit.php?status=success");
    exit();
} else {
    // Redirect to payment page with failure message
    header("Location: user-deposit.php?status=failed");
    exit();
}

http_response_code(200);
echo "Callback received successfully";
?>
