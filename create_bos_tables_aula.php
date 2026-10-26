<?php
require_once __DIR__ . '/vendor/autoload.php';
$db = \App\Core\Database::connect('core');

// Tabel BOS RKAS
$sql1 = "CREATE TABLE IF NOT EXISTS bos_rkas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tahun_anggaran INT NOT NULL,
    snp_id INT NOT NULL COMMENT '1-8 Standar Nasional Pendidikan',
    kode_rekening VARCHAR(50),
    uraian VARCHAR(255) NOT NULL,
    volume INT DEFAULT 1,
    satuan VARCHAR(50),
    harga_satuan DECIMAL(15,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$db->exec($sql1);
echo "bos_rkas created in aula_db.\n";

// Tabel BOS BKU (Buku Kas Umum)
$sql2 = "CREATE TABLE IF NOT EXISTS bos_bku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    no_bukti VARCHAR(50),
    uraian VARCHAR(255) NOT NULL,
    penerimaan DECIMAL(15,2) DEFAULT 0,
    pengeluaran DECIMAL(15,2) DEFAULT 0,
    saldo DECIMAL(15,2) NOT NULL,
    jenis_transaksi ENUM('Tunai', 'Bank') DEFAULT 'Tunai',
    pajak_ppn DECIMAL(15,2) DEFAULT 0,
    pajak_pph21 DECIMAL(15,2) DEFAULT 0,
    pajak_pph22 DECIMAL(15,2) DEFAULT 0,
    pajak_pph23 DECIMAL(15,2) DEFAULT 0,
    rkas_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rkas_id) REFERENCES bos_rkas(id) ON DELETE SET NULL
)";
$db->exec($sql2);
echo "bos_bku created in aula_db.\n";
