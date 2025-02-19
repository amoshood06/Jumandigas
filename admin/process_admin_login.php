<?php
session_start();
require_once "../db/db.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['role'] = $admin['role'];

        echo json_encode(["status" => "success", "message" => "Login successful!", "redirect" => "index.php"]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials!"]);
        exit();
    }
}
?>
