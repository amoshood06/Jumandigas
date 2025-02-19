<?php
require_once "../auth_check.php";
if ($_SESSION['role'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}
?>
