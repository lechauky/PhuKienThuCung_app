<?php
// session_start();
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai,'TH');
?>

<div class="body" style="margin-top: 15px">
    <div class="table_header">
        <div class="add_admin">
            <a href="brand_create.php">
                <i class="fa-solid fa-user-plus"></i>
                <div class="add_title">
                    Thêm thương hiệu
                </div>
            </a>
        </div>
    </div>
    <table class="table_dsadmin">
        <thead>
            <tr>
                <th style="width: 65px;">Mã thương hiệu</th>
                <th style="width: 120px;">Tên thương hiệu</th>
                <th style="width: 150px;">Logo</th>
                <th style="width: 80px;">Chức năng</th>
            </tr>
        </thead>
        <tbody>
            <!-- hiển thị bảng thương hiệu -->
            <?php include '../includes/show_brand_table.php' ?>
        </tbody>
    </table>
    <!-- phân trang -->
    <?php include $_SESSION['rootPath'] . "/includes/pagination.php" ?>

</div>
<?php include '../templates/nav_admin2.php' ?>