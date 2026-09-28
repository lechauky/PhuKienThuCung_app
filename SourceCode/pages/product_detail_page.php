<!--peoduct detail-->
<?php include '../templates/header.php';
require_once('../includes/check_giam_gia.php');
$product_id = $_GET['maSanPham'];
$query = "SELECT  maSanPham,tenSanPham, donGiaBan, maLoai, soLuong, hinhAnh, moTa, tenThuongHieu FROM san_pham JOIN thuong_hieu ON san_pham.maThuongHieu = thuong_hieu.maThuongHieu  WHERE san_pham.maSanPham= '$product_id';";
$stmt = $dbh->prepare($query);
$stmt->execute();
$sanPham = $stmt->fetch(PDO::FETCH_OBJ);
$sql = "SELECT * FROM giam_gia";
$stmt = $dbh->query($sql);
$giamGia = $stmt->fetchAll(PDO::FETCH_OBJ);
if (empty($_SESSION["taiKhoan"])) {
    require_once('../includes/login_required.php');
} else {
    require_once('../includes/ajax_add_product.php');
}
?>

<div class="chiTietSanPham">
    <?php
    if ($sanPham->soLuong != 0) {
        $button = '<button name="submit" style="font-size:20px; color:red; font-weight:bold;" class="button_add_admin" productid="' . $sanPham->maSanPham . '"  onclick="addToCart(this)">Thêm vào giỏ hàng</button>';
    } else {
        $button = '<button name="submit" style="font-size:20px; color:red; font-weight:bold;" value="HẾT HÀNG" disabled>HẾT HÀNG</button>';
    }
    $dongiahienthi = "";
    if (giamGia($sanPham->maSanPham, $giamGia, $sanPham->donGiaBan) != null) {
        $dongiahienthi = "<div class='product_price' style='display: flex'>
    <h5 style='text-decoration: line-through; width: 70px'>" . number_format($sanPham->donGiaBan) . "đ   </h5>     
    <h5 style='color: red;'>   " . number_format(giamGia($sanPham->maSanPham, $giamGia, $sanPham->donGiaBan)) . "đ</h5>
    </div>";
    } else {
        $dongiahienthi = "<div class='product_price'>
    <h5>" . number_format($sanPham->donGiaBan) . "đ</h5>
    </div>";
    }
    echo '
        <div class="chiTietSanPham_left">
            <img src="../assets/img/sanpham/' . $sanPham->hinhAnh . ' " style="height:500px; width:500px;">
            <div class="chiTietSanPham_giamgia">Giảm giá</div>
        </div>
        <div class="chiTietSanPham_right">
            <h4>' . $sanPham->tenSanPham . '</h6>
                <p>Tên thương hiệu:
                    ' . $sanPham->tenThuongHieu . '
                </p>
                <div>
                    
                    <h1 style="color:red;">
                    ' . $dongiahienthi . '
                    </h1>
                </div>
                <div>
                    <h3>
                        Số lượng còn: ' . $sanPham->soLuong . '

                    </h3>
                </div>
                
                <div>
                ' . $button . '
                </div>
                <div class="chiTietSanPham_right_mota">
                    <span>Mô tả: </span>
                    ' . $sanPham->moTa . '
                </div>
        </div>';

    ?>
</div>
<?php include '../templates/footer.php' ?>