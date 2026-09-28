<?php
// Calculate the total number of pages.
$rowOfPage = 5;

$totalRows = $dbh->query('SELECT COUNT(*) FROM chi_tiet_don_dat_hang WHERE maDonHang = "' . $id . '"')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT san_pham.tenSanPham, san_pham.maSanPham, chi_tiet_don_dat_hang.soLuong, chi_tiet_don_dat_hang.donGia, chi_tiet_don_dat_hang.thanhTien,san_pham.hinhAnh
FROM chi_tiet_don_dat_hang 
JOIN san_pham ON chi_tiet_don_dat_hang.maSanPham = san_pham.maSanPham 
JOIN don_dat_hang ON chi_tiet_don_dat_hang.maDonHang = don_dat_hang.maDonHang 
WHERE chi_tiet_don_dat_hang.maDonHang = '" . $id . "' 
ORDER BY san_pham.maSanPham ASC
LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr style=\"height: 50px\">
                            <td class='product_name'>
                            <a href='./product_detail_page.php?maSanPham=" . $row->maSanPham . "' style = 'color: black'>
                                <h5>" . $row->tenSanPham . "</h5>
                                </a>
                            </td>
                            <td><img style = 'width: 100px;' src='../assets/img/sanpham/" . $row->hinhAnh . "' alt=''></td>
                            <td><p>" . $row->soLuong . "</p></td>
                            <td><p>" .  number_format($row->donGia) . "Đ</p></td>
                            <td><p>" . number_format($row->thanhTien) . "Đ</p></td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"10\">Không có dữ liệu</td>
                </tr>";
}
?>