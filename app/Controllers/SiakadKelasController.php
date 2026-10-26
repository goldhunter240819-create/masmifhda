<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadKelasController {

    public function kelas() {
        $db = Database::connect('core');
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $kelas = $db->query("SELECT * FROM kelas ORDER BY tingkat ASC, nama_kelas ASC")->fetchAll();
        
        $stmt_wali = $db->prepare("SELECT gt.keterangan as kelas_nama, g.id as guru_id, g.nama as guru_nama, g.foto as guru_foto FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tugas = 'Wali Kelas' AND gt.tahun_ajaran = ?");
        $stmt_wali->execute([$active_year_name]);
        $walis = $stmt_wali->fetchAll();
        
        $wali_map = [];
        foreach($walis as $w) {
            $wali_map[$w['kelas_nama']] = $w;
        }

        $gurus = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();

        $title = "Manajemen Kelas - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/kelas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function assignWaliKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kelas_nama = $_POST['nama_kelas'];
            $guru_id = $_POST['guru_id'];
            
            $db = Database::connect('core');
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
            $stmt->execute([$kelas_nama, $active_year_name]);
            
            if (!empty($guru_id)) {
                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$guru_id, $kelas_nama, $active_year_name]);
            }
        }
        redirect('/siakad/kelas');
    }

    public function addKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tingkat = $_POST['tingkat'];
            $name = $_POST['nama_kelas'];
            $wali_kelas_id = isset($_POST['wali_kelas_id']) ? $_POST['wali_kelas_id'] : '';

            $db = Database::connect('core');
            $stmt = $db->prepare("INSERT INTO kelas (tingkat, nama_kelas) VALUES (?, ?)");
            $stmt->execute([$tingkat, $name]);

            if (!empty($wali_kelas_id)) {
                $active_year = \App\Core\AcademicYear::current();
                $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
                $kelas_nama_format = "Kelas " . $name;

                $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
                $stmt->execute([$kelas_nama_format, $active_year_name]);

                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$wali_kelas_id, $kelas_nama_format, $active_year_name]);
            }
        }
        redirect('/siakad/kelas');
    }


    public function updateKelas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $tingkat = $_POST['tingkat'];
            $name = $_POST['nama_kelas'];
            $wali_kelas_id = isset($_POST['wali_kelas_id']) ? $_POST['wali_kelas_id'] : '';

            $db = Database::connect('core');
            $stmt = $db->prepare("UPDATE kelas SET tingkat = ?, nama_kelas = ? WHERE id = ?");
            $stmt->execute([$tingkat, $name, $id]);

            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            $kelas_nama_format = "Kelas " . $name;

            $stmt = $db->prepare("DELETE FROM guru_tugas WHERE tugas = 'Wali Kelas' AND keterangan = ? AND tahun_ajaran = ?");
            $stmt->execute([$kelas_nama_format, $active_year_name]);

            if (!empty($wali_kelas_id)) {
                $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Wali Kelas', ?, ?)");
                $stmt->execute([$wali_kelas_id, $kelas_nama_format, $active_year_name]);
            }
        }
        redirect('/siakad/kelas');
    }


    public function deleteKelas($id) {
        $db = Database::connect('core');
        $db->exec("DELETE FROM kelas WHERE id = $id");
        redirect('/siakad/kelas');
    }

}
