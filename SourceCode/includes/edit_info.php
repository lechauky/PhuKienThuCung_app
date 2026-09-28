<!-- UPDATE `khach_hang` SET `hoKhachHang` = 'lee', `tenKhachHang` = 'tamm', `dienThoai` = '0924494118', `diaChiCuThe` = 'nha trangh', `email` = 'letam2002pk@gmail.comm', `ngaySinh` = '2002-06-11', `maXa` = 'X04035' WHERE `khach_hang`.`maKhachHang` = 'KH0004'; -->
<?php
include("config.php");
$maKhachHang = $_SESSION['taiKhoan']['maKhachHang'];
// các thông tin cần cập nhật
$hoKhachHang = $_POST['hoKhachHang'];
$tenKhachHang = $_POST['tenKhachHang'];
$dienThoai = $_POST['dienThoai'];
$diaChiCuThe = $_POST['diaChi'];
$email = $_POST['email'];
$ngaySinh = $_POST['ngaySinh'];
$maXa = $_POST["maXa"];

$sql_edit_info = "UPDATE `khach_hang` SET `hoKhachHang` = '$hoKhachHang', `tenKhachHang` = '$tenKhachHang', `dienThoai` = '$dienThoai', `diaChiCuThe` = '$diaChiCuThe', `email` = '$email', `ngaySinh` = '$ngaySinh', `maXa` = '$maXa' WHERE `khach_hang`.`maKhachHang` = '$maKhachHang'";
$statement = $dbh->prepare($sql_edit_info);
$statement->execute();

$sql = "SELECT * FROM khach_hang WHERE `khach_hang`.`maKhachHang` = '$maKhachHang'";
$stmt = $dbh->query($sql);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['taiKhoan'] = $result;

// đặt xong về trang login
header("Location: ../");

?>