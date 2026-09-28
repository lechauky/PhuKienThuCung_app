<?php
include("config.php");
//tạo mã khách hàng tự động tăng
$maKhachHang = "KH";
$query = "SELECT MAX(CAST(SUBSTRING(khach_hang.maKhachHang, 4) AS SIGNED)) AS max_id FROM khach_hang";
$statement = $dbh->prepare($query);
$statement->execute();
$count = $statement->fetchColumn() + 1;
$index = strval($count);
$temp = array("", "0", "00", "000");
$maKhachHang = $maKhachHang . $temp[4 - strlen($index)] . $index;

// các thông tin để tạo khách hàng
$hoKhachHang = $_POST['hoKhachHang'];
$tenKhachHang = $_POST['tenKhachHang'];
$dienThoai = $_POST['dienThoai'];
$diaChi = $_POST['diaChi'];
$tendn = $_POST['tendn'];
$matKhau = md5($_POST['matKhau']);
$email = $_POST['email'];
$ngaySinh = $_POST['ngaySinh'];
$maXa = ((isset($_POST["maXa"])) ? $_POST["maXa"] : "X00001");

$sql_themKhachHang = "INSERT INTO khach_hang VALUES ('$maKhachHang','$hoKhachHang','$tenKhachHang','$dienThoai','$diaChi','$tendn','$matKhau','$email','$ngaySinh',NULL,NULL,'$maXa');";
$statement = $dbh->prepare($sql_themKhachHang);
$statement->execute();

// đặt xong về trang login
header("Location: ../pages/login.php");

?>