<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class CbtPesertaKhususSeeder {

    public static function getStudentData() {
        return [
            // ─── KELAS X (16 Siswa) ───
            [
                'nis'           => '3112743946',
                'nama'          => 'Putri Hana Pertiwi',
                'tgl_lahir'     => '2010-11-04',
                'jk'            => 'P',
                'ortu'          => 'Muntorip & Siti Haati',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '116887998',
                'nama'          => 'M Ridwan',
                'tgl_lahir'     => '2011-07-19',
                'jk'            => 'L',
                'ortu'          => 'Hermansyah & Suwiti',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3103963106',
                'nama'          => 'Yogi Rizki Pratama',
                'tgl_lahir'     => '2010-05-25',
                'jk'            => 'L',
                'ortu'          => 'Triyono dan Trimulyani',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3085287755',
                'nama'          => 'Heni',
                'tgl_lahir'     => '2008-05-20',
                'jk'            => 'P',
                'ortu'          => 'Usa & Sawiah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3114503833',
                'nama'          => 'Rifa Nur Arafah',
                'tgl_lahir'     => '2011-01-10',
                'jk'            => 'P',
                'ortu'          => 'Acim & Atiah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3322045207100000',
                'nama'          => 'Iswanti Latifah',
                'tgl_lahir'     => '2010-07-12',
                'jk'            => 'P',
                'ortu'          => 'Rusdi Yanto & Nur Laila',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3100035200',
                'nama'          => 'Alif Faiq An Nasuha',
                'tgl_lahir'     => '2010-08-04',
                'jk'            => 'L',
                'ortu'          => 'Bahrun & Anis Farida',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3109032143',
                'nama'          => 'Alfian Nur Adha',
                'tgl_lahir'     => '2011-06-12',
                'jk'            => 'L',
                'ortu'          => 'Takim & Likah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3119567384',
                'nama'          => 'Nur Velizaturrohmah',
                'tgl_lahir'     => '2011-05-11',
                'jk'            => 'P',
                'ortu'          => 'Abdul Basir & Nurtoyibah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '113017883',
                'nama'          => 'Zahro Nur Aulia',
                'tgl_lahir'     => '2011-11-23',
                'jk'            => 'P',
                'ortu'          => 'Mushonif Mashuri & Misbah Khoirohmah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3102304474',
                'nama'          => 'Rian Setiawan',
                'tgl_lahir'     => '2010-02-07',
                'jk'            => 'L',
                'ortu'          => 'Nasihin & Hur Hayati',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '126018293',
                'nama'          => 'Rizki Pratama',
                'tgl_lahir'     => '2011-01-12',
                'jk'            => 'L',
                'ortu'          => 'Sugiarto & Rimba Sumiati',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '3103705227',
                'nama'          => 'Zaini Abdullah',
                'tgl_lahir'     => '2010-10-28',
                'jk'            => 'L',
                'ortu'          => 'Junaedi & Rohamah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '111184251',
                'nama'          => 'Fersa Sandra Putri',
                'tgl_lahir'     => '2011-02-19',
                'jk'            => 'P',
                'ortu'          => 'Eko Budi Santoso',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '89233447',
                'nama'          => 'M Ramli Ainur R',
                'tgl_lahir'     => '2008-10-12',
                'jk'            => 'L',
                'ortu'          => 'Hermansyah & Suwiti',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],
            [
                'nis'           => '1804056006100001',
                'nama'          => 'Jelita Sugiarto Putri',
                'tgl_lahir'     => '2010-06-20',
                'jk'            => 'P',
                'ortu'          => 'Unang Sugiarto & Eis Hasanah',
                'tingkat'       => 'Kelas X',
                'kelas_nama'    => 'X - Kelas Jauh',
            ],

            // ─── KELAS XI (9 Siswa) ───
            [
                'nis'           => '3207130304100003',
                'nama'          => 'Padil Awali',
                'tgl_lahir'     => '2010-04-03',
                'jk'            => 'L',
                'ortu'          => 'Lukman & Adah',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '1804052704080001',
                'nama'          => 'Dede Yusup',
                'tgl_lahir'     => '2008-04-27',
                'jk'            => 'L',
                'ortu'          => 'Agan & Maimunah',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '1804234104100001',
                'nama'          => 'Kholifaturrohmah',
                'tgl_lahir'     => '2010-04-01',
                'jk'            => 'P',
                'ortu'          => 'Suwarno & Nur Azizah',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '18040521108090000',
                'nama'          => 'Kami Bihisarona',
                'tgl_lahir'     => '2009-08-21',
                'jk'            => 'L',
                'ortu'          => 'Riadi & Wakiroh',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '11804236403100001',
                'nama'          => 'Siti Rohiyatul Jannah',
                'tgl_lahir'     => '2010-03-27',
                'jk'            => 'P',
                'ortu'          => 'Dzikrori & Siti Rofiah',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '1804052508080001',
                'nama'          => 'Mikdad Saepul M',
                'tgl_lahir'     => '2008-08-25',
                'jk'            => 'L',
                'ortu'          => 'Rosidin & Rohayati',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '17040730007090001',
                'nama'          => 'Tegar Adi Santoso',
                'tgl_lahir'     => '2009-07-30',
                'jk'            => 'L',
                'ortu'          => 'M Taufik & Maria Ulfa',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '150805420710001',
                'nama'          => 'Vina Maya Safana',
                'tgl_lahir'     => '2010-07-02',
                'jk'            => 'P',
                'ortu'          => 'Paimun & Alfiah',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],
            [
                'nis'           => '1804052608100001',
                'nama'          => 'Wafik Al-kautsar',
                'tgl_lahir'     => '2010-09-10',
                'jk'            => 'L',
                'ortu'          => 'Anddri & Winarsih',
                'tingkat'       => 'Kelas XI',
                'kelas_nama'    => 'XI - Kelas Jauh',
            ],

            // ─── KELAS XII (9 Siswa) ───
            [
                'nis'           => '1804050511090001',
                'nama'          => 'Abdul Rouf',
                'tgl_lahir'     => '2009-11-05',
                'jk'            => 'L',
                'ortu'          => 'Somad & Sursih',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '1804050401060002',
                'nama'          => 'Agus Salim',
                'tgl_lahir'     => '2006-01-04',
                'jk'            => 'L',
                'ortu'          => 'Ija & Siti Maryam',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '1804051410080001',
                'nama'          => 'Arjun Junaedi',
                'tgl_lahir'     => '2008-10-14',
                'jk'            => 'L',
                'ortu'          => 'Uskam & Lis Awangsih',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '1806244706090002',
                'nama'          => 'Ina Hinayatul Mutakiyah',
                'tgl_lahir'     => '2009-06-07',
                'jk'            => 'P',
                'ortu'          => 'Roni & Siti Suprapti',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '3081740563',
                'nama'          => 'Khoirul Anam',
                'tgl_lahir'     => '2008-08-18',
                'jk'            => 'L',
                'ortu'          => 'Katimun & Umi Kulsum',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '180405310509001',
                'nama'          => 'Khoirul Anwar',
                'tgl_lahir'     => '2009-05-31',
                'jk'            => 'L',
                'ortu'          => 'Safrudin & Siti Muthoharoh',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '1804051903090001',
                'nama'          => 'M Ijal Bukhori',
                'tgl_lahir'     => '2009-03-19',
                'jk'            => 'L',
                'ortu'          => 'Mad Yani & Khusnul',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '6545435343',
                'nama'          => 'Valentina Ayu Lestari',
                'tgl_lahir'     => '2009-02-02',
                'jk'            => 'P',
                'ortu'          => 'Sainul & Daryani',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
            [
                'nis'           => '1217468716',
                'nama'          => 'Juwita Indah Sari',
                'tgl_lahir'     => '2009-03-13',
                'jk'            => 'P',
                'ortu'          => 'Hardiyanto & Surtiah',
                'tingkat'       => 'Kelas XII',
                'kelas_nama'    => 'XII - Kelas Jauh',
            ],
        ];
    }

    /**
     * Masukkan daftar peserta kelas jauh ke dalam cbt_peserta_khusus
     */
    public static function seed() {
        $db = Database::connect('core');
        \App\Controllers\CbtController::ensureTables($db);

        $students = self::getStudentData();
        $inserted = 0;
        $updated = 0;

        $stmtCheck = $db->prepare("SELECT id FROM cbt_peserta_khusus WHERE nis = ? OR nama_siswa = ?");
        $stmtInsert = $db->prepare("
            INSERT INTO cbt_peserta_khusus 
            (no_peserta, nis, nama_siswa, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, tingkat, kelas_nama, username, password_plain, password_hash, qr_token, lokasi_cabang, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Kelas Jauh', 'Aktif')
        ");
        $stmtUpdate = $db->prepare("
            UPDATE cbt_peserta_khusus 
            SET no_peserta = ?, nis = ?, nama_siswa = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat = ?, tingkat = ?, kelas_nama = ?, username = ?, password_plain = ?, password_hash = ?, qr_token = ?, lokasi_cabang = 'Kelas Jauh', status = 'Aktif'
            WHERE id = ?
        ");

        $index = 1;
        foreach ($students as $s) {
            $numPad = str_pad($index, 3, '0', STR_PAD_LEFT);
            $no_peserta = 'SAS-KJ-' . $numPad;
            $username = 'saskj' . $numPad;
            $password_plain = 'saskj' . $numPad;
            $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);
            $qr_token = substr(hash('sha256', $no_peserta . $username . 'MASMIFHDA_CBT_KJ_SECURE_2026'), 0, 24);
            $alamat = 'Orang Tua: ' . $s['ortu'] . ', Lampung Barat';
            $tempat_lahir = 'Lampung Barat';

            $stmtCheck->execute([$s['nis'], $s['nama']]);
            $existingId = $stmtCheck->fetchColumn();

            if ($existingId) {
                $stmtUpdate->execute([
                    $no_peserta, $s['nis'], $s['nama'], $s['jk'], $tempat_lahir,
                    $s['tgl_lahir'], $alamat, $s['tingkat'], $s['kelas_nama'],
                    $username, $password_plain, $password_hash, $qr_token, $existingId
                ]);
                $updated++;
            } else {
                $stmtInsert->execute([
                    $no_peserta, $s['nis'], $s['nama'], $s['jk'], $tempat_lahir,
                    $s['tgl_lahir'], $alamat, $s['tingkat'], $s['kelas_nama'],
                    $username, $password_plain, $password_hash, $qr_token
                ]);
                $inserted++;
            }
            $index++;
        }

        return [
            'total'    => count($students),
            'inserted' => $inserted,
            'updated'  => $updated
        ];
    }
}
