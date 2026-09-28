-- =====================================================================
-- CẬP NHẬT CSDL qlpkthucung ĐỂ PHỤC VỤ APP MOBILE (.NET MAUI)
-- Chạy file này SAU KHI đã import qlpkthucung.sql (phpMyAdmin > chọn DB > tab SQL/Import).
-- Chỉ THÊM cột / bảng mới, không xóa hay đổi dữ liệu cũ -> website cũ vẫn chạy bình thường.
-- =====================================================================

-- 1) Thông tin bổ sung cho đơn hàng
--    nguonDat : 'WEB' (đặt trên website) hoặc 'APP' (đặt trên app mobile)
--    ghiChu   : ghi chú của khách khi đặt hàng trên app
--    lyDoHuy  : lý do hủy (nhân viên hủy hoặc khách tự hủy trên app)
ALTER TABLE `don_dat_hang`
  ADD COLUMN `nguonDat` VARCHAR(10) NOT NULL DEFAULT 'WEB' AFTER `maNhanVien`,
  ADD COLUMN `ghiChu` VARCHAR(255) DEFAULT NULL AFTER `nguonDat`,
  ADD COLUMN `lyDoHuy` VARCHAR(255) DEFAULT NULL AFTER `ghiChu`;

-- 2) Banner hiển thị trên trang chủ app, quản lý trong trang admin (menu "Banner app")
CREATE TABLE IF NOT EXISTS `banner` (
  `maBanner` INT(11) NOT NULL AUTO_INCREMENT,
  `hinhAnh` VARCHAR(255) NOT NULL,
  `tieuDe` VARCHAR(255) DEFAULT NULL,
  `maSanPham` VARCHAR(6) DEFAULT NULL,
  `thuTu` INT(11) NOT NULL DEFAULT 0,
  `hienThi` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`maBanner`),
  KEY `banner_sp` (`maSanPham`),
  CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`maSanPham`) REFERENCES `san_pham` (`maSanPham`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `banner` (`hinhAnh`, `tieuDe`, `maSanPham`, `thuTu`, `hienThi`) VALUES
('banner (1).png', 'Banner 1', NULL, 1, 1),
('banner (2).png', 'Banner 2', NULL, 2, 1),
('banner (3).png', 'Banner 3', NULL, 3, 1),
('banner (4).png', 'Banner 4', NULL, 4, 1);

-- 3) Thông báo gửi tới khách hàng (hiện trong app khi đơn hàng đổi trạng thái)
CREATE TABLE IF NOT EXISTS `thong_bao` (
  `maThongBao` INT(11) NOT NULL AUTO_INCREMENT,
  `maKhachHang` VARCHAR(6) NOT NULL,
  `tieuDe` VARCHAR(255) NOT NULL,
  `noiDung` VARCHAR(500) NOT NULL,
  `maDonHang` VARCHAR(6) DEFAULT NULL,
  `daDoc` TINYINT(1) NOT NULL DEFAULT 0,
  `ngayTao` DATETIME NOT NULL,
  PRIMARY KEY (`maThongBao`),
  KEY `tb_kh` (`maKhachHang`),
  CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`maKhachHang`) REFERENCES `khach_hang` (`maKhachHang`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
