<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadEkstraController {

    private function checkAuth() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        // Fetch all teachers for selection
        $gurus = $db_core->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();

        // Fetch all extracurriculars
        $ekstras = $db_core->query("SELECT gt.id, gt.keterangan as nama_ekstra, gt.guru_id, g.nama as pembina_nama, gt.tahun_ajaran 
                                    FROM guru_tugas gt 
                                    JOIN guru g ON gt.guru_id = g.id 
                                    WHERE gt.tugas = 'Pembina Ekstrakurikuler' AND gt.tahun_ajaran = '$active_year_name' 
                                    ORDER BY gt.id DESC")->fetchAll();

        // Count members for each
        foreach ($ekstras as &$e) {
            $stmt = $db_siakad->prepare("SELECT COUNT(*) as total FROM ekstra_anggota WHERE nama_ekstra = ? AND tahun_ajaran = ?");
            $stmt->execute([$e['nama_ekstra'], $active_year_name]);
            $e['total_anggota'] = $stmt->fetch()['total'] ?? 0;
        }

        $title = "Data Ekstrakurikuler - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/ekstra.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function save() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db_core = Database::connect('core');
            $db_siakad = Database::connect('siakad');

            $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;
            $nama_ekstra = trim($_POST['nama_ekstra']);
            $guru_id = intval($_POST['guru_id']);

            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

            if ($id) {
                // Edit
                // 1. Fetch old name to sync changes
                $old = $db_core->query("SELECT keterangan, guru_id FROM guru_tugas WHERE id = $id")->fetch();
                if ($old) {
                    $old_name = $old['keterangan'];
                    $old_guru = $old['guru_id'];

                    // Update core tugas
                    $stmt = $db_core->prepare("UPDATE guru_tugas SET keterangan = ?, guru_id = ? WHERE id = ?");
                    $stmt->execute([$nama_ekstra, $guru_id, $id]);

                    // Update siakad program
                    $stmt = $db_siakad->prepare("UPDATE ekstra_program SET nama_ekstra = ?, guru_id = ? WHERE nama_ekstra = ? AND guru_id = ? AND tahun_ajaran = ?");
                    $stmt->execute([$nama_ekstra, $guru_id, $old_name, $old_guru, $active_year_name]);

                    // Update related tables if name changed
                    if ($old_name !== $nama_ekstra) {
                        $stmt = $db_siakad->prepare("UPDATE ekstra_anggota SET nama_ekstra = ? WHERE nama_ekstra = ? AND tahun_ajaran = ?");
                        $stmt->execute([$nama_ekstra, $old_name, $active_year_name]);

                        $stmt = $db_siakad->prepare("UPDATE ekstra_jurnal SET nama_ekstra = ? WHERE nama_ekstra = ? AND tahun_ajaran = ?");
                        $stmt->execute([$nama_ekstra, $old_name, $active_year_name]);
                    }
                }
            } else {
                // Add new
                $stmt = $db_core->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, 'Pembina Ekstrakurikuler', ?, ?)");
                $stmt->execute([$guru_id, $nama_ekstra, $active_year_name]);

                // Create default program entry in siakad_db
                $stmt = $db_siakad->prepare("SELECT id FROM ekstra_program WHERE nama_ekstra = ? AND guru_id = ? AND tahun_ajaran = ?");
                $stmt->execute([$nama_ekstra, $guru_id, $active_year_name]);
                if (!$stmt->fetch()) {
                    $ins = $db_siakad->prepare("INSERT INTO ekstra_program (nama_ekstra, guru_id, tahun_ajaran, semester, jadwal_hari, jadwal_waktu, target_pencapaian) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $ins->execute([$nama_ekstra, $guru_id, $active_year_name, 'Ganjil', 'Sabtu', '14.00 - 16.00 WIB', '']);
                }
            }
        }
        redirect('/siakad/ekstra');
        exit;
    }

    public function delete($id) {
        $this->checkAuth();
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

        $task = $db_core->query("SELECT keterangan, guru_id FROM guru_tugas WHERE id = " . intval($id))->fetch();
        if ($task) {
            $nama_ekstra = $task['keterangan'];
            $guru_id = $task['guru_id'];

            // Delete task
            $db_core->exec("DELETE FROM guru_tugas WHERE id = " . intval($id));

            // Delete default program if exists
            $stmt = $db_siakad->prepare("DELETE FROM ekstra_program WHERE nama_ekstra = ? AND guru_id = ? AND tahun_ajaran = ?");
            $stmt->execute([$nama_ekstra, $guru_id, $active_year_name]);
        }

        redirect('/siakad/ekstra');
        exit;
    }
}
