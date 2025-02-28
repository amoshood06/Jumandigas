<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'rider') {
    header("Location: ../login.php");
    exit();
}

$rider_id = $_SESSION['user_id'];

// Fetch orders assigned to this rider
$sql = "SELECT o.*, u.full_name as customer_name, u.phone as customer_phone, u.address as customer_address 
        FROM orders o 
        JOIN users u ON o.user_id = u.id
        WHERE o.rider_id = :rider_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['rider_id' => $rider_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-4 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Assigned Deliveries</h2>
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
                            <?php if ($order['status'] == 'processing') { ?>
                                <form action="accept_delivery.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Accept Delivery</button>
                                </form>
                            <?php } elseif ($order['status'] == 'out for delivery') { ?>
                                <form action="mark_delivered.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">I Have Delivered</button>
                                </form>
                            <?php } elseif ($order['status'] == 'completed') { ?>
                                <span class="text-gray-500">Order Completed</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
