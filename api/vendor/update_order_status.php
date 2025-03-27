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
$status = isset($data['status']) ? trim($data['status']) : null;

// Check if order_id and status are provided
if (!$orderId || !$status) {
    echo json_encode(["status" => "error", "message" => "Order ID and status are required"]);
    exit();
}

// Validate status
$validStatuses = ['pending', 'processing', 'moving', 'delivered', 'canceled'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(["status" => "error", "message" => "Invalid status. Valid statuses are: " . implode(', ', $validStatuses)]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Update order status
    $sql = "UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND vendor_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $stmt->bind_param("sii", $status, $orderId, $user['id']);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->affected_rows === 0) {
        throw new Exception("Order not found or you don't have permission to update it");
    }
    
    // Add entry to order status history
    $historySQL = "INSERT INTO order_status_history (order_id, status, notes) VALUES (?, ?, ?)";
    $historyStmt = $conn->prepare($historySQL);
    if (!$historyStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $notes = "Status updated by vendor";
    $historyStmt->bind_param("iss", $orderId, $status, $notes);
    $historyStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        "status" => "success",
        "message" => "Order status updated successfully"
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

