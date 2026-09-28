<?php
/** Thêm / ẩn-hiện / sắp xếp / xóa banner hiển thị trên trang chủ app mobile (chỉ Quản trị viên) */
include 'config.php';
require_once 'app_helpers.php';

if (empty($_SESSION['admin'])) { header('Location: ../pages/Admin_Login.php'); exit(); }
$stmt = $dbh->prepare('SELECT maLoai FROM nhan_vien WHERE maNhanVien = ?');
$stmt->execute([$_SESSION['admin']->maNhanVien]);
if ($stmt->fetchColumn() !== 'LTK001') { header('Location: ../pages/access_permisson.php'); exit(); }

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$action = $_POST['action'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$bannerDir = __DIR__ . '/../../assets/img/banner/';

try {
    if ($action === 'add') {
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Vui lòng chọn ảnh banner');
        if ($_FILES['image']['size'] > 3 * 1024 * 1024) throw new RuntimeException('Ảnh tối đa 3MB');
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) throw new RuntimeException('Chỉ nhận ảnh JPG, PNG, WEBP');
        if (@getimagesize($_FILES['image']['tmp_name']) === false) throw new RuntimeException('File không phải ảnh hợp lệ');
        $fileName = 'app_banner_' . date('YmdHis') . '_' . mt_rand(100, 999) . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $bannerDir . $fileName)) throw new RuntimeException('Không lưu được ảnh');

        $maSanPham = trim($_POST['maSanPham'] ?? '') ?: null;
        $order = (int) $dbh->query('SELECT COALESCE(MAX(thuTu), 0) + 1 FROM banner')->fetchColumn();
        $stmt = $dbh->prepare('INSERT INTO banner (hinhAnh, tieuDe, maSanPham, thuTu, hienThi) VALUES (?, ?, ?, ?, 1)');
        $stmt->execute([$fileName, trim($_POST['tieuDe'] ?? '') ?: null, $maSanPham, $order]);
        $_SESSION['flash'] = ['success', 'Đã thêm banner'];
    } elseif ($action === 'toggle') {
        $dbh->prepare('UPDATE banner SET hienThi = 1 - hienThi WHERE maBanner = ?')->execute([$id]);
        $_SESSION['flash'] = ['success', 'Đã cập nhật trạng thái hiển thị'];
    } elseif ($action === 'up' || $action === 'down') {
        $rows = $dbh->query('SELECT maBanner FROM banner ORDER BY thuTu, maBanner')->fetchAll(PDO::FETCH_COLUMN);
        $pos = array_search($id, array_map('intval', $rows), true);
        $swap = $action === 'up' ? $pos - 1 : $pos + 1;
        if ($pos !== false && isset($rows[$swap])) {
            [$rows[$pos], $rows[$swap]] = [$rows[$swap], $rows[$pos]];
            $upd = $dbh->prepare('UPDATE banner SET thuTu = ? WHERE maBanner = ?');
            foreach ($rows as $i => $bid) $upd->execute([$i + 1, $bid]);
        }
    } elseif ($action === 'delete') {
        $stmt = $dbh->prepare('SELECT hinhAnh FROM banner WHERE maBanner = ?');
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        $dbh->prepare('DELETE FROM banner WHERE maBanner = ?')->execute([$id]);
        // Chỉ xóa file do trang này upload (không xóa ảnh banner gốc của website)
        if ($file && strpos($file, 'app_banner_') === 0 && is_file($bannerDir . $file)) @unlink($bannerDir . $file);
        $_SESSION['flash'] = ['success', 'Đã xóa banner'];
    }
} catch (Throwable $e) {
    $_SESSION['flash'] = ['error', $e->getMessage()];
}
header('Location: ../pages/banner_index.php');
exit();
