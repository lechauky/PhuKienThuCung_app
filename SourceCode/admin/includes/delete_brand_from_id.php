<?php
if (isset($_POST["delete"])) {
    $statement = $dbh->prepare("SELECT * FROM `san_pham` WHERE `maThuongHieu` = '" . $brand_id . "'");
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $result = $statement->fetch();

    if ($statement->rowCount() == 0) {
        $statement = $dbh->prepare("DELETE FROM `thuong_hieu` WHERE `maThuongHieu` = '" . $brand_id . "'");

    } else {
        echo "<script>
            alert(\"Thương hiệu đang có sản phẩm kinh doanh\");
        </script>";

    }
    $statement->execute();
    echo '<script>window.location.href = "brand_index.php";</script>';
}
?>