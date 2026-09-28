<?php include '../templates/nav_admin1.php';
$nhanVien_id = $_GET['maNhanVien'];
$sql = "SELECT *, tenLoai FROM nhan_vien JOIN loai_tai_khoan ON nhan_vien.maLoai = loai_tai_khoan.maLoai WHERE maNhanVien ='$nhanVien_id'";
$statement = $dbh->prepare($sql);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetch();
if (isset($_POST["delete"])) {
    $maNhanVienXoa = $_POST["delete"];
    $checkQuery = "SELECT COUNT(*) FROM don_dat_hang WHERE maNhanVien = '$maNhanVienXoa'";
    $checkStatement = $dbh->prepare($checkQuery);
    $checkStatement->execute();
    $count = $checkStatement->fetchColumn();

    if ($count == 0) {
        $updateQuery = "DELETE FROM nhan_vien WHERE maNhanVien = '$maNhanVienXoa'";
        $updateStatement = $dbh->prepare($updateQuery);
        $updateStatement->execute();
        echo "Xóa nhân viên thành công!";
        echo '<script>window.location.href = "Admin_DsAdmin.php";</script>';
    } else {
        echo "Không thể xóa nhân viên vì có đơn đặt hàng liên quan.";
    }

}
$query_tenloai = "SELECT maLoai, tenLoai FROM loai_tai_khoan";
$statement = $dbh->prepare($query_tenloai);
$statement->execute();
$loaitaikhoan = $statement->fetchAll(PDO::FETCH_OBJ);
?>

<div class="detail_admin">
    <h1 class="Title_Admin_create_form">Bạn có muốn xóa tài khoản này ?</h1>
    <form action="" Method="POST">
        <div class="detai_admin_form">
            <div class="detail_admin_left">
                <img src="../../assets/img/ad_user/<?php echo $result->avatar ?>" alt="">
            </div>
            <div class="detail_admin_right">
                <table class="Table_Details_Admin">
                    <tr>
                        <td>Mã Admin: </td>
                        <td>
                            <?php echo $result->maNhanVien; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Họ tên Admin:</td>
                        <td>
                            <?php echo $result->ho . " " . $result->ten; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Địa chỉ:</td>
                        <td>
                            <?php echo $result->diaChiCuThe; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Số điện thoại :</td>
                        <td>
                            <?php echo $result->dienThoai; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Email :</td>
                        <td>
                            <?php echo $result->email; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Loại tài khoản :</td>
                        <td>
                            <?php echo $result->tenLoai; ?>
                        </td>
                    </tr>
                </table>

            </div>

        </div>
        <div class="button">
            <input type="button" value="Xóa" class="button_add_admin delete_display_alert" />
            <a href="javascript:history.go(-1);"><input type="button" value="Quay lại" class="button_add_admin" /></a>
        </div>
        <div class="alert_delete">
            <form action="" method="POST">
                <div class="notification" style="width:25%">
                    <h1 class="notification_title">Xác nhận xóa nhân viên này!</h1>
                    <button type="submit" value="<?php echo $result->maNhanVien ?>"
                        class="alert_delete_btn delete_conform" name="delete">Xóa</button>
                    <button type="button" value="Không" class="alert_delete_btn delete_cancel">Không</button>
                </div>
            </form>
        </div>
    </form>
</div>
<script>
    const load = document.querySelector.bind(document);
    const alert_delete_btn = load(".delete_display_alert");
    const alert_delete_conform_btn = load(".delete_conform");
    const alert_delete_cancel_btn = load(".delete_cancel");
    const alert_delete = load(".alert_delete");
    alert_delete_btn.onclick = () => {
        alert_delete.style.display = "block";
    };
    alert_delete_cancel_btn.onclick = () => {
        alert_delete.style.display = "none";
    };

</script>
<?php include '../templates/nav_admin2.php' ?>