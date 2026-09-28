<!--change password-->
<?php include '../templates/header.php';
require_once('../includes/config.php');

$warning = 0;
if (isset($_POST['NewPassword'])) {
    $password = md5(trim($_POST['NewPassword']));
} else {
    $password = "";
}
$ma = $_GET['ma'];
$sql = "SELECT * FROM khach_hang WHERE khoiPhucMatKhau = '$ma'";
$stmt = $dbh->query($sql);
if ($stmt->rowCount() <= 0) {
    $warning = 3;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($stmt->rowCount() > 0) {
        $sql = "UPDATE khach_hang SET matKhau = '$password' WHERE khoiPhucMatKhau = '$ma'";
        $stmt = $dbh->query($sql);
        $sql = "UPDATE khach_hang SET khoiPhucMatKhau = '' WHERE khoiPhucMatKhau = '$ma'";
        $stmt = $dbh->query($sql);
        $warning = 1;
    } else {
        $warning = 2;
    }

}
?>

<h6 style="margin-bottom: 40px">Trang chủ > Thay đổi mật khẩu </h6>
<div class="create_admin" style="margin-bottom: 300px">
    <!-- <h1 class="Title_Admin_create_form">Mật khẩu mới</h1> -->
    <!-- <p class="Notification_create_form">Vui lòng điền email để reset mật khẩu</p> -->
    <form action="" class="create_admin_form" method="post" id='form-6'>
        <div class="form_field">
            <label for="" class="name_form_field">Nhập mật khẩu mới: </label>
            <input type="password" class="textfile" id="password" name="NewPassword" style="width: 400px;">
            <span class="error_message"></span>
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Xác nhận mật khẩu mới: </label>
            <input type="password" class="textfile" id="password_confirmation" name="ConfirmPassword"
                style="width: 400px;">
            <span class="error_message"></span>
        </div>
        <?php
        if ($warning == 1) {
            echo "<script>
            alert(\"Đổi mật khẩu thành công\");
            window.location.href = 'login.php';
          </script>";
        } else if ($warning == 2) {
            echo " <h6 style='color: red'>Đổi mật khẩu không thành công</h6>";
        } else if ($warning == 3) {
            echo "<h6 style='color: red'>Không có bất kỳ tài khoản nào yêu cầu quên mật khẩu</h6>";
        } else {
            echo "<h6 style='color: red'></h6>";
        }
        ?>

        <div class="button">
            <input id="datLaiPass" type="submit" value="Đổi mật khẩu" class="button_add_admin" style="width: 150px" />
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mong muốn của chúng ta
        Validator({
            form: '#form-6',
            formGroupSelector: '.form_field',
            errorSelector: '.error_message',
            rules: [
                Validator.isRequired('#password', 'Vui lòng nhập mật khẩu!'),
                Validator.isConfirmed('#password_confirmation', function () {
                    return document.querySelector('#form-6 #password').value;
                }, 'Mật khẩu nhập lại không chính xác'),
            ],
            onSubmit: function (data) {
                // Call API
                //console.log(data);
            }
        });
    });
</script>

<?php include '../templates/footer.php' ?>