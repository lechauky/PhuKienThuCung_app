<!--product page-->
<?php include '../templates/header.php';
require_once('../includes/config.php');
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
</style>
<h4>Lịch sử mua hàng</h4>
<div class="divider">
</div>
<div class="product_list" style="min-width: 700px;">
    <table class="my-table">
        <thead>
            <tr>
                <th style='width:50px;'>Mã đơn</th>
                <th style='width:200px;'>Ngày Đặt</th>
                <th style='width:200px;'>Ngày Giao</th>
                <th style='width:50px;'>Tổng tiền</th>
                <th style='width:100px;'>Tình trạng</th>
                <th style='width:100px; color: black;'>Chi tiết đơn hàng</th>
            </tr>
        </thead>
        <tbody>
            <?php include '../includes/show_order_in_table.php' ?>
        </tbody>
    </table>
    <?php include '../includes/product_pagination.php' ?>
</div>

<?php include '../templates/footer.php' ?>