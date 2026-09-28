<?php
if (isset($_POST["delete"])) {
    // echo $product_id;
    // $statement = $dbh->prepare("SELECT * FROM `chi_tiet_don_dat_hang` WHERE `maSanPham` = '" . $product_id . "'");
    // $statement->execute();
    // $statement->setFetchMode(PDO::FETCH_OBJ);
    // $result = $statement->fetch();

    // if ($statement->rowCount() == 0) {
    //     $statement = $dbh->prepare("SELECT * FROM `chi_tiet_phieu_nhap` WHERE `maSanPham` = '" . $product_id . "'");
    //     $statement->execute();
    //     $statement->setFetchMode(PDO::FETCH_OBJ);
    //     $result = $statement->fetch();

    //     if ($statement->rowCount() == 0) {
    //         $statement = $dbh->prepare("DELETE FROM `san_pham` WHERE `maSanPham` = '" . $product_id . "'");
    //         $result=$statement->execute();
    //     } else {
    //         echo "<script>
    //         alert(\"Sản phẩm đang có sản phẩm kinh doanh\");
    //     </script>";
    //     }

    // } else {
    //     echo "<script>
    //         alert(\"Sản phẩm đang có sản phẩm kinh doanh\");
    //     </script>";

    // }
    // echo '<script>window.location.href = "product_index.php";</script>';
    
    try {
        $statement = $dbh->prepare("DELETE FROM `san_pham` WHERE `maSanPham` = '" . $product_id . "'");
        $result=$statement->execute();
        echo "<script>
          alert(\"Xóa sản phẩm thành công\");
        </script>";
    } catch (PDOException $e) {
        echo "<script>
          alert(\"Sản phẩm đang có sản phẩm kinh doanh\");
        </script>";
        
    }
    echo '<script>window.location.href = "product_index.php";</script>';
}
?>