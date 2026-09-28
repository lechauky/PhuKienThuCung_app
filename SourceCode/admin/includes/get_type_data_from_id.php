<?php
$type_id = $_GET['id'];

$statement = $dbh->prepare("SELECT *  FROM loai_san_pham WHERE `maLoai` = '" . $type_id . "'");
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetch();

?>