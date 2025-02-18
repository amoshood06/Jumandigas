<?php
// Database credentials
$host = ""; // Change to your database host
$dbname = "jumandigas-35303839db50"; // Change to your database name
$username = "jumandigas-35303839db50"; // Change to your database username
$password = "qa7madglb8"; // Change to your database password

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set fetch mode to associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Uncomment to test the connection
    // echo "Connected successfully!";
} catch (PDOException $e) {
    // Display an error message
    die("Database connection failed: " . $e->getMessage());
}
?>
