<?php
require '../db/db.php'; // Include database connection

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Retrieve token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';

if (!$token) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access! Token required."]);
    exit();
}

// Verify token and fetch user details
$stmt = $pdo->prepare("SELECT id, email, balance, currency, country, state, city FROM users WHERE api_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Invalid token!"]);
    exit();
}

try {
    // Fetch vendors in the same location
    $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE role = 'vendor' AND country = ? AND state = ? AND city = ?");
    $stmt->execute([$user['country'], $user['state'], $user['city']]);
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch gas price per kg
    $stmt = $pdo->prepare("SELECT price FROM locations WHERE country = ? AND state = ?");
    $stmt->execute([$user['country'], $user['state']]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    $price_per_kg = $location ? $location['price'] : 0;

    // Fetch bike price based on user's location
    $stmt = $pdo->prepare("SELECT price FROM bike WHERE country = ? AND state = ? AND city = ?");
    $stmt->execute([$user['country'], $user['state'], $user['city']]);
    $bike_location = $stmt->fetch(PDO::FETCH_ASSOC);
    $bike_price = $bike_location ? $bike_location['price'] : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Read JSON input
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['cylinder_type'], $data['exchange'], $data['amount_kg'], $data['vendor_id'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields!"]);
            exit();
        }

        $cylinder_type = htmlspecialchars($data['cylinder_type']);
        $exchange = htmlspecialchars($data['exchange']);
        $amount_kg = (float) $data['amount_kg'];
        $vendor_id = (int) $data['vendor_id'];
        $total_price = $amount_kg * $price_per_kg + $bike_price;
        $currency = $user['currency'];

        // Check if user has enough balance
        if ($user['balance'] >= $total_price) {
            // Deduct from user balance
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$total_price, $user['id']]);

            // Generate tracking ID
            $tracking_id = "TRK" . strtoupper(uniqid());

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, vendor_id, cylinder_type, exchange, amount_kg, total_price, currency, tracking_id) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $vendor_id, $cylinder_type, $exchange, $amount_kg, $total_price, $currency, $tracking_id]);

            echo json_encode(["status" => "success", "message" => "Order placed successfully!", "tracking_id" => $tracking_id]);
        } else {
            echo json_encode(["status" => "error", "message" => "Insufficient balance!"]);
        }
    } else {
        // Return data for GET requests
        echo json_encode([
            "status" => "success",
            "user" => [
                "id" => $user['id'],
                "email" => $user['email'],
                "balance" => $user['balance'],
                "currency" => $user['currency']
            ],
            "vendors" => $vendors,
            "price_per_kg" => $price_per_kg,
            "bike_price" => $bike_price
        ]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "An error occurred!", "error" => $e->getMessage()]);
}
?>
