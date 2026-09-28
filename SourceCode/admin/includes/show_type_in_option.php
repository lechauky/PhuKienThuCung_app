<?php
//lấy danh sách sản phẩm 
$query = "SELECT * FROM `loai_san_pham`";
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$type = $statement->fetchAll();
foreach ($type as $row) {
    if ($result->maLoai == $row->maLoai) {
        echo "<option value=\"" . $row->maLoai . "\" selected>" . $row->tenLoai . "</option>";
    } else {
        echo "<option value=\"" . $row->maLoai . "\">" . $row->tenLoai . "</option>";
    }

}
?>