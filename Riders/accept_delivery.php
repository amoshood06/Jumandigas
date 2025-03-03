<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'rider') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $rider_id = $_SESSION['user_id'];

    // Update order status to "out for delivery"
    $sql = "UPDATE orders SET status = 'out for delivery' WHERE id = :order_id AND rider_id = :rider_id";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['order_id' => $order_id, 'rider_id' => $rider_id])) {
        echo "<script>alert('You have accepted the delivery!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Failed to accept delivery. Try again.'); window.location.href='index.php';</script>";
    }
    exit();
}
?>
