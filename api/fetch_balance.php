<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Database connection
require '../db/db.php';

// Get token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';

if (!$token) {
    echo json_encode([
        "status" => "error", 
        "message" => "Unauthorized access, token required"
    ]);
    exit();
}

try {
    // Get user ID from token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE api_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid token"
        ]);
        exit();
    }

    $user_id = $user['id'];

    // Get user balance and currency
    $stmt = $pdo->prepare("SELECT balance, currency FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode([
            "status" => "error",
            "message" => "User data not found"
        ]);
        exit();
    }

    // Format balance to 2 decimal places
    $balance = number_format((float)$userData['balance'], 2, '.', '');

    echo json_encode([
        "status" => "success",
        "balance" => $balance,
        "currency" => $userData['currency'] ?? 'NGN'
    ]);
} catch (PDOException $e) {
    // Log error to file instead of displaying it
    error_log("Database error in get_balance.php: " . $e->getMessage());
    
    echo json_encode([
        "status" => "error",
        "message" => "Database error occurred"
    ]);
}
?>

