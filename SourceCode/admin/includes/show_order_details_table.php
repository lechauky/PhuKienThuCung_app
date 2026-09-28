<?php
// Calculate the total number of pages.
$rowOfPage = 5;

$totalRows = $dbh->query('SELECT COUNT(*) FROM chi_tiet_don_dat_hang WHERE maDonHang = "' . $id . '"')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT san_pham.tenSanPham, chi_tiet_don_dat_hang.soLuong, chi_tiet_don_dat_hang.donGia, chi_tiet_don_dat_hang.thanhTien 
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
                            <td><p>" . $row->tenSanPham . "</p></td>
                            <td><p>" . $row->soLuong . "</p></td>
                            <td><p>" . $row->donGia . "</p></td>
                            <td><p>" . $row->thanhTien . "</p></td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"10\">Không có dữ liệu</td>
                </tr>";
}
?>