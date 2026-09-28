<?php
$brand_id = $_GET['id'];

$statement = $dbh->prepare("SELECT * FROM `thuong_hieu` WHERE `maThuongHieu` = '" . $brand_id . "'");
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetch();

?>