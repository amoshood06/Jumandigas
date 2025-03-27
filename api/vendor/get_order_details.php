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

// Get tracking ID from query parameters
$trackingId = isset($_GET['tracking_id']) ? trim($_GET['tracking_id']) : null;

if (!$trackingId) {
    echo json_encode(["status" => "error", "message" => "Tracking ID is required"]);
    exit();
}

// Prepare SQL statement to fetch order details
$sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.tracking_id = ? AND o.vendor_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("si", $trackingId, $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Order not found"]);
    exit();
}

$order = $result->fetch_assoc();

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Order details retrieved successfully",
    "order" => $order
]);

// Close the database connection
$conn->close();
?>

