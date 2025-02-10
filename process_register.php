<?php
session_start();
require_once "./db/db.php"; 

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_role'])) {
    echo json_encode(["status" => "error", "message" => "You must be logged in to register!"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $state = trim($_POST['state']);
    $location = trim($_POST['location']);
    $role = trim($_POST['role']);

    // Prevent users from registering as admin
    if ($role == 'admin' && $_SESSION['user_role'] !== 'admin') {
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

    // Insert user data
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, address, state, location, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $inserted = $stmt->execute([$full_name, $email, $phone, $hashedPassword, $address, $state, $location, $role]);

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
