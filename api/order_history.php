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

// Handle GET request - Return user's order history
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Get query parameters for filtering
  $status = isset($_GET['status']) ? $_GET['status'] : null;
  $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
  $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
  $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
  $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
  
  // Build the query with filters
  $query = "SELECT o.*, u.full_name as vendor_name 
            FROM orders o 
            LEFT JOIN users u ON o.vendor_id = u.id 
            WHERE o.user_id = ?";
  
  $params = [$user_id];
  $types = "i";
  
  if ($status) {
    $query .= " AND o.status = ?";
    $params[] = $status;
    $types .= "s";
  }
  
  if ($date_from) {
    $query .= " AND o.created_at >= ?";
    $params[] = $date_from . " 00:00:00";
    $types .= "s";
  }
  
  if ($date_to) {
    $query .= " AND o.created_at <= ?";
    $params[] = $date_to . " 23:59:59";
    $types .= "s";
  }
  
  // Add order by and limit
  $query .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
  $params[] = $limit;
  $params[] = $offset;
  $types .= "ii";
  
  // Prepare and execute the query
  $stmt = $conn->prepare($query);
  if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
  }
  
  // Bind parameters dynamically
  $bind_params = array_merge([$types], $params);
  $bind_params_refs = [];
  foreach ($bind_params as $key => $value) {
    $bind_params_refs[$key] = &$bind_params[$key];
  }
  
  call_user_func_array([$stmt, 'bind_param'], $bind_params_refs);
  $stmt->execute();
  $result = $stmt->get_result();
  
  // Get total count for pagination
  $count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = ?";
  $count_params = [$user_id];
  $count_types = "i";
  
  if ($status) {
    $count_query .= " AND status = ?";
    $count_params[] = $status;
    $count_types .= "s";
  }
  
  if ($date_from) {
    $count_query .= " AND created_at >= ?";
    $count_params[] = $date_from . " 00:00:00";
    $count_types .= "s";
  }
  
  if ($date_to) {
    $count_query .= " AND created_at <= ?";
    $count_params[] = $date_to . " 23:59:59";
    $count_types .= "s";
  }
  
  $count_stmt = $conn->prepare($count_query);
  if (!$count_stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
  }
  
  // Bind parameters dynamically
  $count_bind_params = array_merge([$count_types], $count_params);
  $count_bind_params_refs = [];
  foreach ($count_bind_params as $key => $value) {
    $count_bind_params_refs[$key] = &$count_bind_params[$key];
  }
  
  call_user_func_array([$count_stmt, 'bind_param'], $count_bind_params_refs);
  $count_stmt->execute();
  $count_result = $count_stmt->get_result();
  $total_count = $count_result->fetch_assoc()['total'];
  
  // Fetch all orders
  $orders = [];
  while ($row = $result->fetch_assoc()) {
    $orders[] = [
      'id' => $row['id'],
      'tracking_id' => $row['tracking_id'],
      'cylinder_type' => $row['cylinder_type'],
      'exchange' => $row['exchange'],
      'amount_kg' => $row['amount_kg'],
      'total_price' => $row['total_price'],
      'currency' => $row['currency'],
      'status' => $row['status'] ?: 'pending',
      'vendor_name' => $row['vendor_name'],
      'created_at' => $row['created_at']
    ];
  }
  
  // Get available statuses for filter
  $status_query = "SELECT DISTINCT status FROM orders WHERE user_id = ? AND status IS NOT NULL";
  $status_stmt = $conn->prepare($status_query);
  $status_stmt->bind_param("i", $user_id);
  $status_stmt->execute();
  $status_result = $status_stmt->get_result();
  
  $statuses = [];
  while ($row = $status_result->fetch_assoc()) {
    if (!empty($row['status'])) {
      $statuses[] = $row['status'];
    }
  }
  
  // If no statuses found, add default ones
  if (empty($statuses)) {
    $statuses = ['pending', 'processing', 'moving', 'delivered', 'canceled'];
  }
  
  // Return orders data with pagination info
  echo json_encode([
    "status" => "success",
    "orders" => $orders,
    "pagination" => [
      "total" => (int)$total_count,
      "limit" => $limit,
      "offset" => $offset
    ],
    "filters" => [
      "statuses" => $statuses
    ]
  ]);
  
  exit();
}

// Close the database connection
$conn->close();
?>

