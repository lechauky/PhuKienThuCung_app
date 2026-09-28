<!--product page-->
<?php include '../templates/header.php';
require_once('../includes/config.php');
require_once('../includes/check_giam_gia.php');


if (isset($_GET['LSP'])) {
    $LSP = $_GET['LSP'];
} else {
    $LSP = array(); // Mảng rỗng nếu không có checkbox nào được chọn
}
if (isset($_GET['TH'])) {
    $TH = $_GET['TH'];
    //echo $TH;
} else {
    $TH = array(); // Mảng rỗng nếu không có checkbox nào được chọn
}
if (isset($_GET['giaDau'])) {
    $giaDau = $_GET['giaDau'];
} else {
    $giaDau = 0; // Mảng rỗng nếu không có checkbox nào được chọn
}

if (isset($_GET['giaCuoi'])) {
    $giaCuoi = $_GET['giaCuoi'];
} else {
    $giaCuoi = 0; // Mảng rỗng nếu không có checkbox nào được chọn
}

$sql = "SELECT * FROM thuong_hieu";
$stmt = $dbh->query($sql);
$thuongHieu = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM loai_san_pham";
$stmt = $dbh->query($sql);
$loaiSanPham = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM giam_gia";
$stmt = $dbh->query($sql);
$giamGia = $stmt->fetchAll(PDO::FETCH_OBJ);

// Lấy giá trị từ ô tìm kiếm
$sql = "SELECT p.*,b.tenThuongHieu FROM san_pham p
                 JOIN thuong_hieu b ON p.maThuongHieu = b.maThuongHieu
                 JOIN loai_san_pham c ON c.maLoai = p.maLoai
                ";
$stmt = $dbh->query($sql);
$conditions = [];
$i=0;
if (!empty($LSP)) {
    $list = implode("' ,'", $LSP);
    $conditions[$i++] = "p.maLoai IN ('$list')";
    // echo $list;
}

if (!empty($TH)) {
    $list = implode("','", $TH);
    $conditions[$i++] = "p.maThuongHieu IN ('$list')";
    // echo $list;
}
if ($giaDau > 0) {
    $conditions[$i++] = "p.donGiaBan >= '$giaDau'";
}

if ($giaCuoi > 0) {
    $conditions[$i++] = "p.donGiaBan <= '$giaCuoi'";
}

if (!empty($conditions)) {
    $whereClause = implode(" AND ", $conditions);
    // echo $whereClause;
    $filteredSql = "SELECT p.*,b.tenThuongHieu FROM san_pham p
    JOIN thuong_hieu b ON p.maThuongHieu = b.maThuongHieu
    JOIN loai_san_pham c ON c.maLoai = p.maLoai
    WHERE {$whereClause}";
    //echo $filteredSql;
    $filteredStmt = $dbh->query($filteredSql);
    //echo $filteredStmt->rowCount();
    $sanPham = $filteredStmt->fetchAll(PDO::FETCH_OBJ);
} else {
    $sanPham = $stmt->fetchAll(PDO::FETCH_OBJ);
}
if (empty($_SESSION["taiKhoan"])) {
    require_once('../includes/login_required.php');
} else {
    require_once('../includes/ajax_add_product.php');
}
?>

<h4>Tất cả sản phẩm</h4>
<form class="product_search" action="product_search_page.php" method="GET" enctype="multipart/form-data">
    <button class="icon_search" type="submit" style="width: 40px; height: 40px;">
        <i class="fa-solid fa-filter"></i>
    </button>
    <div class="product_search_item search_search">
        <h3>LOẠI SẢN PHẨM </h3>
        <div>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="product_search_item_list">
            <ul>

                <?php foreach ($loaiSanPham as $row) {
                    echo " <li>
                    <input type='checkbox' name='LSP[]' value='" . $row['maLoai'] . "'>" . $row['tenLoai'] . "
                    </li>";
                } ?>
            </ul>
        </div>
    </div>
    <div class="product_search_item search_search">
        <h3>GIÁ</h3>
        <div>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="product_search_item_list search_search_search" style="height: 150px;">
            <div class="input_saerch" style="display: flex;  justify-content: space-around; align-items: center">
                <input type="text" class="textfile" style="width: 70px;" name="giaDau">
                <h5 style="margin:0"> đến </h5>
                <input type="text" class="textfile" style="width: 70px;" name="giaCuoi">
            </div>
        </div>
    </div>
    <div class="product_search_item search_search">
        <h3>THƯƠNG HIỆU</h3>
        <div>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="product_search_item_list">
            <ul>
                <?php foreach ($thuongHieu as $row) {
                    echo " <li>
                    <input type='checkbox' name='TH[]' value='" . $row['maThuongHieu'] . "'>" . $row['tenThuongHieu'] . "
                    </li>";
                } ?>

            </ul>
        </div>
    </div>
</form>
<div class="divider">

</div>
<div class="product_list">
    <div class="grid">
        <div class="row">
            <?php foreach ($sanPham as $row) {
                $productId = $row->maSanPham;
                echo " <div class='product_item'>
              <img src='../assets/img/sanpham/" . $row->hinhAnh . "'>
              <div class='product_thuonghieu'>
                  <h5>" . $row->tenThuongHieu . "</h5>
              </div>
              <div class='product_name'>
              <a href='./product_detail_page.php?maSanPham=" . $row->maSanPham . "' style = 'color: black'>
                  <h5>" . $row->tenSanPham . "</h5>
                  </a>
              </div>";
                if (giamGia($row->maSanPham, $giamGia, $row->donGiaBan) != null) {
                    echo "<div class='product_price' style='display: flex'>
            <h5 style='text-decoration: line-through; width: 70px'>" . number_format($row->donGiaBan) . "đ   </h5>     
            <h5 style='color: red;'>   " . number_format(giamGia($row->maSanPham, $giamGia, $row->donGiaBan)) . "đ</h5>
        </div>";
                } else {
                    echo "<div class='product_price'>
                <h5>" . number_format($row->donGiaBan) . "đ</h5>
            </div>";
                }
                echo ($row->soLuong == 0)
                    ? "<button class='button_product' productid='" . $productId . "'>Hết hàng</button>"
                    : "<button class='button_product' productid='" . $productId . "'  onclick='addToCart(this)'>Thêm vào giỏ hàng</button>";
                echo "
                    <a class='xem_icon'>
                        <i class='fa-regular fa-eye'></i>
                    </a>
                    </div>";
            }
            ?>

        </div>
    </div>
</div>
</div>
<?php include '../templates/footer.php' ?>