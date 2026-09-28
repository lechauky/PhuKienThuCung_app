<?php
if (empty($_POST["baoCao"]) || $flag == false) {
    echo "<tr>
                <td colspan=\"3\">Không có dữ liệu</td>
                </tr>";
} else {
    $stt2 = 1;
    $query = "SELECT DATE_FORMAT(ngayDat, '%m-%Y') AS thoiGian, SUM(tongTien) as doanhThu FROM don_dat_hang
    WHERE tinhTrang = b'01' AND ngayDat <= DATE(CONCAT(" . $namKetThuc . ", '-', " . $thangKetThuc . ", '-', 1)) AND ngayDat >= DATE(CONCAT(" . $namBatDau . ", '-', " . $thangBatDau . ", '-' ,1))
    GROUP BY DATE_FORMAT(ngayDat, '%m-%Y')
    ORDER BY thoiGian;";
    $statement = $dbh->prepare($query);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $result = $statement->fetchAll();
    if ($result) {
        foreach ($result as $row) {
            echo "<tr height=\"40px\">
                            <td><p>" . $stt2 . "</p></td>
                            <td><p>" . $row->thoiGian . "</p></td>
                            <td><p>" . $row->doanhThu . " đ</p></td>
                        </tr>";
            $stt2++;
        }
    } else {
        echo "<tr>
                <td colspan=\"3\">Không có dữ liệu</td>
                </tr>";
    }
}
?>