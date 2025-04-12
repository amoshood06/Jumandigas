<?php
ini_set('display_errors', 0);
error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit;
}

// Database credentials
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
  header('Content-Type: application/json');
  echo json_encode(["status" => "error", "message" => "Database connection failed"]);
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
  header('Content-Type: application/json');
  echo json_encode(["status" => "error", "message" => "Database error"]);
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

// Handle GET request - Return order tracking information
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Get tracking_id from query parameters
  $tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : null;
  $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
  
  if (!$tracking_id && !$order_id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Tracking ID or Order ID is required"]);
    exit();
  }
  
  try {
    // Check if order_status_history table exists, if not create it
    $table_check = $conn->query("SHOW TABLES LIKE 'order_status_history'");
    if ($table_check->num_rows == 0) {
      // Create the table
      $conn->query("CREATE TABLE order_status_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        status VARCHAR(50) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (order_id)
      )");
    }
    
    // First, check the structure of the riders table to find the correct column name
    $riders_structure = $conn->query("DESCRIBE riders");
    $order_id_column = "order_id"; // Default column name
    
    if ($riders_structure) {
      while ($column = $riders_structure->fetch_assoc()) {
        // Look for columns that might relate to order_id
        if (strpos($column['Field'], 'order') !== false) {
          $order_id_column = $column['Field'];
          break;
        }
      }
    }
    
    // Build the query based on provided parameters with the correct column name
    // Note: We're joining with users table for both vendor and rider information
    if ($tracking_id) {
      $query = "SELECT o.*, 
                v.full_name as vendor_name, v.phone as vendor_phone, v.email as vendor_email,
                r.id as rider_id, r.full_name as rider_name, r.phone as rider_phone,
                rd.latitude as rider_latitude, rd.longitude as rider_longitude
                FROM orders o 
                LEFT JOIN users v ON o.vendor_id = v.id 
                LEFT JOIN users r ON o.rider_id = r.id
                LEFT JOIN riders rd ON r.id = rd.rider_id
                WHERE o.tracking_id = ? AND o.user_id = ?";
      $params = [$tracking_id, $user_id];
      $types = "si";
    } else {
      $query = "SELECT o.*, 
                v.full_name as vendor_name, v.phone as vendor_phone, v.email as vendor_email,
                r.id as rider_id, r.full_name as rider_name, r.phone as rider_phone,
                rd.latitude as rider_latitude, rd.longitude as rider_longitude
                FROM orders o 
                LEFT JOIN users v ON o.vendor_id = v.id 
                LEFT JOIN users r ON o.rider_id = r.id
                LEFT JOIN riders rd ON r.id = rd.rider_id
                WHERE o.id = ? AND o.user_id = ?";
      $params = [$order_id, $user_id];
      $types = "ii";
    }
    
    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    if (!$stmt) {
      header('Content-Type: application/json');
      echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
      exit();
    }
    
    // Bind parameters
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
      http_response_code(404);
      echo json_encode(["status" => "error", "message" => "Order not found"]);
      exit();
    }
    
    $order = $result->fetch_assoc();
    
    // Get order status history
    $status_query = "SELECT status, notes, created_at FROM order_status_history 
                    WHERE order_id = ? ORDER BY created_at ASC";
    $status_stmt = $conn->prepare($status_query);
    
    $status_history = [];
    if ($status_stmt) {
      $status_stmt->bind_param("i", $order['id']);
      $status_stmt->execute();
      $status_result = $status_stmt->get_result();
      
      while ($row = $status_result->fetch_assoc()) {
        $status_history[] = [
          'status' => $row['status'],
          'notes' => $row['notes'],
          'timestamp' => $row['created_at']
        ];
      }
    }
    
    // If no status history, create a default one based on current status
    if (empty($status_history) && $order['status']) {
      $status_history[] = [
        'status' => $order['status'],
        'notes' => 'Order created',
        'timestamp' => $order['created_at']
      ];
      
      // Add processing status if order is beyond pending
      if ($order['status'] != 'pending') {
        $processing_time = strtotime($order['created_at']) + 1800; // 30 minutes after creation
        $status_history[] = [
          'status' => 'processing',
          'notes' => 'Order is being processed',
          'timestamp' => date('Y-m-d H:i:s', $processing_time)
        ];
      }
      
      // Add moving status if order is moving or delivered
      if ($order['status'] == 'moving' || $order['status'] == 'delivered') {
        $moving_time = strtotime($order['created_at']) + 3600; // 1 hour after creation
        $status_history[] = [
          'status' => 'moving',
          'notes' => 'Rider is on the way',
          'timestamp' => date('Y-m-d H:i:s', $moving_time)
        ];
      }
      
      // Add delivered status if order is delivered
      if ($order['status'] == 'delivered') {
        $delivered_time = strtotime($order['created_at']) + 7200; // 2 hours after creation
        $status_history[] = [
          'status' => 'delivered',
          'notes' => 'Order has been delivered',
          'timestamp' => date('Y-m-d H:i:s', $delivered_time)
        ];
      }
    }
    
    // If order doesn't have a rider assigned but status is moving, assign a random rider
    if ((!$order['rider_id'] || $order['rider_id'] == null) && ($order['status'] == 'moving' || $order['status'] == 'delivered')) {
      $random_rider = $conn->query("SELECT id, full_name, phone FROM users WHERE role = 'rider' ORDER BY RAND() LIMIT 1");
      if ($random_rider->num_rows > 0) {
        $rider = $random_rider->fetch_assoc();
        
        // Update order with rider
        $update_rider = $conn->prepare("UPDATE orders SET rider_id = ? WHERE id = ?");
        $update_rider->bind_param("ii", $rider['id'], $order['id']);
        $update_rider->execute();
        
        // Add rider info to order
        $order['rider_id'] = $rider['id'];
        $order['rider_name'] = $rider['full_name'];
        $order['rider_phone'] = $rider['phone'];
        
        // Add default location for rider if not exists
        if (!$order['rider_latitude'] || !$order['rider_longitude']) {
          // Check if there's already a rider entry
          $rider_check = $conn->prepare("SELECT id FROM riders WHERE rider_id = ?");
          $rider_check->bind_param("i", $rider['id']);
          $rider_check->execute();
          $rider_result = $rider_check->get_result();
          
          if ($rider_result->num_rows == 0) {
            // Insert new rider location
            $default_lat = 6.5244;
            $default_lng = 3.3792;
            $insert_rider = $conn->prepare("INSERT INTO riders (vendor_id, track_id, user_id, rider_id, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_rider->bind_param("issdd", $order['vendor_id'], $order['tracking_id'], $order['user_id'], $rider['id'], $default_lat, $default_lng);
            $insert_rider->execute();
            
            $order['rider_latitude'] = $default_lat;
            $order['rider_longitude'] = $default_lng;
          }
        }
      }
    }
    
    // Get delivery address (user's address)
    $address_query = "SELECT address, country, state, city FROM users WHERE id = ?";
    $address_stmt = $conn->prepare($address_query);
    $address_stmt->bind_param("i", $user_id);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();
    $address = $address_result->fetch_assoc();
    
    // Format the response
    $response = [
      "status" => "success",
      "order" => [
        "id" => $order['id'],
        "tracking_id" => $order['tracking_id'],
        "cylinder_type" => $order['cylinder_type'],
        "exchange" => $order['exchange'],
        "amount_kg" => $order['amount_kg'],
        "total_price" => $order['total_price'],
        "currency" => $order['currency'],
        "status" => $order['status'] ?: 'pending',
        "created_at" => $order['created_at'],
        "estimated_delivery" => date('Y-m-d H:i:s', strtotime($order['created_at']) + 7200) // 2 hours after creation
      ],
      "vendor" => [
        "name" => $order['vendor_name'] ?: 'N/A',
        "phone" => $order['vendor_phone'] ?: 'N/A',
        "email" => $order['vendor_email'] ?: 'N/A'
      ],
      "rider" => [
        "name" => $order['rider_name'] ?: 'Not assigned yet',
        "phone" => $order['rider_phone'] ?: 'N/A',
        "location" => [
          "latitude" => $order['rider_latitude'] ?: null,
          "longitude" => $order['rider_longitude'] ?: null
        ]
      ],
      "delivery_address" => [
        "address" => $address['address'] ?: 'N/A',
        "city" => $address['city'] ?: 'N/A',
        "state" => $address['state'] ?: 'N/A',
        "country" => $address['country'] ?: 'N/A'
      ],
      "status_history" => $status_history
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
  } catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
      "status" => "error", 
      "message" => "An unexpected error occurred",
      "error" => $e->getMessage()
    ]);
  }
  exit();
}

// Close the database connection
$conn->close();
?>