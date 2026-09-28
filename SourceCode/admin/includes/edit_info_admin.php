<?php
include("config.php");
$maNhanVien = $_SESSION['admin']->maNhanVien;
// các thông tin cần cập nhật
$ho = $_POST['ho'];
$ten = $_POST['ten'];
$dienThoai = $_POST['dienThoai'];
$diaChiCuThe = $_POST['diaChi'];
$email = $_POST['email'];
$ngaySinh = $_POST['ngaySinh'];
$maXa = $_POST["maXa"];
var_dump($_FILES['image']);

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
  //lấy file hình
  $errors = array();
  $file_name = $_SESSION['admin']->avatar;
  $file_size = $_FILES['image']['size'];
  $file_tmp = $_FILES['image']['tmp_name'];
  $file_type = $_FILES['image']['type'];
  if ($file_size > 2097152) {
    $errors[] = 'File size should be 2MB';
  }
  if (empty($errors) == true) {
    move_uploaded_file($file_tmp, $_SESSION['rootPath'] . "/../assets/img/sanpham/" . $file_name);
  } else {
    print_r($errors);
  }

  $statement = $dbh->prepare("UPDATE nhan_vien SET ho = '$ho', ten = '$ten', ngaySinh = '$ngaySinh', diaChiCuThe = '$diaChiCuThe', dienThoai = '$dienThoai', email = '$email', maXa = '$maXa', avatar = '$file_name' WHERE nhan_vien.maNhanVien = '$maNhanVien'");
  echo "UPDATE nhan_vien SET ho = '$ho', ten = '$ten', ngaySinh = '$ngaySinh', diaChiCuThe = '$diaChiCuThe', dienThoai = '$dienThoai', email = '$email', maXa = '$maXa', avatar = '$file_name' WHERE nhan_vien.maNhanVien = '$maNhanVien'";
  $statement->execute();

} else {
  $statement = $dbh->prepare("UPDATE nhan_vien SET ho = '$ho', ten = '$ten', ngaySinh = '$ngaySinh', diaChiCuThe = '$diaChiCuThe', dienThoai = '$dienThoai', email = '$email', maXa = '$maXa' WHERE nhan_vien.maNhanVien = '$maNhanVien'");
  echo "UPDATE nhan_vien SET ho = '$ho', ten = '$ten', ngaySinh = '$ngaySinh', diaChiCuThe = '$diaChiCuThe', dienThoai = '$dienThoai', email = '$email', maXa = '$maXa' WHERE nhan_vien.maNhanVien = '$maNhanVien'";
  $statement->execute();
}

$sql = "SELECT * FROM nhan_vien WHERE nhan_vien.maNhanVien = '$maNhanVien'";
$stmt = $dbh->query($sql);
$result = $stmt->fetch(PDO::FETCH_OBJ);
$_SESSION['admin'] = $result;

echo "<script>
alert('Cập nhật thông tin thành công');
</script>";
// edit xong về trang index
echo '<script>window.location.href = "../";</script>';
?>