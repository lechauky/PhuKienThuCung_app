<?php include '../templates/nav_admin1.php' ?>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById("password");
        const passwordConfirmationInput = document.getElementById("password_confirmation");
        const passwordError = document.querySelector("#password + .error_message");
        const passwordConfirmationError = document.querySelector("#password_confirmation + .error_message");
        const resetPasswordButton = document.querySelector("#datLaiPass");

        passwordInput.addEventListener("input", validatePassword);
        passwordConfirmationInput.addEventListener("input", validatePasswordConfirmation);

        function validatePassword() {
            const password = passwordInput.value;
            if (password.length < 8 || password.length >20 ) {
                passwordError.textContent = "Mật khẩu phải có ít nhất 8 ký tự và ít hơn 20 ký tự";
                resetPasswordButton.disabled = true;
            } else {
                passwordError.textContent = "";
            }
        }

        function validatePasswordConfirmation() {
            const password = passwordInput.value;
            const passwordConfirmation = passwordConfirmationInput.value;
            if (password !== passwordConfirmation) {
                passwordConfirmationError.textContent = "Mật khẩu xác nhận không khớp.";
                resetPasswordButton.disabled = true;
            } else {
                passwordConfirmationError.textContent = "";
                resetPasswordButton.disabled = false;
            }
        }
    });
</script>

<h1 class="Title_Admin_create_form">Thay đổi mật khẩu</h1>
<div class="create_admin" style="margin-bottom: 300px">
    <form action="../includes/change_password_admin.php" class="create_admin_form" method="post">
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
        <h6 style="color: forestgreen"></h6>
        <div class="button">
            <input disabled id="datLaiPass" type="submit" value="Đổi mật khẩu" class="button_add_admin" style="width: 150px" />
        </div>
    </form>
</div>

<?php include '../templates/nav_admin2.php' ?>