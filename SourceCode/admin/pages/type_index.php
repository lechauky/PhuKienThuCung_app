<?php include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai,'LSP'); 
?>

<div class="table_header">
    <div class="add_admin">
        <a href="type_create.php">
            <i class="fa-solid fa-user-plus"></i>
            <div class="add_title">
                Thêm loại
            </div>
        </a>
    </div>
</div>
<table class="table_dsadmin">
    <thead>
        <tr>
            <th style="width: 65px;">Mã loại</th>
            <th style="width: 120px;">Tên Loại</th>
            <th style="width: 80px;">Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php include '../includes/show_type_table.php' ?>
    </tbody>
</table>
<div align="center" style="margin-top:10px" class="menu-wrapper">
    <ul class="pagination menu">
        <?php include '../includes/pagination.php'; ?>

    </ul>
</div>
<?php include '../templates/nav_admin2.php' ?>