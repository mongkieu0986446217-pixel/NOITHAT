-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 06, 2026 lúc 12:31 PM
-- Phiên bản máy phục vụ: 10.4.11-MariaDB
-- Phiên bản PHP: 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `noithat`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dangnhap`
--

CREATE TABLE `dangnhap` (
  `id` int(11) NOT NULL,
  `hoten` varchar(100) DEFAULT NULL,
  `sdt` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `dangnhap`
--

INSERT INTO `dangnhap` (`id`, `hoten`, `sdt`, `email`, `password`, `role`) VALUES
(10, NULL, NULL, 'admin@gmail.com', 'admin123', 'admin'),
(29, 'NGUYEN ANH VU', '0326726912', 'mongkieu0986446217@gmail.com', '$2y$10$WFSKZbedoIghB2b0IuVukOQjl8lW6iE9YCOplbZVjXObmsZtUEwwC', 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int(11) NOT NULL,
  `ten_danh_muc` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `anh_danh_muc` varchar(255) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `slug`, `anh_danh_muc`, `thu_tu`) VALUES
(1, 'Sofa', 'sofa', NULL, 1),
(2, 'Đèn', 'den', NULL, 2),
(3, 'Giường Ngủ', 'giuong-ngu', NULL, 3),
(4, 'Tủ - Kệ', 'tu-ke', NULL, 4),
(5, 'Bàn & Ghế', 'ban-ghe', NULL, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `san_pham`
--

CREATE TABLE `san_pham` (
  `id` int(11) NOT NULL,
  `danh_muc_id` int(11) NOT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `gia` decimal(15,0) NOT NULL,
  `anh_san_pham` varchar(500) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `hang_moi` tinyint(1) DEFAULT 0,
  `goi_y` tinyint(1) DEFAULT 0,
  `trang_thai` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten_san_pham`, `gia`, `anh_san_pham`, `mo_ta`, `link`, `hang_moi`, `goi_y`, `trang_thai`) VALUES
