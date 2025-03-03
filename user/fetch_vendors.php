<?php
require '../db/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT country, state, city FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit();
}

$stmt = $pdo->prepare("
    SELECT users.id, users.full_name, 
           COALESCE(AVG(ratings.rating), 0) AS avg_rating 
    FROM users 
    LEFT JOIN ratings ON users.id = ratings.vendor_id 
    WHERE users.role = 'vendor' 
    AND users.country = ? AND users.state = ? AND users.city = ?
    GROUP BY users.id, users.full_name
");
$stmt->execute([$user['country'], $user['state'], $user['city']]);
$vendors = $stmt->fetchAll();

if ($vendors) {
    echo json_encode(["status" => "success", "vendors" => $vendors]);
} else {
    echo json_encode(["status" => "error", "message" => "No vendors available in your location"]);
}
?>
