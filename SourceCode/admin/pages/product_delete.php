<?php
include '../templates/nav_admin1.php';
include '../includes/get_product_data_from_id.php';
include '../includes/delete_product_from_id.php';
?>
<div class="body" style="margin-top: 15px">
    <div class="detail_admin">
        <h1 class="Title_Admin_create_form">Bạn có muốn xóa sản phẩm này ?</h1>
        <form action="" method="post">
            <div class="detai_admin_form">
                <div class="detail_admin_left">
                    <img src="<?php echo $_SESSION['rootPath'] . '/../assets/img/sanpham/' . $result->hinhAnh; ?>"
                        alt="">
                </div>
                <div class="detail_admin_right">
                    <table class="Table_Details_Admin">
                        <tr>
                            <td>Mã sản phẩm: </td>
                            <td>
                                <?php echo $result->maSanPham; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tên sản phẩm:</td>
                            <td>
                                <?php echo $result->tenSanPham; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Thương hiệu :</td>
                            <td>
                                <?php echo $result->tenThuongHieu; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Loại sản phẩm :</td>
                            <td>
                                <?php echo $result->tenLoai; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Mô tả :</td>
                            <td style="max-width: 200px; text-align: justify;">
                                <?php echo $result->moTa; ?>
                            </td>
                        </tr>
                    </table>

                </div>

            </div>
            <div class="button">
                <input type="button" value="Xóa" class="button_add_admin delete_display_alert" />
                <a href="javascript:history.go(-1);"><input type="button" value="Quay lại"
                        class="button_add_admin" /></a>
            </div>
            <div class="alert_delete">
                <div class="notification" style="width:25%">
                    <h1 class="notification_title">Xác nhận xóa sản phẩm này!</h1>
                    <input type="submit" value="Xóa" class="alert_delete_btn delete_conform" name="delete" />
                    <input type="button" value="Không" class="alert_delete_btn delete_cancel" />
                </div>
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
</div>
<?php include '../templates/nav_admin2.php' ?>