<?php
include 'config.php';
if (isset($_POST["save"])) {
    $maLoai = $_POST["maLoai"];
    $tenLoai = $_POST["tenLoai"];

    $statement = $dbh->prepare("UPDATE `loai_san_pham` SET `tenLoai`='" . $tenLoai . "' WHERE `maLoai` = '" . $maLoai . "'");
    if ($statement->execute()) {
        echo "<script>alert(\"Cập nhật thành công\")</script>";
    } else {
        echo "<script>alert(\"Có lỗi xảy ra khi cập nhật\")</script>";
    }

    echo '<script>window.location.href = "javascript:history.go(-2);";</script>';
}
?>