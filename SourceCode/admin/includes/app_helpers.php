<?php
/**
 * Hàm dùng chung cho các trang admin liên quan tới app mobile
 * (đơn hàng, thông báo cho khách, banner).
 */

/** Kiểm tra cột có tồn tại không (để admin vẫn chạy được khi chưa chạy file capnhat_app_mobile.sql) */
function app_has_column(PDO $dbh, string $table, string $column): bool
{
    static $cache = [];
    $key = "$table.$column";
    if (!isset($cache[$key])) {
        $stmt = $dbh->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $stmt->execute([$table, $column]);
        $cache[$key] = (int) $stmt->fetchColumn() > 0;
    }
    return $cache[$key];
}

function app_has_table(PDO $dbh, string $table): bool
{
    static $cache = [];
    if (!isset($cache[$table])) {
        $stmt = $dbh->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $stmt->execute([$table]);
        $cache[$table] = (int) $stmt->fetchColumn() > 0;
    }
    return $cache[$table];
}

/** Gửi thông báo cho khách hàng (hiện trong app, mục Thông báo) */
function app_notify(PDO $dbh, string $maKhachHang, string $tieuDe, string $noiDung, ?string $maDonHang = null): void
{
    if (!app_has_table($dbh, 'thong_bao')) return;
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $stmt = $dbh->prepare('INSERT INTO thong_bao (maKhachHang, tieuDe, noiDung, maDonHang, daDoc, ngayTao) VALUES (?, ?, ?, ?, 0, ?)');
    $stmt->execute([$maKhachHang, $tieuDe, $noiDung, $maDonHang, date('Y-m-d H:i:s')]);
}

/** Tình trạng đơn (bit 2): 0 hủy, 1 đã giao, 2 đã xác nhận (đang giao), 3 chưa xác nhận */
function order_status_text(int $s): string
{
    switch ($s) {
        case 0: return 'Đơn hàng bị hủy';
        case 1: return 'Đã giao';
        case 2: return 'Đã xác nhận (Đang giao)';
        default: return 'Chưa xác nhận';
    }
}

function order_status_badge(int $s): string
{
    $map = [0 => ['#dc3545', 'Đã hủy'], 1 => ['#007bff', 'Đã giao'], 2 => ['#28a745', 'Đang giao'], 3 => ['#f88c06', 'Chưa xác nhận']];
    [$color, $text] = $map[$s] ?? $map[3];
    return "<span class='status-badge' style='background:$color'>$text</span>";
}

function source_badge(?string $nguon): string
{
    $nguon = strtoupper($nguon ?: 'WEB');
    return $nguon === 'APP'
        ? "<span class='src-badge src-app'><i class='fa-solid fa-mobile-screen'></i> App</span>"
        : "<span class='src-badge src-web'><i class='fa-solid fa-globe'></i> Web</span>";
}

function vnd($n): string
{
    return number_format((int) $n, 0, ',', '.') . 'đ';
}

function h($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** CSS dùng chung cho trang đơn hàng / banner */
function app_admin_styles(): string
{
    return <<<CSS
<style>
    .status-badge{color:#fff;padding:4px 10px;border-radius:12px;font-size:13px;white-space:nowrap;display:inline-block}
    /* Lưu ý: KHÔNG dùng class "app" vì main.css đã có .app {position:fixed; ...} (khung toàn trang) */
    .src-badge{padding:3px 8px;border-radius:10px;font-size:12px;white-space:nowrap;display:inline-block;position:static}
    .src-badge.src-app{background:#fde8e8;color:#CC3333;border:1px solid #CC3333}
    .src-badge.src-web{background:#e8f1fd;color:#0b84ee;border:1px solid #0b84ee}
    .order-tabs{display:flex;flex-wrap:wrap;gap:8px;margin:10px 0}
    .order-tabs a{padding:6px 14px;border-radius:16px;border:1px solid #ccc;color:#333!important;font-size:14px;background:#fff}
    .order-tabs a.active{background:#CC3333;border-color:#CC3333;color:#fff!important}
    .order-tabs .count{background:rgba(0,0,0,.12);border-radius:8px;padding:0 6px;margin-left:4px;font-size:12px}
    .order-toolbar{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:10px}
    .order-toolbar input[type=text],.order-toolbar select{padding:6px 10px;border:1px solid #ccc;border-radius:6px;color:#333;font-size:14px}
    .order-toolbar button{padding:6px 14px;border:none;border-radius:6px;background:#CC3333;color:#fff;cursor:pointer}
    .act-form{display:inline-block;margin:2px}
    .act-btn{border:none;color:#fff;padding:5px 10px;border-radius:5px;cursor:pointer;font-size:13px}
    .act-btn.ok{background:#28a745}.act-btn.ship{background:#007bff}.act-btn.cancel{background:#dc3545}.act-btn.gray{background:#6c757d}
    .flash{padding:10px 14px;border-radius:6px;margin:10px 0;font-size:14px}
    .flash.success{background:#e6f4ea;color:#1e7e34;border:1px solid #b7dfc2}
    .flash.error{background:#fdecea;color:#b02a37;border:1px solid #f5c2c7}
    .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px;margin:12px 0}
    .info-card{background:#fff;border:1px solid #e3e3e3;border-radius:8px;padding:14px;font-size:14px;line-height:1.8}
    .info-card h3{margin:0 0 6px;font-size:16px;color:#CC3333}
    .muted{color:#777}
    .pager{display:flex;gap:6px;justify-content:center;margin:14px 0;flex-wrap:wrap}
    .pager a{padding:6px 12px;border:1px solid #ddd;border-radius:4px;color:#333!important;background:#fff}
    .pager a.active{background:#CC3333;color:#fff!important;border-color:#CC3333}
</style>
CSS;
}

/** Hiển thị & xóa thông báo flash lưu trong session */
function flash_html(): string
{
    if (empty($_SESSION['flash'])) return '';
    [$type, $msg] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return "<div class='flash " . h($type) . "'>" . h($msg) . "</div>";
}
