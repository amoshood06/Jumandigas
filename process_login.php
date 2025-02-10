<?php
session_start();
require_once "./db/db.php"; 

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] == 'user') {
            $redirect = "user/index.php";
        } elseif ($user['role'] == 'vendor') {
            $redirect = "vendor/index.php";
        } elseif ($user['role'] == 'rider') {
            $redirect = "rider/index.php";
        } else {
            echo json_encode(["status" => "error", "message" => "Unauthorized role!"]);
            exit();
        }

        echo json_encode(["status" => "success", "message" => "Login successful!", "redirect" => $redirect]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password!"]);
        exit();
    }
}
?>
