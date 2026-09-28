<?php
/**
 * REST API cho ứng dụng Android khách hàng - Paddy Pet Shop.
 * Gọi theo dạng: /api/index.php?r=<tên-route>
 * Mọi phản hồi đều có dạng: { "success": bool, "message": string, "data": ... }
 * Các route cần đăng nhập gửi header: Authorization: Bearer <token>
 */
require_once __DIR__ . '/bootstrap.php';

$routes = [
    // Công khai
    'home'           => 'route_home',
    'categories'     => 'route_categories',
    'brands'         => 'route_brands',
    'products'       => 'route_products',
    'product'        => 'route_product',
    'provinces'      => 'route_provinces',
    'districts'      => 'route_districts',
    'wards'          => 'route_wards',
    'check_username' => 'route_check_username',
    'register'       => 'route_register',
    'login'          => 'route_login',
    'forgot_password'=> 'route_forgot_password',
    'reset_password' => 'route_reset_password',
    'shop_info'      => 'route_shop_info',
    // Cần đăng nhập
    'me'             => 'route_me',
    'update_profile' => 'route_update_profile',
    'change_password'=> 'route_change_password',
    'cart'           => 'route_cart',
    'cart_add'       => 'route_cart_add',
    'cart_update'    => 'route_cart_update',
    'cart_remove'    => 'route_cart_remove',
    'checkout'       => 'route_checkout',
    'orders'         => 'route_orders',
    'order'          => 'route_order',
    'cancel_order'   => 'route_cancel_order',
    'notifications'  => 'route_notifications',
    'notifications_read' => 'route_notifications_read',
];

try {
    $r = (string) query_param('r', '');
    if (!isset($routes[$r])) {
        throw new ApiException('Không tìm thấy chức năng: ' . $r, 404);
    }
    $routes[$r]();
} catch (ApiException $e) {
    safe_rollback();
    fail($e->getMessage(), $e->status);
} catch (Throwable $e) {
    safe_rollback();
    error_log('[paddy-api] ' . $e);
    fail('Lỗi máy chủ: ' . $e->getMessage(), 500);
}

function safe_rollback(): void
{
    try {
        if (db()->inTransaction()) db()->rollBack();
    } catch (Throwable $ignored) {
    }
}

/* =============================== SẢN PHẨM =============================== */

function route_home(): void
{
    // Thương hiệu bán chạy (giống trang chủ website)
    $brands = db()->query(
        "SELECT th.maThuongHieu, th.tenThuongHieu, th.logo, SUM(ct.soLuong) AS soLuongBan
         FROM chi_tiet_don_dat_hang ct
         JOIN san_pham sp ON ct.maSanPham = sp.maSanPham
         JOIN thuong_hieu th ON sp.maThuongHieu = th.maThuongHieu
         GROUP BY th.maThuongHieu, th.tenThuongHieu, th.logo
         ORDER BY soLuongBan DESC LIMIT 7"
    )->fetchAll();

    // Sản phẩm bán chạy
    $best = db()->query(
        product_select_sql() . "
         JOIN (SELECT maSanPham, SUM(soLuong) AS soLuongBan FROM chi_tiet_don_dat_hang GROUP BY maSanPham) b
              ON b.maSanPham = sp.maSanPham
         ORDER BY b.soLuongBan DESC LIMIT 10"
    )->fetchAll();

    // Sản phẩm đang khuyến mãi
    $sale = db()->query(product_select_sql() . " WHERE gg.maSanPham IS NOT NULL ORDER BY sp.maSanPham DESC LIMIT 10")->fetchAll();

    // Sản phẩm mới
    $newest = db()->query(product_select_sql() . " ORDER BY sp.maSanPham DESC LIMIT 10")->fetchAll();

    ok([
        'banners' => banners_list(),
        'categories' => categories_list(),
        'topBrands' => array_map(fn($b) => [
            'id' => $b['maThuongHieu'],
            'name' => $b['tenThuongHieu'],
            'logoUrl' => image_url('thuong_hieu', $b['logo']),
        ], $brands),
        'bestSellers' => array_map('map_product', $best),
        'onSale' => array_map('map_product', $sale),
        'newest' => array_map('map_product', $newest),
    ]);
}

/** Banner trang chủ app: lấy từ bảng banner (admin quản lý ở menu "Banner app") */
function banners_list(): array
{
    if (has_table('banner')) {
        $rows = db()->query('SELECT maBanner, hinhAnh, tieuDe, maSanPham FROM banner WHERE hienThi = 1 ORDER BY thuTu, maBanner')->fetchAll();
        return array_map(fn($r) => [
            'id' => (int) $r['maBanner'],
            'imageUrl' => image_url('banner', $r['hinhAnh']),
            'title' => $r['tieuDe'],
            'productId' => $r['maSanPham'],
        ], $rows);
    }
    // Chưa chạy capnhat_app_mobile.sql -> dùng 4 banner có sẵn của website
    $out = [];
    for ($i = 1; $i <= 4; $i++) {
        $out[] = ['id' => $i, 'imageUrl' => image_url('banner', "banner ($i).png"), 'title' => null, 'productId' => null];
    }
    return $out;
}

