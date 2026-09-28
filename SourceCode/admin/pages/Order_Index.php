<?php
include '../templates/nav_admin1.php';
include '../includes/check_permisson.php';
check($nv->maLoai, 'DDH');
require_once '../includes/app_helpers.php';
echo app_admin_styles();
?>
<h2>Đơn Đặt Hàng</h2>
<?php echo flash_html(); ?>
<?php include '../includes/show_order_in_table.php' ?>
<?php include '../templates/nav_admin2.php' ?>
