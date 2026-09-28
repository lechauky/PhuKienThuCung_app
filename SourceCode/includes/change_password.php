<?php
include("config.php");

$maKhachHang = $_SESSION["taiKhoan"]["maKhachHang"];
$newpassword = md5($_POST["ConfirmPassword"]);
// UPDATE `khach_hang` SET `matKhau` = '12341234' WHERE `khach_hang`.`maKhachHang` = 'KH0004';
$sql_change_pass = "UPDATE `khach_hang` SET `matKhau` = '$newpassword' WHERE `khach_hang`.`maKhachHang` = '$maKhachHang'";
$statement = $dbh->prepare($sql_change_pass);
$statement->execute();

// đặt xong về trang chủ
header("Location: ../");

?>