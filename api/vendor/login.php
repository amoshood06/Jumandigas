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

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data['email']) ? trim($data['email']) : null;
$password = isset($data['password']) ? trim($data['password']) : null;

// Check if email and password are provided
if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit();
}

// Prepare SQL statement to fetch user by email
$sql = "SELECT * FROM users WHERE email = ? AND role = 'vendor'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists
if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found or not a vendor"]);
    exit();
}

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    exit();
}

// Generate a unique API token
$token = bin2hex(random_bytes(32));

// Save token in database
$updateTokenSQL = "UPDATE users SET api_token = ? WHERE id = ?";
$stmt = $conn->prepare($updateTokenSQL);
$stmt->bind_param("si", $token, $user['id']);
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to update token"]);
    exit();
}

// Success response
echo json_encode([
    "status" => "success",
    "message" => "Login successful!",
    "user" => [
        "id" => $user['id'],
        "email" => $user['email'],
        "role" => $user['role'],
        "token" => $token,
        "full_name" => $user['full_name'],
        "phone" => $user['phone'],
        "address" => $user['address'],
        "state" => $user['state'],
        "city" => $user['city'],
        "country" => $user['country'],
        "currency" => $user['currency'],
        "balance" => $user['balance']
    ]
]);

// Close the database connection
$conn->close();
?>

