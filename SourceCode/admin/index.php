<?php
include './templates/nav_admin1.php';
if (empty($_SESSION['admin'])) {
    header('Location: ./pages/Admin_Login.php');
    exit();
}
include './templates/nav_admin2.php';
?>