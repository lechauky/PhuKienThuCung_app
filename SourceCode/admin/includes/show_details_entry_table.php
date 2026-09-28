<?php

// Calculate the total number of pages.
$rowOfPage = 10;

$totalRows = $dbh->query("SELECT COUNT(*) FROM `chi_tiet_phieu_nhap` WHERE chi_tiet_phieu_nhap.`maPhieuNhap` = '" . $id . "'")->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = $query . "ORDER BY chi_tiet_phieu_nhap.`maPhieuNhap` ASC LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr>
                            <td>" . $row->tenSanPham . "</td>
                            <td>" . $row->soLuong . "</td>
                            <td>" . $row->donGia . "</td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"4\">Không có dữ liệu</td>
                </tr>";
}
?>