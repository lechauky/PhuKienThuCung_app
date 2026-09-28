<?php include '../templates/header.php' ?>
<?php
if ($_SESSION['gioHang']==0) {
    echo "<script>
    alert('Giỏ hàng trống, vui lòng chọn sản phẩm!!');
    window.location.href = '$rootPath/pages/product_page.php';
</script>";
}
$maKhachHang = $_SESSION['taiKhoan']['maKhachHang'];
$maXa = $_SESSION['taiKhoan']['maXa'];
$statement = $dbh->prepare(
    "SELECT 
        gio_hang.soLuong, 
        san_pham.maSanPham, 
        san_pham.tenSanPham, 
        san_pham.donGiaBan, 
        san_pham.hinhAnh, 
        thuong_hieu.tenThuongHieu, 
        IFNULL(giam_gia.maLoai, 0) AS maLoai, 
        IFNULL(
            CASE 
                WHEN giam_gia.ngayBatDau <= CURDATE() AND giam_gia.ngayKetThuc >= CURDATE() THEN giam_gia.giaTriGiam 
                ELSE 0 
            END, 
            0
        ) AS giaTriGiam 
    FROM gio_hang 
    JOIN san_pham ON gio_hang.maSanPham = san_pham.maSanPham 
    JOIN thuong_hieu ON thuong_hieu.maThuongHieu = san_pham.maThuongHieu 
    LEFT JOIN giam_gia ON san_pham.maSanPham = giam_gia.maSanPham 
    WHERE gio_hang.maKhachHang = '" . $maKhachHang . "'"
);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$get_dia_chi = $dbh->prepare(
    "SELECT xa.tenXa, huyen.tenHuyen, tinh.tenTinh 
    FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN	tinh ON huyen.maTinh = tinh.maTinh 
    WHERE xa.maXa= '$maXa'"
);

$get_dia_chi->execute();
$get_dia_chi->setFetchMode(PDO::FETCH_OBJ);

?>
<div class="thanhToan">
    <div class="thanhToan_user">
        <form action="<?php echo $rootPath; ?>/includes/process_order.php" method="post">
            <h1 class="Title_Admin_create_form">Thông tin đơn đặt hàng</h1>
            <p class="Notification_create_form">Vui lòng kiểm tra thông tin nhận hàng</p>
            <div style="display: flex;">
                <div class="form_field" style="margin-right: 20px; ">
                    <label for="" class="name_form_field">Họ tên: </label>
                    <input disabled type="text"
                        value="<?php echo $_SESSION['taiKhoan']['hoKhachHang'] . ' ' . $_SESSION['taiKhoan']['tenKhachHang']; ?>"
                        class="textfile" id="fullname" style="width: 400px; border-radius: 10px;">
                    <span class="error_message"></span>
                </div>
                <div class="form_field">
                    <label for="" class="name_form_field">Số điện thoại: </label>
                    <input disabled type="text" value="<?php echo $_SESSION['taiKhoan']['dienThoai']; ?>"
                        class="textfile" id="phoneNumber" style="width: 180px;  border-radius: 10px;">
                    <span class="error_message"></span>
                </div>
            </div>

            <div class="form_field">
                <label for="" class="name_form_field">Email: </label>
                <input disabled type="text" value="<?php echo $_SESSION['taiKhoan']['email']; ?>" class="textfile"
                    id="email" style="width: 600px; border-radius: 10px;">
                <span class="error_message"></span>
            </div>
            <div class="form_field">
                <label for="" class="name_form_field">Địa chỉ: </label>
                <input disabled type="text"
                    value="<?php
                    $diachi = $get_dia_chi->fetch();
                    echo $_SESSION['taiKhoan']['diaChiCuThe'] . ', ' . $diachi->tenXa . ', ' . $diachi->tenHuyen . ', ' . $diachi->tenTinh; ?>"
                    class="textfile" id="diaChi" style="width: 600px; border-radius: 10px;">
                <span class="error_message"></span>
            </div>
            <div hidden>
                <input name="tongTien" type="text" value="<?php echo $_POST['thanhTienInput']; ?>">
            </div>
            <div class="button">
                <input type="submit" value="Đặt hàng" class="button_add_admin" style="width: 200px;" />
                <a href="./cart.php"> <input type="button" value="Quay trở lại giỏ hàng" class="button_add_admin"
                        style="width: 200px; " /></a>
            </div>
        </form>
    </div>
    <div class="thanhToan_cart" style="width: 50%;">
        <h1 class="Title_Admin_create_form" style="font-size: 26px;">Thông tin chi tiết đơn hàng</h1>
        <div style="width: 100%; display: inline-flex;">
            <b style="width: 60%; padding-left: 20%;">Sản phẩm</b>
            <b style="width: 20%">Số lượng</b>
            <b style="width: 20%">Thành tiền</b>
        </div>
        <?php
        while ($row = $statement->fetch()) {
            echo '<div class="thanhToan_cart_list_item">
                        <div class="body_table_title body_table_title_sanpham" style="width: 60%;">
                            <img src="' . $rootPath . '/assets/img/sanpham/' . trim($row->hinhAnh) . '" alt="" style="width: 64px; height: 66px; margin-right: 20px;">
                            <div class="thanhToan_cart_list_item_title"  style="max-width: 350px;">
                                   ' . $row->tenSanPham . ' 
                            </div>
                        </div>
                        <div class="thanhToan_cart_list_item_soluong" style="min-width: 50px; margin-left: 10px;width: 20%"">
                            ' . $row->soLuong . '
                        </div>
                        <div class="thanhToan_cart_list_item_gia' . $row->maSanPham . '" style="min-width: 100px;width: 20%"">'
                . (($row->maLoai == 1) ? (($row->donGiaBan - $row->giaTriGiam) * $row->soLuong) : (($row->donGiaBan - $row->donGiaBan * $row->giaTriGiam / 100) * $row->soLuong)) .
                ' VNĐ</div>
                    </div>';
        } ?>
        <div class="thanhToan_cart_tong"><b>Tổng tiền: &nbsp;</b>
            <?php echo $_POST['thanhTienInput']; ?> VNĐ
        </div>
    </div>
</div>
<?php include '../templates/footer.php' ?>