<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maSanPham = $_POST["MASP"];
    $tenSanPham = $_POST["TENSP"];
    $donGiaMua = 0;
    $donGiaBan = $_POST["DONGIABAN"];
    $maThuongHieu = $_POST["MATH"];
    $maLoai = $_POST["MALOAI"];
    $soLuong = 0;
    $desciption = $_POST["MOTA"];

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
            move_uploaded_file($file_tmp, $_SESSION['rootPath'] . "/../assets/img/sanpham/" . $file_name);
        } else {
            print_r($errors);
        }

        $query = "INSERT INTO `san_pham`(`maSanPham`, `tenSanPham`, `donGiaMua`, `donGiaBan`, `maThuongHieu`, `maLoai`, `soLuong`, `hinhAnh`, `moTa`) 
        VALUES('" . $maSanPham . "', '" . $tenSanPham . "', " . $donGiaMua . ", '" . $donGiaBan . "', '" . $maThuongHieu . "', '" . $maLoai . "', " . $soLuong . ", '" . $file_name . "', '" . $desciption . "')";
        $statement = $dbh->prepare($query);
        if ($statement->execute()) {
            echo "<script>alert(\"Thêm loại sản phẩm thành công\")</script>";
        } else {
            echo "<script>alert(\"Có lỗi xảy ra khi thêm\")</script>";
        }
        // echo $query;
    } else {
        echo "<script>alert(\"Có lỗi xảy ra khi thêm\")</script>";
    }

    echo '<script>window.location.href = "javascript:history.go(-2);";</script>';
} else {
}
?>