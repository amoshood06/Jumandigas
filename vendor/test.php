<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];

// Fetch orders for this vendor
$sql = "SELECT o.id, o.amount_kg, o.total_price, o.currency, o.cylinder_type, o.tracking_id, 
               o.status, o.vendor_id, o.rider_id, 
               u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        WHERE o.vendor_id = :vendor_id AND u.role = 'user'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['vendor_id' => $vendor_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available riders in the same country, state, and city as the vendor
$sql_riders = "SELECT id, full_name FROM users WHERE role = 'rider' AND country = (SELECT country FROM users WHERE id = :vendor_id) AND state = (SELECT state FROM users WHERE id = :vendor_id) AND city = (SELECT city FROM users WHERE id = :vendor_id)";
$stmt_riders = $pdo->prepare($sql_riders);
$stmt_riders->execute(['vendor_id' => $vendor_id]);
$riders = $stmt_riders->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-4 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Vendor Orders</h2>
        <?php if (isset($_GET['message'])) { ?>
            <p class="text-green-600"><?php echo $_GET['message']; ?></p>
        <?php } elseif (isset($_GET['error'])) { ?>
            <p class="text-red-600"><?php echo $_GET['error']; ?></p>
        <?php } ?>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($orders as $order) { ?>
        <div class="bg-white shadow-md rounded-lg p-4 border">
            <h3 class="text-lg font-bold mb-2">Order #<?php echo $order['id']; ?></h3>
            <p><strong>Customer:</strong> <?php echo $order['customer_name']; ?> (<?php echo $order['customer_phone']; ?>)</p>
            <p><strong>Address:</strong> <?php echo $order['customer_address']; ?></p>
            <p><strong>Amount:</strong> <?php echo $order['amount_kg']; ?> KG</p>
            <p><strong>Total Price:</strong> <?php echo $order['total_price']; ?> <?php echo $order['currency']; ?></p>
            <p><strong>Cylinder Type:</strong> <?php echo $order['cylinder_type']; ?></p>
            <p><strong>Tracking ID:</strong> <?php echo $order['tracking_id']; ?></p>
            <p class="font-bold text-<?php echo ($order['status'] == 'pending') ? 'yellow-500' : (($order['status'] == 'processing') ? 'blue-500' : 'green-500'); ?>">
                <strong>Status:</strong> <?php echo ucfirst($order['status']); ?>
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                <?php if ($order['status'] == 'pending') { ?>
                    <form action="accept_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Accept</button>
                    </form>
                    <form action="reject_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Reject</button>
                    </form>
                <?php } elseif ($order['status'] == 'processing' && $order['rider_id'] == NULL) { ?>
                    <form action="assign_rider.php" method="POST" class="w-full">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="rider_id" class="border px-2 py-1 rounded w-full">
                            <option value="">Select Rider</option>
                            <?php foreach ($riders as $rider) { ?>
                                <option value="<?php echo $rider['id']; ?>"><?php echo $rider['full_name']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 mt-2 rounded w-full">Assign Rider</button>
                    </form>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

    </div>
</body>
</html>
