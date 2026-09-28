<?php
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'BN');   // 'BN' không thuộc quyền nhân viên bán hàng / kho -> chỉ Quản trị viên
require_once '../includes/app_helpers.php';
echo app_admin_styles();

if (!app_has_table($dbh, 'banner')) {
    echo "<h2>Banner app</h2><div class='flash error'>Chưa có bảng <b>banner</b>. Hãy chạy file Database/capnhat_app_mobile.sql trong phpMyAdmin.</div>";
    include '../templates/nav_admin2.php';
    exit();
}
$banners = $dbh->query(
    'SELECT b.*, sp.tenSanPham FROM banner b LEFT JOIN san_pham sp ON sp.maSanPham = b.maSanPham ORDER BY b.thuTu, b.maBanner'
)->fetchAll(PDO::FETCH_OBJ);
$products = $dbh->query('SELECT maSanPham, tenSanPham FROM san_pham ORDER BY tenSanPham')->fetchAll(PDO::FETCH_OBJ);
?>
<h2>Banner trang chủ app mobile</h2>
<p class="muted">Các banner đang bật sẽ hiện ở đầu trang chủ app theo thứ tự bên dưới. Có thể gắn banner với 1 sản phẩm: khách bấm banner sẽ mở sản phẩm đó.</p>
<?= flash_html() ?>

<div class="info-card" style="margin-bottom:14px">
    <h3><i class="fa-solid fa-plus"></i> Thêm banner</h3>
    <form action="../includes/banner_action.php" method="post" enctype="multipart/form-data" class="order-toolbar">
        <input type="hidden" name="action" value="add">
        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" required>
        <input type="text" name="tieuDe" placeholder="Tiêu đề (không bắt buộc)" style="width:220px">
        <select name="maSanPham">
            <option value="">-- Không gắn sản phẩm --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= h($p->maSanPham) ?>"><?= h($p->maSanPham . ' - ' . $p->tenSanPham) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Thêm</button>
    </form>
    <span class="muted">Khuyến nghị ảnh ngang tỉ lệ khoảng 2.4 : 1 (vd. 1200 x 500), tối đa 3MB.</span>
</div>

<table class="table_dsadmin">
    <thead>
        <tr><th>Thứ tự</th><th>Ảnh</th><th>Tiêu đề</th><th>Sản phẩm liên kết</th><th>Trạng thái</th><th>Thao tác</th></tr>
    </thead>
    <tbody>
    <?php foreach ($banners as $i => $b): ?>
        <tr>
            <td><p><?= $i + 1 ?></p></td>
            <td><img src="../../assets/img/banner/<?= h(rawurlencode($b->hinhAnh)) ?>" style="width:220px;height:90px;object-fit:cover;border-radius:6px" alt=""></td>
            <td><p><?= h($b->tieuDe ?: '—') ?></p></td>
            <td><p><?= $b->maSanPham ? h($b->maSanPham . ' - ' . $b->tenSanPham) : '—' ?></p></td>
            <td><?= $b->hienThi ? "<span class='status-badge' style='background:#28a745'>Đang hiện</span>" : "<span class='status-badge' style='background:#6c757d'>Đang ẩn</span>" ?></td>
            <td>
                <?php foreach ([['up', 'fa-arrow-up', 'gray', 'Lên'], ['down', 'fa-arrow-down', 'gray', 'Xuống'], ['toggle', $b->hienThi ? 'fa-eye-slash' : 'fa-eye', 'ship', $b->hienThi ? 'Ẩn' : 'Hiện'], ['delete', 'fa-trash', 'cancel', 'Xóa']] as [$act, $icon, $cls, $label]): ?>
                    <form class="act-form" action="../includes/banner_action.php" method="post" <?= $act === 'delete' ? "onsubmit=\"return confirm('Xóa banner này?')\"" : '' ?>>
                        <input type="hidden" name="action" value="<?= $act ?>"><input type="hidden" name="id" value="<?= (int) $b->maBanner ?>">
                        <button class="act-btn <?= $cls ?>" type="submit" title="<?= $label ?>"><i class="fa-solid <?= $icon ?>"></i> <?= $label ?></button>
                    </form>
                <?php endforeach; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$banners): ?><tr><td colspan="6">Chưa có banner. App sẽ không hiện khu vực banner.</td></tr><?php endif; ?>
    </tbody>
</table>
<?php include '../templates/nav_admin2.php' ?>
