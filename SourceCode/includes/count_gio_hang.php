<?php
include("config.php"); 
$maSanPham = $_POST['maSanPham'];
$nguoiDung = $_POST['nguoiDung'];
$query = "SELECT COUNT(*) as soLuongTG FROM gio_hang WHERE maKhachHang = '$nguoiDung'";
$statement = $dbh->prepare($query);
$success = $statement->execute();
$response = $statement->fetch(PDO::FETCH_ASSOC);
$_SESSION['gioHang'] = $response['soLuongTG'];
echo json_encode($response);
?>