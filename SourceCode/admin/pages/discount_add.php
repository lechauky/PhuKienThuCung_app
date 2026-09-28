<?php
include '../templates/nav_admin1.php';
include '../includes/get_new_type_id.php';

$get_product = "SELECT san_pham.maSanPham, san_pham.tenSanPham FROM san_pham ORDER BY san_pham.tenSanPham ASC";
$statement_get_product = $dbh->prepare($get_product);
$statement_get_product->execute();
$statement_get_product->setFetchMode(PDO::FETCH_OBJ);

$get_discount = "SELECT * FROM loai_giam_gia";
$statement_get_discount = $dbh->prepare($get_discount);
$statement_get_discount->execute();
$statement_get_discount->setFetchMode(PDO::FETCH_OBJ);

?>

<style>
  input,
  select,
  textarea {
    font-size: 16px;
  }

  label {
    font-size: 20px;
  }
</style>

<div class="create_admin">
  <h1 class="Title_Admin_create_form">Thêm giảm giá</h1>
  <p class="Notification_create_form">Vui lòng điền thông tin bên dưới</p>
  <form action="../includes/create_discount.php" method="post" id="form-3">
    <div class="form_field">
      <label for="" class="name_form_field">Sản phẩm: </label>
      <select  required id='sanPham' class="textfile" name="sanPham" style="width: 195px;" >
        <option disabled selected value="">Chọn sản phẩm muốn giảm giá</option>
        <?php
        while ($row = $statement_get_product->fetch())
          echo "<option value='{$row->maSanPham}'>{$row->tenSanPham}</option>";
        ?>
      </select>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Loại giảm giá: </label>
      <select required id='loaiGiamGia' class="textfile" name="loaiGiamGia" style="width: 195px;">
        <option disabled selected value="">Chọn loại giảm giá</option>
        <?php
        while ($row1 = $statement_get_discount->fetch())
          echo "<option value='{$row1->maLoai}'>{$row1->tenGiamGia}</option>";
        ?>
      </select>
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Nhập giá trị giảm: </label>
      <input required type="number" min="0" class="textfile" id="giaTriGiam" name="giaTriGiam">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Ngày bắt đầu: </label>
      <input required type="date" class="textfile" id="ngayBatDau" name="ngayBatDau">
      <span class="error_message"></span>
    </div>
    <div class="form_field">
      <label for="" class="name_form_field">Ngày kết thúc: </label>
      <input required type="date" class="textfile" id="ngayKetThuc" name="ngayKetThuc">
      <span class="error_message"></span>
    </div>
    <div class="button">
      <input type="submit" value="Thêm" name="add" class="button_add_admin" />
    </div>
  </form>
</div>
<?php include '../templates/nav_admin2.php' ?>