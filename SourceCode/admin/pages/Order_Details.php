<?php
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'DDH');
require_once '../includes/app_helpers.php';
echo app_admin_styles();

$id = $_GET['id'] ?? '';
$stmt = $dbh->prepare(
    "SELECT d.*, (d.tinhTrang + 0) AS trangThai,
            k.hoKhachHang, k.tenKhachHang, k.dienThoai, k.email, k.diaChiCuThe, k.maKhachHang,
            xa.tenXa, huyen.tenHuyen, tinh.tenTinh,
            CONCAT(nv.ho, ' ', nv.ten) AS tenNhanVien
     FROM don_dat_hang d
     JOIN khach_hang k ON k.maKhachHang = d.maKhachHang
     LEFT JOIN xa ON xa.maXa = k.maXa
     LEFT JOIN huyen ON huyen.maHuyen = xa.maHuyen
     LEFT JOIN tinh ON tinh.maTinh = huyen.maTinh
     LEFT JOIN nhan_vien nv ON nv.maNhanVien = d.maNhanVien
     WHERE d.maDonHang = ?"
);
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_OBJ);

if (!$o) {
    echo "<h2>Không tìm thấy đơn hàng " . h($id) . "</h2><a href='Order_Index.php'>« Quay lại danh sách</a>";
    include '../templates/nav_admin2.php';
    exit();
}
$s = (int) $o->trangThai;

$stmt = $dbh->prepare(
    'SELECT sp.maSanPham, sp.tenSanPham, sp.hinhAnh, sp.soLuong AS tonKho, ct.soLuong, ct.donGia, ct.thanhTien
     FROM chi_tiet_don_dat_hang ct JOIN san_pham sp ON sp.maSanPham = ct.maSanPham
     WHERE ct.maDonHang = ? ORDER BY sp.maSanPham'
);
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_OBJ);

$diaChi = implode(', ', array_filter([$o->diaChiCuThe, $o->tenXa, $o->tenHuyen, $o->tenTinh]));
$back = 'Order_Details.php?id=' . rawurlencode($o->maDonHang);
$eid = h($o->maDonHang);
$backField = "<input type='hidden' name='back' value='" . h($back) . "'>";
?>
<a href="Order_Index.php" style="color:#CC3333!important">« Quay lại danh sách đơn</a>
<h2>Đơn hàng <?= $eid ?> <?= order_status_badge($s) ?> <?= isset($o->nguonDat) ? source_badge($o->nguonDat) : '' ?></h2>
<?= flash_html() ?>

<div class="info-grid">
    <div class="info-card">
        <h3><i class="fa-solid fa-user"></i> Khách hàng / Người nhận</h3>
        <b><?= h($o->hoKhachHang . ' ' . $o->tenKhachHang) ?></b> <span class="muted">(<?= h($o->maKhachHang) ?>)</span><br>
        <i class="fa-solid fa-phone"></i> <?= h($o->dienThoai) ?><br>
        <i class="fa-solid fa-envelope"></i> <?= h($o->email) ?><br>
        <i class="fa-solid fa-location-dot"></i> <?= h($diaChi) ?>
    </div>
    <div class="info-card">
        <h3><i class="fa-solid fa-receipt"></i> Thông tin đơn</h3>
        Ngày đặt: <b><?= h(date('d/m/Y H:i', strtotime($o->ngayDat))) ?></b><br>
        Ngày giao: <b><?= $o->ngayGiao ? h(date('d/m/Y H:i', strtotime($o->ngayGiao))) : '—' ?></b><br>
        Tình trạng: <?= h(order_status_text($s)) ?><br>
        Người xử lý: <?= $o->tenNhanVien ? h($o->tenNhanVien) : ($s === 0 ? '<span style="color:#dc3545">Khách tự hủy</span>' : '—') ?><br>
        Thanh toán: Khi nhận hàng (COD)
        <?php if (!empty($o->ghiChu)): ?><br>Ghi chú của khách: <i><?= h($o->ghiChu) ?></i><?php endif; ?>
        <?php if ($s === 0 && !empty($o->lyDoHuy)): ?><br><span style="color:#dc3545">Lý do hủy: <?= h($o->lyDoHuy) ?></span><?php endif; ?>
    </div>
</div>

<div style="margin:6px 0 12px">
    <?php if ($s === 3): ?>
        <form class="act-form" action="../includes/process_order.php?id=<?= $eid ?>&nut=xacNhan" method="post"><?= $backField ?>
            <button class="act-btn ok" type="submit"><i class="fa-solid fa-check"></i> Xác nhận đơn (trừ tồn kho)</button></form>
    <?php endif; ?>
    <?php if ($s === 2): ?>
        <form class="act-form" action="../includes/process_order.php?id=<?= $eid ?>&nut=giao" method="post"><?= $backField ?>
            <button class="act-btn ship" type="submit" onclick="return confirm('Xác nhận đơn đã giao thành công?')"><i class="fa-solid fa-truck"></i> Đã giao</button></form>
    <?php endif; ?>
    <?php if ($s === 3 || $s === 2): ?>
        <form class="act-form" action="../includes/process_order.php?id=<?= $eid ?>&nut=huy" method="post"
              onsubmit="var r = prompt('Lý do hủy đơn (khách sẽ thấy trên app):', ''); if (r === null) return false; this.lyDoHuy.value = r; return true;">
            <?= $backField ?><input type="hidden" name="lyDoHuy" value="">
            <button class="act-btn cancel" type="submit"><i class="fa-solid fa-xmark"></i> Hủy đơn<?= $s === 2 ? ' (hoàn kho)' : '' ?></button></form>
    <?php endif; ?>
</div>

<table class="table_dsadmin">
    <thead>
        <tr>
            <th>Hình</th>
            <th>Sản phẩm</th>
            <th>Số lượng</th>
            <?php if ($s === 3): ?><th>Tồn kho</th><?php endif; ?>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $it): ?>
        <tr style="height:60px">
            <td><img src="../../assets/img/sanpham/<?= h(rawurlencode($it->hinhAnh)) ?>" style="width:60px;height:60px;object-fit:contain" alt=""></td>
            <td style="text-align:left"><p><?= h($it->tenSanPham) ?> <span class="muted">(<?= h($it->maSanPham) ?>)</span></p></td>
            <td><p><?= (int) $it->soLuong ?></p></td>
            <?php if ($s === 3): ?>
                <td><p style="color:<?= $it->tonKho >= $it->soLuong ? '#28a745' : '#dc3545' ?>"><?= (int) $it->tonKho ?></p></td>
            <?php endif; ?>
            <td><p><?= vnd($it->donGia) ?></p></td>
            <td><p><b><?= vnd($it->thanhTien) ?></b></p></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="6">Không có dữ liệu</td></tr><?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="<?= $s === 3 ? 5 : 4 ?>" style="text-align:right"><b>Tổng tiền:</b></td>
            <td><b style="color:#CC3333"><?= vnd($o->tongTien) ?></b></td>
        </tr>
    </tfoot>
</table>
<?php include '../templates/nav_admin2.php' ?>
