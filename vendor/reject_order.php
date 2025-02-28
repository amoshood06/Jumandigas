<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $vendor_id = $_SESSION['user_id'];

    // Verify the order belongs to the vendor
    $check_sql = "SELECT id FROM orders WHERE id = :order_id AND vendor_id = :vendor_id";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([
        'order_id' => $order_id,
        'vendor_id' => $vendor_id
    ]);

    if ($check_stmt->rowCount() > 0) {
        // Update the order status to 'rejected'
        $update_sql = "UPDATE orders SET status = 'rejected' WHERE id = :order_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute(['order_id' => $order_id]);

        // Redirect back with a success message
        header("Location: test.php?message=Order Rejected Successfully");
        exit();
    } else {
        // Redirect back with an error message
        header("Location: vendor_orders.php?error=Order not found or unauthorized action");
        exit();
    }
} else {
    header("Location: vendor_orders.php");
    exit();
}
