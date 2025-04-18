
<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password

// $host = "localhost"; 
// $user = "root"; 
// $password = ""; 
// $database = "jumandigas"; 

$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Extract and sanitize input
$full_name = isset($data['full_name']) ? trim($data['full_name']) : null;
$email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : null;
$phone = isset($data['phone']) ? trim($data['phone']) : null;
$password = isset($data['password']) ? trim($data['password']) : null;
$address = isset($data['address']) ? trim($data['address']) : null;
$country = isset($data['country']) ? trim($data['country']) : null;
$state = isset($data['state']) ? trim($data['state']) : null;
$city = isset($data['city']) ? trim($data['city']) : null;
$currency = isset($data['currency']) ? trim($data['currency']) : null;

// Set role as "user" (No other roles allowed)
$role = "rider";

// Validate required fields
if (!$full_name || !$email || !$phone || !$password || !$address || !$country || !$state || !$city || !$currency) {
    echo json_encode(["status" => "error", "message" => "All fields are required!"]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format!"]);
    exit();
}

// Check if email or phone already exists
$sql = "SELECT * FROM users WHERE email = ? OR phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email or phone number already exists!"]);
    exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert user data
$sql = "INSERT INTO users (full_name, email, phone, password, address, country, state, city, role, currency) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $full_name, $email, $phone, $hashedPassword, $address, $country, $state, $city, $role, $currency);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!", "redirect" => "login.php"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed! Please try again."]);
}

// Close the database connection
$conn->close();
?>
