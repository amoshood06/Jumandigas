<?php
require_once "../auth_check.php";
if ($_SESSION['role'] != 'rider') {
    header("Location: ../login.php");
    exit();
}
?>
