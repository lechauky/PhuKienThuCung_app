<?php
/**
 * Cấu hình cho REST API phục vụ ứng dụng mobile khách hàng (Paddy Pet Shop).
 * API dùng CHUNG cơ sở dữ liệu `qlpkthucung` với website, không thay đổi cấu trúc bảng.
 */

// Thông tin kết nối CSDL (giống includes/config.php của website)
define('API_DB_HOST', 'localhost');
define('API_DB_USER', 'root');
define('API_DB_PASS', '');
define('API_DB_NAME', 'qlpkthucung');

// Khóa bí mật dùng để ký token đăng nhập của app.
// HÃY ĐỔI thành một chuỗi ngẫu nhiên dài trước khi triển khai thật.
define('API_SECRET', 'paddy-mobile-api-doi-chuoi-nay-thanh-chuoi-ngau-nhien');

// Thời hạn token đăng nhập (giây) - mặc định 30 ngày
define('API_TOKEN_TTL', 60 * 60 * 24 * 30);

// Cấu hình gửi mail cho chức năng quên mật khẩu (lấy theo pages/forgot_password.php)
define('API_SMTP_HOST', 'smtp.gmail.com');
define('API_SMTP_USER', 'hoahuongduong05124@gmail.com');
define('API_SMTP_PASS', 'ytotxwzbrwkoddjd');
define('API_SMTP_PORT', 587);
define('API_SMTP_FROM_NAME', 'paddyshop');

// Thời hạn hiệu lực của mã khôi phục mật khẩu (phút)
define('API_RESET_CODE_TTL_MINUTES', 15);
