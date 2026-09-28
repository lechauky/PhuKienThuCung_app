<!--Home page-->

<?php include './templates/header.php';
require_once('includes/config.php');
require_once('includes/check_giam_gia.php');

$sql1 = "
  SELECT th.maThuongHieu, th.tenThuongHieu,th.logo, SUM(ctdh.soLuong) AS soLuongBan
  FROM chi_tiet_don_dat_hang AS ctdh
  INNER JOIN san_pham AS sp ON ctdh.maSanPham = sp.maSanPham
  INNER JOIN thuong_hieu AS th ON sp.maThuongHieu = th.maThuongHieu
  GROUP BY th.maThuongHieu, th.tenThuongHieu
  ORDER BY soLuongBan DESC
  LIMIT 7
  ";
$stmt = $dbh->query($sql1);
$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql2 = "
  SELECT th.tenThuongHieu,sp.*, SUM(ctdh.soLuong) AS soLuongBan
  FROM chi_tiet_don_dat_hang AS ctdh
  INNER JOIN san_pham AS sp ON ctdh.maSanPham = sp.maSanPham
  INNER JOIN thuong_hieu AS th ON sp.maThuongHieu = th.maThuongHieu
  GROUP BY th.maThuongHieu, th.tenThuongHieu
  ORDER BY soLuongBan DESC
  LIMIT 7
  ";
$stmt = $dbh->query($sql2);
$result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM giam_gia";
$stmt = $dbh->query($sql);
$giamGia = $stmt->fetchAll(PDO::FETCH_OBJ);
if (empty($_SESSION["taiKhoan"])) {
  require_once('includes/login_required.php');
} else {
  require_once('includes/ajax_add_product.php');
}
?>

<div class="banner">
  <div class="swiper-container">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <img src="assets/img/banner/banner (1).png" alt="Hình ảnh 1">
      </div>
      <div class="swiper-slide">
        <img src="assets/img/banner/banner (2).png" alt="Hình ảnh 2">
      </div>
      <div class="swiper-slide">
        <img src="assets/img/banner/banner (3).png" alt="Hình ảnh 3">
      </div>
      <div class="swiper-pagination">
        <div class="swiper-pagination_page">

        </div>
        <div class="swiper-pagination_page">

        </div>
        <div class="swiper-pagination_page">

        </div>
      </div>
    </div>

    <div class="swiper-button-prev" style="display: none;">

    </div>
    <div class="swiper-button-next" style="display: none;">

    </div>
  </div>
</div>
<div class="gioithieu">
  <div class="vanChuyen">
    <h6>Miễn Phí Vận Chuyển</h6>
    <p>Áp dụng cho đơn hàng từ 500K</p>
    <p>Hoả tốc 4h trong nội thành HCM</p>
  </div>
  <div class="vanChuyen">
    <h6>Miễn Phí Vận Chuyển</h6>
    <p>Áp dụng cho đơn hàng từ 500K</p>
    <p>Hoả tốc 4h trong nội thành HCM</p>
  </div>
  <div class="vanChuyen">
    <h6>Miễn Phí Vận Chuyển</h6>
    <p>Áp dụng cho đơn hàng từ 500K</p>
    <p>Hoả tốc 4h trong nội thành HCM</p>
  </div>
  <div class="vanChuyen">
    <h6>Miễn Phí Vận Chuyển</h6>
    <p>Áp dụng cho đơn hàng từ 500K</p>
    <p>Hoả tốc 4h trong nội thành HCM</p>
  </div>
</div>
<div class="MuaThucCung">
  <h6>Mua sắm theo giống thú cưng</h6>
  <div class="img_thucung">
    <img src="assets/img/banner/dog_banner_1370x.png" alt="">
    <img src="assets/img/banner/cat_banner_1370x.png" alt="">
  </div>
