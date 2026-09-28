<?php
include 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $maNhanVien = $_POST["maNhanVien"];
        $ho = $_POST["ho"];
        $ten = $_POST["ten"];
        $dienThoai = $_POST["dienThoai"];
        $loaiTK_ID = $_POST["loaiTaiKhoan"];
        $diaChiCuThe = $_POST["diaChiCuThe"];
        $email = $_POST["email"];
        $matKhau = md5($_POST["matKhau"]);
        $ngaySinh = $_POST["ngaySinh"];
        $maXa = $_POST["maXa"];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

            $errors = array();
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];
            if ($file_size > 2097152) {
                $errors[] = 'File size should be 2MB';
            }
            if (empty($errors) == true) {
                move_uploaded_file($file_tmp, "../../assets/img/ad_user/" . $file_name);
            } else {
                print_r($errors);
            }
            $statement = $dbh->prepare("UPDATE nhan_vien SET ho = '" . $ho . "', ten = '" . $ten . "', matKhau = '" . $matKhau . "', ngaySinh = '" . $ngaySinh . "', diaChiCuThe = '" . $diaChiCuThe . "', dienThoai = '" . $dienThoai . "', maLoai = '" . $loaiTK_ID . "',email = '" . $email . "', maXa = '" . $maXa . "', avatar = '" . $file_name . "' WHERE maNhanVien = '" . $maNhanVien . "'");
            $statement->execute();

        } else {

            $statement = $dbh->prepare("UPDATE nhan_vien SET ho = '" . $ho . "', ten = '" . $ten . "', matKhau = '" . $matKhau . "', ngaySinh = '" . $ngaySinh . "', diaChiCuThe = '" . $diaChiCuThe . "', dienThoai = '" . $dienThoai . "', maLoai = '" . $loaiTK_ID . "', email = '" . $email . "', maXa = '" . $maXa . "'  WHERE maNhanVien = '" . $maNhanVien . "'");
            $statement->execute();
        }
        echo '<script>
            alert("Cập nhật thông tin thành công");
            window.location.href = "../pages/Admin_DsAdmin.php?";
        </script>';
    } catch (Exception $e) {
        echo 'Có lỗi trong quá trình cập nhật thông tin' . $e->getMessage();
    }
}
?>