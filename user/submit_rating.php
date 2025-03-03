<?php
require_once "../auth_check.php";
require '../db/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'] ?? '';

    // Get the vendor_id from the order
    $stmt = $pdo->prepare("SELECT vendor_id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo "<script>alert('Invalid order!'); window.history.back();</script>";
        exit();
    }

    $vendor_id = $order['vendor_id'];

    // Insert the rating
    $stmt = $pdo->prepare("INSERT INTO ratings (user_id, vendor_id, order_id, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $vendor_id, $order_id, $rating, $review]);

    echo "<script>alert('Thank you for your feedback!'); window.location='user-order-history.php';</script>";
}
?>
