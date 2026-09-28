<?php
include("config.php");

$maSanPham = $_POST['maSanPham'];
$maKhachHang = $_POST['maKhachHang'];
$quantityChange = (int) $_POST['quantityChange'];

// Thực hiện cập nhật số lượng sản phẩm
if ($quantityChange != 0) {
    $query = "SELECT gio_hang.soLuong AS SLGH,san_pham.soLuong AS SLSP FROM gio_hang JOIN san_pham ON gio_hang.maSanPham = san_pham.maSanPham  WHERE gio_hang.maSanPham ='$maSanPham'and gio_hang.maKhachHang = '$maKhachHang'";
    $statement = $dbh->prepare($query);
    $success = $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result["SLGH"] + $quantityChange <= $result["SLSP"] && $quantityChange == 1) {
        $query = "UPDATE gio_hang SET soLuong = soLuong + $quantityChange WHERE maSanPham ='$maSanPham' and maKhachHang = '$maKhachHang'";
        $statement = $dbh->prepare($query);
        $success = $statement->execute();
        echo json_encode(1);
    } else if ($quantityChange == -1){
        $query = "UPDATE gio_hang SET soLuong = soLuong + $quantityChange WHERE maSanPham ='$maSanPham' and maKhachHang = '$maKhachHang'";
        $statement = $dbh->prepare($query);
        $success = $statement->execute();
        echo json_encode(2);
    } else {
        echo json_encode(0);
    }
}
?>
