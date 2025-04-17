<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$trackingId = isset($data['tracking_id']) ? trim($data['tracking_id']) : null;

if (!$trackingId) {
    echo json_encode(["status" => "error", "message" => "Tracking ID is required"]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Update order status to 'moving'
    $sql = "UPDATE orders SET status = 'moving', rider_id = ?, assigned_at = NOW() WHERE tracking_id = ? AND status = 'processing'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $riderId, $trackingId);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        // Order not found or already assigned
        throw new Exception("Order not found or already assigned to another rider");
    }
    
    // Get order details for rider entry
    $sql = "SELECT o.id, o.user_id, o.vendor_id FROM orders o WHERE o.tracking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trackingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if (!$order) {
        throw new Exception("Order details not found");
    }
    
    // Get rider name
    $sql = "SELECT full_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $riderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $riderData = $result->fetch_assoc();
    
    // Insert into riders table
    $sql = "INSERT INTO riders (full_name, vendor_id, order_id, user_id, rider_id, track_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiss", $riderData['full_name'], $order['vendor_id'], $order['id'], $order['user_id'], $riderId, $trackingId);
    $stmt->execute();
    
    // Add to order status history
    $sql = "INSERT INTO order_status_history (order_id, status, notes) VALUES (?, 'moving', 'Order accepted by rider')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Order accepted successfully"
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>