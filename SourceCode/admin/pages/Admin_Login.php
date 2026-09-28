<!DOCTYPE html>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập tài khoản quản trị viên</title>
    <link rel="icon" href="../../assets/img/logo/header_logo.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">

    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/grid.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="../../assets/font/fontawesome-free-6.1.2-web/css/all.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Asap:ital,wght@0,400;0,600;0,700;0,800;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php
    ob_start();
    require_once('../includes/config.php');
    $warning = "";
    if (isset($_POST['tendn'])) {
        $username = trim($_POST['tendn']);
    } else {
        $username = "";
    }
    if (isset($_POST['matkhau'])) {
        $password = trim($_POST['matkhau']);
    } else {
        $password = "";
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $username !== '' && $password !== '') {
        $md5password = md5($password);
        $sql = "SELECT * FROM nhan_vien WHERE tenNguoiDung = '$username' AND matKhau = '$md5password'";
        $stmt = $dbh->query($sql);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if ($stmt->rowCount() > 0) {
            //echo "<script>alert(\"11\");</script>";
            $_SESSION['admin'] = $result;
            header("Location: ../index.php");
            exit();
        } else {
            $warning = "Tài khoản hoặc mật khẩu không đúng!";
        }
    }
    ob_end_flush();
    ?>
    <div class="app">
        <div class="loginadmin">
            <div class="backGround">
                <img src="../assets/img/bacgroundloginadmin/backgroundLoginadmin.png" alt="">
            </div>
            <div class="login">
                <div class="login1">
                    <h1>Đăng nhập tài khoản admin</h1>
                    <form action="Admin_Login.php" Method="POST" id="form-1">
                        <div class="form_field">
                            <label for="taikhoan" class="name_form_field">Tài khoản : </label>
                            <input type="text" class="textfile" name="tendn" id="taikhoan">
                            <span class="error_message"></span>
                        </div>
                        <div class="form_field">
                            <label for="matkhau" class="name_form_field">Mật khẩu : </label>
                            <input type="password" class="textfile" id="matkhau" name="matkhau">
                            <span class="error_message"></span>
                        </div>
                        <h4 class="error_message">
                            <?php echo $warning ?>
                        </h4>
                        <div class="button">
                            <input type="submit" value="Đăng nhập" class="button_add_admin" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mong muốn của chúng ta
            Validator({
                form: '#form-1',
                formGroupSelector: '.form_field',
                errorSelector: '.error_message',
                rules: [
                    Validator.isRequired('#taikhoan', 'Vui lòng nhập tài khoản!'),
                    Validator.isRequired('#matkhau', 'Vui lòng nhập mật khẩu!'),
                ],
                onSubmit: function (data) {
                    // Call API
                    //console.log(data);
                }
            });
        });
    </script>
</body>

</html>