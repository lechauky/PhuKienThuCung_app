<?php
if (!empty($_SESSION['taiKhoan'])) {
  try {
    $maKhachHang = $_SESSION['taiKhoan']['maKhachHang'];
    // Lấy danh sách sản phẩm trong giỏ hàng của khách hàng
    $selectQuery = "SELECT maKhachHang, maSanPham, soLuong FROM gio_hang WHERE maKhachHang = '$maKhachHang'";
    $selectStatement = $dbh->prepare($selectQuery);
    $selectStatement->execute();
    $gioHangItems = $selectStatement->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra từng sản phẩm trong giỏ hàng
    foreach ($gioHangItems as $item) {
      $maSanPham = $item['maSanPham'];
      $updateQuery = "UPDATE gio_hang gh JOIN san_pham sp ON gh.maSanPham = sp.maSanPham 
                        SET gh.soLuong = IF(sp.soLuong >= gh.soLuong, gh.soLuong, sp.soLuong) 
                        WHERE gh.maKhachHang = '$maKhachHang' AND gh.maSanPham = '$maSanPham'";
      $updateStatement = $dbh->prepare($updateQuery);
      $updateStatement->execute();
      // Xóa sản phẩm khỏi giỏ hàng nếu số lượng sản phẩm hiện có là 0
      $deleteQuery = "DELETE FROM gio_hang WHERE maKhachHang = '$maKhachHang' AND maSanPham = '$maSanPham' AND soLuong = 0";
      $deleteStatement = $dbh->prepare($deleteQuery);
      $deleteStatement->execute();
    }
    echo "update cart done";
  } catch (PDOException $e) {
    echo "update cart fail: " . $e->getMessage();
  }
}
?>