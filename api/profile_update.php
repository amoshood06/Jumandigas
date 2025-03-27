<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database credentials
// $host = "localhost"; // Change to your database host
// $database = "jumandigas"; // Change to your database name
// $user = "root"; // Change to your database username
// $password = ""; // Change to your database password

$host = "sdb-81.hosting.stackcp.net"; // Change to your database host
$database = "jumandigas-353038374f79"; // Change to your database name
$user = "jumandigas"; // Change to your database username
$password = "ks2bs8a8ak"; // Change to your database password


// Create database connection
$conn = new mysqli($host, $user, $password, $database);

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get authorization header
$headers = getallheaders();
$auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$auth || strpos($auth, 'Bearer ') !== 0) {
    // No token provided
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Authentication token required."]);
    exit();
}

$token = substr($auth, 7); // Remove 'Bearer ' from the string

// Verify token and get user details
$sql = "SELECT * FROM users WHERE api_token = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit();
}

$user_id = $user['id'];

// Handle GET request - Return user profile data
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get user's full profile
    $profile_sql = "SELECT id, full_name, email, phone, address, country, state, city, currency, balance, created_at FROM users WHERE id = ?";
    $profile_stmt = $conn->prepare($profile_sql);
    if (!$profile_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    $profile_stmt->bind_param("i", $user_id);
    $profile_stmt->execute();
    $profile_result = $profile_stmt->get_result();
    $profile = $profile_result->fetch_assoc();
    
    if (!$profile) {
        echo json_encode(["status" => "error", "message" => "User profile not found"]);
        exit();
    }
    
    // Get countries, states, and cities for dropdowns
    $countries = ["nigeria", "ghana"];
    
    $states = [
        "nigeria" => ["Lagos", "Abuja", "Kano"],
        "ghana" => ["Accra", "Kumasi", "Takoradi"]
    ];
    
    $cities = [
        "Lagos" => ["Ikeja", "Lekki", "Victoria Island"],
        "Abuja" => ["Garki", "Maitama", "Wuse"],
        "Kano" => ["Fagge", "Gwale", "Kumbotso"],
        "Accra" => ["Osu", "Madina", "East Legon"],
        "Kumasi" => ["Asafo", "Bantama", "Suame"],
        "Takoradi" => ["Sekondi", "Anaji", "Airport Ridge"]
    ];
    
    // Return user profile data
    echo json_encode([
        "status" => "success",
        "profile" => $profile,
        "dropdown_data" => [
            "countries" => $countries,
            "states" => $states,
            "cities" => $cities
        ]
    ]);
    
    exit();
}

// Handle PUT request - Update user profile
if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON data
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Invalid request data"]);
        exit();
    }
    
    // Fields that can be updated
    $updateable_fields = [
        'full_name' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'country' => 'string',
        'state' => 'string',
        'city' => 'string'
    ];
    
    // Build update query
    $update_fields = [];
    $param_types = "";
    $param_values = [];
    
    foreach ($updateable_fields as $field => $type) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $update_fields[] = "$field = ?";
            $param_types .= "s"; // All are strings
            $param_values[] = $data[$field];
        }
    }
    
    // If password is provided, update it
    if (isset($data['password']) && !empty($data['password'])) {
        $update_fields[] = "password = ?";
        $param_types .= "s";
        $param_values[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    
    // If no fields to update
    if (empty($update_fields)) {
        echo json_encode(["status" => "error", "message" => "No fields to update"]);
        exit();
    }
    
    // Add user_id to param values
    $param_types .= "i";
    $param_values[] = $user_id;
    
    // Prepare and execute update query
    $update_sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }
    
    // Bind parameters dynamically
    $bind_params = array_merge([$param_types], $param_values);
    $bind_params_refs = [];
    foreach ($bind_params as $key => $value) {
        $bind_params_refs[$key] = &$bind_params[$key];
    }
    
    call_user_func_array([$update_stmt, 'bind_param'], $bind_params_refs);
    $update_result = $update_stmt->execute();
    
    if ($update_result) {
        // Get updated profile
        $profile_sql = "SELECT id, full_name, email, phone, address, country, state, city, currency, balance, created_at FROM users WHERE id = ?";
        $profile_stmt = $conn->prepare($profile_sql);
        $profile_stmt->bind_param("i", $user_id);
        $profile_stmt->execute();
        $profile_result = $profile_stmt->get_result();
        $updated_profile = $profile_result->fetch_assoc();
        
        // Update user data in shared preferences
        echo json_encode([
            "status" => "success",
            "message" => "Profile updated successfully",
            "profile" => $updated_profile
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update profile: " . $update_stmt->error
        ]);
    }
    
    exit();
}

// Close the database connection
$conn->close();
?>

