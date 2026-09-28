<?php
include("config.php");
$maSanPham = $_POST['maSanPham'];
$nguoiDung = $_POST['nguoiDung'];
$query = "SELECT * FROM gio_hang WHERE maSanPham = '$maSanPham' AND maKhachHang = '$nguoiDung'";
$statement = $dbh->prepare($query);
$success = $statement->execute();
$sanPham = $statement->fetchAll(PDO::FETCH_ASSOC);
$query = "SELECT gio_hang.soLuong AS SLGH,san_pham.soLuong AS SLSP FROM gio_hang JOIN san_pham ON gio_hang.maSanPham = san_pham.maSanPham  
    WHERE gio_hang.maSanPham ='$maSanPham'and gio_hang.maKhachHang = '$nguoiDung'";
 $stmt = $dbh->prepare($query);
 $m = $stmt->execute();
 $result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($statement->rowCount() > 0) {
    if ($result["SLGH"] + 1 <= $result["SLSP"]) {
        $query = "UPDATE gio_hang SET soLuong = soLuong + 1 WHERE maSanPham ='$maSanPham' and maKhachHang = '$nguoiDung'";
        $statement = $dbh->prepare($query);
        $success = $statement->execute();
        echo json_encode(1);
    } else {
        echo json_encode(0);
    }
} else {
    $query = "INSERT INTO gio_hang(maKhachHang, maSanPham, soLuong)
        VALUES ('$nguoiDung', '$maSanPham', 1)";
    $statement = $dbh->prepare($query);
    $success = $statement->execute();
    echo json_encode(1);
}

?>