<?php
require_once "../auth_check.php"; // Ensure user is authenticated
require_once "../db/db.php"; // Include the database connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: ../login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User not authenticated"]);
        exit;
    }

    $vendor_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);
    $bank = trim($_POST['bank']);
    $account_number = trim($_POST['accountNumber']);

    if ($amount <= 0) {
        echo json_encode(["status" => "error", "message" => "Invalid withdrawal amount"]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$vendor_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "User not found"]);
            exit;
        }

        if ($user['balance'] < $amount) {
            echo json_encode(["status" => "error", "message" => "Insufficient balance"]);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $vendor_id]);

        $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, bank, account_number, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$vendor_id, $amount, $bank, $account_number]);

        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "Withdrawal request submitted"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>
