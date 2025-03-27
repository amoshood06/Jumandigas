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

// Include database connection and authentication
include_once 'db_connect.php';
include_once 'authenticate.php';

// Authenticate user
$user = authenticate($conn);
if (!$user) {
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$orderId = isset($data['order_id']) ? intval($data['order_id']) : null;
$riderId = isset($data['rider_id']) ? intval($data['rider_id']) : null;

// Check if order_id and rider_id are provided
if (!$orderId || !$riderId) {
    echo json_encode(["status" => "error", "message" => "Order ID and Rider ID are required"]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Check if rider exists and is available
    $riderSql = "SELECT id, city, state, country, full_name FROM users WHERE id = ? AND role = 'rider' AND online = 1";
    $riderStmt = $conn->prepare($riderSql);
    $riderStmt->bind_param("i", $riderId);
    $riderStmt->execute();
    $riderResult = $riderStmt->get_result();
    
    if ($riderResult->num_rows === 0) {
        throw new Exception("Rider not found or not available");
    }
    
    $rider = $riderResult->fetch_assoc();
    
    // Check if order exists and belongs to the vendor
    $orderSql = "SELECT id, status, tracking_id, user_id FROM orders WHERE id = ? AND vendor_id = ?";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->bind_param("ii", $orderId, $user['id']);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();
    
    if ($orderResult->num_rows === 0) {
        throw new Exception("Order not found or you don't have permission to update it");
    }
    
    $order = $orderResult->fetch_assoc();
    
    // Check if order is in a valid state to assign a rider
    if ($order['status'] !== 'pending' && $order['status'] !== 'processing') {
        throw new Exception("Cannot assign rider to an order with status: " . $order['status']);
    }
    
    // Check if vendor and rider are in the same location
    if (strtolower($user['city']) !== strtolower($rider['city']) || 
        strtolower($user['state']) !== strtolower($rider['state']) || 
        strtolower($user['country']) !== strtolower($rider['country'])) {
        throw new Exception("Rider must be in the same location as the vendor");
    }
    
    // Update order with rider_id, status, and assigned_at timestamp
    $updateSql = "UPDATE orders SET rider_id = ?, status = 'processing', assigned_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ii", $riderId, $orderId);
    $updateStmt->execute();
    
    if ($updateStmt->affected_rows === 0) {
        throw new Exception("Failed to assign rider to order");
    }
    
    // Insert entry into riders table for tracking
    $riderEntrySQL = "INSERT INTO riders (full_name, vendor_id, order_id, user_id, rider_id, track_id) VALUES (?, ?, ?, ?, ?, ?)";
    $riderEntryStmt = $conn->prepare($riderEntrySQL);
    $riderEntryStmt->bind_param("siiiis", $rider['full_name'], $user['id'], $orderId, $order['user_id'], $riderId, $order['tracking_id']);
    $riderEntryStmt->execute();
    
    // Add entry to order status history
    $historySQL = "INSERT INTO order_status_history (order_id, status, notes) VALUES (?, 'processing', ?)";
    $historyStmt = $conn->prepare($historySQL);
    $notes = "Rider assigned: " . $rider['full_name'];
    $historyStmt->bind_param("is", $orderId, $notes);
    $historyStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Rider assigned to order successfully"
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

// Close the database connection
$conn->close();
?>

