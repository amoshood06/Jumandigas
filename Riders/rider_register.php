<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost"; // Change to your database host
$database = "jumandigas"; // Change to your database name
$user = "root"; // Change to your database username
$password = ""; // Change to your database password

// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if all required fields are provided
$requiredFields = ['full_name', 'email', 'phone', 'password', 'address', 'country', 'state', 'city'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
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
$role = 'rider'; // Force role to be rider

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit();
}

// Check if phone already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Phone number already exists"]);
    exit();
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, address, country, state, city, currency, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssss", $fullName, $email, $phone, $hashedPassword, $address, $country, $state, $city, $currency, $role);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Registration successful! Please login to continue."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>