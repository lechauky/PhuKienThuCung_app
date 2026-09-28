<?php
/**
 * Hàm dùng chung cho API: kết nối CSDL, đọc request, trả JSON, xác thực token, tính giá giảm.
 */
require_once __DIR__ . '/config.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Không để PHP in warning/notice lẫn vào JSON
ini_set('display_errors', '0');
error_reporting(E_ALL);

class ApiException extends Exception
{
    public int $status;
    public function __construct(string $message, int $status = 400)
    {
        parent::__construct($message);
        $this->status = $status;
    }
}

function db(): PDO
{
    static $dbh = null;
    if ($dbh === null) {
        $dbh = new PDO(
            'mysql:host=' . API_DB_HOST . ';dbname=' . API_DB_NAME . ';charset=utf8mb4',
            API_DB_USER,
            API_DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $dbh;
}

/* ---------------------------- Request / Response ---------------------------- */

function method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function require_method(string $m): void
{
    if (method() !== $m) {
        throw new ApiException('Phương thức không được hỗ trợ', 405);
    }
}

/** Đọc body JSON (hoặc form-urlencoded) thành mảng. */
function body(): array
{
    static $data = null;
    if ($data === null) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw ?: '', true);
        $data = is_array($json) ? $json : $_POST;
    }
    return $data;
}

function input(string $key, $default = null)
{
    $b = body();
    if (array_key_exists($key, $b)) {
        return is_string($b[$key]) ? trim($b[$key]) : $b[$key];
    }
    return $default;
}

function query_param(string $key, $default = null)
{
    return isset($_GET[$key]) ? (is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key]) : $default;
}

function ok($data = null, string $message = 'OK'): void
{
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $status = 400): void
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message, 'data' => null], JSON_UNESCAPED_UNICODE);
    exit;
}

/* --------------------------------- URL ảnh --------------------------------- */

/** Đường dẫn gốc của website, ví dụ http://10.0.2.2/WebPhuKienThuCung */
function site_root(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $apiDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/api/index.php')); // .../api
    $root = rtrim(dirname($apiDir), '/');
    if ($root === '.' || $root === '\\') {
        $root = '';
    }
    return $scheme . '://' . $host . $root;
}

function image_url(?string $folder, ?string $file): ?string
{
    if ($file === null || trim($file) === '') {
        return null;
    }
    return site_root() . '/assets/img/' . $folder . '/' . rawurlencode(trim($file));
}

/* ------------------------------ Token đăng nhập ------------------------------ */

function b64url_encode(string $s): string
{
    return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
}

function b64url_decode(string $s): string
{
    return (string) base64_decode(strtr($s, '-_', '+/'));
}

/**
 * Token = payload.signature. Khóa ký gồm API_SECRET + mật khẩu (md5) hiện tại của khách hàng,
 * nên khi khách đổi mật khẩu thì các token cũ tự động mất hiệu lực. Không cần thêm bảng mới.
 */
function make_token(array $customer): string
{
    $payload = b64url_encode(json_encode(['id' => $customer['maKhachHang'], 'exp' => time() + API_TOKEN_TTL]));
    $sig = b64url_encode(hash_hmac('sha256', $payload, API_SECRET . $customer['matKhau'], true));
    return $payload . '.' . $sig;
}

function bearer_token(): ?string
{
    $h = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($h === '' && function_exists('apache_request_headers')) {
        foreach (apache_request_headers() as $k => $v) {
            if (strcasecmp($k, 'Authorization') === 0) {
                $h = $v;
            }
        }
    }
    if (preg_match('/Bearer\s+(\S+)/i', $h, $m)) {
        return $m[1];
    }
    return null;
}

/** Trả về bản ghi khách hàng đang đăng nhập, hoặc ném lỗi 401. */
function require_customer(): array
{
    $token = bearer_token();
    if (!$token || substr_count($token, '.') !== 1) {
        throw new ApiException('Vui lòng đăng nhập', 401);
    }
    [$payload, $sig] = explode('.', $token);
    $data = json_decode(b64url_decode($payload), true);
    if (!is_array($data) || empty($data['id']) || empty($data['exp']) || $data['exp'] < time()) {
        throw new ApiException('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại', 401);
    }
    $stmt = db()->prepare('SELECT * FROM khach_hang WHERE maKhachHang = ?');
    $stmt->execute([$data['id']]);
    $customer = $stmt->fetch();
    if (!$customer) {
        throw new ApiException('Tài khoản không tồn tại', 401);
    }
    $expected = b64url_encode(hash_hmac('sha256', $payload, API_SECRET . $customer['matKhau'], true));
    if (!hash_equals($expected, $sig)) {
        throw new ApiException('Phiên đăng nhập không hợp lệ, vui lòng đăng nhập lại', 401);
    }
    return $customer;
}

