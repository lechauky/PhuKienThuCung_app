<?php include '../templates/nav_admin1.php';
$get_tinh = "SELECT `maTinh`, `tenTinh` FROM `tinh`";
$statement = $dbh->prepare($get_tinh);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);

$get_loaiTK = "SELECT * FROM loai_tai_khoan";
$statement1 = $dbh->prepare($get_loaiTK);
$statement1->execute();
$statement1->setFetchMode(PDO::FETCH_OBJ);
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
        function getUsername(userNameAdmin) {
            jQuery.ajax({
                url: '../../includes/get_register.php',
                type: 'POST',
                data: { userNameAdmin: userNameAdmin },
                success: function (data) {
                    $('#userName_message').html(data);
                    updateRegisterButtonState();
                }
            });
        }
        function updateRegisterButtonState() {
            var userNameMessage = $('#userName_message').text();
            // Kiểm tra nếu tên đăng nhập đã tồn tại thì không cho bấm đăng ký
            $('#registerButton').prop('disabled', userNameMessage != "");
        }

        $('#userName').on('input', function () {
            var userNameValue = $(this).val();
            userNameValue = userNameValue.replace(/[^A-Za-z0-9_]/g, '');
            $(this).val(userNameValue);

            // Cập nhật thông tin từ server khi người dùng nhập liệu
            getUsername(userNameValue);
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
    <h1 class="Title_Admin_create_form">Tạo tài khoản quản trị viên</h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form action="../includes/create_admin.php" method="post" enctype="multipart/form-data">
        <div class="form_field">
            <label for="" class="name_form_field">Tên đăng nhập: </label>
            <input type="text" class="textfile" id="userName" name="tendn" style="width: 400px;" required>
            <span class="error_message" id="userName_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Mật khẩu mặc định admin159 </label>
            <input hidden type="password" id="password" name="matKhau" value="admin159" style="width: 400px;">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Loại Nhân viên: </label>
            <select id='loaiNhanVien' class="textfield" name="loaiNhanVien" style="width: 195px;">
                <option value="" disabled selected>Chọn loại</option>
                <?php
                while ($row = $statement1->fetch())
                    echo "<option value='{$row->maLoai}'>{$row->tenLoai}</option>";
                ?>
            </select>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Họ: </label>
            <input required type="text" class="textfield" id="fullname" name="ho">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên: </label>
            <input required type="text" class="textfield" id="fullname" name="ten">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Ngày sinh: </label>
            <input type="date" class="textfile" id="birthDay" name="ngaySinh" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Số điện thoại : </label>
            <input required type="text" class="textfield" id="phoneNumber" name="dienThoai">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Email : </label>
            <input required type="text" class="textfield" id="email" name="email">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tỉnh: </label>
            <select id='provinces' class="textfield" name="provinces" style="width: 195px;">
                <option value="" disabled selected>Chọn tỉnh/Thành phố</option>
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
                <option disabled selected value="">Chọn Huyện</option>
            </select>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Xã: </label>
            <select required id="wards" class="textfield" style="width: 195px;">
                <option value="">Chọn Xã</option>
            </select>
            <input hidden type="text" name="maXa" id="maXaInput">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Địa chỉ cụ thể: </label>
            <input type="diaChi" class="textfield" id="diaChi" name="diaChi" style="width: 195px;" required>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Ảnh đại diện : </label>
            <div class="custom-file">
                <div class="form_field">
                    <input required type="file" class="custom-file-input" id="img_profile_admin" name="image"
                        accept=".png, .jpg, .jpeg">
                    <span class="error_message"></span>
                </div>
            </div>
        </div>
        <div class="button">
            <input disabled id="registerButton" type="submit" value="Thêm mới" class="button_add_admin" />
        </div>
    </form>
</div>

<?php include '../templates/nav_admin2.php' ?>