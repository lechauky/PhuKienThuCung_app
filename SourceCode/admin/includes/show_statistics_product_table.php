<?php
if (empty($_POST["baoCao"]) || $flag == false) {
    echo "<tr>
                <td colspan=\"7\">Không có dữ liệu</td>
                </tr>";
} else {
    $stt1 = 1;
    $query = "SELECT don_dat_hang.maDonHang, ngayDat, tenSanPham, chi_tiet_don_dat_hang.soLuong, chi_tiet_don_dat_hang.donGia, chi_tiet_don_dat_hang.thanhTien FROM don_dat_hang
    JOIN chi_tiet_don_dat_hang on chi_tiet_don_dat_hang.maDonHang = don_dat_hang.maDonHang
    JOIN san_pham ON san_pham.maSanPham = chi_tiet_don_dat_hang.maSanPham
    WHERE tinhTrang = b'01' AND ngayDat <= DATE(CONCAT(" . $namKetThuc . ", '-', " . $thangKetThuc . ", '-', 1)) AND ngayDat >= DATE(CONCAT(" . $namBatDau . ", '-', " . $thangBatDau . ", '-' ,1))
    GROUP BY don_dat_hang.maDonHang
    ORDER BY tenSanPham";
    $statement = $dbh->prepare($query);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $result = $statement->fetchAll();
    if ($result) {
        foreach ($result as $row) {
            echo "<tr>
                            <td><p>" . $stt1 . "</p></td>
                            <td><p>" . $row->maDonHang . "</p></td>
                            <td><p>" . $row->ngayDat . "</p></td>
                            <td><p>" . $row->tenSanPham . "</p></td>
                            <td><p>" . $row->soLuong . "</p></td>
                            <td><p>" . $row->donGia . "</p></td>
                            <td><p>" . $row->thanhTien . " đ</p></td>
                        </tr>";
            $stt1++;
        }

    } else {
        echo "<tr>
                <td colspan=\"7\">Không có dữ liệu</td>
                </tr>";
    }
}
?>