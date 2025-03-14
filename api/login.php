<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database credentials
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data['email']) ? trim($data['email']) : null;
$password = isset($data['password']) ? trim($data['password']) : null;

if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit();
}

// Prepare statement to prevent SQL injection
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    if ($user['role'] !== 'user') {
        echo json_encode(["status" => "error", "message" => "Unauthorized role!"]);
        exit();
    }

    // Generate a unique API token
    $token = bin2hex(random_bytes(32));

    // Save token in database
    $updateTokenSQL = "UPDATE users SET api_token = ? WHERE id = ?";
    $stmt = $conn->prepare($updateTokenSQL);
    $stmt->bind_param("si", $token, $user['id']);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Login successful!",
        "user" => [
            "id" => $user['id'],
            "email" => $user['email'],
            "role" => $user['role'],
            "token" => $token
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid email or password!"]);
}

$conn->close();
?>
