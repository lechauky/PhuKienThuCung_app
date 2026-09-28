<?php
// Calculate the total number of pages.
$rowOfPage = 5;

$totalRows = $dbh->query('SELECT COUNT(*) FROM san_pham')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT *, tenThuongHieu, tenLoai FROM `san_pham`
JOIN thuong_hieu on thuong_hieu.maThuongHieu = san_pham.maThuongHieu
JOIN loai_san_pham on loai_san_pham.maLoai = san_pham.maLoai 
ORDER BY maSanPham ASC
LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr>
                            <td><p>" . $row->maSanPham . "</p></td>
                            <td><p style=\"text-align: justify;\">" . $row->tenSanPham . "</p></td>
                            <td><p>" . $row->donGiaMua . "</p></td>
                            <td><p>" . $row->donGiaBan . "</p></td>
                            <td><p>" . $row->tenThuongHieu . "</p></td>
                            <td><p>" . $row->tenLoai . "</p></td>
                            <td><p>" . $row->soLuong . "</p></td>
                            <td><p style=\"text-align: justify;\">" . $row->moTa . "</p></td>
                            <td>
                                <img src=\"" . $_SESSION['rootPath'] . "/../assets/img/sanpham/" . $row->hinhAnh . "\"style=\"width: 120px; height: 120px; \"
                            </td>
                            <td>
                                <a href=\"product_edit.php?id=" . $row->maSanPham . "\"><i class=\"fa-solid fa-pen-to-square edit\"></i></a>
                                <a href=\"product_delete.php?id=" . $row->maSanPham . "\"> <i class=\"fa-solid fa-xmark remove\"></i></a>
                                <a href=\"product_details.php?id=" . $row->maSanPham . "\"><i class=\"fa-solid fa-circle-info detail\"></i></a>
                            </td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"10\">Không có dữ liệu</td>
                </tr>";
}
?>