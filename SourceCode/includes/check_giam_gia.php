<?php
function isWithinRange($startDate, $endDate)
{
    $today = date("Y-m-d");
    return ($today >= $startDate && $today <= $endDate);
}
function giamGia($maSanPham, $giamGia, $giaBan)
{
    foreach ($giamGia as $row) {
        if ($maSanPham == $row->maSanPham) {
            if (isWithinRange($row->ngayBatDau, $row->ngayKetThuc)) {
                if ($row->maLoai == 1) {
                    return $giaBan - $row->giaTriGiam;
                } else {
                    return $giaBan - round($giaBan * $row->giaTriGiam / 100);
                }
            }
        }
    }
    return null;
}
?>