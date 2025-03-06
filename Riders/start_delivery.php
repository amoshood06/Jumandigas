<?php 
session_start();
require_once "../db/db.php";

// Check if user is a rider
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'rider') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Validate request data
if (!isset($_POST['order_id'], $_POST['track_id'], $_POST['latitude'], $_POST['longitude'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$order_id = $_POST['order_id'];
$track_id = $_POST['track_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$rider_id = $_SESSION['user_id']; // Assuming `user_id` is the rider's ID

try {
    $pdo->beginTransaction(); // Start transaction

    // Fetch vendor_id and user_id from orders table
    $stmt = $pdo->prepare("SELECT vendor_id, user_id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["status" => "error", "message" => "Order not found"]);
        exit();
    }

    $vendor_id = $order['vendor_id'];
    $user_id = $order['user_id'];

    // Update order status to 'moving'
    $stmt1 = $pdo->prepare("UPDATE orders SET status = 'moving' WHERE id = ?");
    $stmt1->execute([$order_id]);

    // Insert rider tracking info into `riders` table
    $stmt2 = $pdo->prepare("INSERT INTO riders (order_id, vendor_id, user_id, rider_id, track_id, latitude, longitude) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt2->execute([$order_id, $vendor_id, $user_id, $rider_id, $track_id, $latitude, $longitude]);

    $pdo->commit(); // Commit transaction

    echo json_encode(["status" => "success", "message" => "Delivery started"]);
} catch (PDOException $e) {
    $pdo->rollBack(); // Rollback on error
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
