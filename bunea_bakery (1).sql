-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 04:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bunea_bakery`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email`, `password`) VALUES
(1, 'Admin Bunéa', 'admin@buneabakery.test', '$2y$12$697j0g1GcAijrInEiYdpReBORQayAvfmwlp0eA5IhX3RbO67O9n2O');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 6, 1, 159000.00, 159000.00),
(2, 2, 2, 1, 175000.00, 175000.00),
(3, 2, 4, 1, 79000.00, 79000.00),
(4, 3, 6, 1, 159000.00, 159000.00),
(5, 4, 2, 1, 175000.00, 175000.00),
(6, 4, 4, 1, 79000.00, 79000.00),
(7, 4, 6, 1, 159000.00, 159000.00),
(8, 5, 6, 1, 159000.00, 159000.00),
(9, 6, 4, 1, 79000.00, 79000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telepon`, `email`, `alamat`, `tanggal_daftar`, `password`) VALUES
(1, 'Pelanggan Demo', '081234567890', 'pelanggan@demo.test', 'Jl. Contoh No. 1, Bandung', '2026-09-01', '$2y$12$u9YCRxzOV1BIHxPFENXVZ.HulQqR4353LxLtUA4q3U4wnqaL6Hf8O'),
(2, 'ipin', '0812-3456-7890', 'ipin@ipin', 'rahasia', '2026-09-01', '$2y$10$M4LyGMeRYjw9GvqFY6qP3.siy87x82F9kZzjPdlYeO6YQJ2LNjIMS'),
(3, 'ajjuma', '0895-3475-876', 'ajuma@lucu', 'jl.anggrek', '2026-09-02', '$2y$10$522XtxBX4WzSLPdo8r0TWuPrmE338NJcDFOiiwRybxQsvaGyElndy'),
(4, 'bela', '089-126-836', 'bela@lucu', 'anggrek', '2026-09-02', '$2y$10$xU/YPPZVTtpMBX.45/zkouI3v6RmDGxg1yO4V9xN8L1ARbe99Q1km');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `tanggal_bayar` datetime DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('lunas','belum lunas') NOT NULL DEFAULT 'belum lunas',
  `bukti_pembayaran` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `tanggal_bayar`, `metode_pembayaran`, `jumlah_bayar`, `status_pembayaran`, `bukti_pembayaran`) VALUES
(1, 1, '2026-09-01 14:38:16', 'Cash', 159000.00, 'lunas', NULL),
(2, 2, '2026-09-02 07:25:52', 'Cash', 254000.00, 'lunas', NULL),
(3, 3, NULL, NULL, 159000.00, 'belum lunas', NULL),
(4, 4, '2026-09-02 08:10:24', 'Transfer Bank - Bank BRI', 413000.00, 'lunas', NULL),
(5, 5, '2026-09-02 08:29:32', 'Transfer Bank - Bank BRI', 159000.00, 'lunas', NULL),
(6, 6, '2026-09-02 08:56:40', 'Transfer Bank - Bank BRI', 79000.00, 'lunas', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal_pesanan` datetime NOT NULL DEFAULT current_timestamp(),
  `total_harga` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_pesanan` enum('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pelanggan`, `tanggal_pesanan`, `total_harga`, `status_pesanan`) VALUES
(1, 2, '2026-09-01 14:38:09', 159000.00, 'selesai'),
(2, 3, '2026-09-02 07:25:39', 254000.00, 'selesai'),
(3, 3, '2026-09-02 07:50:06', 159000.00, 'selesai'),
(4, 4, '2026-09-02 08:02:25', 413000.00, 'selesai'),
(5, 4, '2026-09-02 08:29:23', 159000.00, 'selesai'),
(6, 4, '2026-09-02 08:56:08', 79000.00, 'selesai');

-- --------------------------------------------------------

--
-- Table structure for table `produk_kue`
--

CREATE TABLE `produk_kue` (
  `id_produk` int(11) NOT NULL,
  `id_toko` int(11) NOT NULL,
  `nama_kue` varchar(120) NOT NULL,
  `jenis_kue` varchar(80) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `foto_kue` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk_kue`
--

INSERT INTO `produk_kue` (`id_produk`, `id_toko`, `nama_kue`, `jenis_kue`, `harga`, `stok`, `deskripsi`, `foto_kue`) VALUES
(1, 1, 'Strawberry Cloud Cake', 'Birthday Cake', 189000.00, 8, 'Sponge cake lembut dengan krim vanilla dan strawberry.', 'strawberry-cloud-cake-1-20260902050455.png'),
(2, 1, 'Chocolate Bloom Cake', 'Chocolate Cake', 175000.00, 5, 'Cake cokelat lembut dengan ganache premium.', 'chocolate-bloom-cake-2-20260902050448.png'),
(3, 1, 'Berry Shortcake', 'Cream Cake', 165000.00, 6, 'Vanilla cake, fresh berry, dan whipped cream.', 'berry-shortcake-3-20260902050438.png'),
(4, 1, 'Mini Bento Cake', 'Bento Cake', 79000.00, 9, 'Cake mini personal dengan dekorasi manis.', 'mini-bento-cake-4-20260902050428.png'),
(5, 1, 'Matcha Garden Cake', 'Matcha Cake', 195000.00, 5, 'Cake matcha dengan cream cheese lembut creamy.', 'matcha-garden-cake-5-20260902050419.png'),
(6, 1, 'Caramel Dream Cake', 'Caramel Cake', 159000.00, 6, 'Cake vanilla dengan saus caramel yang sangat lezat dan nikmat.', 'caramel-dream-cake-6-20260902050406.png');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `ulasan` text NOT NULL,
  `tanggal_review` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id_review`, `id_produk`, `id_pelanggan`, `id_pesanan`, `rating`, `ulasan`, `tanggal_review`) VALUES
(1, 2, 3, 2, 4, 'demi apa rasanya enak banget lembut sama creamy dan ngga ebej sama sekali,jempol deh', '2026-09-02 07:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id_toko` int(11) NOT NULL,
  `nama_toko` varchar(100) NOT NULL,
  `alamat_toko` text NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `jam_operasional` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id_toko`, `nama_toko`, `alamat_toko`, `no_telepon`, `email`, `nama_pemilik`, `jam_operasional`) VALUES
(1, 'Bunéa Bakery', 'Jl. Melati No. 18, Bandung', '0812-3456-7890', 'hello@buneabakery.test', 'Bunéa', '09.00 - 20.00 WIB');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `produk_kue`
--
ALTER TABLE `produk_kue`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_toko` (`id_toko`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD UNIQUE KEY `unik_review_pesanan_produk` (`id_produk`,`id_pelanggan`,`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id_toko`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `produk_kue`
--
ALTER TABLE `produk_kue`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `id_toko` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk_kue` (`id_produk`) ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON UPDATE CASCADE;

--
-- Constraints for table `produk_kue`
--
ALTER TABLE `produk_kue`
  ADD CONSTRAINT `produk_kue_ibfk_1` FOREIGN KEY (`id_toko`) REFERENCES `toko` (`id_toko`) ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk_kue` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
