<?php include '../templates/nav_admin1.php' ?>

<div class="search_admin">
    <h1 class="Title_Admin_create_form">Tìm khách hàng</h1>
    <div class="search_admin_header">
        <!-- @using (Html.BeginForm("Search", "khachHang", FormMethod.Post, new { @enctype = "multipart/form-data", @class = "search_admin_form" }))
        { -->
        <form action="">
            <table class="Table_Details_Admin">
                <tr>
                    <td>
                        <label for="" class="name_form_field">Mã Khách hàng : </label>
                    </td>
                    <td>
                        <input type="text" class="textfile" name="maKH" style="min-width:100px" value="@maKH">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="name_form_field">Họ tên hách hàng : </label>
                    </td>
                    <td>
                        <input type="text" class="textfile" id="fullname" name="tenKH" value="@tenKH">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="name_form_field">Số điện thoại : </label>
                    </td>
                    <td>
                        <input type="text" class="textfile" id="phoneNumber" value="@dienthoai" name="dienthoai"
                            style="min-width:150px">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="name_form_field">Ngày sinh: </label>
                    </td>
                    <td>
                        <input type="date" class="textfile" id="phoneNumber" name="ngaysinh" style="min-width:150px"
                            value="@ngaysinh">
                    </td>
                </tr>


            </table>
            <table class="Table_Details_Admin">
                <tr>
                    <td>
                        <label for="" class="name_form_field">Email : </label>
                    </td>
                    <td>
                        <input type="text" class="textfile" id="email" name="email" value="@email">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="name_form_field">Tuổi : </label>
                    </td>
                    <td style="display: flex; justify-content:flex-start; align-items: center">
                        <div style="display: flex; justify-content:center; align-items: center">
                            <label style=" margin-right : 50px">Từ: </label>
                            <input type="number" class="textfile" id="email" name="tuoiBatDau" max="100" min="0"
                                style="min-width:40px" value="@tuoiBT">
                        </div>
                        <div style="display: flex; justify-content:center; align-items: center; margin-left: 50px">
                            <label style=" margin-right: 50px">Đến: </label>
                            <input type="number" class="textfile" id="email" name="tuoiKetThuc" max="100" min="0"
                                style="min-width:40px" value="@tuoiKT">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="" class="name_form_field">Địa chỉ : </label>
                    </td>
                    <td>
                        <textarea class="textfile_address" cols="2" id="address" name="diachi">@diachi</textarea>
                    </td>
                </tr>
            </table>
            <div class="search_button" style="margin-top: 900px;">
                <input type="submit" value="Tìm kiếm" class="search_button_btn" />
                <a href="@Url.Action(" Search","KhachHang")"><input type="button" value="Nhập lại"
                        class="search_button_btn" /></a>
                <a href="@Url.Action(" InBaoCao", "KhachHang" , new
                    {@maKH=ViewBag.maKH,@tenKH=ViewBag.tenKH,@dienthoai=ViewBag.dienthoai,@email=@ViewBag.email,@tuoiBatDau=ViewBag.tuoiBT,@tuoiKetThuc=ViewBag.tuoiKT,@diachi=ViewBag.diachi,@ngaysinh=ViewBag.ngaysinh})"
                    style="position: absolute; right:-400px;"><input type="button" value="In báo cáo"
                        class="search_button_btn" /></a>
            </div>
        </form>
    </div>
    <!-- @if (ViewBag.TB == null)
    { khi tìm thấy -->
    <table class="table_dsadmin" style="margin-top: 160px;">
        <thead>
            <tr>
                <th style="width: 65px;">Mã khách hàng</th>
                <th style="width: 120px;">Họ tên</th>
                <th style="width: 150px;">Địa chỉ</th>
                <th style="width: 80px;">Số điện thoại</th>
                <th style="width: 90px;">Ngày sinh</th>
                <th style="width: 150px;">Email</th>
                <th style="width: 75px;">Hình ảnh</th>
                <th style="width: 80px;">Chức năng</th>
            </tr>
        </thead>
        <tbody>
            <!-- @foreach (var item in Model)
                { hiện danh sách tìm thấy -->
            <tr>
                <td>
                    MAKH </td>
                <td>
                    HOTENKH
                </td>
                <td>
                    DIACHI
                </td>
                <td>
                    DIENTHOAI
                </td>
                <td>
                    NGAYSINH
                </td>
                <td>
                    EMAIL
                </td>
                <!-- @if (item.HINHANH == null)
                        {
                            <td style="height:90px">
                                Không có ảnh
                            </td>
                        }
                        else
                        { -->
                <td>
                    <img src="../../assets/img/khach_hang/images.jpg" alt="" style="width: 70px; height: 70px;">
                </td>
                <!-- } -->

                <td>
                    <a href="@Url.Action(" Edit","Admin", new { id=item.MAKH })"><i
                            class="fa-solid fa-pen-to-square edit"></i></a>
                    <a href="@Url.Action(" Delete","Admin", new { id=item.MAKH })"> <i
                            class="fa-solid fa-xmark remove"></i></a>
                    <a href="@Url.Action(" Detail","Admin", new { id=item.MAKH })"><i
                            class="fa-solid fa-circle-info detail"></i></a>
                </td>
            </tr>

            <!-- } -->
        </tbody>
    </table>
    <!-- <ul class="page">
            @if (Model.PageCount > 1)
            {
                for (int i = 1; i <= Model.PageCount; i++)
                {
                    <li>
                        <a href="@Url.Action("Search", new { page = i })" class="@((i == Model.PageNumber) ? "page_button page_button_active" : "page_button")">@i</a>
                    </li>
                }
            }
        </ul> -->
    <!-- }
    else
    { khi không tìm thấy-->
    <!-- <h1 class="message_not_found">@ViewBag.TB</h1> -->
    <!-- } -->

</div>

<?php include '../templates/nav_admin2.php' ?>