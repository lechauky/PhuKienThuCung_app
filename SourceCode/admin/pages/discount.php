<?php include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'GG');
?>

<div class="table_header">
    <div class="add_admin">
        <a href="discount_add.php">
            <i class="fa-solid fa-user-plus"></i>
            <div class="add_title">
                Thêm giảm giá
            </div>
        </a>
    </div>
</div>
<table class="table_dsadmin">
    <thead>
        <tr>
            <th style="width: 35%;">Tên sản phẩm</th>
            <th style="width: 20%;">Loại giảm giá</th>
            <th style="width: 15%;">Giá trị giảm</th>
            <th style="width: 15%;">Ngày bắt đầu</th>
            <th style="width: 15%;">Ngày kết thúc</th>
        </tr>
    </thead>
    <tbody>
        <?php include '../includes/show_discount_table.php' ?>
    </tbody>
</table>
<div align="center" style="margin-top:10px" class="menu-wrapper">
    <ul class="pagination menu">
        <?php include '../includes/pagination.php'; ?>

    </ul>
</div>
<?php include '../templates/nav_admin2.php' ?>