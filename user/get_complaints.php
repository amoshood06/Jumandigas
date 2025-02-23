<?php
session_start();
require '../db/db.php';

$user_id = $_SESSION['user_id'] ?? 1; // Replace with actual user session

try {
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($complaints);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
}
?>
