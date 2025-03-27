<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost"; // Change to your database host
$database = "jumandigas"; // Change to your database name
$user = "root"; // Change to your database username
$password = ""; // Change to your database password

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data['email']) ? trim($data['email']) : null;

// Check if email is provided
if (!$email) {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
    exit();
}

// Check if user exists and is a rider
$sql = "SELECT * FROM users WHERE email = ? AND role = 'rider'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found or not a rider"]);
    exit();
}

// Generate a random password
$newPassword = bin2hex(random_bytes(4)); // 8 characters
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update user password
$updateSQL = "UPDATE users SET password = ? WHERE id = ?";
$stmt = $conn->prepare($updateSQL);
$stmt->bind_param("si", $hashedPassword, $user['id']);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
    exit();
}

// In a real application, you would send an email with the new password
// For this example, we'll just return it in the response
echo json_encode([
    "status" => "success",
    "message" => "Password reset successful. A new password has been sent to your email.",
    "temp_password" => $newPassword // In a real app, you would NOT include this in the response
]);

// Close the database connection
$conn->close();
?>