/* ------------------------------ Sinh mã tự tăng ------------------------------ */

/** Sinh mã dạng KH0001, DH0012... (giống cách website sinh mã). */
function next_code(string $table, string $column, string $prefix): string
{
    $sql = "SELECT MAX(CAST(SUBSTRING($column, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) FROM $table";
    $max = (int) db()->query($sql)->fetchColumn();
    return $prefix . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
}

/* ------------------------------ Giá & khuyến mãi ------------------------------ */

/**
 * Câu SQL con lấy khuyến mãi ĐANG HIỆU LỰC của sản phẩm (giống check_giam_gia.php):
 * maLoai = 1: giảm theo số tiền; maLoai = 0: giảm theo phần trăm.
 */
function discount_join(string $productAlias = 'sp'): string
{
    return "LEFT JOIN (
                SELECT g.maSanPham, (g.maLoai + 0) AS loaiGiam, g.giaTriGiam
                FROM giam_gia g
                JOIN (SELECT maSanPham, MIN(maGiamGia) AS maGiamGia
                      FROM giam_gia
                      WHERE ngayBatDau <= CURDATE() AND ngayKetThuc >= CURDATE()
                      GROUP BY maSanPham) gm ON gm.maGiamGia = g.maGiamGia
            ) gg ON gg.maSanPham = $productAlias.maSanPham";
}

function final_price(int $price, $discountType, $discountValue): int
{
    if ($discountType === null || $discountValue === null) {
        return $price;
    }
    if ((int) $discountType === 1) {
        return max(0, $price - (int) $discountValue);
    }
    return max(0, $price - (int) round($price * (int) $discountValue / 100));
}

/** Chuẩn hóa 1 dòng sản phẩm (có cột từ discount_join) thành JSON cho app. */
function map_product(array $r, bool $withDescription = false): array
{
    $price = (int) $r['donGiaBan'];
    $type = $r['loaiGiam'] ?? null;
    $value = $r['giaTriGiam'] ?? null;
    $out = [
        'id' => $r['maSanPham'],
        'name' => $r['tenSanPham'],
        'price' => $price,
        'finalPrice' => final_price($price, $type, $value),
        'discountType' => $type === null ? null : ((int) $type === 1 ? 'amount' : 'percent'),
        'discountValue' => $value === null ? null : (int) $value,
        'stock' => (int) $r['soLuong'],
        'imageUrl' => image_url('sanpham', $r['hinhAnh']),
        'brandId' => $r['maThuongHieu'] ?? null,
        'brandName' => $r['tenThuongHieu'] ?? null,
        'categoryId' => $r['maLoai'] ?? null,
        'categoryName' => $r['tenLoai'] ?? null,
    ];
    if ($withDescription) {
        // Mô tả trong CSDL có chứa chuỗi "\r\n" dạng ký tự thường -> đổi thành xuống dòng thật
        $out['description'] = str_replace(['\\r\\n', '\\n', "\r\n"], "\n", (string) $r['moTa']);
    }
    return $out;
}

function product_select_sql(): string
{
    return "SELECT sp.maSanPham, sp.tenSanPham, sp.donGiaBan, sp.maThuongHieu, sp.maLoai, sp.soLuong,
                   sp.hinhAnh, sp.moTa, th.tenThuongHieu, lsp.tenLoai, gg.loaiGiam, gg.giaTriGiam
            FROM san_pham sp
            JOIN thuong_hieu th ON th.maThuongHieu = sp.maThuongHieu
            JOIN loai_san_pham lsp ON lsp.maLoai = sp.maLoai
            " . discount_join('sp');
}

/* ------------------------------ Khách hàng ------------------------------ */

