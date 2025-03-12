<?php
require '../db/db.php';
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user location
    $stmt = $pdo->prepare("SELECT country, state, city FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit();
    }

    // Fetch vendors in the same location with their average ratings
    $stmt = $pdo->prepare("
        SELECT users.id, users.full_name, 
               COALESCE(AVG(ratings.rating), 0) AS avg_rating 
        FROM users 
        LEFT JOIN ratings ON users.id = ratings.vendor_id 
        WHERE users.role = 'vendor' 
        AND users.country = ? 
        AND users.state = ? 
        AND users.city = ? 
        GROUP BY users.id, users.full_name
    ");
    $stmt->execute([$user['country'], $user['state'], $user['city']]);
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($vendors) {
        echo json_encode(["status" => "success", "vendors" => $vendors]);
    } else {
        echo json_encode(["status" => "error", "message" => "No vendors available in your location"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "An error occurred!", "error" => $e->getMessage()]);
}
?>
