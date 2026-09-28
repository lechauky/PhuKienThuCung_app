<?php
include("config.php");

$maNhanVien = $_SESSION["admin"]->maNhanVien;
$newpassword = md5($_POST["ConfirmPassword"]);
// UPDATE `nhan_vien` SET `matKhau` = 'xcxcxcxc' WHERE `nhan_vien`.`maNhanVien` = 'AD0003';
$sql_change_pass = "UPDATE `nhan_vien` SET `matKhau` = '$newpassword' WHERE `nhan_vien`.`maNhanVien` = '$maNhanVien'";
$statement = $dbh->prepare($sql_change_pass);
$statement->execute();

echo "<script>
alert('Đổi mật khẩu thành công');
</script>";
// đổi xong về trang chủ
echo '<script>window.location.href = "../";</script>';

?>