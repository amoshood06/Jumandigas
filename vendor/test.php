<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}

$vendor_id = $_SESSION['user_id'];

// Fetch orders for this vendor
$sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
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
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Order ID</th>
                    <th class="border px-4 py-2">Customer</th>
                    <th class="border px-4 py-2">Address</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo $order['id']; ?></td>
                        <td class="border px-4 py-2"><?php echo $order['customer_name']; ?> (<?php echo $order['customer_phone']; ?>)</td>
                        <td class="border px-4 py-2"><?php echo $order['customer_address']; ?></td>
                        <td class="border px-4 py-2"><?php echo ucfirst($order['status']); ?></td>
                        <td class="border px-4 py-2">
                            <?php if ($order['status'] == 'pending') { ?>
                                <form action="accept_order.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Accept</button>
                                </form>
                            <?php } elseif ($order['status'] == 'processing' && $order['rider_id'] == NULL) { ?>
                                <form action="assign_rider.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="rider_id" class="border px-2 py-1 rounded">
                                        <option value="">Select Rider</option>
                                        <?php foreach ($riders as $rider) { ?>
                                            <option value="<?php echo $rider['id']; ?>"><?php echo $rider['full_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Assign Rider</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
