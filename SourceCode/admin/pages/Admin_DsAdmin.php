<?php include '../templates/nav_admin1.php';

include '../includes/check_permisson.php';
check($nv->maLoai, 'NV');

$query = 'SELECT maNhanVien, ho,ten,diaChiCuThe,dienThoai,tenLoai, tenNguoiDung, avatar, email, concat(nhan_vien.diaChiCuThe,", ",xa.tenXa,", ",huyen.tenHuyen,", ",tinh.tenTinh) AS diaChi FROM nhan_vien JOIN loai_tai_khoan ON nhan_vien.maLoai = loai_tai_khoan.maLoai JOIN xa on nhan_vien.maXa=xa.maXa JOIN huyen on huyen.maHuyen=xa.maHuyen JOIN tinh on tinh.maTinh=huyen.maTinh';
$stmt = $dbh->prepare($query);
$stmt->execute();
$nhanVien = $stmt->fetchAll(PDO::FETCH_OBJ);

$query_tenloai = "SELECT maLoai, tenLoai FROM loai_tai_khoan";
$statement = $dbh->prepare($query_tenloai);
$statement->execute();
$loaitaikhoan = $statement->fetchAll(PDO::FETCH_OBJ);

?>
<div class="table_header">

    <div class="add_admin">
        <a href="./Admin_Create.php">
            <i class="fa-solid fa-user-plus"></i>
            <div class="add_title">
                Thêm Nhân viên
            </div>
        </a>
    </div>
</div>
<table class="table_dsadmin">
    <thead>
        <tr>
            <th style="width: 7%;">Mã Nhân Viên</th>
            <th style="width: 10%;">Họ tên</th>
            <th style="width: 25%;">Địa chỉ</th>
            <th style="width: 7%;">Số điện thoại</th>
            <th style="width: 10%;">Loại tài khoản</th>
            <th style="width: 7%;">Tên đăng nhập</th>
            <th style="width: 7%;">Hình ảnh</th>
            <th style="width: 10%;">Email</th>
            <th style="width: 7%;">Chức năng</th>
        </tr>
    </thead>
    <tbody>
        <?php

        foreach ($nhanVien as $row) {
            $xoasua = "";
            if ($row->tenLoai != "Quản Lý") {
                $xoasua = '<a href="./Admin_editPQ.php?maNhanVien=' . $row->maNhanVien . '" ><i class="fa-solid fa-pen-to-square edit"></i></a>
                <a href="./Admin_delete.php?maNhanVien=' . $row->maNhanVien . '" ><i class="fa-solid fa-xmark remove"></i></a>';
            }
            echo '
            <tr>
                <td>
                ' . $row->maNhanVien . '
                </td>
                <td>
                ' . $row->ho . " " . $row->ten . '
                </td>
                <td>
                ' . $row->diaChi . '
                </td>
                <td>
                ' . $row->dienThoai . '
                </td>
                <td>
                ' . $row->tenLoai . '
                </td>
                <td>
                ' . $row->tenNguoiDung . '
                </td>
                <td>
                    <img src="../../assets/img/ad_user/' . $row->avatar . '" alt="" style="width: 70px; height: 70px;">
                </td>
                <td>
                ' . $row->email . '
                </td>
                <td>
                        ' . $xoasua . '
                        
                        <a href="./Admin_DetailsDs.php?maNhanVien=' . $row->maNhanVien . '"><i class="fa-solid fa-circle-info detail"></i></a>
                </td>
            </tr>
            ';
        }
        ?>
    </tbody>


</table>
<?php include '../templates/nav_admin2.php' ?>