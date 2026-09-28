<?php
include("config.php");
//tạo mã nhân viên tự động tăng
$maNhanVien = "AD";
$query = "SELECT MAX(CAST(SUBSTRING(nhan_vien.maNhanVien, 4) AS SIGNED)) AS max_id FROM nhan_vien";
$statement = $dbh->prepare($query);
$statement->execute();
$count = $statement->fetchColumn() + 1;
$index = strval($count);
$temp = array("", "0", "00", "000");
$maNhanVien = $maNhanVien . $temp[4 - strlen($index)] . $index;

// các thông tin để tạo nhân viên
$ho = $_POST['ho'];
$ten = $_POST['ten'];
$dienThoai = $_POST['dienThoai'];
$diaChi = $_POST['diaChi'];
$tendn = $_POST['tendn'];
$matKhau = md5($_POST['matKhau']);
$loaiNhanVien = $_POST['loaiNhanVien'];
$email = $_POST['email'];
$ngaySinh = $_POST['ngaySinh'];
$maXa = ((isset($_POST["maXa"])) ? $_POST["maXa"] : "X00001");

//lấy file hình
$errors = array();
$file_ext = @strtolower(end(explode('.', $_FILES['image']['name'])));
$file_name = $maNhanVien . '.' . $file_ext;
$file_size = $_FILES['image']['size'];
$file_tmp = $_FILES['image']['tmp_name'];
$file_type = $_FILES['image']['type'];
if ($file_size > 2097152) {
  $errors[] = 'File size should be 2MB';
}
if (empty($errors) == true) {
  move_uploaded_file($file_tmp, $_SESSION['rootPath'] . "/../assets/img/ad_user/" . $file_name);
} else {
  print_r($errors);
}

//  INSERT INTO `nhan_vien` (`maNhanVien`, `ho`, `ten`, `ngaySinh`, `diaChiCuThe`, `dienThoai`, `maLoai`, `tenNguoiDung`, `matKhau`, `avatar`, `email`, `maXa`) VALUES ('AD0005', 'nguyen', 'tam', '2002-01-08', 'nha tang', '0924494118', 'LTK002', 'letam1', 'qpalqpal', 'letam1.png', 'lenaids@gmail.com', 'X00012'); 

$sql_themNhanVien = "INSERT INTO nhan_vien VALUES ('$maNhanVien','$ho','$ten','$ngaySinh','$diaChi','$dienThoai','$loaiNhanVien','$tendn','$matKhau','$file_name','$email','$maXa');";
$statement = $dbh->prepare($sql_themNhanVien);
$statement->execute();

// đặt xong về trang login
header("Location: ../pages/Admin_DsAdmin.php");

?>