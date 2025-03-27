<?php
// Function to authenticate user based on token
function authenticate($conn) {
    // Get authorization header
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    // Extract token
    $token = '';
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
    }

    // Check if token is provided
    if (!$token) {
        echo json_encode(["status" => "error", "message" => "Authorization token is required"]);
        return false;
    }

    // Prepare SQL statement to fetch user by token
    $sql = "SELECT * FROM users WHERE api_token = ? AND role = 'vendor'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        return false;
    }
    
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
        return false;
    }
    
    // Return user data
    return $result->fetch_assoc();
}
?>

