<?php
$product_id = $_GET['id'];

$statement = $dbh->prepare("SELECT *, tenThuongHieu, tenLoai FROM `san_pham`
JOIN thuong_hieu on thuong_hieu.maThuongHieu = san_pham.maThuongHieu
JOIN loai_san_pham on loai_san_pham.maLoai = san_pham.maLoai 
WHERE `maSanPham` = '" . $product_id . "'");
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetch();

?>