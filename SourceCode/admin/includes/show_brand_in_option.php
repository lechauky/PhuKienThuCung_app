<?php
//lấy danh sách sản phẩm 
$query = "SELECT * FROM `thuong_hieu`";
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$brand = $statement->fetchAll();
foreach ($brand as $row) {
    if ($result->maThuongHieu == $row->maThuongHieu) {
        echo "<option value=\"" . $row->maThuongHieu . "\" selected>" . $row->tenThuongHieu . "</option>";
    } else {
        echo "<option value=\"" . $row->maThuongHieu . "\">" . $row->tenThuongHieu . "</option>";
    }

}
?>