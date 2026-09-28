<?php
// Calculate the total number of pages.
$rowOfPage = 10;

$totalRows = $dbh->query("SELECT COUNT(*) FROM don_dat_hang WHERE maKhachHang = '{$_SESSION['taiKhoan']['maKhachHang']}'")->fetchColumn();
$totalPages = ceil($totalRows / $rowOfPage);

// Determine the current page number.
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Get the rows for the current page.

$query = "SELECT don_dat_hang.* FROM don_dat_hang WHERE maKhachHang = '{$_SESSION['taiKhoan']['maKhachHang']}'
ORDER BY ngayDat ASC, maDonHang ASC 
LIMIT " . $rowOfPage . " OFFSET " . ($currentPage - 1) * $rowOfPage;
$statement = $dbh->prepare($query);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_OBJ);
$result = $statement->fetchAll();
if ($result) {
    foreach ($result as $row) {
        $tinhTrang = "";
        $btn = "";
        $btnHuy = "<form action='../includes/process_order.php?id=" . $row->maDonHang . "&nut=huy' method='post'>
            <input class=\"btn-danger\" type='submit' value=\"Hủy đơn\" >
        </form>";

        $btnXacNhan = "<form action='../includes/process_order.php?id=" . $row->maDonHang . "&nut=xacNhan' method='post'>
        <input class=\"btn-success\" type='submit' value=\"Xác nhận\" >
    </form>";
        $btnGiao = "<form action='../includes/process_order.php?id=" . $row->maDonHang . "&nut=giao' method='post'>
        <input class=\"btn-primary\" type='submit' value=\"Đã giao\" >
    </form>";

        if ($row->tinhTrang == 0) {
            $tinhTrang = "<p style=\"color:red;\">Đơn hàng bị hủy</p>";
        } else if ($row->tinhTrang == 1) {
            $tinhTrang = "<p style=\"color:blue;\">Đã giao</p>";
        } else if ($row->tinhTrang == 2) {
            $tinhTrang = "<p style=\"color:green;\">Đã xác nhận (Đang giao)</p>";
            $btn = $btnGiao;

        } else {
            $tinhTrang = "<p>Chưa xác nhận</p>";
            $btn = $btnXacNhan . $btnHuy;
        }


        echo "<tr>
                            <td><p>" . $row->maDonHang . "</p></td>
                            <td><p>" . $row->ngayDat . "</p></td>
                            <td><p>" . $row->ngayGiao . "</p></td>
                            <td><p>" . number_format($row->tongTien) . "Đ</p></td>
                            <td>" . $tinhTrang . "</td>
                            <td>
                                <a style='color: black;' href=\"detail_buy_history_page.php?id=" . $row->maDonHang . "&tinhTrang=".$row->tinhTrang."\" ><i class=\"fa-solid fa-circle-info detail\"></i></a>
                            </td>
                        </tr>";
    }
} else {
    echo "<tr>
                <td colspan=\"10\">Không có dữ liệu</td>
                </tr>";
}
?>