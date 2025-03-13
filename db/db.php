<?php
// Database credentials
$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$dbname = "jumandigas-353038374f79"; // Change to your database name
$username = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password

// $host = "localhost"; // Change to your database host
// $dbname = "jumandigas"; // Change to your database name
// $username = "root"; // Change to your database username
// $password = ""; // Change to your database password

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode to exceptionsi
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set fetch mode to associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Uncomment to test the connection
     //echo "Connected successfully!";
} catch (PDOException $e) {
    // Display an error message
    die("Database connection failed: " . $e->getMessage());
}
?>
