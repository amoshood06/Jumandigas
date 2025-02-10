<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);

$allowed_pages = [
    "user" => ["index.php"],
    "vendor" => ["index.php"],
    "rider" => ["index.php"]
];

if (!in_array($current_page, $allowed_pages[$role])) {
    echo "Access Denied!";
    exit();
}
?>
