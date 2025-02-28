<?php
require_once "auth_admin.php";  // Authentication check for admin
require_once "../db/db.php";  // Database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $country = htmlspecialchars($_POST['country']);
    $state = htmlspecialchars($_POST['state']);
    $city = htmlspecialchars($_POST['city']); // Included city field
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $currency = htmlspecialchars($_POST['currency']);

    // Ensure price is a valid decimal value
    if ($price === false) {
        echo json_encode(["status" => "error", "message" => "Invalid price format."]);
        exit;
    }

    try {
        // Insert data into the `bike` table with `city`
        $stmt = $pdo->prepare("INSERT INTO bike (country, state, city, price, currency) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$country, $state, $city, $price, $currency]);

        // Send a JSON response indicating success
        echo json_encode(["status" => "success", "message" => "City price added successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>
