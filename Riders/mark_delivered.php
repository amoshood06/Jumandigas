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

    // Update order status to "completed"
    $sql = "UPDATE orders SET status = 'completed' WHERE id = :order_id AND rider_id = :rider_id";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute(['order_id' => $order_id, 'rider_id' => $rider_id])) {
        echo "<script>alert('Order has been successfully completed!'); window.location.href='rider_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status. Try again.'); window.location.href='rider_dashboard.php';</script>";
    }
    exit();
}
?>
