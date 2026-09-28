<?php
/** Gửi thông báo (khuyến mãi, tin tức...) tới app của 1 khách hoặc tất cả khách hàng */
include 'config.php';
require_once 'app_helpers.php';

if (empty($_SESSION['admin'])) { header('Location: ../pages/Admin_Login.php'); exit(); }
$stmt = $dbh->prepare('SELECT maLoai FROM nhan_vien WHERE maNhanVien = ?');
$stmt->execute([$_SESSION['admin']->maNhanVien]);
if ($stmt->fetchColumn() === 'LTK003') { header('Location: ../pages/access_permisson.php'); exit(); }

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$tieuDe = trim($_POST['tieuDe'] ?? '');
$noiDung = trim($_POST['noiDung'] ?? '');
$nguoiNhan = trim($_POST['maKhachHang'] ?? '');

try {
    if ($tieuDe === '' || $noiDung === '') throw new RuntimeException('Vui lòng nhập tiêu đề và nội dung');
    $tieuDe = mb_substr($tieuDe, 0, 255);
    $noiDung = mb_substr($noiDung, 0, 500);
    if ($nguoiNhan === '') {
        $ids = $dbh->query('SELECT maKhachHang FROM khach_hang')->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $stmt = $dbh->prepare('SELECT maKhachHang FROM khach_hang WHERE maKhachHang = ?');
        $stmt->execute([$nguoiNhan]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!$ids) throw new RuntimeException('Không tìm thấy khách hàng ' . $nguoiNhan);
    }
    $dbh->beginTransaction();
    foreach ($ids as $id) app_notify($dbh, $id, $tieuDe, $noiDung, null);
    $dbh->commit();
    $_SESSION['flash'] = ['success', 'Đã gửi thông báo tới ' . count($ids) . ' khách hàng'];
} catch (Throwable $e) {
    if ($dbh->inTransaction()) $dbh->rollBack();
    $_SESSION['flash'] = ['error', $e->getMessage()];
}
header('Location: ../pages/notify_index.php');
exit();
