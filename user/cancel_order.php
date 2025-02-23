<?php
session_start();
require '../db/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status='cancelled' WHERE id=? AND user_id=? AND status='pending'");
    if ($stmt->execute([$order_id, $user_id])) {
        echo "Order cancelled successfully!";
    } else {
        echo "Failed to cancel order.";
    }
} else {
    echo "Invalid request.";
}
?>
