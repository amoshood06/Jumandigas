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
$sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address,
        r.id as rider_id, r.full_name as rider_name, r.phone as rider_phone
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        LEFT JOIN users r ON o.rider_id = r.id
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

// Check if order has a rider assigned and is in 'moving' status
if (!$order['rider_id'] || $order['status'] !== 'moving') {
    echo json_encode([
        "status" => "error", 
        "message" => "Order is not currently being delivered or no rider assigned"
    ]);
    exit();
}

// Get rider's last known location from riders table
$locationSql = "SELECT * FROM riders WHERE track_id = ? AND rider_id = ? ORDER BY created_at DESC LIMIT 1";
$locationStmt = $conn->prepare($locationSql);
$locationStmt->bind_param("si", $trackingId, $order['rider_id']);
$locationStmt->execute();
$locationResult = $locationStmt->get_result();

if ($locationResult->num_rows === 0) {
    echo json_encode([
        "status" => "error", 
        "message" => "Rider location not available"
    ]);
    exit();
}

$location = $locationResult->fetch_assoc();

// Prepare location response
$locationData = [
    "rider_id" => $order['rider_id'],
    "rider_name" => $order['rider_name'],
    "rider_phone" => $order['rider_phone'],
    "latitude" => $location['latitude'] ?? 0,
    "longitude" => $location['longitude'] ?? 0,
    "canceled_by_rider" => $location['canceled_by_rider'] == 1,
    "created_at" => $location['created_at']
];

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Order tracking information retrieved successfully",
    "location" => $locationData
]);

// Close the database connection
$conn->close();
?>

