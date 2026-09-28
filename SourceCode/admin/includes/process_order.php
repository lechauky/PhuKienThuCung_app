<?php
/**
 * Xử lý đơn hàng trong trang admin: Xác nhận / Đã giao / Hủy đơn.
 * Cập nhật cho app mobile:
 *  - Chỉ cho phép chuyển trạng thái hợp lệ: Chưa xác nhận -> Đang giao -> Đã giao; hủy khi chưa giao.
 *  - Xác nhận: kiểm tra đủ tồn kho rồi mới trừ kho (trước đây có thể trừ âm kho).
 *  - Đã giao: ghi ngày giao (ngayGiao) để khách xem trên app.
 *  - Hủy đơn đã xác nhận: hoàn lại tồn kho đã trừ; lưu lý do hủy.
 *  - Mỗi lần đổi trạng thái gửi 1 thông báo cho khách (mục Thông báo trên app).
 *  - Dùng prepared statement + transaction.
 */
include 'config.php';
require_once 'app_helpers.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (empty($_SESSION['admin'])) {
    header('Location: ../pages/Admin_Login.php');
    exit();
}
// Chỉ Quản trị viên và Nhân viên bán hàng được xử lý đơn (giống check_permisson.php với 'DDH')
$stmt = $dbh->prepare('SELECT maLoai FROM nhan_vien WHERE maNhanVien = ?');
$stmt->execute([$_SESSION['admin']->maNhanVien]);
if ($stmt->fetchColumn() === 'LTK003') {
    header('Location: ../pages/access_permisson.php');
    exit();
}

$maDonHang = $_GET['id'] ?? '';
$hanhDong = $_GET['nut'] ?? '';
$lyDoHuy = trim($_POST['lyDoHuy'] ?? '');
$maNhanVien = $_SESSION['admin']->maNhanVien;

// Quay lại đúng trang đang đứng (danh sách có bộ lọc hoặc trang chi tiết)
$back = $_POST['back'] ?? '';
if (!preg_match('#^Order_(Index|Details)\.php(\?[\w=&%\-\+\.]*)?$#', $back)) {
    $back = 'Order_Index.php';
}

try {
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->beginTransaction();

    $stmt = $dbh->prepare('SELECT maDonHang, maKhachHang, (tinhTrang + 0) AS tinhTrang FROM don_dat_hang WHERE maDonHang = ? FOR UPDATE');
    $stmt->execute([$maDonHang]);
    $order = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$order) throw new RuntimeException("Không tìm thấy đơn hàng $maDonHang");
    $status = (int) $order->tinhTrang;

    $stmt = $dbh->prepare(
        'SELECT ct.maSanPham, ct.soLuong, sp.soLuong AS tonKho, sp.tenSanPham
         FROM chi_tiet_don_dat_hang ct JOIN san_pham sp ON sp.maSanPham = ct.maSanPham
         WHERE ct.maDonHang = ? FOR UPDATE'
    );
    $stmt->execute([$maDonHang]);
    $lines = $stmt->fetchAll(PDO::FETCH_OBJ);

    if ($hanhDong === 'xacNhan') {
        if ($status !== 3) throw new RuntimeException("Đơn $maDonHang không còn ở trạng thái Chưa xác nhận");
        foreach ($lines as $l) {
            if ((int) $l->soLuong > (int) $l->tonKho) {
                throw new RuntimeException("Không đủ hàng: \"{$l->tenSanPham}\" cần {$l->soLuong}, kho chỉ còn {$l->tonKho}. Hãy nhập thêm hàng hoặc hủy đơn.");
            }
        }
        $upd = $dbh->prepare('UPDATE san_pham SET soLuong = soLuong - ? WHERE maSanPham = ?');
        foreach ($lines as $l) $upd->execute([$l->soLuong, $l->maSanPham]);
        $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'10', maNhanVien = ? WHERE maDonHang = ?");
        $stmt->execute([$maNhanVien, $maDonHang]);
        app_notify($dbh, $order->maKhachHang, 'Đơn hàng đã được xác nhận',
            "Đơn hàng $maDonHang đã được cửa hàng xác nhận và đang được giao tới bạn.", $maDonHang);
        $msg = "Đã xác nhận đơn $maDonHang và trừ tồn kho.";
    } elseif ($hanhDong === 'giao') {
        if ($status !== 2) throw new RuntimeException("Chỉ đơn đang giao mới chuyển được sang Đã giao");
        $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'01', ngayGiao = ?, maNhanVien = ? WHERE maDonHang = ?");
        $stmt->execute([date('Y-m-d H:i:s'), $maNhanVien, $maDonHang]);
        app_notify($dbh, $order->maKhachHang, 'Giao hàng thành công',
            "Đơn hàng $maDonHang đã được giao thành công. Cảm ơn bạn đã mua sắm tại Paddy Pet Shop!", $maDonHang);
        $msg = "Đơn $maDonHang đã chuyển sang Đã giao.";
    } elseif ($hanhDong === 'huy') {
        if ($status !== 3 && $status !== 2) throw new RuntimeException("Không thể hủy đơn đã giao hoặc đã hủy");
        if ($status === 2) {
            // Đơn đã xác nhận thì kho đã bị trừ -> hoàn lại
            $upd = $dbh->prepare('UPDATE san_pham SET soLuong = soLuong + ? WHERE maSanPham = ?');
            foreach ($lines as $l) $upd->execute([$l->soLuong, $l->maSanPham]);
        }
        if (app_has_column($dbh, 'don_dat_hang', 'lyDoHuy')) {
            $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'00', maNhanVien = ?, lyDoHuy = ? WHERE maDonHang = ?");
            $stmt->execute([$maNhanVien, $lyDoHuy !== '' ? mb_substr($lyDoHuy, 0, 255) : null, $maDonHang]);
        } else {
            $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'00', maNhanVien = ? WHERE maDonHang = ?");
            $stmt->execute([$maNhanVien, $maDonHang]);
        }
        app_notify($dbh, $order->maKhachHang, 'Đơn hàng đã bị hủy',
            "Đơn hàng $maDonHang đã bị cửa hàng hủy." . ($lyDoHuy !== '' ? " Lý do: $lyDoHuy" : ''), $maDonHang);
        $msg = "Đã hủy đơn $maDonHang" . ($status === 2 ? ' và hoàn lại tồn kho.' : '.');
    } else {
        throw new RuntimeException('Hành động không hợp lệ');
    }

    $dbh->commit();
    $_SESSION['flash'] = ['success', $msg];
} catch (Throwable $e) {
    if ($dbh->inTransaction()) $dbh->rollBack();
    $_SESSION['flash'] = ['error', $e->getMessage()];
}

header('Location: ../pages/' . $back);
exit();
