<?php
require_once "../auth_check.php"; // Ensure user is authenticated
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db/db.php';

try {
    $sql = "SELECT o.id, u.full_name, u.address, u.telephone, o.items, o.total, o.status 
            FROM orders o 
            JOIN users u ON o.vendor_id = u.id";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll();

    echo json_encode($orders);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error fetching orders: " . $e->getMessage()]);
}
?>
