<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AcademicYear;

class SiakadWaliKelasController {

    private static function checkAksesWaliKelas() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            redirect('/login');
            exit;
        }

        $db = Database::connect('core');
        $user_id = $_SESSION['user_id'];
        
        // Cek guru_id
        $guru = $db->query("SELECT id, nama, nip FROM guru WHERE user_id = " . intval($user_id))->fetch();
        if (!$guru) {
            redirect('/guru/dashboard?error=not_guru');
            exit;
        }

        $activeYear = AcademicYear::current();
        if (!$activeYear) {
            redirect('/guru/dashboard?error=no_active_year');
            exit;
        }

        // Cek apakah dia wali kelas di tahun ajaran aktif
        $stmt_tugas = $db->prepare("SELECT keterangan FROM guru_tugas WHERE guru_id = ? AND tugas = 'Wali Kelas' AND tahun_ajaran = ? LIMIT 1");
        $stmt_tugas->execute([$guru['id'], $activeYear['name']]);
        $tugas = $stmt_tugas->fetchColumn();

        if (!$tugas) {
            redirect('/guru/tugas_tambahan/wali_kelas?error=not_walikelas');
            exit;
        }

        $nama_kelas = str_replace('Kelas ', '', trim($tugas));
        $stmt_kelas = $db->prepare("SELECT id as kelas_id, nama_kelas, tingkat FROM kelas WHERE nama_kelas = ? LIMIT 1");
        $stmt_kelas->execute([$nama_kelas]);
        $tugas_wali = $stmt_kelas->fetch();

        if (!$tugas_wali) {
            redirect('/guru/tugas_tambahan/wali_kelas?error=kelas_not_found');
            exit;
        }

        return [
            'guru' => $guru,
            'kelas' => $tugas_wali,
            'tahun_ajaran' => $activeYear,
            'db' => $db
        ];
    }

    public static function pengaturan() {
        $context = self::checkAksesWaliKelas();
        $db = $context['db'];
        $kelas_id = $context['kelas']['kelas_id'];
        $ta_id = $context['tahun_ajaran']['id'];

        // Ambil meta saat ini
        $stmt = $db->prepare("SELECT meta_key, meta_value FROM kelas_meta WHERE kelas_id = ? AND tahun_ajaran_id = ?");
        $stmt->execute([$kelas_id, $ta_id]);
        $metas = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $struktur = isset($metas['struktur']) ? json_decode($metas['struktur'], true) : ['ketua'=>'', 'sekretaris'=>'', 'bendahara'=>''];
        $jadwal_piket = isset($metas['jadwal_piket']) ? json_decode($metas['jadwal_piket'], true) : [];

        // Ambil jadwal pelajaran dari siakad_db.jadwal_pelajaran
        $dbSiakad = \App\Core\Database::connect('siakad');
        $stmt_jadwal = $dbSiakad->prepare("
            SELECT jp.*, m.nama_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
            JOIN aula_db.guru g ON jp.guru_id = g.id
            WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ?
            ORDER BY jp.hari ASC, jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$kelas_id, $ta_id]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        $jadwal_mapel = [];
        foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
            $jadwal_mapel[$hari] = array_filter($jadwal_raw, function($j) use ($hari) { return $j['hari'] == $hari; });
        }

        // Ambil daftar siswa kelas ini
        $stmt_siswa = $db->prepare("SELECT id, nama, nis, nisn FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswas = $stmt_siswa->fetchAll();

        $guru = $context['guru'];
        $kelas = $context['kelas'];
        require_once __DIR__ . '/../../resources/views/siakad/guru/wali_kelas_pengaturan.php';
    }

    public static function savePengaturan() {
        $context = self::checkAksesWaliKelas();
        $db = $context['db'];
        $kelas_id = $context['kelas']['kelas_id'];
        $ta_id = $context['tahun_ajaran']['id'];

        $struktur = [
            'ketua' => $_POST['ketua'] ?? '',
            'sekretaris' => $_POST['sekretaris'] ?? '',
            'bendahara' => $_POST['bendahara'] ?? ''
        ];

        $jadwal_piket = [
            'Senin' => $_POST['piket_senin'] ?? [],
            'Selasa' => $_POST['piket_selasa'] ?? [],
            'Rabu' => $_POST['piket_rabu'] ?? [],
            'Kamis' => $_POST['piket_kamis'] ?? [],
            'Jumat' => $_POST['piket_jumat'] ?? [],
            'Sabtu' => $_POST['piket_sabtu'] ?? []
        ];

        $data_to_save = [
            'struktur' => json_encode($struktur),
            'jadwal_piket' => json_encode($jadwal_piket)
        ];

        foreach ($data_to_save as $key => $value) {
            $stmt = $db->prepare("INSERT INTO kelas_meta (kelas_id, tahun_ajaran_id, meta_key, meta_value) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)");
            $stmt->execute([$kelas_id, $ta_id, $key, $value]);
        }

        redirect('/siakad/guru/wali-kelas/pengaturan?msg=saved');
        exit;
    }

    public static function cetakProgram() {
        $context = self::checkAksesWaliKelas();
        $db = $context['db'];
        $kelas_id = $context['kelas']['kelas_id'];
        $ta_id = $context['tahun_ajaran']['id'];

        $guru = $context['guru'];
        $kelas = $context['kelas'];
        $tahun_ajaran = $context['tahun_ajaran'];

        // Ambil meta
        $stmt = $db->prepare("SELECT meta_key, meta_value FROM kelas_meta WHERE kelas_id = ? AND tahun_ajaran_id = ?");
        $stmt->execute([$kelas_id, $ta_id]);
        $metas = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $struktur = isset($metas['struktur']) ? json_decode($metas['struktur'], true) : ['ketua'=>'', 'sekretaris'=>'', 'bendahara'=>''];
        $jadwal_piket = isset($metas['jadwal_piket']) ? json_decode($metas['jadwal_piket'], true) : [];

        // Ambil jadwal pelajaran dari siakad_db.jadwal_pelajaran
        $dbSiakad = \App\Core\Database::connect('siakad');
        $stmt_jadwal = $dbSiakad->prepare("
            SELECT jp.*, m.nama_mapel, g.nama as nama_guru 
            FROM jadwal_pelajaran jp
            JOIN mata_pelajaran m ON jp.mata_pelajaran_id = m.id
            JOIN aula_db.guru g ON jp.guru_id = g.id
            WHERE jp.kelas_id = ? AND jp.tahun_ajaran_id = ?
            ORDER BY jp.hari ASC, jp.jam_mulai ASC
        ");
        $stmt_jadwal->execute([$kelas_id, $ta_id]);
        $jadwal_raw = $stmt_jadwal->fetchAll();

        $jadwal_mapel = [];
        foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari) {
            $jadwal_mapel[$hari] = array_filter($jadwal_raw, function($j) use ($hari) { return $j['hari'] == $hari; });
        }

        // Ambil data Kepsek
        $kepsek = $db->query("SELECT nama, nip FROM guru WHERE is_kamad = 1 LIMIT 1")->fetch();

        // Ambil data Institusi
        $institusi = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Ambil daftar siswa
        $stmt_siswa = $db->prepare("SELECT * FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
        $stmt_siswa->execute([$kelas_id]);
        $siswas = $stmt_siswa->fetchAll();

        // Peta ID siswa ke Nama
        $siswa_map = [];
        foreach($siswas as $s) {
            $siswa_map[$s['id']] = $s['nama'];
        }

        require_once __DIR__ . '/../../resources/views/siakad/guru/wali_kelas_cetak.php';
    }
}
