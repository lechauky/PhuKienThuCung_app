<?php include '../templates/nav_admin1.php';
$NVID = $_GET['maNhanVien'];
$sql = "SELECT maNhanVien,ho,ten,diaChiCuThe,dienThoai,email,tenLoai,avatar, maXa FROM nhan_vien JOIN loai_tai_khoan ON nhan_vien.maLoai = loai_tai_khoan.maLoai WHERE maNhanVien = '$NVID';";
$statement = $dbh->prepare($sql);
$statement->execute();
$nhanVien = $statement->fetch(PDO::FETCH_OBJ);
?>

<div class="detail_admin">
    <h1 class="Title_Admin_create_form">Thông tin tài khoản</h1>
    <div class="detai_admin_form">
        <div class="detail_admin_left">
            <img src="../../assets/img/ad_user/<?php echo $nhanVien->avatar; ?>">
        </div>
        <div class="detail_admin_right">
            <table class="Table_Details_Admin">
                <tr>
                    <td>Mã nhân viên: </td>
                    <td>
                        <?php echo $nhanVien->maNhanVien; ?>
                    </td>
                </tr>
                <tr>
                    <td>Họ tên nhân viên:</td>
                    <td>
                        <?php echo $nhanVien->ho . ' ' . $nhanVien->ten ?>
                    </td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td>
                        <?php
                        $get_diachi = "SELECT xa.tenXa, huyen.tenHuyen, tinh.tenTinh FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE xa.maXa = '$nhanVien->maXa'";
                        $statement = $dbh->prepare($get_diachi);
                        $statement->execute();
                        $diachis = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($diachis as $diachi)
                            echo $nhanVien->diaChiCuThe . ', ' . $diachi['tenXa'] . ', ' . $diachi['tenHuyen'] . ', ' . $diachi['tenTinh'];
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Số điện thoại :</td>
                    <td>
                        <?php echo $nhanVien->dienThoai ?>
                    </td>
                </tr>
                <tr>
                    <td>Email :</td>
                    <td>
                        <?php echo $nhanVien->email ?>
                    </td>
                </tr>
                <tr>
                    <td>Loại tài khoản :</td>
                    <td>
                        <!-- SELECT tenLoai FROM loai_tai_khoan WHERE maLoai='LTK001' -->
                        <?php echo
                            $nhanVien->tenLoai;
                        ?>
                    </td>
                </tr>
            </table>

        </div>

    </div>

    <br>
    <div class="button">
        <a href="./Admin_DsAdmin.php"><input type="button" class="button_add_admin" value="Quay Lại"></a>
    </div>
</div>

<?php include '../templates/nav_admin2.php' ?>