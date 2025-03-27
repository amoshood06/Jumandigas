<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
// $host = "localhost"; // Change to your database host
// $database = "jumandigas"; // Change to your database name
// $user = "root"; // Change to your database username
// $password = ""; // Change to your database password
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password


// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get callback data from Flutterwave
$status = isset($_GET['status']) ? $_GET['status'] : '';
$tx_ref = isset($_GET['tx_ref']) ? $_GET['tx_ref'] : '';
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';

// Verify the transaction with Flutterwave
// In a real implementation, you would make an API call to Flutterwave to verify the transaction
// For this example, we'll simulate a successful verification

if ($status === 'successful' && $tx_ref) {
    // Update payment status in database
    $update_sql = "UPDATE payment_history SET status = 'successful' WHERE tx_ref = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $update_stmt->bind_param("s", $tx_ref);
    $update_stmt->execute();
    
    // Get payment details
    $payment_sql = "SELECT user_id, amount FROM payment_history WHERE tx_ref = ?";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("s", $tx_ref);
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();
    $payment = $payment_result->fetch_assoc();
    
    if ($payment) {
        $user_id = $payment['user_id'];
        $amount = $payment['amount'];
        
        // Update user balance
        $balance_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $balance_stmt = $conn->prepare($balance_sql);
        $balance_stmt->bind_param("di", $amount, $user_id);
        $balance_stmt->execute();
        
        // Return success message
        echo json_encode([
            "status" => "success",
            "message" => "Payment successful and balance updated",
            "tx_ref" => $tx_ref,
            "amount" => $amount
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Payment record not found"]);
    }
} else {
    // Update payment status to failed
    if ($tx_ref) {
        $update_sql = "UPDATE payment_history SET status = 'failed' WHERE tx_ref = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $tx_ref);
        $update_stmt->execute();
    }
    
    echo json_encode(["status" => "error", "message" => "Payment failed or cancelled"]);
}

// Close the database connection
$conn->close();
?>

