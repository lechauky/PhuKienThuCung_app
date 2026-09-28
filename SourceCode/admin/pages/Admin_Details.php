<?php include '../templates/nav_admin1.php' ?>

<div class="detail_admin">
    <h1 class="Title_Admin_create_form">Thông tin tài khoản</h1>
    <div class="detai_admin_form">
        <div class="detail_admin_left">
            <img src="<?php echo $_SESSION['rootPath'] . "/../assets/img/ad_user/" . $_SESSION['admin']->avatar ?>"
                alt="">
        </div>
        <div class="detail_admin_right">
            <table class="Table_Details_Admin">
                <tr>
                    <td>Mã Admin: </td>
                    <td>
                        <?php echo $_SESSION['admin']->maNhanVien ?>
                    </td>
                </tr>
                <tr>
                    <td>Họ tên:</td>
                    <td>
                        <?php echo $_SESSION['admin']->ho . ' ' . $_SESSION['admin']->ten ?>
                    </td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td>
                        <?php
                        $get_diachi = "SELECT xa.tenXa, huyen.tenHuyen, tinh.tenTinh FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE xa.maXa = '{$_SESSION["admin"]->maXa}'";
                        $statement = $dbh->prepare($get_diachi);
                        $statement->execute();
                        $diachis = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($diachis as $diachi)
                            echo $_SESSION["admin"]->diaChiCuThe . ', ' . $diachi['tenXa'] . ', ' . $diachi['tenHuyen'] . ', ' . $diachi['tenTinh'];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td>
                        <?php echo $_SESSION['admin']->dienThoai ?>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <?php echo $_SESSION['admin']->email ?>
                    </td>
                </tr>
                <tr>
                    <td>Loại tài khoản:</td>
                    <td>
                        <!-- SELECT tenLoai FROM loai_tai_khoan WHERE maLoai='LTK001' -->
                        <?php
                        $get_loaiTK = "SELECT tenLoai FROM loai_tai_khoan WHERE maLoai='{$_SESSION["admin"]->maLoai}'";
                        $statement = $dbh->prepare($get_loaiTK);
                        $statement->execute();
                        $loaiTK = $statement->fetch(PDO::FETCH_ASSOC);
                        echo $loaiTK['tenLoai'];
                        ?>
                    </td>
                </tr>
            </table>

        </div>

    </div>
    <div class="button">
        <form action="Admin_Edit.php" method="post">
            <input type="submit" value="Chỉnh sửa" class="button_add_admin" />
        </form>
    </div>

</div>

<?php include '../templates/nav_admin2.php' ?>