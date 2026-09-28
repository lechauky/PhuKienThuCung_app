<!--product page-->
<?php include '../templates/header.php';
require_once('../includes/config.php');
$id = $_GET['id'];
$tinhTrang = $_GET['tinhTrang'];

?>
<style>
    .my-table {
        width: 100%;
        border-collapse: collapse;
    }

    .my-table th,
    .my-table td {
        padding: 8px;
        border: 1px solid #ccc;
        text-align: center;
    }

    .my-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .my-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .my-table tbody tr:hover {
        background-color: #e6e6e6;
    }

    .menu-wrapper {

        height: auto;
        width: 100%;
    }

    .menu {
        margin: 0;
        padding: 0 0 0 20px;
    }

    .pagination a {
        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        font-size: 22px;
    }

    .pagination a.active {
        background-color: #CC3333;
        color: white;
        border: 1px solid #CC3333;
    }

    .menu li {
        display: inline-block;
        margin: 5px;
    }

    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }

    ul {
        list-style-type: none;
    }
</style>
<h4>Chi tiết đơn hàng</h4>
<div class="divider">
</div>
<h2>Mã đơn hàng:
    <?php echo $id; ?>
</h2>
<h2>Tình trạng:
    <?php if ($tinhTrang == 0) {
        echo "<span style=\"color:red;\">Đơn hàng bị hủy</span>";
    } else if ($tinhTrang == 1) {
        echo "<span style=\"color:blue;\">Đã giao</span>";
    } else if ($tinhTrang == 2) {
        echo "<span style=\"color:green;\">Đã xác nhận (Đang giao)</span>";

    } else {
        echo "<span>Chưa xác nhận</span>";
    } ?>
</h2>
<a href="javascript:history.go(-1);">
    <button class="button_add_admin" class="form-submit" style="width: 150px">Quay lại</button>
</a>
<div class="product_list" style="min-width: 700px;">
    <table class="my-table">
        <thead>
            <tr>
                <th style='width:200px;'>Sản phẩm</th>
                <th style='width:100px;'>Hình ảnh</th>
                <th style='width:50px;'>Số lượng</th>
                <th style='width:100px;'>Đơn giá</th>
                <th style='width:100px;'>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php include '../includes/show_order_details_table.php' ?>
        </tbody>
    </table>
    <div align="center" style="margin-top:10px" class="menu-wrapper">
        <ul class="pagination menu">
            <li>
                <a href="<?php echo "?page=1&id=" . $id . "&tinhTrang=" . $tinhTrang; ?>">&laquo;</a>
            </li>
            <?php
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i != $currentPage) {
                    echo "<li><a href=\"?id=" . $id . "&tinhTrang=" . $tinhTrang . "&page=" . $i . "\">" . $i . "</a></li>";
                } else {
                    echo "<li><a class=\"active\" href=\"?id=" . $id . "&tinhTrang=" . $tinhTrang . "&page=" . $i . "\">" . $i . "</a></li>";
                }

            }
            echo "<li>
            <a href=\"?id=" . $id . "&tinhTrang=" . $tinhTrang . "&page=$totalPages\">&raquo;</a>
        </li>";
            ?>

        </ul>
    </div>
</div>

<?php include '../templates/footer.php' ?>