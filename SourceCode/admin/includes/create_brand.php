<?php
include 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $brand_id = $_POST["MATH"];
        $brand_name = $_POST["TENTH"];
        //lấy file hình
        $errors = array();
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        if ($file_size > 2097152) {
            $errors[] = 'File size should be 2MB';
        }
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $_SESSION['rootPath'] . "/../assets/img/thuong_hieu/" . $file_name);
        } else {
            print_r($errors);
        }

        $statement = $dbh->prepare("INSERT INTO `thuong_hieu`(`maThuongHieu`, `tenThuongHieu`, `logo`) VALUES ('" . $brand_id . "','" . $brand_name . "','" . $file_name . "')");
        $statement->execute();

        echo '<script>
            alert("Thêm thông tin thành công");
            window.location.href = "../pages/brand_index.php?";
        </script>';
    } catch (Exception $e) {
        echo 'Có lỗi trong quá trình thêm thông tin' . $e->getMessage();
        echo "<script>alert(\"Lỗi\")</script>";
    }
}
?>