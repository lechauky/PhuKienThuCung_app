<!--login-->
<?php
ob_start();
include '../templates/header.php';
require_once('../includes/config.php');
$warning = "";
if (isset($_POST['tendn'])) {
    $username = trim($_POST['tendn']);
} else {
    $username = "";
}
if (isset($_POST['matkhau'])) {
    $password = md5(trim($_POST['matkhau']));
} else {
    $password = "";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT * FROM khach_hang WHERE tenNguoiDung = '$username' AND matKhau = '$password'";
    $stmt = $dbh->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result != false) {
        $_SESSION['taiKhoan'] = $result;
        header("Location: product_page.php");
        exit();
    } else {
        $warning = "Tài khoản hoặc mật khẩu không đúng!";
    }
}
ob_end_flush();
?>
<div class="login_flex_right_title">
    <h6>Đăng Nhập</h6>
</div>
<form action="login.php" class="create_admin_form" id="form-2" method="POST">
    <div class="form_field">
        <label for="taikhoan" class="name_form_field">Tài khoản : </label>
        <input type="text" class="textfile" name="tendn" id="taikhoan" value="<?php if ($username != "")
            echo $username; ?>">
        <span class="error_message"></span>
    </div>
    <div class="form_field">
        <label for="matkhau" class="name_form_field">Mật khẩu : </label>
        <input type="password" class="textfile" id="matkhau" name="matkhau">
        <span class="error_message">
        </span>
    </div>
    <div class="form_field" style="min-height: 10px">
        <span class="error_message" style="font-weight: bold;">
            <?php if ($warning != "")
                echo $warning; ?>
        </span>
    </div>
    <button type="submit" class="button_add_admin" class="form-submit" style="width: 150px">Đăng nhập</button>
    <!-- <input  value="Đăng Nhập"  style="width: 150px" /> -->
    <a href="forgot_password.php" class="qmk" style="display: block; margin-left: 10px; color: black">Quên
        mật khẩu?</a>
    <a href="register.php" style="text-decoration: none; color:black">
        <input type="button" value="Tạo Tài Khoản" class="button_add_admin" style="width: 150px; margin-bottom: 50px" />
    </a>
</form>
</div>
<script src="<?php echo $rootPath ?>/assets/js/app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mong muốn của chúng ta
        Validator({
            form: '#form-2',
            formGroupSelector: '.form_field',
            errorSelector: '.error_message',
            rules: [
                Validator.isRequired('#taikhoan', 'Vui lòng nhập tài khoản!'),
                Validator.isRequired('#matkhau', 'Vui lòng nhập mật khẩu!'),
            ],
            onSubmit: function (data) {
                // Call API
                console.log(data);
            }
        });
    });
</script>
<?php include '../templates/footer.php' ?>