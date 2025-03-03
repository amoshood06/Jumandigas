<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "jumandigas"; 

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
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] !== 'user') {
        echo json_encode(["status" => "error", "message" => "Unauthorized role!"]);
        exit();
    }

    echo json_encode(["status" => "success", "message" => "Login successful!", "user" => [
        "id" => $user['id'],
        "email" => $user['email'],
        "role" => $user['role']
    ]]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid email or password!"]);
}

$conn->close();
?>
