<?php
require_once "auth_admin.php";  // Authentication check for admin
require_once "../db/db.php";  // Database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $country = $_POST['country'];
    $state = $_POST['state'];
    $gas = $_POST['gas'];
    $price = $_POST['price'];
    $currency = $_POST['currency'];

    // Handle image upload
    if (isset($_FILES['gas_image']) && $_FILES['gas_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['gas_image']['tmp_name'];  // Temporary file path
        $imageName = $_FILES['gas_image']['name'];  // Original image name
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);  // Get the image extension

        // Generate a unique name for the image to avoid overwriting
        $newImageName = uniqid('gas_', true) . '.' . $imageExtension;
        $imageUploadPath = '../uploads/gas_images/' . $newImageName;  // Define the upload path

        // Create uploads directory if it doesn't exist
        if (!is_dir('../uploads/gas_images')) {
            mkdir('../uploads/gas_images', 0777, true);
        }

        // Move the uploaded image to the designated folder
        if (move_uploaded_file($imageTmpName, $imageUploadPath)) {
            // Insert data into the database, including the image path
            $stmt = $pdo->prepare("INSERT INTO locations (country, state, gas, price, currency, gas_image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$country, $state, $gas, $price, $currency, $imageUploadPath]);

            // Send a JSON response indicating success
            echo json_encode(["status" => "success", "message" => "Location added successfully!"]);
        } else {
            // Send a JSON response indicating failure
            echo json_encode(["status" => "error", "message" => "Image upload failed. Please try again."]);
        }
    } else {
        // Send a JSON response indicating no image or failed upload
        echo json_encode(["status" => "error", "message" => "No image uploaded or there was an error with the image upload."]);
    }
}
?>
