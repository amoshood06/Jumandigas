<?php
// Start session and include authentication
require_once "../auth_check.php";
require '../db/db.php'; // Include database connection

// Check if the user is logged in and their role is 'user'
if ($_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// Initialize message variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($current_password, $user['password'])) {
                $error_message = "Current password is incorrect.";
            } else {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                
                $success_message = "Password updated successfully!";
            }
        } catch (PDOException $e) {
            $error_message = "Error updating password: " . $e->getMessage();
        }
    }
    
    // Store messages in session to display after redirect
    if (!empty($success_message)) {
        $_SESSION['success_message'] = $success_message;
    }
    if (!empty($error_message)) {
        $_SESSION['error_message'] = $error_message;
    }
    
    // Redirect back to profile page
    header("Location: user-profile.php");
    exit();
}
?>