function categories_list(): array
{
    $rows = db()->query('SELECT maLoai, tenLoai FROM loai_san_pham ORDER BY maLoai')->fetchAll();
    return array_map(fn($r) => ['id' => $r['maLoai'], 'name' => $r['tenLoai']], $rows);
}

function route_categories(): void
{
    ok(categories_list());
}

function route_brands(): void
{
    $rows = db()->query('SELECT maThuongHieu, tenThuongHieu, logo FROM thuong_hieu ORDER BY tenThuongHieu')->fetchAll();
    ok(array_map(fn($r) => [
        'id' => $r['maThuongHieu'],
        'name' => $r['tenThuongHieu'],
        'logoUrl' => image_url('thuong_hieu', $r['logo']),
    ], $rows));
}

/** Tách tham số dạng "A,B,C" hoặc mảng thành mảng chuỗi. */
function list_param(string $key): array
{
    $v = $_GET[$key] ?? [];
    if (is_string($v)) $v = explode(',', $v);
    return array_values(array_filter(array_map('trim', (array) $v), fn($x) => $x !== ''));
}

/**
 * Danh sách sản phẩm: tìm kiếm (q), lọc loại (categories), thương hiệu (brands),
 * khoảng giá (minPrice, maxPrice), sắp xếp (sort) và phân trang (page, pageSize).
 * Tìm kiếm theo tên sản phẩm / tên thương hiệu / tên loại giống search_page.php.
 */
function route_products(): void
{
    $where = [];
    $params = [];

    $q = (string) query_param('q', '');
    if ($q !== '') {
        $where[] = '(sp.tenSanPham LIKE ? OR th.tenThuongHieu LIKE ? OR lsp.tenLoai LIKE ?)';
        array_push($params, "%$q%", "%$q%", "%$q%");
    }
    $cats = list_param('categories');
    if ($cats) {
        $where[] = 'sp.maLoai IN (' . implode(',', array_fill(0, count($cats), '?')) . ')';
        $params = array_merge($params, $cats);
    }
    $brands = list_param('brands');
    if ($brands) {
        $where[] = 'sp.maThuongHieu IN (' . implode(',', array_fill(0, count($brands), '?')) . ')';
        $params = array_merge($params, $brands);
    }
    $min = (int) query_param('minPrice', 0);
    $max = (int) query_param('maxPrice', 0);
    if ($min > 0) { $where[] = 'sp.donGiaBan >= ?'; $params[] = $min; }
    if ($max > 0) { $where[] = 'sp.donGiaBan <= ?'; $params[] = $max; }

    $whereSql = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

    $sortMap = [
        'newest' => 'sp.maSanPham DESC',
        'price_asc' => 'sp.donGiaBan ASC',
        'price_desc' => 'sp.donGiaBan DESC',
        'name' => 'sp.tenSanPham ASC',
    ];
    $order = $sortMap[(string) query_param('sort', '')] ?? 'sp.maSanPham ASC';

    $page = max(1, (int) query_param('page', 1));
    $pageSize = min(50, max(1, (int) query_param('pageSize', 10)));

    $countSql = 'SELECT COUNT(*) FROM san_pham sp
                 JOIN thuong_hieu th ON th.maThuongHieu = sp.maThuongHieu
                 JOIN loai_san_pham lsp ON lsp.maLoai = sp.maLoai' . $whereSql;
    $stmt = db()->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $sql = product_select_sql() . $whereSql . " ORDER BY $order LIMIT $pageSize OFFSET " . (($page - 1) * $pageSize);
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $items = array_map('map_product', $stmt->fetchAll());

    ok([
        'items' => $items,
        'page' => $page,
        'pageSize' => $pageSize,
        'total' => $total,
        'totalPages' => (int) ceil($total / $pageSize),
    ]);
}

