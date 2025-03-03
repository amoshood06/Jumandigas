<?php
session_start();
require_once "../db/db.php";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // **Update online status to 0 (Offline)**
    $updateStmt = $pdo->prepare("UPDATE users SET online = 0 WHERE id = ?");
    $updateStmt->execute([$user_id]);

    // Destroy session
    session_unset();
    session_destroy();
}

// If it's an AJAX request, return JSON response
if (isset($_POST['ajax']) && $_POST['ajax'] == true) {
    echo json_encode(["status" => "success", "message" => "User logged out due to inactivity."]);
    exit();
}

// Redirect to login page (for normal logout)
header("Location: ../login.php");
exit();
?>
