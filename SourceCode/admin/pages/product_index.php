<?php include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai,'SP');
?>
<div class="body" style="margin-top: 15px">
    <div class="table_header">
        <div class="add_admin">
            <a href="product_create.php">
                <i class="fa-solid fa-user-plus"></i>
                <div class="add_title">
                    Thêm sản phẩm
                </div>
            </a>
        </div>
    </div>
    <table class="table_dsadmin">
        <thead>
            <tr style="font-size: 15px;">
                <th style="width: 7%;">Mã Sản Phẩm </th>
                <th style="width: 10%;">Tên Sản Phẩm</th>
                <th style="width: 5%;">Giá mua</th>
                <th style="width: 5%;">Giá bán</th>
                <th style="width: 7%;">Thương hiệu</th>
                <th style="width: 9%;">Loại</th>
                <th style="width: 5%;">Số lượng</th>
                <th style="width: 15%;">Mô tả</th>
                <th style="width: 6%;">Hình ảnh</th>
                <th style="width: 7%;">Chức năng</th>
            </tr>
        </thead>
        <tbody>
            <!-- hiển thị danh sách sản phẩm -->
            <?php include '../includes/show_product_table.php' ?>

        </tbody>
    </table>
    <style>
        p {
            font-size: 15px;
            padding: 5px;
        }
    </style>
    <?php include '../includes/pagination.php' ?>
</div>
<?php include '../templates/nav_admin2.php' ?>