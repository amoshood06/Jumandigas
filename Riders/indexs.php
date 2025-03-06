<?php
require_once "../auth_check.php";
require_once "../db/db.php";

if ($_SESSION['role'] != 'rider') {
    header("Location: ../login.php");
    exit();
}

?>
