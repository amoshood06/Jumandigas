<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
require '../db/db.php';

// Get token from the Authorization header
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim($headers['Authorization']) : '';

if (!$token) {
    echo json_encode([
        "status" => "error", 
        "message" => "Unauthorized access, token required"
    ]);
    exit();
}

try {
    // Verify the token and get user data from the database
    $stmt = $pdo->prepare("
        SELECT 
            id,
            full_name,
            email,
            phone,
            address,
            country,
            state,
            city,
            currency,
            role,
            is_verified,
            is_active,
            created_at,
            last_login
        FROM users 
        WHERE api_token = ?
    ");
    
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Remove sensitive data before sending
        unset($user['api_token']);
        
        // Format dates
        $user['created_at'] = date('Y-m-d H:i:s', strtotime($user['created_at']));
        $user['last_login'] = $user['last_login'] ? date('Y-m-d H:i:s', strtotime($user['last_login'])) : null;
        
        // Convert boolean fields
        $user['is_verified'] = (bool)$user['is_verified'];
        $user['is_active'] = (bool)$user['is_active'];

        // If the role is not set in the database, default to "user"
        if (!isset($user['role']) || empty($user['role'])) {
            $user['role'] = 'user';
        }
        
        // Update last login time
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET last_login = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $updateStmt->execute([$user['id']]);
        
        echo json_encode([
            "status" => "success",
            "message" => "User data retrieved successfully",
            "user" => $user
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid token!"
        ]);
    }
} catch (PDOException $e) {
    // Log the error for debugging (in a production environment)
    error_log("Database error: " . $e->getMessage());
    
    echo json_encode([
        "status" => "error",
        "message" => "Database error occurred"
    ]);
}
?>

