<?php

namespace App\Controllers;

use App\Core\Helper;
use App\Core\Database;
use PDO;

class BkController
{
    private function checkAccess()
    {
        // Must be logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . base_url('login')); exit;
        }

        // Developer / Admin always access
        if (in_array($_SESSION['role_id'], [1, 99])) {
            return true;
        }

        // Check if teacher has 'BK' or 'Kesiswaan' tugas tambahan
        if (isset($_SESSION['guru_id'])) {
            $db = Database::connect('core');
            $stmt = $db->prepare("SELECT COUNT(*) FROM guru_tugas_tambahan WHERE guru_id = ? AND (LOWER(jabatan_tugas) LIKE '%bk%' OR LOWER(jabatan_tugas) LIKE '%bimbingan%' OR LOWER(jabatan_tugas) LIKE '%kesiswaan%')");
            $stmt->execute([$_SESSION['guru_id']]);
            $hasAccess = $stmt->fetchColumn() > 0;
            if ($hasAccess) {
                return true;
            }
        }

        $_SESSION['flash_error'] = 'Anda tidak memiliki hak akses untuk modul Bimbingan Konseling.';
        header("Location: " . base_url('portal')); exit;
    }

    private function getActiveTahunAjaran()
    {
        return \App\Core\AcademicYear::current()['id'] ?? 1;
    }

    public function index()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();

        // Get Top 5 Pelanggar (Siswa dengan poin tertinggi tahun ini)
        $stmt = $db->prepare("
            SELECT s.nama as nama_siswa, k.nama_kelas, SUM(kat.poin) as total_poin
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            WHERE bp.tahun_ajaran_id = ? AND kat.tipe = 'pelanggaran'
            GROUP BY s.id
            ORDER BY total_poin DESC
            LIMIT 5
        ");
        $stmt->execute([$ta_id, $ta_id]);
        $topPelanggar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get recent entries
        $stmt2 = $db->prepare("
            SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, kat.nama_kategori, kat.poin, kat.tipe, g.nama as nama_guru
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = bp.tahun_ajaran_id
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            JOIN guru g ON bp.guru_id = g.id
            WHERE bp.tahun_ajaran_id = ?
            ORDER BY bp.created_at DESC
            LIMIT 10
        ");
        $stmt2->execute([$ta_id]);
        $recentLog = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_dashboard';
        $title = 'Dashboard E-BK | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/dashboard.php';
    }

    public function kategori()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $kategoriList = $db->query("SELECT * FROM bk_kategori ORDER BY tipe, poin ASC")->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_kategori';
        $title = 'Kategori Poin BK | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/kategori.php';
    }

    public function saveKategori()
    {
        $this->checkAccess();
        $db = Database::connect('core');

        $id = $_POST['id'] ?? '';
        $nama = $_POST['nama_kategori'] ?? '';
        $tipe = $_POST['tipe'] ?? 'pelanggaran';
        $tingkat = $_POST['tingkat'] ?? null;
        if ($tipe === 'prestasi') {
            $tingkat = null;
        }
        $poin = (int)($_POST['poin'] ?? 0);

        if ($id) {
            $stmt = $db->prepare("UPDATE bk_kategori SET nama_kategori = ?, tipe = ?, tingkat = ?, poin = ? WHERE id = ?");
            $stmt->execute([$nama, $tipe, $tingkat, $poin, $id]);
            $_SESSION['flash_success'] = 'Kategori berhasil diupdate.';
        } else {
            $stmt = $db->prepare("INSERT INTO bk_kategori (nama_kategori, tipe, tingkat, poin) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $tipe, $tingkat, $poin]);
            $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan.';
        }

        header("Location: " . base_url('bk/kategori')); exit;
    }

    public function deleteKategori($id)
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM bk_kategori WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
        header("Location: " . base_url('bk/kategori')); exit;
    }

    public function pelanggaran()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $ta_id = $this->getActiveTahunAjaran();

        // Ambil list siswa untuk dropdown
        $stmtSiswa = $db->prepare("
            SELECT s.id, s.nama as nama_siswa, k.nama_kelas 
            FROM siswa s 
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = ?
            JOIN kelas k ON rks.kelas_id = k.id
            ORDER BY k.nama_kelas ASC, s.nama ASC
        ");
        $stmtSiswa->execute([$ta_id]);
        $siswaList = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

        // Ambil list kategori
        $kategoriList = $db->query("SELECT * FROM bk_kategori ORDER BY tipe ASC, poin ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Ambil riwayat input poin
        $stmtLog = $db->prepare("
            SELECT bp.*, s.nama as nama_siswa, k.nama_kelas, kat.nama_kategori, kat.poin, kat.tipe, g.nama as nama_guru
            FROM bk_poin bp
            JOIN siswa s ON bp.siswa_id = s.id
            JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id AND rks.tahun_ajaran_id = bp.tahun_ajaran_id
            JOIN kelas k ON rks.kelas_id = k.id
            JOIN bk_kategori kat ON bp.kategori_id = kat.id
            JOIN guru g ON bp.guru_id = g.id
            WHERE bp.tahun_ajaran_id = ?
            ORDER BY bp.created_at DESC
        ");
        $stmtLog->execute([$ta_id]);
        $riwayat = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        $activeMenu = 'bk_pelanggaran';
        $title = 'Catat Poin Siswa | MTs RS';
        require_once __DIR__ . '/../../resources/views/bk/pelanggaran.php';
    }

    public function savePelanggaran()
    {
        $this->checkAccess();
        $db = Database::connect('core');
        
        $siswa_id = $_POST['siswa_id'] ?? '';
        $kategori_id = $_POST['kategori_id'] ?? '';
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $keterangan = $_POST['keterangan'] ?? '';
        
        $guru_id = $_SESSION['guru_id'] ?? 1; // Fallback to 1 if admin
        $ta_id = $this->getActiveTahunAjaran();

        if ($siswa_id && $kategori_id) {
            $stmt = $db->prepare("INSERT INTO bk_poin (siswa_id, kategori_id, tahun_ajaran_id, guru_id, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$siswa_id, $kategori_id, $ta_id, $guru_id, $tanggal, $keterangan]);
            $_SESSION['flash_success'] = 'Catatan poin berhasil disimpan!';
        }

        header("Location: " . base_url('bk/pelanggaran')); exit;
    }

    public function deletePelanggaran($id)
    {
        $this->checkAccess();
        $db = Database::connect('core');
        $stmt = $db->prepare("DELETE FROM bk_poin WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Catatan poin berhasil dihapus.';
        header("Location: " . base_url('bk/pelanggaran')); exit;
    }
}