function address_of(string $maXa): array
{
    $stmt = db()->prepare(
        'SELECT xa.maXa, xa.tenXa, huyen.maHuyen, huyen.tenHuyen, tinh.maTinh, tinh.tenTinh
         FROM xa JOIN huyen ON xa.maHuyen = huyen.maHuyen JOIN tinh ON huyen.maTinh = tinh.maTinh
         WHERE xa.maXa = ?'
    );
    $stmt->execute([$maXa]);
    return $stmt->fetch() ?: [];
}

function map_customer(array $c): array
{
    $a = address_of($c['maXa']);
    $parts = array_filter([$c['diaChiCuThe'], $a['tenXa'] ?? null, $a['tenHuyen'] ?? null, $a['tenTinh'] ?? null]);
    return [
        'id' => $c['maKhachHang'],
        'lastName' => $c['hoKhachHang'],
        'firstName' => $c['tenKhachHang'],
        'fullName' => trim($c['hoKhachHang'] . ' ' . $c['tenKhachHang']),
        'phone' => $c['dienThoai'],
        'email' => $c['email'],
        'birthday' => $c['ngaySinh'],
        'username' => $c['tenNguoiDung'],
        'street' => $c['diaChiCuThe'],
        'wardId' => $c['maXa'],
        'wardName' => $a['tenXa'] ?? null,
        'districtId' => $a['maHuyen'] ?? null,
        'districtName' => $a['tenHuyen'] ?? null,
        'provinceId' => $a['maTinh'] ?? null,
        'provinceName' => $a['tenTinh'] ?? null,
        'fullAddress' => implode(', ', $parts),
        'avatarUrl' => image_url('khach_hang', $c['avatar']) ?? (site_root() . '/assets/img/banner/Default_pfp.svg.png'),
    ];
}

/**
 * Kiểm tra dữ liệu hồ sơ khách hàng (dùng cho đăng ký & cập nhật).
 * Giới hạn độ dài lấy theo kiểu cột trong bảng khach_hang.
 */
function validate_profile(array $d): void
{
    $required = [
        'lastName' => 'Họ', 'firstName' => 'Tên', 'phone' => 'Số điện thoại',
        'email' => 'Email', 'birthday' => 'Ngày sinh', 'street' => 'Địa chỉ cụ thể', 'wardId' => 'Phường/Xã',
    ];
    foreach ($required as $k => $label) {
        if (!isset($d[$k]) || trim((string) $d[$k]) === '') {
            throw new ApiException("Vui lòng nhập $label");
        }
    }
    if (mb_strlen($d['lastName']) > 50) throw new ApiException('Họ tối đa 50 ký tự');
    if (mb_strlen($d['firstName']) > 10) throw new ApiException('Tên tối đa 10 ký tự');
    if (!preg_match('/^0\d{9}$/', $d['phone'])) throw new ApiException('Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0');
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($d['email']) > 50) throw new ApiException('Email không hợp lệ');
    $dt = DateTime::createFromFormat('Y-m-d', $d['birthday']);
    if (!$dt || $dt->format('Y-m-d') !== $d['birthday'] || $dt > new DateTime()) throw new ApiException('Ngày sinh không hợp lệ (định dạng yyyy-MM-dd)');
    if (mb_strlen($d['street']) > 255) throw new ApiException('Địa chỉ quá dài');
    $stmt = db()->prepare('SELECT COUNT(*) FROM xa WHERE maXa = ?');
    $stmt->execute([$d['wardId']]);
    if (!$stmt->fetchColumn()) throw new ApiException('Phường/Xã không hợp lệ');
}

function validate_new_password(string $p): void
{
    $len = mb_strlen($p);
    if ($len < 8 || $len > 20) {
        throw new ApiException('Mật khẩu phải từ 8 đến 20 ký tự');
    }
}

/* ------------------------ Kiểm tra cấu trúc CSDL ------------------------ */

/** Cột có tồn tại không (API vẫn chạy được khi chưa chạy capnhat_app_mobile.sql) */
function has_column(string $table, string $column): bool
{
    static $cache = [];
    $key = "$table.$column";
    if (!isset($cache[$key])) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $stmt->execute([$table, $column]);
        $cache[$key] = (int) $stmt->fetchColumn() > 0;
    }
    return $cache[$key];
}

function has_table(string $table): bool
{
    static $cache = [];
    if (!isset($cache[$table])) {
        $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $stmt->execute([$table]);
        $cache[$table] = (int) $stmt->fetchColumn() > 0;
    }
    return $cache[$table];
}
