<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost";
$database = "jumandigas";
$user = "root";
$password = "";

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Authorization token required"]);
    exit();
}

// Verify token and get rider ID
$sql = "SELECT id, balance FROM users WHERE api_token = ? AND role = 'rider'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;
$bank = isset($data['bank']) ? trim($data['bank']) : null;
$accountNumber = isset($data['account_number']) ? trim($data['account_number']) : null;

if (!$amount || !$bank || !$accountNumber) {
    echo json_encode(["status" => "error", "message" => "Amount, bank, and account number are required"]);
    exit();
}

// Check if rider has sufficient balance
if ($user['balance'] < $amount) {
    echo json_encode(["status" => "error", "message" => "Insufficient balance"]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Deduct amount from rider's balance
    $sql = "UPDATE users SET balance = balance - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $amount, $user['id']);
    $stmt->execute();
    
    // Create withdrawal record
    $sql = "INSERT INTO withdrawals (user_id, amount, bank, account_number) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idss", $user['id'], $amount, $bank, $accountNumber);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Withdrawal request submitted successfully",
        "new_balance" => $user['balance'] - $amount
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>