<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

// Include database connection and authentication
include_once 'db_connect.php';
include_once 'authenticate.php';

// Authenticate user
$user = authenticate($conn);
if (!$user) {
    exit();
}

// Prepare SQL statement to fetch withdrawal history
$sql = "SELECT id, amount, bank, account_number, status, created_at 
        FROM withdrawals 
        WHERE user_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

$withdrawals = [];
while ($row = $result->fetch_assoc()) {
    $withdrawals[] = $row;
}

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Withdrawal history retrieved successfully",
    "withdrawals" => $withdrawals
]);

// Close the database connection
$conn->close();
?>

