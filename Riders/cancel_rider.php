<?php
require 'db.php';

if (!isset($_POST['rider_id'], $_POST['order_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$rider_id = $_POST['rider_id'];
$order_id = $_POST['order_id'];

// Get user location from order table
$stmt = $pdo->prepare("SELECT o.user_id, u.country, u.state, u.city 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(["status" => "error", "message" => "Order not found"]);
    exit;
}

// Mark the current rider as canceled
$stmt = $pdo->prepare("UPDATE riders SET canceled_by_rider = 1 WHERE rider_id = ?");
$stmt->execute([$rider_id]);

// Find a new rider from the same location
$stmt = $pdo->prepare("SELECT id FROM riders WHERE country = ? AND state = ? AND city = ? AND canceled_by_rider = 0 ORDER BY RAND() LIMIT 1");
$stmt->execute([$order['country'], $order['state'], $order['city']]);
$new_rider = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$new_rider) {
    echo json_encode(["status" => "error", "message" => "No available riders in your location"]);
    exit;
}

// Assign the new rider to the order
$stmt = $pdo->prepare("UPDATE orders SET rider_id = ? WHERE id = ?");
if ($stmt->execute([$new_rider['id'], $order_id])) {
    echo json_encode(["status" => "success", "message" => "Rider reassigned successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reassign rider"]);
}
?>
