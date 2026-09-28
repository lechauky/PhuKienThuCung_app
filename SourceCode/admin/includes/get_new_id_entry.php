<?php
//tạo mã phiếu tự động
$maPhieu = "PN";

$query = "SELECT MAX(CAST(SUBSTRING(`maPhieuNhap`, 3) AS SIGNED)) AS max_id FROM `phieu_nhap`";
$statement = $dbh->prepare($query);
$statement->execute();

// Sử dụng fetchColumn
$count = $statement->fetchColumn() + 1;
if ($count <= 9) {
    $maPhieu .= "000" . $count;
} else if ($count <= 99) {
    $maPhieu .= "00" . $count;
} else if ($count <= 999) {
    $maPhieu .= "0" . $count;
} else {
    $maPhieu .= "" . $count;
}

?>