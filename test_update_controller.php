<?php
$file = 'C:/xampp/htdocs/masmifhda/app/Controllers/AbsenV2Controller.php';
$content = file_get_contents($file);

// Replace 1: aksesScanner
$content = str_replace(
    "COALESCE((SELECT akses_guru FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_guru",
    "COALESCE((SELECT akses_guru FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_guru,
                    COALESCE((SELECT akses_izin FROM absen_akses_scanner WHERE guru_id = g.id), 0) as akses_izin",
    $content
);

// Replace 2: saveAksesScanner param check
$content = str_replace(
    "\$tipe = isset(\$_POST['tipe']) ? \$_POST['tipe'] : ''; // 'siswa' or 'guru'",
    "\$tipe = isset(\$_POST['tipe']) ? \$_POST['tipe'] : ''; // 'siswa' or 'guru' or 'izin'",
    $content
);
$content = str_replace(
    "in_array(\$tipe, ['siswa', 'guru'])",
    "in_array(\$tipe, ['siswa', 'guru', 'izin'])",
    $content
);

// Replace 3: update statement
$content = str_replace(
    "} else {
                    \$stmt = \$db_core->prepare(\"UPDATE absen_akses_scanner SET akses_guru = ? WHERE guru_id = ?\");
                }",
    "} elseif (\$tipe == 'guru') {
                    \$stmt = \$db_core->prepare(\"UPDATE absen_akses_scanner SET akses_guru = ? WHERE guru_id = ?\");
                } else {
                    \$stmt = \$db_core->prepare(\"UPDATE absen_akses_scanner SET akses_izin = ? WHERE guru_id = ?\");
                }",
    $content
);

// Replace 4: cleanup statement
$content = str_replace(
    "WHERE akses_siswa = 0 AND akses_guru = 0",
    "WHERE akses_siswa = 0 AND akses_guru = 0 AND akses_izin = 0",
    $content
);

// Replace 5: insert statement
$content = str_replace(
    "if (\$tipe == 'siswa') {
                        \$stmt = \$db_core->prepare(\"INSERT INTO absen_akses_scanner (guru_id, akses_siswa, akses_guru) VALUES (?, 1, 0)\");
                    } else {
                        \$stmt = \$db_core->prepare(\"INSERT INTO absen_akses_scanner (guru_id, akses_siswa, akses_guru) VALUES (?, 0, 1)\");
                    }",
    "if (\$tipe == 'siswa') {
                        \$stmt = \$db_core->prepare(\"INSERT INTO absen_akses_scanner (guru_id, akses_siswa, akses_guru, akses_izin) VALUES (?, 1, 0, 0)\");
                    } elseif (\$tipe == 'guru') {
                        \$stmt = \$db_core->prepare(\"INSERT INTO absen_akses_scanner (guru_id, akses_siswa, akses_guru, akses_izin) VALUES (?, 0, 1, 0)\");
                    } else {
                        \$stmt = \$db_core->prepare(\"INSERT INTO absen_akses_scanner (guru_id, akses_siswa, akses_guru, akses_izin) VALUES (?, 0, 0, 1)\");
                    }",
    $content
);

file_put_contents($file, $content);
echo "AbsenV2Controller.php updated.\n";
