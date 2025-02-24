<?php
require_once "../auth_check.php"; // Ensure user is authenticated
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

include '../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["status" => $status, "id" => $orderId]);

        echo json_encode(["success" => true, "message" => "Order updated successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error updating order: " . $e->getMessage()]);
    }
}
?>
