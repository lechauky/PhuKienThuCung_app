<?php
// Calculate the total number of pages.
$rowOfPage = 10;

$totalRows = $dbh->query('SELECT COUNT(*) FROM phieu_nhap')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT phieu_nhap.*, CONCAT(ho, ' ', ten) as hoten FROM phieu_nhap join nhan_vien on nhan_vien.maNhanVien = phieu_nhap.maNhanVien ORDER BY maPhieuNhap ASC LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr>
                            <td>" . $row->maPhieuNhap . "</td>
                            <td>" . $row->ngayNhap . "</td>
                            <td>" . $row->hoten . "</td>
                            <td>
                                <a href=\"product_details_entry.php?id=" . $row->maPhieuNhap . "\"><i class=\"fa-solid fa-circle-info detail\"></i></a>
                            </td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"4\">Không có dữ liệu</td>
                </tr>";
}
?>