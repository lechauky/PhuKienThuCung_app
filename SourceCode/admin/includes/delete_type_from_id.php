<?php
if (isset($_POST["delete"])) {
    $statement = $dbh->prepare("SELECT * FROM `san_pham` WHERE `maLoai` = '" . $type_id . "'");
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $result = $statement->fetch();

    if ($statement->rowCount() == 0) {
        $statement = $dbh->prepare("DELETE FROM `loai_san_pham` WHERE `maLoai` = '" . $type_id . "'");
        $statement->execute();

    } else {
        echo "<script>
            alert(\"Loại sản phẩm này đang kinh doanh\");
        </script>";

    }
    echo '<script>window.location.href = "type_index.php";</script>';
}
?>