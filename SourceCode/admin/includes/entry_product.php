<?php
include 'config.php';
try {
    $maNV = $_SESSION['admin']->maNhanVien;
    // Get the data from AJAX
    $maPhieuNK = $_POST["maPhieuNK"];
    $ngayNK = $_POST["ngayNK"];
    $maSanPhams = $_POST["maSanPhams"];
    $soLuongs = $_POST["soLuongs"];
    $donGias = $_POST["donGias"];

    // Check if the data is null
    if ($maPhieuNK == null || $ngayNK == null || $maSanPhams == null || $soLuongs == null || $donGias == null) {
        // Handle the error
        exit('Invalid data');
    } else {
        try {
            $statement = $dbh->prepare("INSERT INTO `phieu_nhap`(`maPhieuNhap`, `ngayNhap`, `maNhanVien`) VALUES ('" . $maPhieuNK . "','" . $ngayNK . "','" . $maNV . "')");
            $statement->execute();

            for ($i = 0; $i < sizeof($maSanPhams); $i++) {
                $statement = $dbh->prepare("INSERT INTO `chi_tiet_phieu_nhap`(`maPhieuNhap`, `maSanPham`, `soLuong`, `donGia`) VALUES ('" . $maPhieuNK . "','" . $maSanPhams[$i] . "','" . $soLuongs[$i] . "','" . $donGias[$i] . "')");
                $statement->execute();
                //cập nhật giá bán và số lượng ở bản sản phẩm
                $statement = $dbh->prepare("UPDATE san_pham SET donGiaMua = '" . $donGias[$i] . "', soLuong = soLuong + " . $soLuongs[$i] . " WHERE maSanPham= '" . $maSanPhams[$i] . "'");
                $statement->execute();
            }
        } catch (Exception $e) {
            echo 'Có lỗi khi tạo phiếu nhập hàng: ';
            echo $e->getMessage();
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>