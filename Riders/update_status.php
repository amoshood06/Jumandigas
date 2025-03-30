<?php
// Start the session (if not already started)
session_start();

// Check if user is logged in and is a rider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'rider') {
    // Redirect to login page if not logged in or not a rider
    header("Location: login.php");
    exit();
}

// Include database connection
require_once '../db/db.php';

// Get rider ID from session
$rider_id = $_SESSION['user_id'];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'] ? 1 : 0;
    
    try {
        // Update rider's online status
        $stmt = $pdo->prepare("UPDATE users SET online = ? WHERE id = ? AND role = 'rider'");
        $stmt->execute([$status, $rider_id]);
        
        // Redirect back to profile page
        header("Location: rider_profile.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating status: " . $e->getMessage());
    }
} else {
    // Redirect back to profile page if form wasn't submitted
    header("Location: rider_profile.php");
    exit();
}
?>