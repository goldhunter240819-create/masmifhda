<?php
$content = file_get_contents('c:/xampp/htdocs/masmifhda/apk/absen.php');
$content = str_replace('siswa_id', 'guru_id', $content);
$content = str_replace('absensi_siswa', 'absensi_guru', $content);
$content = str_replace('Data Absensi Siswa', 'Data Absensi Guru', $content);
$content = str_replace('Bolos', 'Terlambat', $content);
$content = str_replace('bolos', 'terlambat', $content);
$content = str_replace("'B' => 0", "'T' => 0", $content);
$content = str_replace("['B']", "['T']", $content);
$content = str_replace("\$stat=='B'?'Bolos'", "\$stat=='T'?'Terlambat'", $content);
$content = str_replace("\$statKode=='A' || \$statKode=='B'?'value-red'", "\$statKode=='A' || \$statKode=='T'?'value-red'", $content);

file_put_contents('c:/xampp/htdocs/masmifhda/apk/absen_guru.php', $content);
