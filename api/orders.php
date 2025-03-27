<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database credentials
// $host = "localhost"; // Change to your database host
// $database = "jumandigas"; // Change to your database name
// $user = "root"; // Change to your database username
// $password = ""; // Change to your database password
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
$auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$auth || strpos($auth, 'Bearer ') !== 0) {
    // No token provided
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Authentication token required."]);
    exit();
}

$token = substr($auth, 7); // Remove 'Bearer ' from the string

// Verify token and get user details
$sql = "SELECT id FROM users WHERE api_token = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit();
}

$user_id = $user['id'];

// Get orders for this user
$order_sql = "SELECT o.*, u.full_name as vendor_name 
              FROM orders o 
              LEFT JOIN users u ON o.vendor_id = u.id 
              WHERE o.user_id = ? 
              ORDER BY o.created_at DESC 
              LIMIT 10";

$order_stmt = $conn->prepare($order_sql);
if (!$order_stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

$orders = [];
while ($row = $order_result->fetch_assoc()) {
    $orders[] = [
        'id' => $row['id'],
        'tracking_id' => $row['tracking_id'],
        'cylinder_type' => $row['cylinder_type'],
        'amount_kg' => $row['amount_kg'],
        'total_price' => $row['total_price'],
        'currency' => $row['currency'],
        'status' => $row['status'] ?: 'pending',
        'vendor_name' => $row['vendor_name'],
        'created_at' => $row['created_at']
    ];
}

// Return orders data
echo json_encode([
    "status" => "success",
    "orders" => $orders
]);

// Close the database connection
$conn->close();
?>

