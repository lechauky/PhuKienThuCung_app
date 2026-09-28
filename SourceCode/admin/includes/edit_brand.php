<?php
include 'config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $brand_id = $_POST["maTH"];
        $brand_name = $_POST["TENTH"];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
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

            $statement = $dbh->prepare("UPDATE thuong_hieu SET tenThuongHieu = '" . $brand_name . "', logo = '" . $file_name . "' WHERE maThuongHieu = '" . $brand_id . "'");
            $statement->execute();

        } else {
            $statement = $dbh->prepare("UPDATE thuong_hieu SET tenThuongHieu = '" . $brand_name . "' WHERE maThuongHieu = '" . $brand_id . "'");
            $statement->execute();
        }
        echo '<script>
        alert("Cập nhật thông tin thành công");
        window.location.href = "../pages/brand_index.php?";
    </script>';
    } catch (Exception $e) {
        echo 'Có lỗi trong quá trình cập nhật thông tin' . $e->getMessage();
    }
}
?>