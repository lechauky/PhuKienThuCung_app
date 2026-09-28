<?php
include '../templates/nav_admin1.php';
//lấy dữ liệu từ id
include '../includes/get_brand_data_from_id.php';
//xử lý xóa thương hiệu
include '../includes/delete_brand_from_id.php';
?>
<div class="body" style="margin-top: 15px">
    <div class="detail_admin">
        <h1 class="Title_Admin_create_form">BẠN CÓ MUỐN XÓA THƯƠNG HIỆU NÀY?</h1>
        <form action="" method="post">
            <div class="detai_admin_form">
                <div class="detail_thuonghieu_left">
                    <img src="<?php echo $_SESSION['rootPath'] . "/../assets/img/thuong_hieu/" . $result->logo; ?>">
                </div>
                <div class="detail_admin_right">
                    <table class="Table_Details_Admin">
                        <tr>
                            <td>Mã thương hiệu: </td>
                            <td>
                                <?php echo $result->maThuongHieu; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Tên thương hiệu:</td>
                            <td>
                                <?php echo $result->tenThuongHieu; ?>
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
                <div class="notification">
                    <h1 class="notification_title">Xác nhận xóa thương hiệu!</h1>
                    <input type="submit" name="delete" value="Xóa" class="alert_delete_btn delete_conform" />
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