</div>
<div class="boSuutap">
  <div class="boSuuTap_header">
    <h6></h6>
    <h6>Bộ Sưu Tập Nổi Bật</h6>
    <a href="">Xem tất cả</a>
  </div>
  <div class="boSuuTap_body">
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/collection_banner_pate_rc_kitten_570x.png" alt=""
        style="width: 200px; height: 200px;">
      <h6>Pate Cho Miu</h6>
    </a>
  </div>
  <div class="boSuuTap_body">
    <a href="#">
      <img src="assets/img/banner/hairball_510x.png" alt="" style="width: 320px; height: 320px;">
      <h6 style="color: #f6c518;">Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/hairball_510x.png" alt="" style="width: 320px; height: 320px;">
      <h6 style="color: #f6c518;">Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/hairball_510x.png" alt="" style="width: 320px; height: 320px;">
      <h6 style="color: #f6c518;">Pate Cho Miu</h6>
    </a>
    <a href="#">
      <img src="assets/img/banner/hairball_510x.png" alt="" style="width: 320px; height: 320px;">
      <h6 style="color: #f6c518;">Pate Cho Miu</h6>
    </a>
  </div>
  <div class="boSuutap">
    <div class="boSuuTap_header">
      <h6></h6>
      <h6>Được boss yêu thích</h6>
      <a href="">Xem tất cả</a>
    </div>
    <div class="boSuuTap_body">


      <?php foreach ($result2 as $row) {
          $productId = $row['maSanPham'];
        echo "<div class=\"product_item\" style='height: 490px'>
         <img src='assets/img/sanpham/" . $row['hinhAnh'] . "' alt=\"\" height=\"350px\">
         <div class=\"product_thuonghieu\">
         
           <h5>" . $row['tenThuongHieu'] . "</h5>
           
         </div>
         <div class=\"product_name\">
         <a href='pages/product_detail_page.php?maSanPham=" . $row['maSanPham'] . "' style = 'color: black; text-align: left; '>
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
      <h5>" .  number_format($row['donGiaBan']) . "đ</h5>
  </div>";
        }
        echo ($row['soLuong'] == 0)
          ? "<button class='button_product' productid='" . $productId . "'>Hết hàng</button>"
          : "<button class='button_product' productid='" . $productId . "'  onclick='addToCart(this)'>Thêm vào giỏ hàng</button>";
        echo "
          <a class='xem_icon'>
              <i class='fa-regular fa-eye'></i>
          </a>
          </div>";
      } ?>
      <!-- <div class="product_item">
        <img src="assest/img/img_product/12-1682483525450_1066x.webp" alt="">
        <div class="product_thuonghieu">
          <h5>Paddy</h5>
        </div>
        <div class="product_name">
          <h5>Bát Ăn Cho Chó Mèo Bằng Nhựa Hình Mèo May Mắn</h5>
        </div>
        <div class="product_price">
          <h5>55.000đ</h5>
        </div>
        <button class="button_product">Thêm vào giỏ hàng</button>
        <div class="xem_icon">
          <i class="fa-regular fa-eye"></i>
        </div>
      </div> -->
    </div>
  </div>
  <div class="divider" style="border-bottom: 3px solid black;">

  </div>
  <div class="MuaThucCung">
    <div class="MuaThucCung_header">
      <h6>1000+ Thương Hiệu Boss Thích</h6>
      <a href="">Xem tất cả</a>
    </div>
    <div class='thuongHieu'>
      <?php foreach ($result1 as $row) {
        echo "
      <a href='pages/product_search_page.php?TH[]=".$row['maThuongHieu']."'  method='GET' style='margin-left: 50px'>
        <img src='assets/img/thuong_hieu/" . $row['logo'] . "' alt='' style='width: 100%; height: 80px;'>
        <h6>
          " . $row['tenThuongHieu'] . "
        </h6>
      </a>
    ";
      } ?>
    </div>

  </div>
</div>
</div>
<?php include './templates/footer.php' ?>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
  // Lấy các phần tử cần thiết
  // Lấy các phần tử cần thiết
  const banner = document.querySelector('.banner');
  const swiperContainer = banner.querySelector('.swiper-container');
  const swiperWrapper = swiperContainer.querySelector('.swiper-wrapper');
  const swiperSlides = swiperWrapper.querySelectorAll('.swiper-slide');
  const prevButton = swiperContainer.querySelector('.swiper-button-prev');
  const nextButton = swiperContainer.querySelector('.swiper-button-next');

  // Thiếtlập SwiperJS
  const swiper = new Swiper(swiperContainer, {
    loop: true,
    autoplay: {
      delay: 2000,
    },
  });

  // Xử lý sự kiện click vào nút prev
  prevButton.addEventListener('click', () => {
    // Di chuyển đến slide trước đó
    swiper.slidePrev();

    // Cập nhật trạng thái các slide
    swiperSlides.forEach((slide) => {
      if (slide.classList.contains('swiper-slide-active')) {
        slide.classList.remove('swiper-slide-active');
      } else if (!slide.classList.contains('swiper-slide-duplicate')) {
        slide.classList.add('swiper-slide-active');
      }
    });
  });
</script>