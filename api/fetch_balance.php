<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Database connection
require '../db/db.php';

// Get token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access, token required"]);
    exit();
}

try {
    // Verify the token in the database
    $stmt = $pdo->prepare("SELECT id, balance, currency FROM users WHERE api_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "status" => "success",
            "balance" => number_format($user['balance'], 2),
            "currency" => $user['currency']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid token!"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
