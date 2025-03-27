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

// Prepare SQL statement to fetch all orders for the vendor
$sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.vendor_id = ? 
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Orders retrieved successfully",
    "orders" => $orders
]);

// Close the database connection
$conn->close();
?>

