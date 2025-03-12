<?php
session_start();
require '../db/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user's balance and currency
    $stmt = $pdo->prepare("SELECT balance, currency FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "status" => "success",
            "balance" => number_format($user['balance'], 2),
            "currency" => $user['currency']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
