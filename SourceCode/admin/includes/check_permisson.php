<?php
function check($loai, $trang) {
    $kho_pan = ['TH','LSP','SP','NK'];
    $sale_pan = ['DDH','KH','TB']; // TB: gửi thông báo tới app khách hàng
    if ($loai == 'LTK002') {
        if (!in_array($trang, $sale_pan)) { 
            header('Location: ../pages/access_permisson.php');
            exit();
        } 
    }
    if ($loai == 'LTK003') {
        if (!in_array($trang, $kho_pan)) { 
            header('Location: ../pages/access_permisson.php');
            exit();
        } 
    }
}
if (empty($_SESSION['admin'])) {
    header('Location: ./pages/Admin_Login.php');
    exit();
} else {
    $sql = "SELECT * FROM nhan_vien JOIN loai_tai_khoan ON nhan_vien.maLoai = loai_tai_khoan.maLoai WHERE nhan_vien.maNhanVien = '{$_SESSION['admin']->maNhanVien}' ";
    $stmt = $dbh->query($sql);
    $nv = $stmt->fetch(PDO::FETCH_OBJ);
}

?>