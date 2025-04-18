<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
    // Get order details
    $sql = "SELECT o.id, o.total_price, o.vendor_id, u.country, u.state, u.city 
            FROM orders o 
            JOIN users u ON o.user_id = u.id
            WHERE o.tracking_id = ? AND o.rider_id = ? AND o.status = 'moving'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $trackingId, $riderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if (!$order) {
        throw new Exception("Order not found or not in 'moving' status");
    }
    
    // Get bike price for this location
    $sql = "SELECT price FROM bike WHERE country = ? AND state = ? AND city = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $order['country'], $order['state'], $order['city']);
    $stmt->execute();
    $result = $stmt->get_result();
    $bikeData = $result->fetch_assoc();
    
    if (!$bikeData) {
        // Default bike price if not found
        $bikePrice = 600.00;
    } else {
        $bikePrice = $bikeData['price'];
    }
    
    // Update order status to 'delivered'
    $sql = "UPDATE orders SET status = 'delivered' WHERE tracking_id = ? AND rider_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $trackingId, $riderId);
    $stmt->execute();
    
    // Credit rider with bike price
    $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $bikePrice, $riderId);
    $stmt->execute();
    
    // Credit vendor with order price minus bike price
    $vendorAmount = $order['total_price'] - $bikePrice;
    $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $vendorAmount, $order['vendor_id']);
    $stmt->execute();
    
    // Add to order status history
    $sql = "INSERT INTO order_status_history (order_id, status, notes) 
            VALUES (?, 'delivered', 'Order completed by rider')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Order completed successfully",
        "rider_payment" => $bikePrice,
        "vendor_payment" => $vendorAmount
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