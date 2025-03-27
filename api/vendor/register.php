<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
   http_response_code(200);
   exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if all required fields are provided
$requiredFields = ['full_name', 'email', 'phone', 'password', 'address', 'country', 'state', 'city'];
foreach ($requiredFields as $field) {
   if (!isset($data[$field]) || empty($data[$field])) {
       echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
       exit();
   }
}

// Extract data
$fullName = trim($data['full_name']);
$email = trim($data['email']);
$phone = trim($data['phone']);
$password = trim($data['password']);
$address = trim($data['address']);
$country = trim($data['country']);
$state = trim($data['state']);
$city = trim($data['city']);
$currency = isset($data['currency']) ? trim($data['currency']) : 'NGN';
$role = isset($data['role']) ? trim($data['role']) : 'vendor';

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
   echo json_encode(["status" => "error", "message" => "Invalid email format"]);
   exit();
}

// Check if email already exists
$checkEmailSql = "SELECT id FROM users WHERE email = ?";
$checkEmailStmt = $conn->prepare($checkEmailSql);
$checkEmailStmt->bind_param("s", $email);
$checkEmailStmt->execute();
$checkEmailResult = $checkEmailStmt->get_result();

if ($checkEmailResult->num_rows > 0) {
   echo json_encode(["status" => "error", "message" => "Email already exists"]);
   exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$insertSql = "INSERT INTO users (full_name, email, phone, password, address, country, state, city, currency, role, balance) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00)";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("ssssssssss", $fullName, $email, $phone, $hashedPassword, $address, $country, $state, $city, $currency, $role);

if ($insertStmt->execute()) {
   echo json_encode([
       "status" => "success",
       "message" => "Registration successful! You can now login."
   ]);
} else {
   echo json_encode([
       "status" => "error",
       "message" => "Registration failed: " . $conn->error
   ]);
}

// Close the database connection
$conn->close();
?>

