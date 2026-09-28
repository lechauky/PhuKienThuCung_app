<?php
include("config.php"); 
$maKhachHang = $_POST['maKhachHang'];
$query = "SELECT COUNT(*) as soLuongTG FROM gio_hang WHERE maKhachHang = '$maKhachHang'";
$statement = $dbh->prepare($query);
$success = $statement->execute();
$response = $statement->fetch(PDO::FETCH_ASSOC);
$_SESSION['gioHang'] = $response['soLuongTG'];
echo $response['soLuongTG'];
?>