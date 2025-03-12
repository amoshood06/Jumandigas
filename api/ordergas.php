<?php
//require_once "../auth_check.php";
require '../db/db.php'; // Include database connection
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access!"]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found!"]);
        exit();
    }

    // Fetch vendors in the same location
    $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'vendor' AND country = ? AND state = ? AND city = ?");
    $stmt->execute([$user['country'], $user['state'], $user['city']]);
    $vendors = $stmt->fetchAll();

    // Fetch gas price per kg
    $stmt = $pdo->prepare("SELECT price FROM locations WHERE country = ? AND state = ?");
    $stmt->execute([$user['country'], $user['state']]);
    $location = $stmt->fetch();
    $price_per_kg = $location ? $location['price'] : 0;

    // Fetch bike price based on user's location
    $stmt = $pdo->prepare("SELECT price FROM bike WHERE country = ? AND state = ? AND city = ?");
    $stmt->execute([$user['country'], $user['state'], $user['city']]);
    $bike_location = $stmt->fetch();
    $bike_price = $bike_location ? $bike_location['price'] : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['cylinder_type'], $data['exchange'], $data['amount_kg'], $data['vendor_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields!"]);
            exit();
        }

        $cylinder_type = $data['cylinder_type'];
        $exchange = $data['exchange'];
        $amount_kg = $data['amount_kg'];
        $vendor_id = $data['vendor_id'];
        $total_price = $amount_kg * $price_per_kg + $bike_price;
        $currency = $user['currency'];

        // Check if user has enough balance
        if ($user['balance'] >= $total_price) {
            // Deduct from user balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$total_price, $user_id]);

            // Generate tracking ID
            $tracking_id = "TRK" . strtoupper(uniqid());

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, vendor_id, cylinder_type, exchange, amount_kg, total_price, currency, tracking_id) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $vendor_id, $cylinder_type, $exchange, $amount_kg, $total_price, $currency, $tracking_id]);

            echo json_encode(["status" => "success", "message" => "Order placed successfully!", "tracking_id" => $tracking_id]);
        } else {
            echo json_encode(["status" => "error", "message" => "Insufficient balance!"]);
        }
    } else {
        // Return data for GET requests
        echo json_encode([
            "status" => "success",
            "user" => ["id" => $user['id'], "email" => $user['email'], "balance" => $user['balance']],
            "vendors" => $vendors,
            "price_per_kg" => $price_per_kg,
            "bike_price" => $bike_price
        ]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "An error occurred!", "error" => $e->getMessage()]);
}
?>