function route_product(): void
{
    $id = (string) query_param('id', '');
    $stmt = db()->prepare(product_select_sql() . ' WHERE sp.maSanPham = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) throw new ApiException('Không tìm thấy sản phẩm', 404);

    $product = map_product($row, true);

    // Sản phẩm cùng loại
    $stmt = db()->prepare(product_select_sql() . ' WHERE sp.maLoai = ? AND sp.maSanPham <> ? ORDER BY sp.maSanPham DESC LIMIT 8');
    $stmt->execute([$row['maLoai'], $id]);
    $product['related'] = array_map('map_product', $stmt->fetchAll());

    ok($product);
}

/* =============================== ĐỊA CHỈ =============================== */

function route_provinces(): void
{
    $rows = db()->query('SELECT maTinh AS id, tenTinh AS name FROM tinh ORDER BY tenTinh')->fetchAll();
    ok($rows);
}

function route_districts(): void
{
    $stmt = db()->prepare('SELECT maHuyen AS id, tenHuyen AS name FROM huyen WHERE maTinh = ? ORDER BY tenHuyen');
    $stmt->execute([(string) query_param('provinceId', '')]);
    ok($stmt->fetchAll());
}

function route_wards(): void
{
    $stmt = db()->prepare('SELECT maXa AS id, tenXa AS name FROM xa WHERE maHuyen = ? ORDER BY tenXa');
    $stmt->execute([(string) query_param('districtId', '')]);
    ok($stmt->fetchAll());
}

function route_shop_info(): void
{
    // Thông tin liên hệ lấy từ footer website
    ok([
        'name' => 'Paddy Pet Shop',
        'company' => 'CÔNG TY CỔ PHẦN THƯƠNG MẠI & DỊCH VỤ PADDY',
        'address' => '116 Nguyễn Văn Thủ, Phường Đa Kao, Quận 1, Thành phố Hồ Chí Minh, Việt Nam',
        'hotline' => '0347693333',
        'email' => 'marketing@paddy.vn',
        'logoUrl' => site_root() . '/assets/img/logo/logopaddy.png',
    ]);
}

/* =============================== TÀI KHOẢN =============================== */

function username_taken(string $username): bool
{
    $stmt = db()->prepare('SELECT COUNT(*) FROM khach_hang WHERE tenNguoiDung = ?');
    $stmt->execute([$username]);
    return (int) $stmt->fetchColumn() > 0;
}

function route_check_username(): void
{
    $u = (string) query_param('username', '');
    ok(['username' => $u, 'available' => $u !== '' && !username_taken($u)]);
}

function route_register(): void
{
    require_method('POST');
    $d = body();
    $d = array_map(fn($v) => is_string($v) ? trim($v) : $v, $d);

    validate_profile($d);
    $username = (string) ($d['username'] ?? '');
    $password = (string) ($d['password'] ?? '');
    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
        throw new ApiException('Tên đăng nhập 3-20 ký tự, chỉ gồm chữ không dấu, số và dấu _');
    }
    validate_new_password($password);

    $dbh = db();
    $dbh->beginTransaction();
    if (username_taken($username)) {
        throw new ApiException('Tên người dùng đã được sử dụng', 409);
    }
    $stmt = $dbh->prepare('SELECT COUNT(*) FROM khach_hang WHERE email = ?');
    $stmt->execute([$d['email']]);
    if ((int) $stmt->fetchColumn() > 0) {
        throw new ApiException('Email đã được sử dụng cho tài khoản khác', 409);
    }

    $id = next_code('khach_hang', 'maKhachHang', 'KH');
    $stmt = $dbh->prepare(
        'INSERT INTO khach_hang (maKhachHang, hoKhachHang, tenKhachHang, dienThoai, diaChiCuThe, tenNguoiDung,
                                 matKhau, email, ngaySinh, avatar, khoiPhucMatKhau, maXa)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?)'
    );
    // Mật khẩu băm MD5 để tương thích với trang đăng nhập của website
    $stmt->execute([
        $id, $d['lastName'], $d['firstName'], $d['phone'], $d['street'], $username,
        md5($password), $d['email'], $d['birthday'], $d['wardId'],
    ]);
    $dbh->commit();

    $customer = find_customer($id);
    ok(['token' => make_token($customer), 'customer' => map_customer($customer)], 'Đăng ký thành công');
}

