<?php
/**
 * Danh sách đơn hàng (admin) - đã nâng cấp để quản lý đơn từ cả Website và App mobile:
 * lọc theo tình trạng / nguồn đặt, tìm theo mã đơn hoặc tên / SĐT khách, hiển thị nguồn đặt,
 * người xử lý (hoặc "Khách tự hủy"), nút thao tác theo đúng trạng thái.
 */
require_once __DIR__ . '/app_helpers.php';

$hasSource = app_has_column($dbh, 'don_dat_hang', 'nguonDat');
$status = isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : null;
$nguon = strtoupper($_GET['nguon'] ?? '');
$q = trim($_GET['q'] ?? '');

// Điều kiện lọc (prepared statement)
$where = [];
$params = [];
if ($q !== '') {
    $where[] = "(d.maDonHang LIKE ? OR CONCAT(k.hoKhachHang, ' ', k.tenKhachHang) LIKE ? OR k.dienThoai LIKE ?)";
    array_push($params, "%$q%", "%$q%", "%$q%");
}
if ($hasSource && in_array($nguon, ['WEB', 'APP'], true)) {
    $where[] = 'd.nguonDat = ?';
    $params[] = $nguon;
}
$baseWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Đếm số đơn theo từng tình trạng (cho các tab)
$stmt = $dbh->prepare("SELECT (d.tinhTrang + 0) AS s, COUNT(*) AS c FROM don_dat_hang d JOIN khach_hang k ON k.maKhachHang = d.maKhachHang $baseWhere GROUP BY s");
$stmt->execute($params);
$counts = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) $counts[(int) $r['s']] = (int) $r['c'];
$allCount = array_sum($counts);

if ($status !== null) {
    $where[] = '(d.tinhTrang + 0) = ?';
    $params[] = $status;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$rowOfPage = 10;
$stmt = $dbh->prepare("SELECT COUNT(*) FROM don_dat_hang d JOIN khach_hang k ON k.maKhachHang = d.maKhachHang $whereSql");
$stmt->execute($params);
$totalRows = (int) $stmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $rowOfPage));
$currentPage = min($totalPages, max(1, (int) ($_GET['page'] ?? 1)));

$stmt = $dbh->prepare(
    "SELECT d.*, (d.tinhTrang + 0) AS trangThai,
            CONCAT(k.hoKhachHang, ' ', k.tenKhachHang) AS tenKhachHang, k.dienThoai,
            CONCAT(nv.ho, ' ', nv.ten) AS tenNhanVien
     FROM don_dat_hang d
     JOIN khach_hang k ON k.maKhachHang = d.maKhachHang
     LEFT JOIN nhan_vien nv ON nv.maNhanVien = d.maNhanVien
     $whereSql
     ORDER BY FIELD(d.tinhTrang + 0, 3, 2, 1, 0), d.ngayDat DESC, d.maDonHang DESC
     LIMIT $rowOfPage OFFSET " . (($currentPage - 1) * $rowOfPage)
);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_OBJ);

// Tạo link giữ nguyên bộ lọc
function order_link(array $override = []): string
{
    $p = array_merge(['status' => $_GET['status'] ?? '', 'nguon' => $_GET['nguon'] ?? '', 'q' => $_GET['q'] ?? '', 'page' => 1], $override);
    $p = array_filter($p, fn($v) => $v !== '' && $v !== null);
    return 'Order_Index.php' . ($p ? '?' . http_build_query($p) : '');
}
$currentUrl = order_link(['page' => $currentPage]);

$tabs = [['', 'Tất cả', $allCount], [3, 'Chưa xác nhận', $counts[3]], [2, 'Đang giao', $counts[2]], [1, 'Đã giao', $counts[1]], [0, 'Đã hủy', $counts[0]]];
?>
<div class="order-tabs">
    <?php foreach ($tabs as [$val, $label, $cnt]):
        $active = ($status === null && $val === '') || ($status !== null && $val !== '' && $status === $val); ?>
        <a class="<?= $active ? 'active' : '' ?>" href="<?= h(order_link(['status' => $val])) ?>">
            <?= h($label) ?><span class="count"><?= $cnt ?></span>
        </a>
    <?php endforeach; ?>
