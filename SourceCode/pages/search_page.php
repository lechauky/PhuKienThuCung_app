<!--Chức năng tìm kiếm-->

<?php include '../templates/header.php';
require_once('../includes/config.php');
require_once('../includes/check_giam_gia.php');



$sql = "SELECT * FROM thuong_hieu";
$stmt = $dbh->query($sql);
$thuongHieu = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT * FROM loai_san_pham";
$stmt = $dbh->query($sql);

$loaiSanPham = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT * FROM giam_gia";
$stmt = $dbh->query($sql);
$giamGia = $stmt->fetchAll(PDO::FETCH_OBJ);

$SearchString = $_GET['SearchString'];

$rowOfPage = 9;
$totalRows = $dbh->query("SELECT COUNT(*) FROM san_pham p
JOIN thuong_hieu b ON p.maThuongHieu = b.maThuongHieu
JOIN loai_san_pham c ON c.maLoai = p.maLoai
WHERE p.tenSanPham LIKE '%$SearchString%' OR b.tenThuongHieu LIKE '%$SearchString%' OR c.tenLoai LIKE '%$SearchString%'")->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;


// Lấy giá trị từ ô tìm kiếm
$sql = "SELECT p.*,b.tenThuongHieu FROM san_pham p
                 JOIN thuong_hieu b ON p.maThuongHieu = b.maThuongHieu
                 JOIN loai_san_pham c ON c.maLoai = p.maLoai
                 WHERE p.tenSanPham LIKE '%$SearchString%' OR b.tenThuongHieu LIKE '%$SearchString%' OR c.tenLoai LIKE '%$SearchString%'" . " LIMIT $rowOfPage  OFFSET " . (($currentPage - 1) * $rowOfPage);
$stmt = $dbh->query($sql);
$sanPham = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($_SESSION["taiKhoan"])) {
    require_once('../includes/login_required.php');
} else {
    require_once('../includes/ajax_add_product.php');
}
?>

<style>
    .menu-wrapper {

        height: auto;
        width: 100%;
    }

    .menu {
        margin: 0;
        padding: 0 0 0 20px;
    }

    .pagination a {
        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        font-size: 22px;
    }

    .pagination a.active {
        background-color: #CC3333;
        color: white;
        border: 1px solid #CC3333;
    }

    .menu li {
        display: inline-block;
        margin: 5px;
    }

    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }

    ul {
        list-style-type: none;
    }
</style>
<h2 style="text-align: center;">Danh Sách Tìm Kiếm</h2>
<div class="timKiem_page">

    <div class="timKiem_page_left">
        <div class="catalog_timKiem">
            <h6>LOẠI SẢN PHẨM</h6>
            <ul>
                <?php foreach ($loaiSanPham as $row) {
                    echo " <li>
                    <a href='product_search_page.php?LSP[]=".$row['maLoai']."'  method='GET''>
                    " . $row['tenLoai'] . "
                </a>
            </li>";
                } ?>


            </ul>
        </div>
        <div class="catalog_timKiem">
            <h6>THƯƠNG HIỆU</h6>
            <ul>
                <?php foreach ($thuongHieu as $row) {
                    echo " <li>
                <a href='product_search_page.php?TH[]=".$row['maThuongHieu']."'  method='GET''>
                " . $row['tenThuongHieu'] . "
                </a>
            </li>";
                } ?>
            </ul>
        </div>
    </div>
    <div class="timKiem_page_right" style="width: 80%;">
        <div class="product_list">
            <div class="grid">
                <div class="row">
                    <?php foreach ($sanPham as $row) {
                        $productId = $row['maSanPham'];
                        echo "<div class='product_item product_item_timkiem'>
                    <img src='../assets/img/sanpham/" . $row['hinhAnh'] . "' alt=''>
                    <div class='product_thuonghieu'>
                   
                        <h5>" . $row['tenThuongHieu'] . "</h5>
                        
                    </div>
                    <div class='product_name'>
                    <a href='./product_detail_page.php?maSanPham=" . $row['maSanPham'] . "' style = 'color: black'>
                        <h5>" . $row['tenSanPham'] . "</h5>
                        </a>
                    </div>";
                        if (giamGia($row['maSanPham'], $giamGia, $row['donGiaBan']) != null) {
                            echo "<div class='product_price' style='display: flex'>
                        <h5 style='text-decoration: line-through; width: 70px'>" . number_format($row['donGiaBan']) . "đ   </h5>     
                        <h5 style='color: red;'>   " . number_format(giamGia($row['maSanPham'], $giamGia, $row['donGiaBan'])) . "đ</h5>
                    </div>";
                        } else {
                            echo "<div class='product_price'>
                            <h5>" . number_format($row['donGiaBan']) . "đ</h5>
                        </div>";
                        }
                        echo ($row['soLuong'] == 0)
                            ? "<button class='button_product' productid='" . $productId . "'>Hết hàng</button>"
                            : "<button class='button_product' productid='" . $productId . "'  onclick='addToCart(this)'>Thêm vào giỏ hàng</button>";
                        echo "
                    <div class='xem_icon'>
                        <i class='fa-regular fa-eye'></i>
                    </div>
                    </div>";
                    } ?>
                </div>
            </div>
        </div>

        <div align="center" style="margin-top:10px" class="menu-wrapper">
        <?php 
         if ($stmt->rowCount() > 0) { 
            echo "<ul class='pagination menu'>
            <li>
               <a href='search_page.php?SearchString=" . $SearchString . "&page=1'>&laquo;</a> 
            </li>";
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i != $currentPage) {
                    echo "<li><a href='search_page.php?SearchString=" . $SearchString . "&page=" . $i . "' method='POST'>" . $i . "</a></li>";
                } else {
                    echo "<li><a class='active' href='search_page.php?SearchString=" . $SearchString . "&page=" . $i . "' method='POST'>" . $i . "</a></li>";
                }

            }
            echo "<li>
            <a href=\"?SearchString=" . $SearchString . "&page=$totalPages\">&raquo;</a>
        </li> 
        </ul>";
         }
        ?>  
        </div>
    </div>
</div>
<?php include '../templates/footer.php' ?>