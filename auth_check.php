<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);

$allowed_pages = [
    "user" => ["index.php", "order-page.php", "user-profile.php", "item-tracking.php"],
    "vendor" => ["index.php", "withdrawal.php", "withdraw.php", "text.php", "vendor-report.php", "vendor-order-management.php", "test.php", "accept_order.php", "assign_rider.php", "reject_order.php"],
    "rider" => ["index.php", "mark_delivered.php", "rider-delivery-history.php", "accept_delivery.php", "mark_delivered.php", "rider_dashboard.php"]
];

if (!in_array($current_page, $allowed_pages[$role])) {
    echo "Access Denied!";
    exit();
}
?>
