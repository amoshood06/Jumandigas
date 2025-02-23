<?php
session_start();
require '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 1; // Replace with actual user session
    $complaint_type = $_POST['complaintType'];
    $description = $_POST['complaintDescription'];
    $order_number = !empty($_POST['orderNumber']) ? $_POST['orderNumber'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO complaints (user_id, complaint_type, description, order_number) VALUES (:user_id, :complaint_type, :description, :order_number)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':complaint_type' => $complaint_type,
            ':description' => $description,
            ':order_number' => $order_number
        ]);

        echo json_encode(["status" => "success", "message" => "Complaint submitted successfully"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>
