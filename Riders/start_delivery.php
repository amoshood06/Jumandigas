<?php
require 'db.php';

if (!isset($_POST['order_id'], $_POST['latitude'], $_POST['longitude'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$order_id = $_POST['order_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$stmt = $pdo->prepare("UPDATE orders SET status = 'En Route' WHERE id = ?");
if ($stmt->execute([$order_id])) {
    echo json_encode(["status" => "success", "message" => "Delivery started"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to start delivery"]);
}
?>
