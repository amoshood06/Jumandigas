<?php
// Database credentials
$host = "sdb-82.hosting.stackcp.net"; // Database host
$dbname = "Jumandigas-3530383908cf"; // Database name
$username = "Jumandigas-3530383908cf"; // Database username
$password = "pGui.r?2GzKE"; // Database password
$port = "3306"; // Default MySQL port

try {
    // Create a new PDO connection
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Uncomment to test the connection
    echo "Connected successfully!";
} catch (PDOException $e) {
    // Display an error message
    die("Database connection failed: " . $e->getMessage());
}
?>
