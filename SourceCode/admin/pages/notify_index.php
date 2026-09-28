<?php
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'TB');
require_once '../includes/app_helpers.php';
echo app_admin_styles();

if (!app_has_table($dbh, 'thong_bao')) {
    echo "<h2>Thông báo app</h2><div class='flash error'>Chưa có bảng <b>thong_bao</b>. Hãy chạy file Database/capnhat_app_mobile.sql trong phpMyAdmin.</div>";
    include '../templates/nav_admin2.php';
    exit();
}
$customers = $dbh->query("SELECT maKhachHang, CONCAT(hoKhachHang, ' ', tenKhachHang) AS ten, tenNguoiDung FROM khach_hang ORDER BY maKhachHang")->fetchAll(PDO::FETCH_OBJ);
// Thông báo gửi gần đây (gộp các thông báo gửi hàng loạt cùng lúc)
$recent = $dbh->query(
    "SELECT tieuDe, noiDung, ngayTao, maDonHang, COUNT(*) AS soNguoi, SUM(daDoc) AS daDoc,
            MIN(maKhachHang) AS maKH
     FROM thong_bao GROUP BY tieuDe, noiDung, ngayTao, maDonHang ORDER BY ngayTao DESC LIMIT 30"
)->fetchAll(PDO::FETCH_OBJ);
?>
<h2>Thông báo tới app khách hàng</h2>
<p class="muted">Thông báo hiện trong mục <b>Thông báo</b> của app. Hệ thống cũng tự gửi thông báo khi đơn hàng được xác nhận, giao hoặc hủy.</p>
<?= flash_html() ?>

<div class="info-card" style="margin-bottom:14px">
    <h3><i class="fa-solid fa-bullhorn"></i> Gửi thông báo mới</h3>
    <form action="../includes/notify_action.php" method="post" style="display:flex;flex-direction:column;gap:8px;max-width:640px">
        <select name="maKhachHang" style="padding:6px;border:1px solid #ccc;border-radius:6px;color:#333">
            <option value="">Tất cả khách hàng (<?= count($customers) ?>)</option>
            <?php foreach ($customers as $c): ?>
                <option value="<?= h($c->maKhachHang) ?>"><?= h($c->maKhachHang . ' - ' . $c->ten . ' (@' . $c->tenNguoiDung . ')') ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="tieuDe" maxlength="255" required placeholder="Tiêu đề, vd: Giảm 20% pate cho mèo cuối tuần"
               style="padding:6px;border:1px solid #ccc;border-radius:6px;color:#333">
        <textarea name="noiDung" maxlength="500" required rows="3" placeholder="Nội dung thông báo"
                  style="padding:6px;border:1px solid #ccc;border-radius:6px;color:#333"></textarea>
        <div><button type="submit" class="act-btn ok" style="padding:8px 16px"><i class="fa-solid fa-paper-plane"></i> Gửi</button></div>
    </form>
</div>

<table class="table_dsadmin">
    <thead><tr><th>Thời gian</th><th>Tiêu đề</th><th>Nội dung</th><th>Đơn hàng</th><th>Người nhận</th><th>Đã đọc</th></tr></thead>
    <tbody>
    <?php foreach ($recent as $r): ?>
        <tr>
            <td><p><?= h(date('d/m/Y H:i', strtotime($r->ngayTao))) ?></p></td>
            <td style="text-align:left"><p><b><?= h($r->tieuDe) ?></b></p></td>
            <td style="text-align:left"><p><?= h($r->noiDung) ?></p></td>
            <td><p><?= $r->maDonHang ? "<a href='Order_Details.php?id=" . h($r->maDonHang) . "' style='color:#CC3333!important'>" . h($r->maDonHang) . "</a>" : '—' ?></p></td>
            <td><p><?= $r->soNguoi > 1 ? (int) $r->soNguoi . ' khách' : h($r->maKH) ?></p></td>
            <td><p><?= (int) $r->daDoc ?>/<?= (int) $r->soNguoi ?></p></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$recent): ?><tr><td colspan="6">Chưa có thông báo nào</td></tr><?php endif; ?>
    </tbody>
</table>
<?php include '../templates/nav_admin2.php' ?>
