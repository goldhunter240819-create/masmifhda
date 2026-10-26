<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadLainLainController {

    public function rapat() {
        $db = Database::connect('core');
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        
        $active_module = 'siakad';
        $active_page = 'rapat';
        $adminNama = $_SESSION['nama'] ?? 'Admin';
        $adminInitial = substr($adminNama, 0, 2);

        $title = "Daftar Hadir Rapat - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/rapat.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetakRapat() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/siakad/lain-lain/rapat');
            exit;
        }

        $kelas_id = $_POST['kelas_id'] ?? '';
        $judul = $_POST['judul'] ?? 'DAFTAR HADIR RAPAT';
        $tanggal = $_POST['tanggal'] ?? date('d F Y');
        $agenda = $_POST['agenda'] ?? '';

        $db = Database::connect('core');
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $siswas = [];
        $kelas_info = null;

        if ($kelas_id) {
            $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
            if ($kelas_id !== 'all') {
                $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($kelas_id);
                $kelas_info = $db->query("SELECT * FROM kelas WHERE id = " . intval($kelas_id))->fetch();
            }

            $siswas = $db->query("SELECT s.nama,
                                 COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas
                                 FROM siswa s 
                                 LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                     AND rks.tahun_ajaran_id IN (
                                         SELECT id FROM tahun_ajaran WHERE name = '$active_year_name'
                                     )
                                 LEFT JOIN kelas k ON s.kelas_id = k.id 
                                 LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                                 $where
                                 ORDER BY s.nama ASC")->fetchAll();
        }

        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Siapkan baris agenda (split by newline)
        $agenda_lines = [];
        if (trim($agenda) !== '') {
            $lines = explode("\n", $agenda);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line !== '') {
                    $agenda_lines[] = $line;
                }
            }
        }

        ob_start();
        include __DIR__ . '/../../resources/views/siakad/cetak_rapat.php';
        echo ob_get_clean();
        exit;
    }

    public function broadcast() {
        $active_module = 'siakad';
        $active_page = 'broadcast';
        $adminNama = $_SESSION['nama'] ?? 'Admin';
        $adminInitial = substr($adminNama, 0, 2);

        $db = Database::connect('core');
        $pengumumans = $db->query("SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC")->fetchAll();

        $title = "Broadcast Notifikasi - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/broadcast.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function sendBroadcast() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul = $_POST['judul'] ?? 'Pengumuman';
            $pesan = $_POST['pesan'] ?? '';
            $url = !empty($_POST['url']) ? $_POST['url'] : null;
            $tanggal_berakhir = !empty($_POST['tanggal_berakhir']) ? $_POST['tanggal_berakhir'] : date('Y-m-d', strtotime('+7 days'));

            if (trim($pesan) === '') {
                echo "<script>alert('Pesan tidak boleh kosong!'); window.history.back();</script>";
                return;
            }

            // Simpan ke Database
            $db = \App\Core\Database::connect('core');
            $stmt = $db->prepare("INSERT INTO pengumuman (judul, pesan, url, tanggal_berakhir) VALUES (?, ?, ?, ?)");
            $stmt->execute([$judul, $pesan, $url, $tanggal_berakhir]);

            // Panggil OneSignalService
            require_once __DIR__ . '/../Services/OneSignalService.php';
            $response = \App\Services\OneSignalService::sendNotification($judul, $pesan, $url);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<body style='background:#f1f5f9; font-family:sans-serif;'>";
            echo "<script>
                Swal.fire({
                    title: 'Berhasil Terkirim!',
                    text: 'Broadcast Notifikasi telah disebar ke seluruh HP Wali Murid & Mading Pengumuman!',
                    icon: 'success',
                    confirmButtonColor: '#0ea5e9',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href='/admin/siakad/lain-lain/broadcast';
                });
            </script></body>";
        }
    }

    public function deleteBroadcast() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $db = Database::connect('core');
                $stmt = $db->prepare("DELETE FROM pengumuman WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            }
        }
        exit;
    }
}
