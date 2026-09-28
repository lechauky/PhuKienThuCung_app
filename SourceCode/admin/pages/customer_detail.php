<?php include '../templates/nav_admin1.php' ?>


<div class="detail_admin">
    <h1 class="Title_Admin_create_form">Thông tin tài khoản</h1>
    <div class="detai_admin_form">
        <!-- @if (Model.HINHANH != null)
        {
            <div class="detail_admin_left">
                <img src="~/assest/img/khach_hang/@Model.HINHANH" alt="">
            </div>
        }
        else
        { -->
            <div class="detail_admin_left">
                <img src="../../assets/img/khach_hang/images.jpg" alt="">
            </div>
        <!-- } -->

        <div class="detail_admin_right">
            <table class="Table_Details_Admin">
                <tr>
                    <td>Mã khách hàng: </td>
                    <td>@MAKH</td>
                </tr>
                <tr>
                    <td>Họ tên Khách hàng:</td>
                    <td>@HOTENKH</td>
                </tr>
                <tr>
                    <td>Số điện thoại :</td>
                    <td>@DIENTHOAI</td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td>@Diachi</td>
                </tr>

                <tr>
                    <td>Email :</td>
                    <td>@EMAIL</td>
                </tr>
                <tr>
                    <td>Ngày sinh :</td>
                    <td>@NGAYSINH </td>
                </tr>
            </table>

        </div>

    </div>
    <div class="button">
        <a href="@Url.Action("Edit","KhachHang",new {id = Model.MAKH})"><input type="submit" value="Chỉnh sửa" class="button_add_admin" /></a>
        <a href="@Url.Action("Index","KhachHang")"><input type="button" value="Quay lại" class="button_add_admin" /></a>
    </div>

</div>


<?php include '../templates/nav_admin2.php' ?>
