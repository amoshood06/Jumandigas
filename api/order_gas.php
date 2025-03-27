<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
$sql = "SELECT * FROM users WHERE api_token = ?";
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

// If it's a GET request, return the necessary data for the order form
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Fetch vendors in the same location (same city)
    $vendor_sql = "SELECT id, full_name FROM users WHERE role = 'vendor' AND country = ? AND state = ? AND city = ?";
    $vendor_stmt = $conn->prepare($vendor_sql);
    if (!$vendor_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $vendor_stmt->bind_param("sss", $user['country'], $user['state'], $user['city']);
    $vendor_stmt->execute();
    $vendor_result = $vendor_stmt->get_result();
    
    $vendors = [];
    while ($row = $vendor_result->fetch_assoc()) {
        $vendors[] = [
            'id' => $row['id'],
            'name' => $row['full_name']
        ];
    }
    
    // Fetch gas price per kg
    $gas_sql = "SELECT price, currency FROM locations WHERE country = ? AND state = ?";
    $gas_stmt = $conn->prepare($gas_sql);
    if (!$gas_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $gas_stmt->bind_param("ss", $user['country'], $user['state']);
    $gas_stmt->execute();
    $gas_result = $gas_stmt->get_result();
    $gas_price = $gas_result->fetch_assoc();
    
    $price_per_kg = $gas_price ? $gas_price['price'] : 0;
    $gas_currency = $gas_price ? $gas_price['currency'] : 
                ($user['country'] == 'ghana' ? 'GHS' : 'NGN');
    
    // Fetch bike price based on user's location
    $bike_sql = "SELECT price FROM bike WHERE country = ? AND state = ? AND city = ?";
    $bike_stmt = $conn->prepare($bike_sql);
    if (!$bike_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $bike_stmt->bind_param("sss", $user['country'], $user['state'], $user['city']);
    $bike_stmt->execute();
    $bike_result = $bike_stmt->get_result();
    $bike_price_row = $bike_result->fetch_assoc();
    
    $bike_price = $bike_price_row ? $bike_price_row['price'] : 0;
    
    // Return all the data needed for the order form
    echo json_encode([
        "status" => "success",
        "user" => [
            "id" => $user['id'],
            "name" => $user['full_name'],
            "balance" => $user['balance'],
            "currency" => $user['currency']
        ],
        "vendors" => $vendors,
        "price_per_kg" => $price_per_kg,
        "bike_price" => $bike_price,
        "currency" => $gas_currency
    ]);
    
    exit();
}

// Handle Order Submission (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Invalid request data"]);
        exit();
    }
    
    $cylinder_type = $data['cylinder_type'] ?? '';
    $exchange = $data['exchange'] ?? '';
    $amount_kg = $data['amount_kg'] ?? 0;
    $vendor_id = $data['vendor_id'] ?? 0;
    
    // Fetch gas price per kg
    $gas_sql = "SELECT price FROM locations WHERE country = ? AND state = ?";
    $gas_stmt = $conn->prepare($gas_sql);
    $gas_stmt->bind_param("ss", $user['country'], $user['state']);
    $gas_stmt->execute();
    $gas_result = $gas_stmt->get_result();
    $gas_price = $gas_result->fetch_assoc();
    
    $price_per_kg = $gas_price ? $gas_price['price'] : 0;
    
    // Fetch bike price based on user's location
    $bike_sql = "SELECT price FROM bike WHERE country = ? AND state = ? AND city = ?";
    $bike_stmt = $conn->prepare($bike_sql);
    $bike_stmt->bind_param("sss", $user['country'], $user['state'], $user['city']);
    $bike_stmt->execute();
    $bike_result = $bike_stmt->get_result();
    $bike_price_row = $bike_result->fetch_assoc();
    
    $bike_price = $bike_price_row ? $bike_price_row['price'] : 0;
    
    // Calculate total price
    $total_price = $amount_kg * $price_per_kg + $bike_price;
    $currency = $user['currency'];
    
    // Check if user has enough balance
    if ($user['balance'] >= $total_price) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Deduct from user balance
            $update_sql = "UPDATE users SET balance = balance - ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("di", $total_price, $user_id);
            $update_stmt->execute();
            
            // Add to vendor balance
            $vendor_update_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $vendor_update_stmt = $conn->prepare($vendor_update_sql);
            $vendor_update_stmt->bind_param("di", $total_price, $vendor_id);
            $vendor_update_stmt->execute();
            
            // Generate tracking ID
            $tracking_id = "TRK" . strtoupper(bin2hex(random_bytes(5)));
            
            // Insert order with cylinder_type
            $order_sql = "INSERT INTO orders (user_id, vendor_id, cylinder_type, exchange, amount_kg, total_price, currency, tracking_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $order_stmt = $conn->prepare($order_sql);
            $order_stmt->bind_param("iissidss", $user_id, $vendor_id, $cylinder_type, $exchange, $amount_kg, $total_price, $currency, $tracking_id);
            $order_stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                "status" => "success",
                "message" => "Order placed successfully!",
                "tracking_id" => $tracking_id,
                "total_price" => $total_price,
                "new_balance" => $user['balance'] - $total_price
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo json_encode([
                "status" => "error",
                "message" => "Error placing order: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Insufficient balance",
            "required" => $total_price,
            "available" => $user['balance']
        ]);
    }
}

// Close the database connection
$conn->close();
?>