</div>
<form class="order-toolbar" method="GET" action="Order_Index.php">
    <?php if ($status !== null): ?><input type="hidden" name="status" value="<?= $status ?>"><?php endif; ?>
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Mã đơn, tên hoặc SĐT khách" style="width:260px">
    <?php if ($hasSource): ?>
        <select name="nguon">
            <option value="">Tất cả nguồn</option>
            <option value="WEB" <?= $nguon === 'WEB' ? 'selected' : '' ?>>Website</option>
            <option value="APP" <?= $nguon === 'APP' ? 'selected' : '' ?>>App mobile</option>
        </select>
    <?php endif; ?>
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Lọc</button>
    <?php if ($q !== '' || $nguon !== ''): ?><a href="<?= h(order_link(['q' => '', 'nguon' => ''])) ?>" style="color:#CC3333!important">Xóa lọc</a><?php endif; ?>
</form>

<table class="table_dsadmin">
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <?php if ($hasSource): ?><th>Nguồn</th><?php endif; ?>
            <th>Ngày đặt</th>
            <th>Ngày giao</th>
            <th>Tổng tiền</th>
            <th>Tình trạng</th>
            <th>Người xử lý</th>
            <th>Chi tiết</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
<?php
if ($result) {
    foreach ($result as $row) {
        $s = (int) $row->trangThai;
        $id = h($row->maDonHang);
        $backField = "<input type='hidden' name='back' value='" . h($currentUrl) . "'>";
        $btnXacNhan = "<form class='act-form' action='../includes/process_order.php?id=$id&nut=xacNhan' method='post'>$backField
            <button class='act-btn ok' type='submit'>Xác nhận</button></form>";
        $btnGiao = "<form class='act-form' action='../includes/process_order.php?id=$id&nut=giao' method='post'>$backField
            <button class='act-btn ship' type='submit' onclick=\"return confirm('Xác nhận đơn $id đã giao thành công?')\">Đã giao</button></form>";
        $btnHuy = "<form class='act-form' action='../includes/process_order.php?id=$id&nut=huy' method='post'
            onsubmit=\"var r = prompt('Lý do hủy đơn $id (khách sẽ thấy trên app):', ''); if (r === null) return false; this.lyDoHuy.value = r; return true;\">
            $backField<input type='hidden' name='lyDoHuy' value=''>
            <button class='act-btn cancel' type='submit'>Hủy đơn</button></form>";

        $btn = '';
        if ($s === 3) $btn = $btnXacNhan . $btnHuy;
        elseif ($s === 2) $btn = $btnGiao . $btnHuy;

        $nguoiXuLy = $row->tenNhanVien
            ? h($row->tenNhanVien)
            : ($s === 0 ? "<span style='color:#dc3545'>Khách tự hủy</span>" : "<span class='muted'>—</span>");

        echo "<tr>
                <td><p><b>$id</b></p></td>
                <td><p>" . h($row->tenKhachHang) . "<br><span class='muted'>" . h($row->dienThoai) . "</span></p></td>"
            . ($hasSource ? "<td>" . source_badge($row->nguonDat ?? 'WEB') . "</td>" : '') .
            "<td><p>" . h(date('d/m/Y H:i', strtotime($row->ngayDat))) . "</p></td>
                <td><p>" . ($row->ngayGiao ? h(date('d/m/Y H:i', strtotime($row->ngayGiao))) : '—') . "</p></td>
                <td><p><b>" . vnd($row->tongTien) . "</b></p></td>
                <td>" . order_status_badge($s) . "</td>
                <td><p>$nguoiXuLy</p></td>
                <td><a href=\"Order_Details.php?id=$id\"><i class=\"fa-solid fa-circle-info detail\"></i></a></td>
                <td>$btn</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='10'>Không có đơn hàng phù hợp</td></tr>";
}
?>
    </tbody>
</table>
<?php if ($totalPages > 1): ?>
<div class="pager">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="<?= $i === $currentPage ? 'active' : '' ?>" href="<?= h(order_link(['page' => $i])) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
