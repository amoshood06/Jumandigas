<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Database credentials
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password



// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Authorization token required"]);
    exit();
}

// Verify token and get rider ID
$sql = "SELECT id FROM users WHERE api_token = ? AND role = 'rider'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit();
}

$riderId = $user['id'];

// Get orders assigned to this rider
$sql = "SELECT o.id, o.tracking_id, o.total_price, o.amount_kg, o.status, 
               u.address, u.phone, u.full_name as customer_name, 
               v.full_name as vendor_name, v.phone as vendor_phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN users v ON o.vendor_id = v.id
        WHERE o.rider_id = ? AND o.status IN ('processing', 'moving')
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $riderId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Get rider's completed orders history
$sql = "SELECT o.id, o.tracking_id, o.total_price, o.amount_kg, o.status, o.created_at
        FROM orders o
        WHERE o.rider_id = ? AND o.status = 'delivered'
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $riderId);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode([
    "status" => "success",
    "orders" => $orders,
    "history" => $history
]);

$conn->close();
?>