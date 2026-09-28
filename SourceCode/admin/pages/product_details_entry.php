<?php
include '../templates/nav_admin1.php';
$id = $_GET['id'];

$query = "SELECT chi_tiet_phieu_nhap.*, phieu_nhap.ngayNhap, tenSanPham FROM `chi_tiet_phieu_nhap` 
JOIN phieu_nhap on phieu_nhap.maPhieuNhap = chi_tiet_phieu_nhap.maPhieuNhap 
JOIN san_pham on san_pham.maSanPham = chi_tiet_phieu_nhap.maSanPham
WHERE chi_tiet_phieu_nhap.`maPhieuNhap` = '" . $id . "'";
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetch();
?>
<div class="body" style="margin-top: 15px">
    <style>
        input {
            font-size: 16px;
        }

        h3 {
            font-size: 20px;
        }

        table {
            border-collapse: collapse;
        }
    </style>
    <h1 align="center" class="Title_Admin_create_form">CHI TIẾT PHIẾU NHẬP</h1>

    <div class="form_field" style="width: 50%; float: left;">
        <h3>Mã phiếu</h3>
        <input type="text" class="textfile" readonly value="<?php echo $id; ?>" name="MAPHIEUNK">
    </div>
    <div class="form_field" style="width: 50%; float: right;">
        <h3>Ngày nhập</h3>
        <input type="date" class="textfile" id="ngay" value="<?php echo $result->ngayNhap; ?>" name="NGAYNK" readonly>
        <span class="error_message"></span>
    </div>
    <table id="productTable" width="100%">
        <tr>
            <th>Sản Phẩm</th>
            <th>Số Lượng</th>
            <th>Giá Nhập</th>
        </tr>
        <tr>
            <!-- hiển thị chi tiết nhập kho -->
            <?php include '../includes/show_details_entry_table.php' ?>
        </tr>

    </table>

    <div align="center" style="margin-top:10px" class="menu-wrapper">
        <ul class="pagination menu">
            <!-- phân trang -->
            <?php include '../includes/paging_with_id.php' ?>

        </ul>
    </div>
    <div class="button">
        <a href="<?php echo "entry_index.php"; ?> "><input type="button" value="Quay lại"
                class="button_add_admin" /></a>
    </div>


</div>

<script src="../../assets/js/jquery-3.6.0.min.js"></script>
<?php include '../templates/nav_admin2.php' ?>