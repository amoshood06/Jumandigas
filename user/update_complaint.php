<?php
require '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE complaints SET status = :status WHERE id = :complaint_id");
        $stmt->execute([':status' => $status, ':complaint_id' => $complaint_id]);

        echo json_encode(["status" => "success", "message" => "Complaint status updated"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>
