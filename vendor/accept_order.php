<?php
require_once "../auth_check.php";
require_once "../db_connection.php";

if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $vendor_id = $_SESSION['user_id'];

    // Update order status to processing
    $sql = "UPDATE orders SET status = 'processing' WHERE id = :order_id AND vendor_id = :vendor_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_id' => $order_id, 'vendor_id' => $vendor_id]);

    // Fetch available riders in the same country, state, and city as the vendor
    $sql = "SELECT * FROM users WHERE role = 'rider' AND country = (SELECT country FROM users WHERE id = :vendor_id)
            AND state = (SELECT state FROM users WHERE id = :vendor_id) AND city = (SELECT city FROM users WHERE id = :vendor_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['vendor_id' => $vendor_id]);
    $riders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($stmt->rowCount() == 0) {
        echo "<script>alert('No available riders in your area.'); window.location.href='vendor_dashboard.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Rider</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-4 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Assign Rider</h2>
        <form action="assign_rider.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <label for="rider_id" class="block text-sm font-medium text-gray-700">Select Rider:</label>
            <select name="rider_id" id="rider_id" class="w-full p-2 border rounded mb-4" required>
                <?php foreach ($riders as $rider) { ?>
                    <option value="<?php echo $rider['id']; ?>">
                        <?php echo $rider['full_name'] . " - " . $rider['phone']; ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Assign Rider</button>
        </form>
    </div>
</body>
</html>
