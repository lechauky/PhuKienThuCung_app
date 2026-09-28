<?php include '../templates/header.php';
require_once('../includes/config.php');

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
function generateRandomString($length = 32)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function SendVerificationLinkEmail($mail, $emailId, $activationCode)
{
    try {
        //Server settings
        $link = "http://" . $_SERVER['HTTP_HOST'] . "/WebPhuKienThuCung/pages/change_password_page.php?ma=" . $activationCode;
        $mail->isSMTP(); //Send using SMTP
        $mail->CharSet = 'UTF-8'; // Đặt bộ mã hóa cho email là UTF-8
        $mail->Encoding = 'base64'; // Đặt phương thức mã hóa là base64
        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'hoahuongduong05124@gmail.com'; //SMTP username
        $mail->Password = 'ytotxwzbrwkoddjd'; //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
        $mail->Port = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('hoahuongduong05124@gmail.com', 'paddyshop');
        $mail->addAddress($emailId); //Add a recipient
        $mail->SMTPSecure = 'tls';

        $subject = "";
        $body = "";
        $subject = "Đặt lại mật khẩu";
        $body = "<b>Xin chào bạn</b>,<br/><br/> Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu của bạn. Vui lòng nhấp vào liên kết dưới đây để thiết lập mật khẩu mới cho tài khoản của bạn " . "<br/><br/><a href=" . $link . " . method='GET'>Link đặt lại mật khẩu</a>";

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
$warning = "";
if (isset($_POST['quenMK'])) {
    $quenMK = trim($_POST['quenMK']);
} else {
    $quenMK = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT * FROM khach_hang WHERE email = '$quenMK'";
    $stmt = $dbh->query($sql);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$account == false) {
        // Gửi email để thay đổi mật khẩu
        $email = new PHPMailer(true);
        $resetCode = generateRandomString();
        SendVerificationLinkEmail($email, $quenMK, $resetCode);
        $account['khoiPhucMatKhau'] = $resetCode;
        $sql = "UPDATE khach_hang SET khoiPhucMatKhau = '$resetCode' WHERE email = '$quenMK'";
        $stmt = $dbh->query($sql);
        $warning = "Đã gửi mail xác nhận cập nhật tài khoản";
    } else {
        $warning = "Email không tồn tại";
    }
}

?>
<h6 style="margin-bottom: 40px">Trang chủ > Quên mật khẩu </h6>
<div class="create_admin" style="margin-bottom: 300px">
    <h1 class="Title_Admin_create_form">Bạn đã quên mât khẩu</h1>
    <p class="Notification_create_form">Vui lòng điền email để reset mật khẩu</p>
    <form action="forgot_password.php" id='form-6' method='POST'>
        <div class="form_field">
            <label for="" class="name_form_field">Nhập email tài khoản: </label>
            <input type="text" class="textfile" id="email" name="quenMK" style="width: 400px;">
            <span class="error_message"></span>
        </div>
        <h6 style="color: forestgreen">
            <?php echo $warning; ?>
        </h6>
        <div class="button">
            <input type="submit" value="Đặt lại mật khẩu" class="button_add_admin" style="width: 150px" />
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
                Validator.isRequired('#email', 'Vui lòng nhập Email!'),
                Validator.isEmail('#email'),
            ],
            onSubmit: function (data) {
                // Call API
                //console.log(data);
            }
        });
    });
</script>
<?php include '../templates/footer.php' ?>