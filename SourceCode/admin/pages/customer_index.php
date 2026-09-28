<?php include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'KH');
?>
<!-- SELECT concat(khach_hang.hoKhachHang,' ',khach_hang.tenKhachHang) AS hoTenKhachHang, khach_hang.dienThoai, khach_hang.ngaySinh,khach_hang.email, concat(khach_hang.diaChiCuThe,", ",xa.tenXa,", ",huyen.tenHuyen,", ",tinh.tenTinh) AS diaChi FROM khach_hang JOIN xa on khach_hang.maXa=xa.maXa JOIN huyen on huyen.maHuyen=xa.maHuyen JOIN tinh on tinh.maTinh=huyen.maTinh -->

<?php
$rowOfPage = 10;
$totalRows = $dbh->query('SELECT COUNT(*) FROM khach_hang')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$query = 'SELECT concat(khach_hang.hoKhachHang," ",khach_hang.tenKhachHang) AS hoTenKhachHang, khach_hang.dienThoai, khach_hang.ngaySinh, khach_hang.email, concat(khach_hang.diaChiCuThe,", ",xa.tenXa,", ",huyen.tenHuyen,", ",tinh.tenTinh) AS diaChi FROM khach_hang JOIN xa on khach_hang.maXa=xa.maXa JOIN huyen on huyen.maHuyen=xa.maHuyen JOIN tinh on tinh.maTinh=huyen.maTinh';
$query_extend = '';
$where_conditions = [];
if (!empty($_POST['tenKH']))
    $where_conditions[] = "concat(khach_hang.hoKhachHang,' ',khach_hang.tenKhachHang) LIKE '%{$_POST["tenKH"]}%'";
if (!empty($_POST['dienthoai']))
    $where_conditions[] = "khach_hang.dienThoai LIKE '%{$_POST["dienthoai"]}%'";
if (!empty($_POST['email']))
    $where_conditions[] = "khach_hang.email = '{$_POST["email"]}'";
if (!empty($where_conditions))
    $query_extend = " WHERE " . implode(" OR ", $where_conditions);
$query .= $query_extend . " LIMIT $rowOfPage  OFFSET " . (($currentPage - 1) * $rowOfPage);

$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
?>

<style>
    .Table_Details_Admin1 {
        width: 95%;
        margin-bottom: -15px;
        border: none;
        border-collapse: collapse;
    }

    .Table_Details_Admin1 td {
        text-align: left;
        border: none;
        padding-left: 10px;
        padding-bottom: -15px;
        border-collapse: collapse;

    }

    .table_dskhadmin {
        width: 100%;
        margin-top: 25px;
        border-collapse: collapse;
    }
</style>
<h1 class="Title_Admin_create_form">Tìm khách hàng</h1>
<div class="search_admin_header">
    <form action="" method="post">
        <table class="Table_Details_Admin1">
            <tr>
                <td>
                    <label for="">Họ tên khách hàng:</label>
                </td>
                <td>
                    <label for="">Số điện thoại:</label>
                </td>
                <td>
                    <label for="">Email:</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="textfile" id="fullname" name="tenKH" value="<?php if (isset($_POST['tenKH']))
                        echo $_POST['tenKH'] ?>" style="width: 100%; min-width: 300px;">
                    </td>
                    <td>
                        <input type="text" class="textfile" id="phoneNumber" value="<?php if (isset($_POST['dienthoai']))
                        echo $_POST['dienthoai'] ?>" name="dienthoai" style="width: 100%; min-width: 300px;">
                    </td>
                    <td>
                        <input type="text" class="textfile" id="email" name="email" value="<?php if (isset($_POST['email']))
                        echo $_POST['email'] ?>" style="width: 100%; min-width: 300px;">
                    </td>
                </tr>
            </table>
            <div class="search_button" style="position: absolute;top: 80px;">
                <input type="submit" value="Tìm kiếm" class="search_button_btn" />
            </div>
        </form>
    </div>
    <br><br>
    <table class="table_dskhadmin">
        <thead>
            <tr style="height: 40px;">
                <th style="width: 15%;">Họ tên</th>
                <th style="width: 10%;">Số điện thoại</th>
                <th style="width: 10%;">Ngày sinh</th>
                <th style="width: 20%;">Email</th>
                <th style="width: 45%;">Địa chỉ</th>
            </tr>
        </thead>
        <tbody>
            <?php
                    while ($row = $statement->fetch())
                        echo '
                            <tr style="height: 35px;">
                            <td>'
                            . $row->hoTenKhachHang . '
                            </td>
                            <td>'
                            . $row->dienThoai . '
                            </td>
                            <td>'
                            . $row->ngaySinh . '
                            </td>
                            <td>'
                            . $row->email . '
                            </td>
                            <td>'
                            . $row->diaChi . '
                            </td>
                        </tr>';
                    ?>
    </tbody>
</table>
<?php include '../includes/pagination.php' ?>
<?php include '../templates/nav_admin2.php' ?>