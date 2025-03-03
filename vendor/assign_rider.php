<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['rider_id'])) {
    $order_id = $_POST['order_id'];
    $rider_id = $_POST['rider_id'];
    $vendor_id = $_SESSION['user_id'];

    // Assign the rider to the order
    $sql = "UPDATE orders SET rider_id = :rider_id, status = 'processing' WHERE id = :order_id AND vendor_id = :vendor_id";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['order_id' => $order_id, 'rider_id' => $rider_id, 'vendor_id' => $vendor_id])) {
        echo "<script>alert('Rider assigned successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Failed to assign rider. Try again.'); window.location.href='index.php';</script>";
    }
    exit();
}
?>
