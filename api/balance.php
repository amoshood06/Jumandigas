<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include database connection
include_once 'config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Extract token from Authorization header
$headers = getallheaders();
$auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$auth || strpos($auth, 'Bearer ') !== 0) {
    // No token provided
    http_response_code(401);
    echo json_encode(array(
        "status" => false,
        "message" => "Unauthorized access. Authentication token required."
    ));
    exit;
}

$token = substr($auth, 7); // Remove 'Bearer ' from the string

try {
    // Verify the token and get user details
    $query = "SELECT id, full_name, balance, currency FROM users WHERE api_token = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $token);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        // Token is valid, return user balance
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set response code - 200 OK
        http_response_code(200);
        
        // Return success response
        echo json_encode(array(
            "status" => true,
            "user_id" => $row['id'],
            "full_name" => $row['full_name'],
            "balance" => $row['balance'],
            "currency" => $row['currency']
        ));
    } else {
        // Invalid token
        http_response_code(401);
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid or expired token"
        ));
    }
} catch (Exception $e) {
    // Log the error
    error_log("Balance API error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode(array(
        "status" => false,
        "message" => "An error occurred while fetching balance",
        "error" => $e->getMessage()
    ));
}
?>

