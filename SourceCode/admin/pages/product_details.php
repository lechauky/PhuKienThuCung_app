<?php
include '../templates/nav_admin1.php';
//lấy dữ liệu sản phẩm
include '../includes/get_product_data_from_id.php';
?>
<div class="body" style="margin-top: 15px">
    <div class="detail_admin">
        <h1 class="Title_Admin_create_form">Thông tin sản phẩm</h1>
        <div class="detai_admin_form">
            <div class="detail_admin_left">
                <img src="<?php echo $_SESSION['rootPath'] . '/../assets/img/sanpham/' . $result->hinhAnh ?>" alt="">
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
                        <td>Đơn giá mua:</td>
                        <td>
                            <?php echo $result->donGiaMua . " đ"; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Đơn giá bán :</td>
                        <td>
                            <?php echo $result->donGiaBan . " đ"; ?>
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
                        <td>Số lượng tồn:</td>
                        <td>
                            <?php echo $result->soLuong; ?>
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
            <?php echo "<a href='product_edit.php?id=" . $product_id . "'><input type=\"submit\" value=\"Chỉnh sửa\" class=\"button_add_admin\" /></a>" ?>
            <a href="javascript:history.go(-1);"><input type="button" value="Quay lại" class="button_add_admin" /></a>
        </div>

    </div>
</div>
<?php include '../templates/nav_admin2.php' ?>