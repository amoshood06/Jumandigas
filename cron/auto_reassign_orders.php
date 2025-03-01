<?php
require_once "../db/db.php";

// Get orders that have not been accepted within 10 minutes
$sql = "SELECT id, vendor_id FROM orders 
        WHERE status = 'pending' 
        AND TIMESTAMPDIFF(MINUTE, assigned_at, NOW()) >= 10
        AND reassigned = 0";

$stmt = $pdo->query($sql);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as $order) {
    $order_id = $order['id'];

    // Find another available online vendor
    $new_vendor = $pdo->query("SELECT id FROM users WHERE role = 'vendor' AND online_status = 1 ORDER BY RAND() LIMIT 1")
                       ->fetch(PDO::FETCH_ASSOC);

    if ($new_vendor) {
        // Update order with new vendor
        $update = $pdo->prepare("UPDATE orders SET vendor_id = ?, assigned_at = NOW(), reassigned = 1 WHERE id = ?");
        $update->execute([$new_vendor['id'], $order_id]);
    }
}
?>
