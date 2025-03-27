<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection and authentication
include_once 'db_connect.php';
include_once 'authenticate.php';

// Authenticate user
$user = authenticate($conn);
if (!$user) {
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$amount = isset($data['amount']) ? floatval($data['amount']) : null;
$bank = isset($data['bank']) ? trim($data['bank']) : null;
$accountNumber = isset($data['account_number']) ? trim($data['account_number']) : null;

// Check if all required fields are provided
if (!$amount || !$bank || !$accountNumber) {
    echo json_encode(["status" => "error", "message" => "Amount, bank, and account number are required"]);
    exit();
}

// Check if amount is valid
if ($amount <= 0) {
    echo json_encode(["status" => "error", "message" => "Amount must be greater than 0"]);
    exit();
}

// Check if user has sufficient balance
if ($amount > $user['balance']) {
    echo json_encode(["status" => "error", "message" => "Insufficient balance"]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Create withdrawal record
    $withdrawalSql = "INSERT INTO withdrawals (user_id, amount, bank, account_number, status) 
                      VALUES (?, ?, ?, ?, 'pending')";
    $withdrawalStmt = $conn->prepare($withdrawalSql);
    if (!$withdrawalStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $withdrawalStmt->bind_param("idss", $user['id'], $amount, $bank, $accountNumber);
    $withdrawalStmt->execute();
    
    if ($withdrawalStmt->affected_rows === 0) {
        throw new Exception("Failed to create withdrawal request");
    }
    
    // Update user balance
    $updateBalanceSql = "UPDATE users SET balance = balance - ? WHERE id = ?";
    $updateBalanceStmt = $conn->prepare($updateBalanceSql);
    if (!$updateBalanceStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $updateBalanceStmt->bind_param("di", $amount, $user['id']);
    $updateBalanceStmt->execute();
    
    if ($updateBalanceStmt->affected_rows === 0) {
        throw new Exception("Failed to update balance");
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Withdrawal request created successfully"
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>

