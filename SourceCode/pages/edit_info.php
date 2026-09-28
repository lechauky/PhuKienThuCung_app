<?php
include '../templates/header.php';
$get_tinh = "SELECT `maTinh`, `tenTinh` FROM `tinh`";
$statement = $dbh->prepare($get_tinh);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);

$get_address = "SELECT xa.maXa, xa.tenXa, huyen.maHuyen,huyen.tenHuyen, tinh.maTinh, tinh.tenTinh FROM khach_hang JOIN xa ON khach_hang.maXa=xa.maXa JOIN huyen ON xa.maHuyen=huyen.maHuyen JOIN tinh ON huyen.maTinh=tinh.maTinh WHERE khach_hang.maKhachHang='{$_SESSION['taiKhoan']['maKhachHang']}'";
$statement1 = $dbh->prepare($get_address);
$statement1->execute();
$address_input = $statement1->fetch(PDO::FETCH_OBJ);

?>

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

    function getUsername(userName) {
      jQuery.ajax({
        url: '../includes/get_register.php',
        type: 'POST',
        data: { userName: userName },
        success: function (data) {
          $('#userName_message').html(data);
        }
      });
    }
    $('#userName').change(function () {
      // cập nhật giá trị của #maXaInput khi thay đổi xã để gửi đi
      var userName = $(this).val();
      getUsername(userName);
    });

  });


</script>

<div class="create_admin">
  <h1 class="Title_Admin_create_form">Chỉnh sửa tài khoản </h1>
  <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
  <form class="create_admin_form" action="<?php echo $rootPath . '/includes/edit_info.php' ?>" method="POST">
    <div class="form_field">
      <label for="" class="name_form_field">Họ: </label>
      <input type="text" class="textfile" name="hoKhachHang" style="width: 400px;" required
        value="<?php echo $_SESSION['taiKhoan']['hoKhachHang'] ?>">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Tên: </label>
      <input type="text" class="textfile" name="tenKhachHang" style="width: 400px;" required
        value="<?php echo $_SESSION['taiKhoan']['tenKhachHang'] ?>">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Số điện thoại: </label>
      <input type="text" class="textfile" id="phoneNumber" name="dienThoai" style="width: 400px;" required
        value="<?php echo $_SESSION['taiKhoan']['dienThoai'] ?>">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Email: </label>
      <input type="text" class="textfile" id="email" name="email" style="width: 400px;" required
        value="<?php echo $_SESSION['taiKhoan']['email'] ?>">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Ngày sinh: </label>
      <input type="date" class="textfile" id="birthDay" name="ngaySinh" style="width: 400px;" required
        value="<?php echo $_SESSION['taiKhoan']['ngaySinh'] ?>">
      <span class="error_message"></span>
    </div>
    <div style="display: flex; justify-content: space-between; width: 400px;">
      <div class="form_field">
        <label for="" class="name_form_field">Tỉnh: </label>
        <select id='provinces' class="textfile" name="provinces" style="width: 195px;">
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
        <select id='districts' class="textfile" name="districts" style="width: 195px;">
          <option value="<?php echo $address_input->maHuyen; ?>" disabled selected>
            <?php echo $address_input->tenHuyen; ?>
          </option>
        </select>
        <span class="error_message"></span>
      </div>
    </div>
    <div style="display: flex; justify-content: space-between; width: 400px;">
      <div class="form_field">
        <label for="" class="name_form_field">Xã: </label>
        <select required id="wards" class="textfile" style="width: 195px;">
          <option value="<?php echo $address_input->maXa; ?>" disabled selected>
            <?php echo $address_input->tenXa; ?>
          </option>
        </select>
        <input hidden type="text" name="maXa" id="maXaInput" value="<?php echo $address_input->maXa; ?>">
        <span class="error_message"></span>
      </div>
      <div class="form_field">
        <label for="" class="name_form_field">Địa chỉ cụ thể: </label>
        <input type="diaChi" class="textfile" id="diaChi" name="diaChi" style="width: 195px;" required
          value="<?php echo $_SESSION['taiKhoan']['diaChiCuThe'] ?>">
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
      <input type="submit" name="submit" value="Xac nhận thay đổi" class="button_add_admin" />
    </div>
  </form>
</div>
<?php include '../templates/footer.php' ?>