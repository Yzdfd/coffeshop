-- ============================================================
-- MIGRATION: Cafe System - Admin Tables
-- Jalankan SQL ini di database MySQL kamu
-- ============================================================

-- Tabel kategoris
CREATE TABLE IF NOT EXISTS `kategoris` (
    `id`             INT AUTO_INCREMENT PRIMARY KEY,
    `nama_kategori`  VARCHAR(80) NOT NULL UNIQUE,
    `deskripsi`      TEXT,
    `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel menus
CREATE TABLE IF NOT EXISTS `menus` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `kategori_id`  INT NOT NULL,
    `nama_menu`    VARCHAR(100) NOT NULL,
    `harga`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `varian`       VARCHAR(255) COMMENT 'Contoh: Kecil,Sedang,Besar',
    `deskripsi`    TEXT,
    `gambar`       VARCHAR(255),
    `status`       ENUM('tersedia','habis') DEFAULT 'tersedia',
    `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`kategori_id`) REFERENCES `kategoris`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel stok_bahan
CREATE TABLE IF NOT EXISTS `stok_bahan` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `nama_bahan`   VARCHAR(100) NOT NULL,
    `satuan`       VARCHAR(30) NOT NULL COMMENT 'gram, kg, ml, liter, pcs, dst',
    `stok`         DECIMAL(12,3) NOT NULL DEFAULT 0,
    `min_stok`     DECIMAL(12,3) NOT NULL DEFAULT 5 COMMENT 'Batas notifikasi stok rendah',
    `harga_satuan` DECIMAL(12,2) DEFAULT 0,
    `keterangan`   TEXT,
    `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel users
CREATE TABLE IF NOT EXISTS `users` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `username`     VARCHAR(50) NOT NULL UNIQUE,
    `password`     VARCHAR(255) NOT NULL,
    `role`         ENUM('admin','waiter','kasir','dapur','owner') NOT NULL DEFAULT 'waiter',
    `status`       ENUM('aktif','nonaktif') DEFAULT 'aktif',
    `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel settings (key-value store)
CREATE TABLE IF NOT EXISTS `settings` (
    `id`    INT AUTO_INCREMENT PRIMARY KEY,
    `key`   VARCHAR(80) NOT NULL UNIQUE,
    `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Data Awal ────────────────────────────────────────────────────
-- User admin default (password: admin123)
INSERT IGNORE INTO `users` (`nama_lengkap`, `username`, `password`, `role`, `status`)
VALUES ('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'aktif');

-- Setting default
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
('nama_cafe',      'Café Kami'),
('telepon',        ''),
('alamat',         ''),
('footer_struk',   'Terima kasih atas kunjungan Anda!'),
('pajak',          '0'),
('service_charge', '0'),
('mata_uang',      'IDR'),
('manajemen_meja', '1'),
('jumlah_meja',    '10');

-- Kategori contoh
INSERT IGNORE INTO `kategoris` (`nama_kategori`, `deskripsi`) VALUES
('Minuman Panas',  'Kopi, teh, dan minuman panas lainnya'),
('Minuman Dingin', 'Es kopi, es teh, smoothie, dll'),
('Makanan Berat',  'Nasi, pasta, dan makanan mengenyangkan'),
('Snack & Kue',    'Cemilan ringan dan kue');
