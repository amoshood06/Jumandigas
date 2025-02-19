<?php 
session_start();
require_once "./db/db.php"; 

header('Content-Type: application/json');
// Check if session is stcountryarted and if user is logged in
if (isset($_SESSION['user_role'])) {
    echo json_encode(["status" => "error", "message" => "You are already logged in and cannot register!"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $full_name = trim($_POST['full_name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $country = trim($_POST['country']);
    $state = trim($_POST['state']);
    $city = trim($_POST['city']);
    $role = trim($_POST['role']);
    $currency = trim($_POST['currency']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format!"]);
        exit();
    }

    // Prevent users from registering as admin
    if ($role == 'admin' && (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')) {
        echo json_encode(["status" => "error", "message" => "You cannot register as an admin directly!"]);
        exit();
    }

    // Check if email or phone already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo json_encode(["status" => "error", "message" => "Email or phone number already exists!"]);
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user data, including currency
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, address, country, state, city, role, currency) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $inserted = $stmt->execute([$full_name, $email, $phone, $hashedPassword, $address, $country, $state, $city, $role, $currency]);
    
    if ($inserted) {
        // Redirect user to the correct dashboard based on role
        $redirectPage = match ($role) {
            'user' => 'user/index.php',
            'vendor' => 'vendor/index.php',
            'rider' => 'rider/index.php',
            default => 'login.php'
        };

        echo json_encode(["status" => "success", "message" => "Registration successful!", "redirect" => $redirectPage]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed! Please try again."]);
        exit();
    }
}
?>
