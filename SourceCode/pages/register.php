<?php
include '../templates/header.php';
$get_tinh = "SELECT `maTinh`, `tenTinh` FROM `tinh`";
$statement = $dbh->prepare($get_tinh);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);

?>

<h6>Trang chủ > Đăng ký </h6>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function getDistricts(selectedProvince) {
            jQuery.ajax({
                url: '../includes/get_register.php',
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
                url: '../includes/get_register.php',
                type: 'POST',
                data: { district_id: selectedDistrict },
                success: function (data) {
                    $('#wards').html(data);
                    var selectedWard = $('#wards').val();
                    $('#maXaInput').val(selectedWard);
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

        function getUsername(userName) {
            jQuery.ajax({
                url: '../includes/get_register.php',
                type: 'POST',
                data: { userName: userName },
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

<div class="create_admin">
    <h1 class="Title_Admin_create_form">Tạo tài khoản </h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form class="create_admin_form" action="../includes/register.php" method="POST">
        <div class="form_field">
            <label for="" class="name_form_field">Họ: </label>
            <input type="text" class="textfile" name="hoKhachHang" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên: </label>
            <input type="text" class="textfile" name="tenKhachHang" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Số điện thoại: </label>
            <input type="text" class="textfile" id="phoneNumber" name="dienThoai" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Email: </label>
            <input type="text" class="textfile" id="email" name="email" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Ngày sinh: </label>
            <input type="date" class="textfile" id="birthDay" name="ngaySinh" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên đăng nhập: </label>
            <input type="text" class="textfile" id="userName" name="tendn" style="width: 400px;" required>
            <span class="error_message" id="userName_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Mật khẩu: </label>
            <input type="password" class="textfile" id="password" name="matKhau" style="width: 400px;" required>
            <span class="error_message"></span>
        </div>
        <div style="display: flex; justify-content: space-between; width: 400px;">
            <div class="form_field">
                <label for="" class="name_form_field">Tỉnh: </label>
                <select id='provinces' class="textfile" name="provinces" style="width: 195px;">
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
                <select id='districts' class="textfile" name="districts" style="width: 195px;">
                    <option disabled selected value="">Chọn Huyện</option>
                </select>
                <span class="error_message"></span>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; width: 400px;">
            <div class="form_field">
                <label for="" class="name_form_field">Xã: </label>
                <select required id="wards" class="textfile" style="width: 195px;">
                    <option value="">Chọn Xã</option>
                </select>
                <input hidden type="text" name="maXa" id="maXaInput">
                <span class="error_message"></span>
            </div>
            <div class="form_field">
                <label for="" class="name_form_field">Địa chỉ cụ thể: </label>
                <input type="diaChi" class="textfile" id="diaChi" name="diaChi" style="width: 195px;" required>
            </div>
        </div>

        <!-- <div class="form_field" style="max-width: 400px">
            <label for="" class="name_form_field">Ảnh đại diện : </label>
            <div class="custom-file">
                <div class="form_field">
                    <input type="file" class="custom-file-input" id="img_profile_admin" name="fileUpload"
                        style="max-width: 300px;">
                    <span class="error_message"></span>
                </div>
                <div class="custom-file-img">
                    <img src="" alt="" id="custom-file-img-display">
                </div>
            </div>
        </div> -->
        <div class="button">
            <input disabled type="submit" name="submit" id="registerButton" value="Đăng ký" class="button_add_admin" />
        </div>
    </form>
</div>
<?php include '../templates/footer.php' ?>