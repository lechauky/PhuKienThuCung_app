<?php
include("config.php");
// lấy danh sách huyện thuộc tỉnh đã chọn
if (isset($_POST['province_id'])) {
  $query = "SELECT `maHuyen`, `tenHuyen` FROM `huyen` WHERE maTinh= '{$_POST['province_id']}'";
  $statement = $dbh->prepare($query);
  $statement->execute();
  $districts = $statement->fetchAll(PDO::FETCH_ASSOC);

  // Tạo danh sách tùy chọn cho thẻ 'select' của huyện và trả về cho AJAX.
  echo '<option value="" disabled selected>Chọn Huyện</option>';
  foreach ($districts as $district) {
    echo "<option value='{$district['maHuyen']}'>{$district['tenHuyen']}</option>";
  }
}

// lấy danh sách xã thuộc huyện đã chọn
if (isset($_POST['district_id'])) {
  $query = "SELECT xa.maXa, xa.tenXa FROM xa WHERE xa.maHuyen =  '{$_POST['district_id']}'";
  $statement = $dbh->prepare($query);
  $statement->execute();
  $wards = $statement->fetchAll(PDO::FETCH_ASSOC);

  // Tạo danh sách tùy chọn cho thẻ 'select' của xã và trả về cho AJAX.
  echo '<option value="" disabled selected>Chọn Xã</option>';
  foreach ($wards as $ward) {
    echo "<option value='{$ward['maXa']}'>{$ward['tenXa']}</option>";
  }
}

// SELECT COUNT(*) AS tontai FROM `khach_hang` WHERE tenNguoiDung = "thanhhao"
if (isset($_POST['userName'])) {
  $query = "SELECT COUNT(*) AS tontai FROM `khach_hang` WHERE tenNguoiDung = '{$_POST['userName']}'";
  $statement = $dbh->prepare($query);
  $statement->execute();
  $tontai = $statement->fetch(PDO::FETCH_OBJ);

  if ($tontai->tontai) 
    echo "Tên người dùng dã được sử dụng";
}

if (isset($_POST['userNameAdmin'])) {
  $query = "SELECT COUNT(*) AS tontai FROM `nhan_vien` WHERE tenNguoiDung = '{$_POST['userNameAdmin']}'";
  $statement = $dbh->prepare($query);
  $statement->execute();
  $tontai = $statement->fetch(PDO::FETCH_OBJ);

  if ($tontai->tontai) 
    echo "Tên người dùng dã được sử dụng";
}
?>