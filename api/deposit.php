<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

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

// Create payment_history table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS payment_history (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    tx_ref VARCHAR(50) NOT NULL,
    status ENUM('successful', 'failed', 'pending') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
)");

// Get authorization header
$headers = getallheaders();
$auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$auth || strpos($auth, 'Bearer ') !== 0) {
    // No token provided
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Authentication token required."]);
    exit();
}

$token = substr($auth, 7); // Remove 'Bearer ' from the string

// Verify token and get user details
$sql = "SELECT * FROM users WHERE api_token = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit();
}

$user_id = $user['id'];

// If it's a GET request, return the user's payment history
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $history_sql = "SELECT * FROM payment_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
    $history_stmt = $conn->prepare($history_sql);
    if (!$history_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $history_stmt->bind_param("i", $user_id);
    $history_stmt->execute();
    $history_result = $history_stmt->get_result();
    
    $history = [];
    while ($row = $history_result->fetch_assoc()) {
        $history[] = [
            'id' => $row['id'],
            'amount' => $row['amount'],
            'currency' => $row['currency'],
            'tx_ref' => $row['tx_ref'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        ];
    }
    
    // Return user data and payment history
    echo json_encode([
        "status" => "success",
        "user" => [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "email" => $user['email'],
            "balance" => $user['balance'],
            "currency" => $user['currency'],
            "country" => $user['country']
        ],
        "payment_history" => $history
    ]);
    
    exit();
}

// Handle deposit request (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data['amount']) || $data['amount'] <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid amount"]);
        exit();
    }
    
    $amount = $data['amount'];
    $currency = $user['currency'] ?? 'NGN';
    
    // Generate transaction reference
    $tx_ref = "TX_" . time() . "_" . $user_id;
    
    // Flutterwave credentials
    $public_key = "FLWPUBK-35614b38c377f9f0c86ce78c4ee9c6e0-X";
    $secret_key = "FLWSECK-f361939897b2bd2eed221ca7a38542f3-1956596e20cvt-X";
    
    // Create payment data
    $payment_data = [
        "tx_ref" => $tx_ref,
        "amount" => $amount,
        "currency" => $currency,
        "redirect_url" => "http://locahost/jumandi/api/callback.php", // Replace with your callback URL
        "customer" => [
            "email" => $user['email'],
            "name" => $user['full_name']
        ],
        "customizations" => [
            "title" => "JumandiGas Payment",
            "description" => "Wallet Funding"
        ]
    ];
    
    // For testing purposes, we'll simulate a successful payment
    // In production, you would make an actual API call to Flutterwave
    
    // Insert payment record with pending status
    $insert_sql = "INSERT INTO payment_history (user_id, amount, currency, tx_ref, status) VALUES (?, ?, ?, ?, 'pending')";
    $insert_stmt = $conn->prepare($insert_sql);
    if (!$insert_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $insert_stmt->bind_param("idss", $user_id, $amount, $currency, $tx_ref);
    $insert_stmt->execute();
    
    // Return payment data that would be used to redirect to Flutterwave
    echo json_encode([
        "status" => "success",
        "message" => "Payment initiated",
        "payment_data" => [
            "tx_ref" => $tx_ref,
            "amount" => $amount,
            "currency" => $currency,
            "public_key" => $public_key,
            // In a real implementation, you would return a link to Flutterwave's payment page
            "payment_link" => "https://checkout.flutterwave.com/v3/hosted/pay"
        ]
    ]);
}

// Handle payment verification (for testing/simulation)
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data['tx_ref']) || !isset($data['status'])) {
        echo json_encode(["status" => "error", "message" => "Invalid verification data"]);
        exit();
    }
    
    $tx_ref = $data['tx_ref'];
    $status = $data['status'] === 'successful' ? 'successful' : 'failed';
    
    // Update payment status
    $update_sql = "UPDATE payment_history SET status = ? WHERE tx_ref = ? AND user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $update_stmt->bind_param("ssi", $status, $tx_ref, $user_id);
    $update_stmt->execute();
    
    // If payment was successful, update user balance
    if ($status === 'successful') {
        // Get the amount from payment history
        $amount_sql = "SELECT amount FROM payment_history WHERE tx_ref = ? AND user_id = ?";
        $amount_stmt = $conn->prepare($amount_sql);
        $amount_stmt->bind_param("si", $tx_ref, $user_id);
        $amount_stmt->execute();
        $amount_result = $amount_stmt->get_result();
        $amount_row = $amount_result->fetch_assoc();
        
        if ($amount_row) {
            $amount = $amount_row['amount'];
            
            // Update user balance
            $balance_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $balance_stmt = $conn->prepare($balance_sql);
            $balance_stmt->bind_param("di", $amount, $user_id);
            $balance_stmt->execute();
            
            // Get updated user data
            $user_sql = "SELECT balance, currency FROM users WHERE id = ?";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $user_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $updated_user = $user_result->fetch_assoc();
            
            echo json_encode([
                "status" => "success",
                "message" => "Payment verified and balance updated",
                "new_balance" => $updated_user['balance'],
                "currency" => $updated_user['currency']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Payment record not found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Payment verification failed"]);
    }
}

// Close the database connection
$conn->close();
?>

