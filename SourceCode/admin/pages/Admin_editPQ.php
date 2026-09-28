<?php include '../templates/nav_admin1.php';
$get_tinh = "SELECT `maTinh`, `tenTinh` FROM `tinh`";
$statement = $dbh->prepare($get_tinh);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$IDNV = $_GET['maNhanVien'];
$sql = "SELECT *,tenXa, tenHuyen,tenTinh FROM nhan_vien JOIN xa ON nhan_vien.maXa = xa.maXa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh = tinh.maTinh WHERE maNhanVien = '$IDNV';";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nhanVien = $stmt->fetch(PDO::FETCH_OBJ);
$get_tinh = "SELECT `maTinh`, `tenTinh` FROM `tinh`";
$statement = $dbh->prepare($get_tinh);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$get_address = "SELECT xa.maXa, xa.tenXa, huyen.maHuyen,huyen.tenHuyen, tinh.maTinh, tinh.tenTinh FROM nhan_vien JOIN xa ON nhan_vien.maXa=xa.maXa JOIN huyen ON xa.maHuyen=huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE nhan_vien.maNhanVien='{$_SESSION['admin']->maNhanVien}'";
$statement1 = $dbh->prepare($get_address);
$statement1->execute();
$address_input = $statement1->fetch(PDO::FETCH_OBJ);

$sql_LTK = "SELECT * FROM loai_tai_khoan";
$stmt_loaitk = $dbh->prepare($sql_LTK);
$stmt_loaitk->execute();
$loaiTK = $stmt_loaitk->fetchAll(PDO::FETCH_OBJ);
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function getDistricts(selectedProvince) {
            jQuery.ajax({
                url: '../../includes/get_register.php',
                type: 'POST',
                data: { province_id: selectedProvince },
                success: function (data) {
                    $('#districts').html(data);
                    $('#wards').html('<option value="">Chọn Xã</option>');
                    $('#maXaInput').val('');
                }
            });
        }
        function getWards(selectedDistrict) {
            jQuery.ajax({
                url: '../../includes/get_register.php',
                type: 'POST',
                data: { district_id: selectedDistrict },
                success: function (data) {
                    $('#wards').html(data);
                    // var selectedWard = $('#wards').val();
                    // $('#maXaInput').val(selectedWard);
                }
            });
        }
        $('#provinces').change(function () {
            var selectedProvince = $(this).val();
            getDistricts(selectedProvince);
        });
        $('#districts').change(function () {
            var selectedDistrict = $(this).val();
            getWards(selectedDistrict);
        });
        $('#wards').change(function () {
            // Cập nhật giá trị của #maXaInput khi bạn thay đổi xã.
            var selectedWard = $(this).val();
            $('#maXaInput').val(selectedWard);
        });
    });
</script>

<style>
    .textfield {
        display: block;
        min-width: 450px;
        height: 34px;
        margin-bottom: 6px;
        border-radius: 10px;
        border: 1px solid rgb(0, 0, 0, 0.3);
        text-indent: 5px;
        outline: none;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
    }

    .form_field {
        padding: 3px;
        min-height: 50px;
        font-size: 150%;
    }
</style>
<div class="create_admin">
    <h1 class="Title_Admin_create_form">Sửa thông tin tài khoản quản trị viên</h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form action="../includes/edit_info_adminDS.php" method="post" enctype="multipart/form-data">
        <input style="display:none;" type="text" class="textfield" id="maNhanVien" name="maNhanVien"
            value="<?php echo $nhanVien->maNhanVien ?>">
        <div class="form_field">
            <label for="" class="name_form_field">Họ: </label>
            <input required type="text" class="textfield" id="fullname" name="ho" value="<?php echo $nhanVien->ho ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên: </label>
            <input required type="text" class="textfield" id="fullname" name="ten" value="<?php echo $nhanVien->ten ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Ngày sinh: </label>
            <input type="date" class="textfile" id="birthDay" name="ngaySinh" style="width: 400px;" required
                value="<?php echo $nhanVien->ngaySinh ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Số điện thoại : </label>
            <input required type="number" class="textfield" id="phoneNumber" name="dienThoai"
                value="<?php echo $nhanVien->dienThoai ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Loại tài khoản : </label>
            <select class="textfile" name="loaiTaiKhoan" id="loaiTaiKhoan">
                <?php
                foreach ($loaiTK as $row) {
                    if ($nhanVien->maLoai == $row->maLoai) {
                        echo '<option value="' . $row->maLoai . '" selected>' . $row->tenLoai . '</option>';
                    } else {
                        echo '<option value="' . $row->maLoai . '">' . $row->tenLoai . '
                        </option>';
                    }
                }
                ?>
            </select>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Email : </label>
            <input type="text" class="textfield" id="email" name="email" value="<?php echo $nhanVien->email ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Mật Khẩu : </label>
            <input type="password" class="textfield" id="matKhau" name="matKhau" >
            <span class="error_message">(Để trống nếu không thay đổi!!)</span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tỉnh: </label>
            <select id='provinces' class="textfield" name="provinces" style="width: 195px;">
                <option value="<?php echo $address_input->maTinh; ?>" disabled selected>
                    <?php echo $address_input->tenTinh; ?>
                </option>
                <?php
                while ($row = $statement->fetch())
                    echo "<option value='{$row->maTinh}'>{$row->tenTinh}</option>";
                ?>
            </select>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Huyện: </label>
            <select id='districts' class="textfield" name="districts" style="width: 195px;">
                <option value="<?php echo $address_input->maHuyen; ?>" disabled selected>
                    <?php echo $address_input->tenHuyen; ?>
                </option>
            </select>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Xã: </label>
            <select required id="wards" class="textfield" style="width: 195px;">
                <option value="<?php echo $address_input->maXa; ?>" disabled selected>
                    <?php echo $address_input->tenXa; ?>
                </option>
            </select>
            <input hidden type="text" name="maXa" id="maXaInput" value="<?php echo $address_input->maXa; ?>">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Địa chỉ cụ thể: </label>
            <input type="diaChi" class="textfield" id="diaChi" name="diaChiCuThe" style="width: 195px;" required
                value="<?php echo $nhanVien->diaChiCuThe ?>">
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Ảnh đại diện : </label>
            <div class="custom-file">
                <div class="form_field">
                    <input type="file" class="custom-file-input" id="" name="image" accept=".png, .jpg, .jpeg">
                    <span class="error_message"></span>
                </div>
            </div>
        </div>
        <div class="button">
            <input type="submit" value="Cập nhật" class="button_add_admin" />
        </div>
    </form>

</div>

<?php include '../templates/nav_admin2.php' ?>