<?php
// Calculate the total number of pages.
$rowOfPage = 10;

$totalRows = $dbh->query('SELECT COUNT(*) FROM thuong_hieu')->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT * FROM loai_san_pham ORDER BY maLoai ASC LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        echo "<tr style=\"height: 50px;\">
                            <td>" . $row->maLoai . "</td>
                            <td>" . $row->tenLoai . "</td>

                            <td>
                                <a href=\"type_edit.php?id=" . $row->maLoai . "\"><i class=\"fa-solid fa-pen-to-square edit\"></i></a>
                                <a href=\"type_delete.php?id=" . $row->maLoai . "\"> <i class=\"fa-solid fa-xmark remove\"></i></a>
                            </td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"3\">Không có dữ liệu</td>
                </tr>";
}
?>