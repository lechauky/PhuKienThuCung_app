<?php include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai,'NK');
?>

<div class="body" style="margin-top: 15px">
    <div class="table_header">
        <div class="add_admin">
            <a href="entry.php">
                <i class="fa-solid fa-user-plus"></i>
                <div class="add_title">
                    Thêm sản phẩm vào kho
                </div>
            </a>
        </div>
    </div>
    <table class="table_dsadmin">
        <thead>
            <tr>
                <th style="width: 65px;">Mã phiếu nhập</th>
                <th style="width: 65px;">Ngày nhập kho</th>
                <th style="width: 65px;">Nhân viên nhập</th>
                <th style="width: 50px;">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <!-- hiển thị bảng nhập kho -->
            <?php include '../includes/show_warehouse_table.php' ?>
        </tbody>
    </table>
    <!-- phân trang -->
    <?php include $_SESSION['rootPath'] . "/includes/pagination.php" ?>
</div>

<?php include '../templates/nav_admin2.php' ?>