(1, 1, 'Ghế sofa AURORA', '13999000', 'img/sofa/Ghe-sofa-AURORA.png', NULL, 'sofaAURORA.php', 1, 0, 1),
(2, 1, 'Ghế sofa gỗ', '12000000', 'img/sofa/Ghe-sofa-go.png', NULL, NULL, 1, 0, 1),
(3, 1, 'Ghế sofa KLINE', '3200000', 'img/sofa/Ghe-sofa-KLINE.png', NULL, NULL, 0, 0, 1),
(4, 1, 'Ghế sofa NARVIK', '2599999', 'img/sofa/Ghe-sofa-NARVIK.png', NULL, NULL, 0, 0, 1),
(5, 1, 'Ghế sofa Armchair', '1790000', 'img/sofa/Ghe-Armchair.png', NULL, 'sofaArmchair.php', 0, 0, 1),
(6, 1, 'Sofa đơn', '1180000', 'img/sofa/Sofa-don.png', NULL, NULL, 0, 0, 1),
(7, 1, 'Ghế sofa đơn', '749000', 'img/sofa/Ghe-don.png', NULL, 'NULL', 0, 0, 1),
(8, 1, 'Ghế sofa đơn bập bênh', '2500000', 'img/sofa/Ghe-sofa-don-bat-benh.png', NULL, NULL, 0, 0, 1),
(9, 1, 'Ghế sofa 3 chỗ BED SERTA', '10899000', 'img/sofa/Ghe-sofa-3-cho-BED SERTA.png', NULL, NULL, 0, 0, 1),
(10, 1, 'Ghế sofa 3 chỗ ASHER', '8499000', 'img/sofa/Ghe-sofa-3-cho-ASHER.png', NULL, NULL, 0, 0, 1),
(11, 1, 'Ghế sofa 3 chỗ ABBYSON', '13299000', 'img/sofa/Ghe-sofa-3-cho-ABBYSON.png', NULL, NULL, 0, 0, 1),
(12, 1, 'Ghế sofa 3 chỗ', '7299999', 'img/sofa/Ghe-sofa-3-cho.png', NULL, NULL, 0, 0, 1),
(13, 1, 'Sofa góc Coastal', '20210000', 'img/sofa/sofa-goc.png', NULL, NULL, 0, 0, 1),
(14, 1, 'Sofa Moon', '12299000', 'img/sofa/sofa-moon.png', NULL, NULL, 0, 0, 1),
(15, 1, 'Sofa Square Next', '33200000', 'img/sofa/sofa-squara.png', NULL, NULL, 0, 0, 1),
(16, 1, 'Sofa Ona', '13199000', 'img/sofa/sofa-Ona.png', NULL, NULL, 0, 0, 1),
(17, 2, 'Đèn trang trí', '699000', 'img/Den/Den-TT.png', NULL, 'dentrangtri.php', 0, 0, 1),
(18, 2, 'Đèn trung hoa', '2980000', 'img/Den/Den-trung-hoa.png', NULL, NULL, 0, 0, 1),
(19, 2, 'Đèn bàn trang trí euroto', '690000', 'img/Den/Den-HTDB.png', NULL, NULL, 0, 0, 1),
(20, 2, 'Đèn bàn', '590999', 'img/Den/Den-ban.png', NULL, NULL, 0, 0, 1),
(21, 2, 'Đèn chùm', '3000000', 'img/Den/Den-chum.png', NULL, 'denchum.php', 0, 0, 1),
(22, 2, 'Đèn chùm cổ điển', '5000000', 'img/Den/Den-chum-co-dien.png', NULL, NULL, 0, 0, 1),
(23, 2, 'Đèn chùm gốm lụa', '6699000', 'img/Den/Den-chum-gom-lua.png', NULL, NULL, 0, 0, 1),
(24, 2, 'Đèn chùm pha lê', '9799999', 'img/Den/Den-chum-pha-le.png', NULL, NULL, 0, 0, 1),
(25, 2, 'Đèn trần Melt Copper D28', '12990000', 'img/Den/Den-tran-nho.png', NULL, NULL, 0, 1, 1),
(26, 2, 'Đèn trần D34', '12500000', 'img/Den/Den-tra-D34.png', NULL, NULL, 0, 0, 1),
(27, 2, 'Đèn chùm pha lê Elip', '3780000', 'img/Den/Den.png', NULL, NULL, 0, 0, 1),
(28, 2, 'Đèn LED ốp trần HT2885', '1380000', 'img/Den/Den-tran.png', NULL, NULL, 0, 0, 1),
(29, 2, 'Đèn cây Vintage', '1900000', 'img/Den/Den-cay-Vintage.png', NULL, NULL, 0, 0, 1),
(30, 2, 'Đèn cây CafaLand', '1700000', 'img/Den/Den-cay.png', NULL, NULL, 0, 0, 1),
(31, 2, 'Đèn cây đứng', '1899000', 'img/Den/Den-cay-dung.png', NULL, NULL, 1, 0, 1),
(32, 2, 'Đèn cây phòng khách', '2000000', 'img/Den/Den-cay-phong-khach.png', NULL, NULL, 0, 0, 1),
(33, 3, 'Giường ngủ gỗ tràm', '13000000', 'img/Giuong/Giuong-ngu-go-tram.png', NULL, 'giuongngugotram1.php', 0, 1, 1),
(34, 3, 'Giường ngủ bọc da', '17500000', 'img/Giuong/Giuong-ngu-boc-da.png', NULL, 'giuongngubocda.php', 0, 1, 1),
(35, 3, 'Giường ngủ bọc vải SCARLET', '14500000', 'img/Giuong/Giuong-ngu-hoc-vai-SCARLET.png', NULL, NULL, 0, 0, 1),
(36, 3, 'Giường ngủ VLINE', '15000000', 'img/Giuong/Giuong-ngu-VLINE.png', NULL, NULL, 0, 0, 1),
(37, 3, 'Giường ngủ bọc vải', '11900000', 'img/Giuong/Giuong-ngu-hoc-vai-SCARLET.png', NULL, NULL, 0, 0, 1),
(38, 3, 'Giường Dolly', '11200000', 'img/Giuong/Giuong-dolly.png', NULL, NULL, 0, 0, 1),
(39, 3, 'Giường ngủ có hộc', '12599000', 'img/Giuong/Giuong-ngu-co-hoc.png', NULL, NULL, 0, 0, 1),
(40, 3, 'Giường ngủ gỗ DALUMD', '11709000', 'img/Giuong/Giuong-ngu-go-DALUMD.png', NULL, NULL, 0, 0, 1),
(41, 3, 'Giường ngủ LEMAN', '13599000', 'img/Giuong/Giuong-ngu-LEMAN.png', NULL, NULL, 0, 0, 1),
(42, 3, 'Giường Pio 1M8', '16650000', 'img/Giuong/giuong-pio.png', NULL, NULL, 0, 0, 1),
(43, 3, 'Giường ngủ Maxine', '19100000', 'img/Giuong/Giuong-ngu-Maxine.png', NULL, NULL, 0, 0, 1),
(44, 3, 'Giường ngủ Penny', '23199599', 'img/Giuong/Giuong-ngu-penny.png', NULL, NULL, 0, 0, 1),
(45, 3, 'Giường Cabo', '11111999', 'img/Giuong/Giuong-Cabo.png', NULL, NULL, 0, 0, 1),
(46, 3, 'Giường Penny hộc kéo', '13459999', 'img/Giuong/Giuong-ngu-hoc-keo.png', NULL, NULL, 0, 0, 1),
(47, 3, 'Giường Dixie 1M8', '13854000', 'img/Giuong/GIUONG-DIXIE.png', NULL, NULL, 0, 0, 1),
(48, 3, 'Giường Skagen', '12560999', 'img/Giuong/Giuong-ngu-hien-dai.png', NULL, NULL, 0, 0, 1),
(49, 4, 'Tủ bếp Charmy', '13000000', 'img/Ghe-Ban/Tu-bep.png', NULL, 'tubepCharmy.php', 0, 0, 1),
(50, 4, 'Tủ bếp Fancy', '15000000', 'img/Ghe-Ban/Tu-bep-Fancy.png', NULL, NULL, 0, 0, 1),
(51, 4, 'Kệ gia vị Nora GAPI', '150000', 'img/TvK/Ke-gia-vi.png', NULL, NULL, 0, 0, 1),
(52, 4, 'Kệ bếp', '390000', 'img/TvK/Ke-bep.png', NULL, NULL, 0, 0, 1),
(53, 4, 'Tủ quần áo VLINE', '9699000', 'img/TvK/Tu-quan-ao-VLINE.png', NULL, 'tuquanaoVILINE1.php', 0, 0, 1),
(54, 4, 'Tủ quần áo DALUMD', '13200000', 'img/TvK/Tu-quan-ao-DALUMD.jpg', NULL, NULL, 0, 0, 1),
(55, 4, 'Kệ đa năng GAPI GM141', '200000', 'img/TvK/Ke-da-nang.png', NULL, NULL, 0, 0, 1),
(56, 4, 'Kệ gia vị 2 tầng GAPI GA18', '100000', 'img/TvK/Ke.png', NULL, NULL, 0, 1, 1),
(57, 4, 'Tủ giày', '3650000', 'img/TvK/Tu-giay.png', NULL, 'tugiay.php', 1, 0, 1),
(58, 4, 'Tủ giày gỗ', '1199000', 'img/TvK/Tu-giay-go.png', NULL, NULL, 0, 0, 1),
(59, 4, 'Kệ sách Taura', '1390000', 'img/TvK/Ke-sach-Taura.png', NULL, NULL, 0, 0, 1),
(60, 4, 'Kệ sách Artigo', '3854000', 'img/TvK/Ke-sach-Artigo.png', NULL, NULL, 1, 0, 1),
(61, 4, 'Tủ kệ tivi gỗ', '2200000', 'img/TvK/Tu-ke-go-tivi.png', NULL, 'tudangkedetivigo.php', 0, 0, 1),
(62, 4, 'Tủ kệ tivi NARVIK', '3299000', 'img/TvK/Tu-ke-ti-vi-NARVIK.png', NULL, NULL, 0, 0, 1),
(63, 4, 'Kệ 3 tầng Gold', '11200000', 'img/TvK/Ke-go-3-tang-Gold.png', NULL, NULL, 0, 0, 1),
(64, 4, 'Kệ trang trí hình tròn', '3450000', 'img/TvK/Ke-tt-hinh-tron.png', NULL, NULL, 1, 0, 1),
(65, 4, 'Đồng hồ treo tường nhỏ', '520000', 'img/TvK/dong ho treo t.png', NULL, NULL, 0, 0, 1),
(66, 4, 'Bàn trang điểm', '3510000', 'img/Ghe-Ban/Ban-trang-diem.png', NULL, 'bantrangdiem.php', 0, 0, 1),
(67, 4, 'Tranh bông súng', '1650000', 'img/TvK/Tranh-Bong-Sung.png', NULL, NULL, 0, 1, 1),
(68, 4, 'Gương Curvy', '1100000', 'img/TvK/Guong-Curvy.png', NULL, NULL, 1, 0, 1),
(69, 4, 'Gương Gynko Ovale GM', '3000000', 'img/TvK/Guong-Gynko-Ovale-GM.png', NULL, NULL, 0, 0, 1),
(70, 4, 'Tủ bếp Charmy', '13000000', 'img/Ghe-Ban/Tu-bep.png', NULL, 'tubepCharmy.php', 0, 0, 1),
(71, 4, 'Tủ bếp Fancy', '15000000', 'img/Ghe-Ban/Tu-bep-Fancy.png', NULL, NULL, 0, 0, 1),
(72, 4, 'Kệ gia vị Nora GAPI', '150000', 'img/TvK/Ke-gia-vi.png', NULL, NULL, 0, 0, 1),
(73, 4, 'Kệ bếp', '390000', 'img/TvK/Ke-bep.png', NULL, NULL, 0, 0, 1),
(74, 4, 'Tủ quần áo VLINE', '9699000', 'img/TvK/Tu-quan-ao-VLINE.png', NULL, 'tuquanaoVILINE1.php', 0, 0, 1),
(75, 4, 'Tủ quần áo DALUMD', '13200000', 'img/TvK/Tu-quan-ao-DALUMD.jpg', NULL, NULL, 0, 0, 1),
(77, 4, 'Kệ đa năng GAPI GM141', '200000', 'img/TvK/Ke-da-nang.png', NULL, NULL, 0, 0, 1),
(78, 4, 'Kệ gia vị 2 tầng GAPI GA18', '100000', 'img/TvK/Ke.png', NULL, NULL, 0, 1, 1),
(79, 4, 'Tủ giày', '3650000', 'img/TvK/Tu-giay.png', NULL, 'tugiay.php', 1, 0, 1),
(80, 4, 'Tủ giày tràm', '1199000', 'img/TvK/Tu-giay-tram.png', NULL, NULL, 0, 0, 1),
(81, 4, 'Tủ giày gỗ', '1199000', 'img/TvK/Tu-giay-go.png', NULL, NULL, 0, 0, 1),
(82, 4, 'Kệ sách Taura', '1390000', 'img/TvK/Ke-sach-Taura.png', NULL, NULL, 0, 0, 1),
(83, 4, 'Kệ sách Artigo', '3854000', 'img/TvK/Ke-sach-Artigo.png', NULL, NULL, 1, 0, 1),
(84, 4, 'Tủ kệ tivi gỗ', '2200000', 'img/TvK/Tu-ke-go-tivi.png', NULL, 'tudangkedetivigo.php', 0, 0, 1),
(85, 4, 'Tủ kệ tivi NARVIK', '3299000', 'img/TvK/Tu-ke-ti-vi-NARVIK.png', NULL, NULL, 0, 0, 1),
(86, 4, 'Kệ 3 tầng Gold', '11200000', 'img/TvK/Ke-go-3-tang-Gold.png', NULL, NULL, 0, 0, 1),
(87, 4, 'Kệ trang trí hình tròn', '3450000', 'img/TvK/Ke-tt-hinh-tron.png', NULL, NULL, 1, 0, 1),
(88, 4, 'Đồng hồ treo tường nhỏ', '520000', 'img/TvK/dong ho treo t.png', NULL, NULL, 0, 0, 1),
(89, 4, 'Bàn trang điểm', '3510000', 'img/Ghe-Ban/Ban-trang-diem.png', NULL, 'bantrangdiem.php', 0, 0, 1),
(90, 4, 'Tranh bông súng', '1650000', 'img/TvK/Tranh-Bong-Sung.png', NULL, NULL, 0, 1, 1),
(91, 4, 'Gương Curvy', '1100000', 'img/TvK/Guong-Curvy.png', NULL, NULL, 1, 0, 1),
(92, 4, 'Gương Gynko Ovale GM', '3000000', 'img/TvK/Guong-Gynko-Ovale-GM.png', NULL, NULL, 0, 0, 1),
(93, 4, 'Tủ đầu giường gỗ', '3900000', 'img/TvK/Tu-dau-giuong-go.png', NULL, NULL, 0, 0, 1),
(94, 4, 'Tủ đầu giường HOBRO', '4550000', 'img/TvK/Tu-dau-giuong-go-HOBRO.png', NULL, NULL, 0, 0, 1),
(95, 4, 'Tủ đầu giường VIENNA', '2900000', 'img/TvK/Tu-dau-giuong-go-VIENNA.png', NULL, NULL, 0, 0, 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `dangnhap`
--
ALTER TABLE `dangnhap`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`);

--
-- Chỉ mục cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `dangnhap`
--
ALTER TABLE `dangnhap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
