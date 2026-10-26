<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadAkademikController {

    public function nilai() {
        $db = Database::connect('core');
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY is_active DESC, name DESC")->fetchAll();
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        
        $dbS = Database::connect('siakad');
        $mapels = $dbS->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $title = "Pilih Filter Nilai - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/nilai_filter.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function kalender() {
        $db = Database::connect('siakad');
        
        // Setup current month viewing
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        // Fetch agendas
        $agendas = $db->query("SELECT * FROM kalender_akademik ORDER BY tanggal_mulai ASC")->fetchAll();
        
        // Also get active tahun ajaran from core
        $dbC = Database::connect('core');
        $ta = $dbC->query("SELECT * FROM tahun_ajaran WHERE is_active=1")->fetch();

        $title = "Kalender Akademik - SIAKAD";
        $page_title = "Kalender Akademik";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/kalender.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function saveKalender() {
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? $_POST['id'] : null;
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $tipe = $_POST['tipe'];
        $keterangan = $_POST['keterangan'];
        $jam_mulai_kosong = isset($_POST['jam_mulai_kosong']) && $_POST['jam_mulai_kosong'] !== '' ? $_POST['jam_mulai_kosong'] : null;

        $dbC = Database::connect('core');
        $ta = $dbC->query("SELECT id FROM tahun_ajaran WHERE is_active=1")->fetch();
        $ta_id = $ta ? $ta['id'] : null;

        $db = Database::connect('siakad');
        if ($id) {
            $stmt = $db->prepare("UPDATE kalender_akademik SET tanggal_mulai=?, tanggal_selesai=?, tipe=?, keterangan=?, jam_mulai_kosong=? WHERE id=?");
            $stmt->execute([$tanggal_mulai, $tanggal_selesai, $tipe, $keterangan, $jam_mulai_kosong, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO kalender_akademik (tanggal_mulai, tanggal_selesai, tipe, keterangan, jam_mulai_kosong, tahun_ajaran_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal_mulai, $tanggal_selesai, $tipe, $keterangan, $jam_mulai_kosong, $ta_id]);
        }
        
        
        redirect('/admin/siakad/kalender?zns=Agenda berhasil disimpan');
    }

    public function deleteKalender($id) {
        $db = Database::connect('siakad');
        $stmt = $db->prepare("DELETE FROM kalender_akademik WHERE id=?");
        $stmt->execute([$id]);
        redirect('/admin/siakad/kalender?zns=Agenda berhasil dihapus');
    }

    public function inputNilai() {
        $tahunId = $_GET['tahun_ajaran_id'];
        $mapelId = $_GET['mapel_id'];
        $kelasId = $_GET['kelas_id'];

        $dbC = Database::connect('core');
        $dbS = Database::connect('siakad');

        // Ensure nilai table exists
        $dbS->exec("CREATE TABLE IF NOT EXISTS nilai (
            id INT AUTO_INCREMENT PRIMARY KEY,
            siswa_id INT,
            mapel_id INT,
            tahun_ajaran_id INT,
            nilai_tugas FLOAT DEFAULT 0,
            nilai_uts FLOAT DEFAULT 0,
            nilai_uas FLOAT DEFAULT 0,
            nilai_akhir FLOAT DEFAULT 0,
            keterangan TEXT
        )");

        $kelasInfo = $dbC->query("SELECT * FROM kelas WHERE id = $kelasId")->fetch();
        $mapelInfo = $dbS->query("SELECT * FROM mapel WHERE id = $mapelId")->fetch();
        $tahunInfo = $dbC->query("SELECT * FROM tahun_ajaran WHERE id = $tahunId")->fetch();

        // Get students in this class
        $students = $dbC->query("SELECT * FROM siswa WHERE kelas_id = $kelasId ORDER BY nama ASC")->fetchAll();

        // Get existing grades
        $grades = $dbS->query("SELECT * FROM nilai WHERE mapel_id = $mapelId AND tahun_ajaran_id = $tahunId")->fetchAll();
        $gradeMap = [];
        foreach($grades as $g) {
            $gradeMap[$g['siswa_id']] = $g;
        }

        $title = "Input Nilai - " . $mapelInfo['nama_mapel'];
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/nilai_input.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function saveNilai() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tahunId = $_POST['tahun_ajaran_id'];
            $mapelId = $_POST['mapel_id'];
            $kelasId = $_POST['kelas_id'];
            $nilaiData = $_POST['nilai'];

            $dbS = Database::connect('siakad');
            
            foreach($nilaiData as $siswaId => $data) {
                $tugas = $data['tugas'];
                $uts = $data['uts'];
                $uas = $data['uas'];
                $akhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);

                // Check if exists
                $stmt = $dbS->prepare("SELECT id FROM nilai WHERE siswa_id = ? AND mapel_id = ? AND tahun_ajaran_id = ?");
                $stmt->execute([$siswaId, $mapelId, $tahunId]);
                $exists = $stmt->fetch();

                if ($exists) {
                    $stmt = $dbS->prepare("UPDATE nilai SET nilai_tugas = ?, nilai_uts = ?, nilai_uas = ?, nilai_akhir = ? WHERE id = ?");
                    $stmt->execute([$tugas, $uts, $uas, $akhir, $exists['id']]);
                } else {
                    $stmt = $dbS->prepare("INSERT INTO nilai (siswa_id, mapel_id, tahun_ajaran_id, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$siswaId, $mapelId, $tahunId, $tugas, $uts, $uas, $akhir]);
                }
            }
        }
        redirect("/siakad/nilai/input?tahun_ajaran_id=$tahunId&mapel_id=$mapelId&kelas_id=$kelasId");
    }


    public function templateNilaiFilter() {
        $db = Database::connect('core');
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY is_active DESC, name DESC")->fetchAll();
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        
        $dbS = Database::connect('siakad');
        $mapels = $dbS->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $title = "Download Template Nilai - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/template_nilai_filter.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function cetakTemplateNilai() {
        $tahunId = $_GET['tahun_ajaran_id'];
        $mapelId = $_GET['mapel_id'];
        $kelasId = $_GET['kelas_id'];

        $dbC = Database::connect('core');
        $dbS = Database::connect('siakad');

        // Data for the inner filter
        $years = $dbC->query("SELECT * FROM tahun_ajaran ORDER BY is_active DESC, name DESC")->fetchAll();
        $kelas = $dbC->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mapels = $dbS->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();

        $kelasInfo = $dbC->query("SELECT * FROM kelas WHERE id = $kelasId")->fetch();
        $mapelInfo = $dbS->query("SELECT * FROM mapel WHERE id = $mapelId")->fetch();
        $tahunInfo = $dbC->query("SELECT * FROM tahun_ajaran WHERE id = $tahunId")->fetch();
        $inst = $dbC->query("SELECT * FROM institusi LIMIT 1")->fetch();

        // Get students in this class
        $students = $dbC->query("SELECT * FROM siswa WHERE kelas_id = $kelasId ORDER BY nama ASC")->fetchAll();

        include __DIR__ . '/../../resources/views/siakad/template_nilai_print.php';
    }


    public function raport() {
        $dbC = Database::connect('core');
        $years = $dbC->query("SELECT * FROM tahun_ajaran ORDER BY is_active DESC, name DESC")->fetchAll();
        $kelas = $dbC->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $selectedKelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : null;
        $selectedTahun = isset($_GET['tahun_ajaran_id']) ? $_GET['tahun_ajaran_id'] : (isset($years[0]['id']) ? $years[0]['id'] : null);

        $siswas = [];
        if ($selectedKelas) {
            $siswas = $dbC->query("SELECT * FROM siswa WHERE kelas_id = $selectedKelas ORDER BY nama ASC")->fetchAll();
        }

        $title = "Laporan Raport - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/raport_list.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function cetakRaport($siswaId, $tahunId) {
        $dbC = Database::connect('core');
        $dbS = Database::connect('siakad');

        $siswa = $dbC->query("SELECT s.*, k.nama_kelas FROM siswa s JOIN kelas k ON s.kelas_id = k.id WHERE s.id = $siswaId")->fetch();
        $tahun = $dbC->query("SELECT * FROM tahun_ajaran WHERE id = $tahunId")->fetch();
        $inst = $dbC->query("SELECT * FROM institusi LIMIT 1")->fetch();
        
        $nilai = $dbS->query("SELECT n.*, m.nama_mapel, m.kode_mapel 
                             FROM nilai n 
                             JOIN mapel m ON n.mapel_id = m.id 
                             WHERE n.siswa_id = $siswaId AND n.tahun_ajaran_id = $tahunId")->fetchAll();

        include __DIR__ . '/../../resources/views/siakad/raport_print.php';
    }

}
