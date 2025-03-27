<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

// Get location parameters
$city = isset($_GET['city']) ? trim($_GET['city']) : null;
$state = isset($_GET['state']) ? trim($_GET['state']) : null;
$country = isset($_GET['country']) ? trim($_GET['country']) : null;

// Prepare SQL statement to fetch available riders
$sql = "SELECT id, full_name, phone, city, state, country, online as is_available 
        FROM users 
        WHERE role = 'rider' AND online = 1";

// Add location filters if provided
$params = [];
$types = "";

if ($city) {
    $sql .= " AND city = ?";
    $params[] = $city;
    $types .= "s";
}

if ($state) {
    $sql .= " AND state = ?";
    $params[] = $state;
    $types .= "s";
}

if ($country) {
    $sql .= " AND country = ?";
    $params[] = $country;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$riders = [];
while ($row = $result->fetch_assoc()) {
    $riders[] = $row;
}

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Available riders retrieved successfully",
    "riders" => $riders
]);

// Close the database connection
$conn->close();
?>

