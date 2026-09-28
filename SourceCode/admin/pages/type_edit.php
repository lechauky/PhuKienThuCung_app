<?php
include '../templates/nav_admin1.php';
include '../includes/get_type_data_from_id.php';
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
    <h1 class="Title_Admin_create_form">Chỉnh sửa loại sản phẩm</h1>
    <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
    <form action="../includes/edit_type.php" method="post" id="form">
        <div class="form_field">
            <label for="" class="name_form_field">Mã loại: </label>
            <input type="text" class="textfile" readonly value="<?php echo $result->maLoai ?>" name="maLoai">
        </div>
        <div class="form_field">
            <label for="" class="name_form_field">Tên loại: </label>
            <input type="text" class="textfile" id="tenloai" value="<?php echo $result->tenLoai ?>" name="tenLoai">
            <span class="error_message"></span>
        </div>
        <div class="button">
            <input type="submit" name="save" value="Chỉnh sửa" class="button_add_admin delete_display_alert" />
            <a href="javascript:history.go(-1);"><input type="button" value="Quay lại" class="button_add_admin" /></a>
        </div>
    </form>
</div>

<?php include '../templates/nav_admin2.php' ?>