function find_customer(string $id): array
{
    $stmt = db()->prepare('SELECT * FROM khach_hang WHERE maKhachHang = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: [];
}

function route_login(): void
{
    require_method('POST');
    $username = (string) input('username', '');
    $password = (string) input('password', '');
    if ($username === '' || $password === '') {
        throw new ApiException('Vui lòng nhập tài khoản và mật khẩu');
    }
    $stmt = db()->prepare('SELECT * FROM khach_hang WHERE tenNguoiDung = ? AND matKhau = ?');
    $stmt->execute([$username, md5($password)]);
    $customer = $stmt->fetch();
    if (!$customer) {
        throw new ApiException('Tài khoản hoặc mật khẩu không đúng!', 401);
    }
    ok(['token' => make_token($customer), 'customer' => map_customer($customer)], 'Đăng nhập thành công');
}

function route_me(): void
{
    ok(map_customer(require_customer()));
}

function route_update_profile(): void
{
    require_method('POST');
    $c = require_customer();
    $d = array_map(fn($v) => is_string($v) ? trim($v) : $v, body());
    validate_profile($d);

    $stmt = db()->prepare('SELECT COUNT(*) FROM khach_hang WHERE email = ? AND maKhachHang <> ?');
    $stmt->execute([$d['email'], $c['maKhachHang']]);
    if ((int) $stmt->fetchColumn() > 0) {
        throw new ApiException('Email đã được sử dụng cho tài khoản khác', 409);
    }

    $stmt = db()->prepare(
        'UPDATE khach_hang SET hoKhachHang = ?, tenKhachHang = ?, dienThoai = ?, diaChiCuThe = ?, email = ?,
                               ngaySinh = ?, maXa = ?
         WHERE maKhachHang = ?'
    );
    $stmt->execute([$d['lastName'], $d['firstName'], $d['phone'], $d['street'], $d['email'], $d['birthday'], $d['wardId'], $c['maKhachHang']]);
    ok(map_customer(find_customer($c['maKhachHang'])), 'Cập nhật thông tin thành công');
}

function route_change_password(): void
{
    require_method('POST');
    $c = require_customer();
    $old = (string) input('oldPassword', '');
    $new = (string) input('newPassword', '');
    if (md5($old) !== $c['matKhau']) {
        throw new ApiException('Mật khẩu hiện tại không đúng');
    }
    validate_new_password($new);
    $stmt = db()->prepare('UPDATE khach_hang SET matKhau = ? WHERE maKhachHang = ?');
    $stmt->execute([md5($new), $c['maKhachHang']]);
    // Token cũ hết hiệu lực vì mật khẩu thay đổi -> cấp token mới
    ok(['token' => make_token(find_customer($c['maKhachHang']))], 'Đổi mật khẩu thành công');
}

/**
 * Quên mật khẩu: gửi mã 6 số qua email. Mã lưu vào cột khoiPhucMatKhau (cột có sẵn)
 * dạng "APP:<mã>:<hạn dùng>" để không đụng tới luồng reset qua link của website.
 */
function route_forgot_password(): void
{
    require_method('POST');
    $email = (string) input('email', '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new ApiException('Email không hợp lệ');
    $stmt = db()->prepare('SELECT maKhachHang FROM khach_hang WHERE email = ?');
    $stmt->execute([$email]);
    if (!$stmt->fetch()) throw new ApiException('Email không tồn tại', 404);

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires = time() + API_RESET_CODE_TTL_MINUTES * 60;

    send_reset_mail($email, $code);

    $stmt = db()->prepare('UPDATE khach_hang SET khoiPhucMatKhau = ? WHERE email = ?');
    $stmt->execute(["APP:$code:$expires", $email]);
    ok(null, 'Đã gửi mã xác nhận tới email của bạn');
}

function send_reset_mail(string $to, string $code): void
{
    $base = dirname(__DIR__) . '/PHPMailer/src/';
    require_once $base . 'Exception.php';
    require_once $base . 'PHPMailer.php';
    require_once $base . 'SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Host = API_SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = API_SMTP_USER;
        $mail->Password = API_SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = API_SMTP_PORT;
        $mail->Timeout = 15; // tránh app phải chờ quá lâu nếu máy chủ mail không phản hồi
        $mail->setFrom(API_SMTP_USER, API_SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Mã đặt lại mật khẩu Paddy Pet Shop';
        $mail->Body = '<b>Xin chào bạn</b>,<br/><br/>Mã xác nhận để đặt lại mật khẩu trên ứng dụng Paddy của bạn là: '
            . '<h2 style="letter-spacing:4px">' . $code . '</h2>Mã có hiệu lực trong ' . API_RESET_CODE_TTL_MINUTES
            . ' phút. Nếu bạn không yêu cầu, vui lòng bỏ qua email này.';
        $mail->AltBody = "Mã đặt lại mật khẩu của bạn: $code";
        $mail->send();
    } catch (Throwable $e) {
        throw new ApiException('Không gửi được email: ' . $mail->ErrorInfo, 500);
    }
}

function route_reset_password(): void
{
    require_method('POST');
    $email = (string) input('email', '');
    $code = (string) input('code', '');
    $new = (string) input('newPassword', '');

    $stmt = db()->prepare('SELECT maKhachHang, khoiPhucMatKhau FROM khach_hang WHERE email = ?');
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    $parts = $row ? explode(':', (string) $row['khoiPhucMatKhau']) : [];
    if (count($parts) !== 3 || $parts[0] !== 'APP' || !hash_equals($parts[1], $code)) {
        throw new ApiException('Mã xác nhận không đúng');
    }
    if ((int) $parts[2] < time()) {
        throw new ApiException('Mã xác nhận đã hết hạn, vui lòng gửi lại');
    }
    validate_new_password($new);
    $stmt = db()->prepare("UPDATE khach_hang SET matKhau = ?, khoiPhucMatKhau = '' WHERE maKhachHang = ?");
    $stmt->execute([md5($new), $row['maKhachHang']]);
    ok(null, 'Đặt lại mật khẩu thành công, vui lòng đăng nhập');
}

/* =============================== GIỎ HÀNG =============================== */

/**
 * Đồng bộ giỏ với tồn kho (giống includes/update_cart_first.php):
 * số lượng trong giỏ không vượt quá tồn kho, sản phẩm hết hàng bị xóa khỏi giỏ.
 */
function sync_cart_with_stock(string $customerId): void
{
    $stmt = db()->prepare(
        'UPDATE gio_hang gh JOIN san_pham sp ON gh.maSanPham = sp.maSanPham
         SET gh.soLuong = LEAST(gh.soLuong, sp.soLuong)
         WHERE gh.maKhachHang = ?'
    );
    $stmt->execute([$customerId]);
    $stmt = db()->prepare('DELETE FROM gio_hang WHERE maKhachHang = ? AND soLuong <= 0');
    $stmt->execute([$customerId]);
}

function cart_payload(string $customerId): array
{
    $stmt = db()->prepare(
        str_replace('FROM san_pham sp', 'FROM gio_hang gh JOIN san_pham sp ON gh.maSanPham = sp.maSanPham', product_select_sql())
        . ' WHERE gh.maKhachHang = ? ORDER BY sp.tenSanPham'
    );
    $stmt->execute([$customerId]);
    $rows = $stmt->fetchAll();

    // Lấy số lượng trong giỏ
    $q = db()->prepare('SELECT maSanPham, soLuong FROM gio_hang WHERE maKhachHang = ?');
    $q->execute([$customerId]);
    $qty = [];
    foreach ($q->fetchAll() as $r) $qty[$r['maSanPham']] = (int) $r['soLuong'];

    $items = [];
    $subtotal = 0;
    $total = 0;
    $count = 0;
    foreach ($rows as $r) {
        $p = map_product($r);
        $n = $qty[$p['id']] ?? 0;
        $items[] = [
            'product' => $p,
            'quantity' => $n,
            'lineTotal' => $p['finalPrice'] * $n,
        ];
        $subtotal += $p['price'] * $n;
        $total += $p['finalPrice'] * $n;
        $count += $n;
    }
    return [
        'items' => $items,
        'itemCount' => count($items),   // số loại sản phẩm (giống badge giỏ hàng trên web)
        'totalQuantity' => $count,
        'subtotal' => $subtotal,
        'discount' => $subtotal - $total,
        'total' => $total,
    ];
}

function route_cart(): void
{
    $c = require_customer();
    sync_cart_with_stock($c['maKhachHang']);
    ok(cart_payload($c['maKhachHang']));
}

function product_stock(string $productId): int
{
    $stmt = db()->prepare('SELECT soLuong FROM san_pham WHERE maSanPham = ?');
    $stmt->execute([$productId]);
    $v = $stmt->fetchColumn();
    if ($v === false) throw new ApiException('Không tìm thấy sản phẩm', 404);
    return (int) $v;
}

function cart_quantity(string $customerId, string $productId): int
{
    $stmt = db()->prepare('SELECT soLuong FROM gio_hang WHERE maKhachHang = ? AND maSanPham = ?');
    $stmt->execute([$customerId, $productId]);
    $v = $stmt->fetchColumn();
    return $v === false ? 0 : (int) $v;
}

function set_cart_quantity(string $customerId, string $productId, int $qty): void
{
    if ($qty <= 0) {
        $stmt = db()->prepare('DELETE FROM gio_hang WHERE maKhachHang = ? AND maSanPham = ?');
        $stmt->execute([$customerId, $productId]);
        return;
    }
    $stmt = db()->prepare(
        'INSERT INTO gio_hang (maKhachHang, maSanPham, soLuong) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE soLuong = VALUES(soLuong)'
    );
    $stmt->execute([$customerId, $productId, $qty]);
}

/** Thêm vào giỏ (mặc định +1, giống nút "Thêm vào giỏ" trên web), không vượt tồn kho. */
function route_cart_add(): void
{
    require_method('POST');
    $c = require_customer();
    $pid = (string) input('productId', '');
    $add = max(1, (int) input('quantity', 1));
    $stock = product_stock($pid);
    $current = cart_quantity($c['maKhachHang'], $pid);
    if ($stock <= 0) throw new ApiException('Sản phẩm đã hết hàng', 409);
    if ($current + $add > $stock) {
        throw new ApiException("Đã đạt số lượng tối đa (còn $stock sản phẩm, giỏ đang có $current)", 409);
    }
    set_cart_quantity($c['maKhachHang'], $pid, $current + $add);
    ok(cart_payload($c['maKhachHang']), 'Đã thêm vào giỏ');
}

/** Đặt số lượng cụ thể cho 1 sản phẩm trong giỏ (0 = xóa). */
function route_cart_update(): void
{
    require_method('POST');
    $c = require_customer();
    $pid = (string) input('productId', '');
    $qty = (int) input('quantity', 0);
    $stock = product_stock($pid);
    if ($qty > $stock) {
        throw new ApiException("Số lượng tối đa cho sản phẩm này là $stock", 409);
    }
    set_cart_quantity($c['maKhachHang'], $pid, $qty);
    ok(cart_payload($c['maKhachHang']));
}

function route_cart_remove(): void
{
    require_method('POST');
    $c = require_customer();
    set_cart_quantity($c['maKhachHang'], (string) input('productId', ''), 0);
    ok(cart_payload($c['maKhachHang']), 'Đã xóa sản phẩm khỏi giỏ');
}

/* =============================== ĐẶT HÀNG =============================== */

/**
 * Đặt hàng từ toàn bộ giỏ hàng (giống includes/process_order.php của web):
 * - tạo don_dat_hang với tình trạng b'11' (Chưa xác nhận), maNhanVien = NULL
 * - chép từng dòng giỏ vào chi_tiet_don_dat_hang với đơn giá sau khuyến mãi
 * - xóa giỏ hàng
 * Tổng tiền được tính lại ở máy chủ, không tin số tiền gửi từ app.
 * Tồn kho KHÔNG bị trừ ở bước này: nhân viên trừ kho khi bấm "Xác nhận" trong trang admin (giữ nguyên).
 */
function route_checkout(): void
{
    require_method('POST');
    $c = require_customer();
    $cid = $c['maKhachHang'];
    $dbh = db();

    $dbh->beginTransaction();

    // Khóa các dòng giỏ & sản phẩm liên quan trong lúc đặt hàng
    $stmt = $dbh->prepare(
        'SELECT gh.maSanPham, gh.soLuong AS soLuongGio, sp.soLuong AS tonKho, sp.tenSanPham
         FROM gio_hang gh JOIN san_pham sp ON gh.maSanPham = sp.maSanPham
         WHERE gh.maKhachHang = ? FOR UPDATE'
    );
    $stmt->execute([$cid]);
    $lines = $stmt->fetchAll();
    if (!$lines) throw new ApiException('Giỏ hàng trống, vui lòng chọn sản phẩm!');

    foreach ($lines as $l) {
        if ((int) $l['soLuongGio'] > (int) $l['tonKho']) {
            $dbh->rollBack();
            sync_cart_with_stock($cid);
            throw new ApiException("Sản phẩm \"{$l['tenSanPham']}\" chỉ còn {$l['tonKho']} trong kho. Giỏ hàng đã được cập nhật, vui lòng kiểm tra lại.", 409);
        }
    }

    $cart = cart_payload($cid);

    // Khóa bảng đơn hàng khi sinh mã để tránh trùng mã khi 2 người đặt cùng lúc
    $dbh->query('SELECT maDonHang FROM don_dat_hang ORDER BY maDonHang DESC LIMIT 1 FOR UPDATE');
    $orderId = next_code('don_dat_hang', 'maDonHang', 'DH');

    $note = mb_substr(trim((string) input('note', '')), 0, 255);
    if (has_column('don_dat_hang', 'nguonDat')) {
        // Đánh dấu đơn đặt từ app để admin phân biệt với đơn từ website
        $stmt = $dbh->prepare(
            "INSERT INTO don_dat_hang (maDonHang, maKhachHang, ngayDat, ngayGiao, tinhTrang, tongTien, maNhanVien, nguonDat, ghiChu)
             VALUES (?, ?, ?, NULL, b'11', ?, NULL, 'APP', ?)"
        );
        $stmt->execute([$orderId, $cid, date('Y-m-d H:i:s'), $cart['total'], $note !== '' ? $note : null]);
    } else {
        $stmt = $dbh->prepare(
            "INSERT INTO don_dat_hang (maDonHang, maKhachHang, ngayDat, ngayGiao, tinhTrang, tongTien, maNhanVien)
             VALUES (?, ?, ?, NULL, b'11', ?, NULL)"
        );
        $stmt->execute([$orderId, $cid, date('Y-m-d H:i:s'), $cart['total']]);
    }

    $ins = $dbh->prepare('INSERT INTO chi_tiet_don_dat_hang (maDonHang, maSanPham, soLuong, donGia, thanhTien) VALUES (?, ?, ?, ?, ?)');
    foreach ($cart['items'] as $it) {
        $ins->execute([$orderId, $it['product']['id'], $it['quantity'], $it['product']['finalPrice'], $it['lineTotal']]);
    }

    $stmt = $dbh->prepare('DELETE FROM gio_hang WHERE maKhachHang = ?');
    $stmt->execute([$cid]);

    $dbh->commit();

    ok(order_detail($cid, $orderId), 'Đặt hàng thành công');
}

/* ============================ LỊCH SỬ ĐƠN HÀNG ============================ */

/** Tình trạng đơn (cột bit(2)): 0 = Bị hủy, 1 = Đã giao, 2 = Đã xác nhận (Đang giao), 3 = Chưa xác nhận */
function status_text(int $s): string
{
    switch ($s) {
        case 0: return 'Đơn hàng bị hủy';
        case 1: return 'Đã giao';
        case 2: return 'Đã xác nhận (Đang giao)';
        default: return 'Chưa xác nhận';
    }
}

function map_order(array $o): array
{
    $s = (int) $o['tinhTrang'];
    return [
        'id' => $o['maDonHang'],
        'orderDate' => $o['ngayDat'],
        'deliveryDate' => $o['ngayGiao'],
        'status' => $s,
        'statusText' => status_text($s),
        'total' => (int) $o['tongTien'],
        'itemCount' => isset($o['soMatHang']) ? (int) $o['soMatHang'] : null,
        'firstImageUrl' => isset($o['hinhDau']) ? image_url('sanpham', $o['hinhDau']) : null,
        'source' => $o['nguonDat'] ?? 'WEB',
    ];
}

function route_orders(): void
{
    $c = require_customer();
    $page = max(1, (int) query_param('page', 1));
    $pageSize = min(50, max(1, (int) query_param('pageSize', 10)));

    $where = 'WHERE d.maKhachHang = ?';
    $params = [$c['maKhachHang']];
    $status = query_param('status', null);
    if ($status !== null && $status !== '') {
        $where .= ' AND (d.tinhTrang + 0) = ?';
        $params[] = (int) $status;
    }

    $stmt = db()->prepare("SELECT COUNT(*) FROM don_dat_hang d $where");
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $stmt = db()->prepare(
        "SELECT d.*, (d.tinhTrang + 0) AS tinhTrang,
                (SELECT COUNT(*) FROM chi_tiet_don_dat_hang ct WHERE ct.maDonHang = d.maDonHang) AS soMatHang,
                (SELECT sp.hinhAnh FROM chi_tiet_don_dat_hang ct JOIN san_pham sp ON sp.maSanPham = ct.maSanPham
                  WHERE ct.maDonHang = d.maDonHang ORDER BY sp.maSanPham LIMIT 1) AS hinhDau
         FROM don_dat_hang d $where
         ORDER BY d.ngayDat DESC, d.maDonHang DESC
         LIMIT $pageSize OFFSET " . (($page - 1) * $pageSize)
    );
    $stmt->execute($params);

    ok([
        'items' => array_map('map_order', $stmt->fetchAll()),
        'page' => $page,
        'pageSize' => $pageSize,
        'total' => $total,
        'totalPages' => (int) ceil($total / $pageSize),
    ]);
}

function order_detail(string $customerId, string $orderId): array
{
    $stmt = db()->prepare(
        'SELECT d.*, (d.tinhTrang + 0) AS tinhTrang, CONCAT(nv.ho, \' \', nv.ten) AS tenNhanVien
         FROM don_dat_hang d LEFT JOIN nhan_vien nv ON nv.maNhanVien = d.maNhanVien
         WHERE d.maDonHang = ? AND d.maKhachHang = ?'
    );
    $stmt->execute([$orderId, $customerId]);
    $o = $stmt->fetch();
    if (!$o) throw new ApiException('Không tìm thấy đơn hàng', 404);

    $stmt = db()->prepare(
        'SELECT sp.maSanPham, sp.tenSanPham, sp.hinhAnh, ct.soLuong, ct.donGia, ct.thanhTien
         FROM chi_tiet_don_dat_hang ct JOIN san_pham sp ON ct.maSanPham = sp.maSanPham
         WHERE ct.maDonHang = ? ORDER BY sp.maSanPham'
    );
    $stmt->execute([$orderId]);
    $items = array_map(fn($r) => [
        'productId' => $r['maSanPham'],
        'productName' => $r['tenSanPham'],
        'imageUrl' => image_url('sanpham', $r['hinhAnh']),
        'quantity' => (int) $r['soLuong'],
        'unitPrice' => (int) $r['donGia'],
        'lineTotal' => (int) $r['thanhTien'],
    ], $stmt->fetchAll());

    $order = map_order($o);
    $order['itemCount'] = count($items);
    $order['firstImageUrl'] = $items[0]['imageUrl'] ?? null;
    $order['items'] = $items;
    $order['shipping'] = map_customer(find_customer($customerId));
    $order['note'] = $o['ghiChu'] ?? null;
    $order['cancelReason'] = $o['lyDoHuy'] ?? null;
    $order['handledBy'] = $o['tenNhanVien'] ?? null;
    $order['canCancel'] = (int) $o['tinhTrang'] === 3;
    return $order;
}

function route_order(): void
{
    $c = require_customer();
    ok(order_detail($c['maKhachHang'], (string) query_param('id', '')));
}

/* ============================ HỦY ĐƠN (KHÁCH) ============================ */

/**
 * Khách tự hủy đơn khi đơn còn "Chưa xác nhận" (chưa trừ kho nên không cần hoàn kho).
 * maNhanVien để NULL -> trang admin hiển thị "Khách tự hủy".
 */
function route_cancel_order(): void
{
    require_method('POST');
    $c = require_customer();
    $id = (string) input('orderId', '');
    $reason = mb_substr(trim((string) input('reason', '')), 0, 200);
    $dbh = db();
    $dbh->beginTransaction();
    $stmt = $dbh->prepare('SELECT (tinhTrang + 0) FROM don_dat_hang WHERE maDonHang = ? AND maKhachHang = ? FOR UPDATE');
    $stmt->execute([$id, $c['maKhachHang']]);
    $status = $stmt->fetchColumn();
    if ($status === false) throw new ApiException('Không tìm thấy đơn hàng', 404);
    if ((int) $status !== 3) throw new ApiException('Chỉ hủy được đơn hàng chưa được cửa hàng xác nhận', 409);

    if (has_column('don_dat_hang', 'lyDoHuy')) {
        $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'00', maNhanVien = NULL, lyDoHuy = ? WHERE maDonHang = ?");
        $stmt->execute(['Khách hàng hủy' . ($reason !== '' ? ": $reason" : ''), $id]);
    } else {
        $stmt = $dbh->prepare("UPDATE don_dat_hang SET tinhTrang = b'00', maNhanVien = NULL WHERE maDonHang = ?");
        $stmt->execute([$id]);
    }
    $dbh->commit();
    ok(order_detail($c['maKhachHang'], $id), 'Đã hủy đơn hàng');
}

/* ============================== THÔNG BÁO ============================== */

function route_notifications(): void
{
    $c = require_customer();
    if (!has_table('thong_bao')) ok(['items' => [], 'unread' => 0]);
    $stmt = db()->prepare(
        'SELECT maThongBao, tieuDe, noiDung, maDonHang, daDoc, ngayTao FROM thong_bao
         WHERE maKhachHang = ? ORDER BY ngayTao DESC, maThongBao DESC LIMIT 50'
    );
    $stmt->execute([$c['maKhachHang']]);
    $items = array_map(fn($r) => [
        'id' => (int) $r['maThongBao'],
        'title' => $r['tieuDe'],
        'content' => $r['noiDung'],
        'orderId' => $r['maDonHang'],
        'isRead' => (bool) $r['daDoc'],
        'createdAt' => $r['ngayTao'],
    ], $stmt->fetchAll());
    $stmt = db()->prepare('SELECT COUNT(*) FROM thong_bao WHERE maKhachHang = ? AND daDoc = 0');
    $stmt->execute([$c['maKhachHang']]);
    ok(['items' => $items, 'unread' => (int) $stmt->fetchColumn()]);
}

/** Đánh dấu đã đọc: gửi {id} để đánh dấu 1 thông báo, bỏ trống để đánh dấu tất cả */
function route_notifications_read(): void
{
    require_method('POST');
    $c = require_customer();
    if (!has_table('thong_bao')) ok(null);
    $id = (int) input('id', 0);
    if ($id > 0) {
        $stmt = db()->prepare('UPDATE thong_bao SET daDoc = 1 WHERE maThongBao = ? AND maKhachHang = ?');
        $stmt->execute([$id, $c['maKhachHang']]);
    } else {
        $stmt = db()->prepare('UPDATE thong_bao SET daDoc = 1 WHERE maKhachHang = ?');
        $stmt->execute([$c['maKhachHang']]);
    }
    ok(null, 'Đã đánh dấu đã đọc');
}
