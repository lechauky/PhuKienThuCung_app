<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maLoai = $_POST["maLoai"];
    $tenLoai = $_POST["tenLoai"];

    $statement = $dbh->prepare("INSERT INTO `loai_san_pham`(`maLoai`, `tenLoai`) VALUES ('" . $maLoai . "','" . $tenLoai . "')");
    if ($statement->execute()) {
        echo "<script>alert(\"Thêm loại sản phẩm thành công\")</script>";
    } else {
        echo "<script>alert(\"Có lỗi xảy ra khi thêm\")</script>";
    }

    echo '<script>window.location.href = "javascript:history.go(-2);";</script>';
} else {
}
?>