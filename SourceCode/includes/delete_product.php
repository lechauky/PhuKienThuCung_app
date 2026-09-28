<?php
include("config.php");
$maSanPham = $_POST['maSanPham'];
$maKhachHang = $_POST['maKhachHang'];
// DELETE FROM gio_hang WHERE `gio_hang`.`maKhachHang` = 'KH0001' AND `gio_hang`.`maSanPham` = 'SP0002'
    $query = "DELETE FROM gio_hang WHERE maSanPham ='$maSanPham' and maKhachHang = '$maKhachHang'";
    $statement = $dbh->prepare($query);
    $success = $statement->execute();
?>