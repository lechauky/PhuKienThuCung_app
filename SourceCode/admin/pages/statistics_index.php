<?php
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'TK');
$thangBatDau;
$thangBatDau;
$thangKetThuc;
$namKetThuc;
$flag = false;

if (isset($_POST["baoCao"])) {
    if (empty($_POST["thangBatDau"]) || empty($_POST["namBatDau"]) || empty($_POST["thangKetThuc"]) || empty($_POST["namKetThuc"])) {
        $error = "Chưa nhập thông tin đủ";

    } else {
        $thangBatDau = $_POST["thangBatDau"];
        $namBatDau = $_POST["namBatDau"];
        $thangKetThuc = $_POST["thangKetThuc"];
        $namKetThuc = $_POST["namKetThuc"];

        if (($namBatDau > $namKetThuc) || ($namBatDau <= $namKetThuc && $thangBatDau > $thangKetThuc)) {
            $error = "Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc";
        } else {
            $flag = true;
        }
    }

}
?>
<div class="body" style="margin-top: 15px">
    <div class="search_admin">
        <h1 class="Title_Admin_create_form">Thống kế doanh thu</h1>
        <div class="search_admin_header" style="margin-bottom:80px;">
            <form action="" method="post">
                <table class="Table_Details_Admin" style="margin-left:330px; margin-bottom:20px">
                    <tr style="display:flex; justify-content:center; align-items:center">
                        <td style="display:flex; justify-content: center; align-items:center;">
                            <label for="" class="name_form_field" style="margin-right:10px">Tháng bắt đầu : </label>
                            <input type="number" class="textfile" name="thangBatDau" style="min-width:50px" min="1"
                                max="12" value="<?php if (!empty($thangBatDau))
                                    echo $thangBatDau; ?>" maxlength="2" id="thangBatDau">
                        </td>
                        <td style="display:flex; justify-content: center; align-items:center;">
                            <label for="" class="name_form_field" style="margin-right:10px">Năm bắt đầu: </label>
                            <input type="number" class="textfile" name="namBatDau" style="min-width:50px" value="<?php if (!empty($thangBatDau))
                                echo $namBatDau; ?>" minlength="4" maxlength="4" id="namBatDau">
                        </td>
                    </tr>
                    <tr style="display:flex">
                        <td style="display:flex; justify-content: center; align-items:center;">
                            <label for="" class="name_form_field" style="margin-right:10px">Tháng kết thúc: </label>
                            <input type="number" class="textfile" name="thangKetThuc" style="min-width:50px" min="1"
                                max="12" value="<?php if (!empty($thangKetThuc))
                                    echo $thangKetThuc; ?>" maxlength="2" id="thangKetThuc">
                        </td>
                        <td style="display:flex; justify-content: center; align-items:center;">
                            <label for="" class="name_form_field" style="margin-right:10px">Năm kết thúc: </label>
                            <input type="number" class="textfile" name="namKetThuc" style="min-width:50px" value="<?php if (!empty($namKetThuc))
                                echo $namKetThuc; ?>" minlength="4" maxlength="4" id="namKetThuc">
                        </td>
                    </tr>

                </table>
                <h6 id="error" style="font-size: 16px; color: red">
                    <?php if (!empty($error))
                        echo $error; ?>
                </h6>
                <div class="search_button" style="">
                    <input type="submit" value="Báo cáo" name="baoCao" class="search_button_btn"
                        onclick="validateForm()" />
                    <a href="statistics_index.php"><input type="button" value="Nhập lại"
                            class="search_button_btn" /></a>
                </div>
            </form>
        </div>
        <table class="table_dsadmin">
            <thead>
                <tr>
                    <th style="width: 30px;">STT</th>
                    <th style="width: 65px;">Mã đơn hàng</th>
                    <th style="width: 65px;">Ngày đơn được đặt</th>
                    <th style="width: 150px;">Tên sản phẩm</th>
                    <th style="width: 65px;">Số lượng bán</th>
                    <th style="width: 75px;">Đơn giá bán</th>
                    <th style="width: 80px;">Thành Tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php include "../includes/show_statistics_product_table.php" ?>
            </tbody>

        </table>
        <div style="display:block; justify-content:center; width: 100%">
            <div>
                <h4
                    style="font-size: 18px; display: flex; justify-content: center; margin-top :50px; margin-bottom: 15px;">
                    Thống kê theo tháng
                </h4>
                <div style="display: flex; justify-content: center">
                    <table class="table_dsadmin" style="max-width: 5000px">
                        <thead>
                            <tr>
                                <th style="width: 30px;">STT</th>
                                <th style="width: 65px;">Thời gian</th>
                                <th style="width: 75px;">Tổng doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php include "../includes/show_statistics_month_table.php" ?>
                        </tbody>

                    </table>

                </div>
                <h6 style="font-size: 20px; display: flex; justify-content: center; margin:70px">Tổng toàn bộ doanh thu:
                    <?php include "../includes/caculate_revenue.php" ?>
                </h6>
            </div>
        </div>

    </div>
</div>

<?php include '../templates/nav_admin2.php' ?>