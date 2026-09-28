<?php
// Calculate the total number of pages.
$rowOfPage = 10;

$totalRows = $dbh->query('SELECT COUNT(*) FROM thuong_hieu')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT * FROM thuong_hieu ORDER BY maThuongHieu ASC LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr>
                            <td>" . $row->maThuongHieu . "</td>
                            <td>" . $row->tenThuongHieu . "</td>
                            <td>
                                <img src=\"" . $_SESSION['rootPath'] . "/../assets/img/thuong_hieu/" . $row->logo . "\"style=\"width: 231px; height: 74px; \"
                            </td>
                            <td>
                                <a href=\"brand_edit.php?id=" . $row->maThuongHieu . "\"><i class=\"fa-solid fa-pen-to-square edit\"></i></a>
                                <a href=\"brand_delete.php?id=" . $row->maThuongHieu . "\"> <i class=\"fa-solid fa-xmark remove\"></i></a>
                            </td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"4\">Không có dữ liệu</td>
                </tr>";
}
?>