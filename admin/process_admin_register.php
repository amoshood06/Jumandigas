<?php
require_once "../db/db.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';

    // Check if admin email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists!"]);
        exit();
    }

    // Insert admin into database
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$full_name, $email, $phone, $password, $role])) {
        echo json_encode(["status" => "success", "message" => "Admin registered successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed!"]);
    }
}
?>
