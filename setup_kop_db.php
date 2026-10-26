<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=aula_db', 'root', '');
$db->exec("CREATE TABLE IF NOT EXISTS pengaturan_kop_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_yayasan VARCHAR(255),
    font_yayasan INT DEFAULT 16,
    nama_madrasah VARCHAR(255),
    font_madrasah INT DEFAULT 24,
    alamat_jalan TEXT,
    telepon VARCHAR(50),
    email VARCHAR(100),
    website VARCHAR(100),
    logo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$cek = $db->query("SELECT COUNT(*) FROM pengaturan_kop_surat")->fetchColumn();
if ($cek == 0) {
    $db->exec("INSERT INTO pengaturan_kop_surat (nama_yayasan, font_yayasan, nama_madrasah, font_madrasah, alamat_jalan, telepon, email, website, logo) VALUES (
        'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA',
        16,
        'MADRASAH ALIYAH SUNAN AMPEL',
        24,
        'Jl. Tirtomoyo, No. 083, Margorahayu',
        '081938240819',
        'masmiftahulhuda083@gmail.com',
        '',
        'logo.png'
    )");
}
echo "Table created and seeded.";
