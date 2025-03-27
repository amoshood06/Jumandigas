<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
include_once 'db_connect.php';

// Get authorization header
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

// Extract token
$token = '';
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $token = $matches[1];
}

// Check if token is provided
if (!$token) {
    echo json_encode(["status" => "error", "message" => "Authorization token is required"]);
    exit();
}

// Prepare SQL statement to invalidate token
$sql = "UPDATE users SET api_token = NULL WHERE api_token = ? AND role = 'vendor'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("s", $token);
$stmt->execute();

// Check if any row was affected
if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Logout successful"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid token or already logged out"]);
}

// Close the database connection
$conn->close();
?>

