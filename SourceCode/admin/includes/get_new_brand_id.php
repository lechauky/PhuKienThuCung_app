<?php
//tạo mã thương hiệu tự động
$maThuongHieu = "THH";

$query = "SELECT MAX(CAST(SUBSTRING(`maThuongHieu`, 4) AS SIGNED)) AS max_id FROM `thuong_hieu`";
$statement = $dbh->prepare($query);
$statement->execute();

// Sử dụng fetchColumn
$count = $statement->fetchColumn() + 1;
if ($count <= 9) {
    $maThuongHieu .= "00" . $count;
} else if ($count <= 99) {
    $maThuongHieu .= "0" . $count;
} else {
    $maThuongHieu .= "" . $count;
}

?>