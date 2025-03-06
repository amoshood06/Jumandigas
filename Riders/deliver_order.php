<?php
session_start();
require_once "../db/db.php";

// Check if the user is logged in and is a rider
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'rider') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Check if the order ID is provided
if (!isset($_POST['order_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$order_id = $_POST['order_id'];

try {
    // Update the order status to "delivered"
    $stmt = $pdo->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?");
    if ($stmt->execute([$order_id])) {
        echo json_encode(["status" => "success", "message" => "Order marked as delivered"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update order status"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
