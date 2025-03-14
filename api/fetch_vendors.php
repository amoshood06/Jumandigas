<?php
require '../db/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Get token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access, token required"]);
    exit();
}

// Verify the token in the database
$stmt = $pdo->prepare("SELECT id, country, state, city FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Invalid token!"]);
    exit();
}

try {
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
