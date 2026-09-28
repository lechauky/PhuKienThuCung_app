<?php
include '../templates/nav_admin1.php';
include '../includes/get_new_type_id.php';
?>

<style>
    input,
    select,
    textarea {
        font-size: 16px;
    }

    label {
        font-size: 20px;
    }
</style>

<div class="create_admin">
    <h1 class="Title_Admin_create_form">Thêm loại sản phẩm</h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form action="../includes/create_type.php" method="post" id="form-3">
        <div class="form_field">
            <label for="" class="name_form_field">Mã loai: </label>
            <input type="text" class="textfile" readonly value="<?php echo $maLoai ?>" name="maLoai">
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên loại: </label>
            <input type="text" class="textfile" id="tenloai" name="tenLoai">
            <span class="error_message"></span>
        </div>
        <div class="button">
            <input type="submit" value="Thêm" name="add" class="button_add_admin delete_display_alert" />
            <a href="javascript:history.go(-1);"><input type="button" value="Quay lại" class="button_add_admin" /></a>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mong muốn của chúng ta
        Validator({
            form: '#form-3',
            formGroupSelector: '.form_field',
            errorSelector: '.error_message',
            rules: [
                Validator.isRequired('#tenloai', 'Vui lòng nhập tên loại!'),
            ],
            onSubmit: function (data) {
                // Call API
                //console.log(data);
            }
        });
    });
    const load = document.querySelector.bind(document);
    const alert_delete_btn = load(".delete_display_alert");
    const alert_delete_conform_btn = load(".delete_conform");
    const alert_delete = load(".alert_delete");
    alert_delete_btn.onclick = () => {
        alert_delete.style.display = "block";
    };
</script>
<?php include '../templates/nav_admin2.php' ?>