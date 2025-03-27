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

// Create alerts table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS alerts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  type ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',
  is_read BOOLEAN NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  INDEX (is_read)
)");

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

// Handle GET request - Return user alerts
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Check if we need to mark an alert as read
  if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $alert_id = intval($_GET['mark_read']);
    $mark_read_sql = "UPDATE alerts SET is_read = 1 WHERE id = ? AND (user_id = ? OR user_id IS NULL)";
    $mark_read_stmt = $conn->prepare($mark_read_sql);
    $mark_read_stmt->bind_param("ii", $alert_id, $user_id);
    $mark_read_stmt->execute();
  }
  
  // Check if we need to mark all alerts as read
  if (isset($_GET['mark_all_read']) && $_GET['mark_all_read'] == '1') {
    $mark_all_sql = "UPDATE alerts SET is_read = 1 WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0";
    $mark_all_stmt = $conn->prepare($mark_all_sql);
    $mark_all_stmt->bind_param("i", $user_id);
    $mark_all_stmt->execute();
  }
  
  // Get user's alerts (including global alerts where user_id is NULL)
  $alerts_sql = "SELECT * FROM alerts WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC";
  $alerts_stmt = $conn->prepare($alerts_sql);
  $alerts_stmt->bind_param("i", $user_id);
  $alerts_stmt->execute();
  $alerts_result = $alerts_stmt->get_result();
  
  $alerts = [];
  while ($row = $alerts_result->fetch_assoc()) {
    $alerts[] = [
      'id' => $row['id'],
      'title' => $row['title'],
      'message' => $row['message'],
      'type' => $row['type'],
      'is_read' => (bool)$row['is_read'],
      'created_at' => $row['created_at']
    ];
  }
  
  // Count unread alerts
  $unread_sql = "SELECT COUNT(*) as count FROM alerts WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0";
  $unread_stmt = $conn->prepare($unread_sql);
  $unread_stmt->bind_param("i", $user_id);
  $unread_stmt->execute();
  $unread_result = $unread_stmt->get_result();
  $unread_count = $unread_result->fetch_assoc()['count'];
  
  // Return alerts data
  echo json_encode([
    "status" => "success",
    "alerts" => $alerts,
    "unread_count" => (int)$unread_count
  ]);
  
  exit();
}

// Close the database connection
$conn->close();
?>

