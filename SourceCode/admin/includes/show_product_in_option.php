<?php
//lấy danh sách sản phẩm 
$query = "SELECT * FROM `san_pham` ORDER BY maSanPham ASC";
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();

foreach ($result as $row) {
    echo "<option value=\"" . $row->maSanPham . "\">" . $row->tenSanPham . "</option>";
}
?>