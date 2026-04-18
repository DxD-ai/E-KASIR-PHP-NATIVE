-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Apr 2026 pada 11.38
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `syadandb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3'),
(2, 'e0233f864cc07aa5c49ffa1c5865a21d', ''),
(3, 'e0233f864cc07aa5c49ffa1c5865a21d', 'e0233f864cc07aa5c49ffa1c5865a21d'),
(4, 'nopa', 'e0233f864cc07aa5c49ffa1c5865a21d');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `kode_barang` varchar(10) DEFAULT NULL,
  `nama_barang` varchar(40) DEFAULT NULL,
  `harga_beli` int(11) DEFAULT NULL,
  `harga_jual` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `satuan` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id_dp` int(11) NOT NULL,
  `id_penjualan` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `jumlah_trans` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(20) DEFAULT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `kode_rak` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `faktur` varchar(50) NOT NULL,
  `total_modal` int(11) NOT NULL,
  `total_jual` int(11) NOT NULL,
  `laba` int(11) NOT NULL,
  `uang_bayar` int(11) NOT NULL DEFAULT 0,
  `kembalian` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tanggal`, `faktur`, `total_modal`, `total_jual`, `laba`, `uang_bayar`, `kembalian`) VALUES
(1, '2026-04-14', 'FKT-20260414063616', 20000, 100000, 80000, 0, 0),
(2, '2026-04-14', 'FKT-20260414074301', 10000, 50000, 40000, 0, 0),
(3, '2026-04-14', 'FKT-20260414074617', 10000, 100000, 90000, 0, 0),
(4, '2026-04-14', 'FKT-20260414075523', 10000, 50000, 40000, 0, 0),
(5, '2026-04-14', 'FKT-20260414083034', 54000, 100000, 46000, 0, 0),
(6, '2026-04-14', 'FKT-20260414095237', 108000, 200000, 92000, 0, 0),
(7, '2026-04-14', 'FKT-20260414102707', 14000, 25000, 11000, 50000, 25000),
(8, '2026-04-14', 'FKT-20260414104657', 54000, 100000, 46000, 0, 0),
(9, '2026-04-14', 'FKT-20260414104714', 540000, 1000000, 460000, 0, 0),
(10, '2026-04-14', 'FKT-20260414104946', 14000, 25000, 11000, 0, 0),
(11, '2026-04-14', 'FKT-20260414105348', 54000, 100000, 46000, 0, 0),
(12, '2026-04-14', 'FKT-20260414111127', 108000, 200000, 92000, 0, 0),
(13, '2026-04-14', 'FKT-20260414111520', 54000, 100000, 46000, 0, 0),
(14, '2026-04-14', 'FKT-20260414111559', 54000, 100000, 46000, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `nama_konsumen` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `no_faktur` varchar(50) DEFAULT NULL,
  `bayar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `nama_konsumen`, `tanggal`, `no_faktur`, `bayar`) VALUES
(1, 'Umum', '2026-04-14', 'FKT-20260414083034', NULL),
(2, 'Umum', '2026-04-14', 'FKT-20260414095237', NULL),
(3, 'Umum', '2026-04-14', 'FKT-20260414101851', NULL),
(4, 'Umum', '2026-04-14', 'FKT-20260414102004', NULL),
(5, 'Umum', '2026-04-14', 'FKT-20260414102707', NULL),
(6, 'Umum', '2026-04-14', 'FKT-20260414104657', NULL),
(7, 'Umum', '2026-04-14', 'FKT-20260414104714', NULL),
(8, 'Umum', '2026-04-14', 'FKT-20260414104946', NULL),
(9, 'Umum', '2026-04-14', 'FKT-20260414105348', NULL),
(10, 'Umum', '2026-04-14', 'FKT-20260414111127', NULL),
(11, 'Umum', '2026-04-14', 'FKT-20260414111520', 200000),
(12, 'Umum', '2026-04-14', 'FKT-20260414111559', 500000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `fk_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id_dp`),
  ADD KEY `fk_penjualan` (`id_penjualan`),
  ADD KEY `fk_barang` (`id_barang`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indeks untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id_dp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `fk_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `penjualan` (`id_penjualan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
