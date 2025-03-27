<?php
// Database connection settings
// $host = "localhost";
// $database = "jumandigas";
// $user = "root";
// $password = "";

// Database credentials
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$dbname = "jumandigas-353038374f79"; // Change to your database name
$username = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // In production, you would log this error instead of displaying it
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]));
}
?>

