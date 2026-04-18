One klik in shell xammp
mysql -u root -p

-- 1. Bersihkan database lama jika ada
DROP DATABASE IF EXISTS syadanDb;
CREATE DATABASE syadanDb;
USE syadanDb;

-- =====================
-- 2. TABEL ADMIN
-- =====================
CREATE TABLE admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(40) NOT NULL,
    password VARCHAR(255) NOT NULL -- Panjang 255 buat jaga-jaga kalau ganti ke password_hash
);

INSERT INTO admin (username, password)
VALUES ('admin', MD5('admin'));

-- =====================
-- 3. TABEL KATEGORI
-- =====================
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(20),
    merk VARCHAR(50),
    kode_rak VARCHAR(10)
);

-- =====================
-- 4. TABEL BARANG
-- =====================
CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT,
    kode_barang VARCHAR(10),
    nama_barang VARCHAR(40),
    harga_beli INT(11),
    harga_jual INT(11),
    stok INT(11) DEFAULT 0,
    satuan VARCHAR(30),
    CONSTRAINT fk_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
);

-- =====================
-- 5. TABEL PENJUALAN
-- =====================
CREATE TABLE penjualan (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    nama_konsumen VARCHAR(50),
    tanggal DATE,
    no_faktur VARCHAR(50) -- Sesuaikan tipe datanya dengan tabel laporan nanti
);

-- =====================
-- 6. TABEL DETAIL PENJUALAN
-- =====================
CREATE TABLE detail_penjualan (
    id_dp INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT,
    id_barang INT,
    jumlah_trans INT,
    CONSTRAINT fk_penjualan FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan) ON DELETE CASCADE,
    CONSTRAINT fk_barang FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
);

-- =====================
-- 7. TABEL LAPORAN (Rekapitulasi)
-- =====================
CREATE TABLE laporan (
    id_laporan INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    faktur VARCHAR(50) NOT NULL,
    total_modal INT(11) NOT NULL,
    total_jual INT(11) NOT NULL,
    laba INT(11) NOT NULL
) ENGINE=InnoDB;




jika table penjualan error
SELECT * FROM penjualan;

ALTER TABLE penjualan ADD COLUMN bayar INT(11) AFTER no_faktur;

ALTER TABLE laporan 
ADD uang_bayar INT(11) NOT NULL DEFAULT 0,
ADD kembalian INT(11) NOT NULL DEFAULT 0