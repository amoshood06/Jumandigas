<?php
// Database credentials
$host = "sdb-82.hosting.stackcp.net"; // Change to your database host
$dbname = "Jumandigas-3530383908cf"; // Change to your database name
$username = "Jumandigas-3530383908cf"; // Change to your database username
$password = "&h{TO+oFD8sV"; // Change to your database password

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
