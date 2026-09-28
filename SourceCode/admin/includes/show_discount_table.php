<?php
// Calculate the total number of pages.
$rowOfPage = 5;

$totalRows = $dbh->query('SELECT COUNT(*) FROM giam_gia')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT tenSanPham, tenGiamGia, giaTriGiam, ngayBatDau, ngayKetThuc
FROM giam_gia
JOIN san_pham ON san_pham.maSanPham = giam_gia.maSanPham
JOIN loai_giam_gia ON loai_giam_gia.maLoai = giam_gia.maLoai
ORDER BY ngayBatDau DESC, ngayKetThuc DESC;
LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();

if ($result) {
    foreach ($result as $row) {
        $giaTriGiam = "";
        if ($row->tenGiamGia == "Theo giá") {
            $giaTriGiam = $row->giaTriGiam . " VNĐ";
        } else {
            $giaTriGiam = $row->giaTriGiam . "%";
        }
        echo "<tr>
                            <td><p>" . $row->tenSanPham . "</p></td>
                            <td><p>" . $row->tenGiamGia . "</p></td>
                            <td><p>" . $giaTriGiam . "</p></td>
                            <td><p>" . $row->ngayBatDau . "</p></td>
                            <td><p>" . $row->ngayKetThuc . "</p></td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"5\">Không có dữ liệu</td>
                </tr>";
}
?>