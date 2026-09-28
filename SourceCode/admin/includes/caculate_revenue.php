<?php
if (empty($_POST["baoCao"]) || $flag == false) {
    echo "0Đ";
} else {
    $query = "SELECT SUM(tongTien) as doanhThu
    FROM don_dat_hang
    WHERE tinhTrang = b'01' AND ngayDat <= DATE(CONCAT(" . $namKetThuc . ", '-', " . $thangKetThuc . ", '-', 1)) AND ngayDat >= DATE(CONCAT(" . $namBatDau . ", '-', " . $thangBatDau . ", '-' ,1))";
    $statement = $dbh->prepare($query);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $result = $statement->fetch();
    if ($result->doanhThu) {
        echo $result->doanhThu . "Đ";
    } else {
        echo "Không có dữ liệu</td>
                ";
    }
}
?>