<?php
namespace App\Controllers;

use App\Core\Database;

class OsimController {

    private $db;
    private $is_admin;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            redirect('/login');
        }

        $this->is_admin = isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99]);
        if (!$this->is_admin) {
            die("Akses ditolak. Hanya untuk Administrator.");
        }

        try {
            $this->db = Database::connect('core');
            $this->initTables();
        } catch (\Exception $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }

    private function initTables() {
        // Create OSIM Tables if they don't exist
        $sql1 = "CREATE TABLE IF NOT EXISTS osim_pengurus (
            id INT AUTO_INCREMENT PRIMARY KEY,
            siswa_id INT NOT NULL,
            jabatan VARCHAR(100) NOT NULL,
            periode VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $sql2 = "CREATE TABLE IF NOT EXISTS osim_proker (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_kegiatan VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            tanggal_pelaksanaan DATE,
            status ENUM('Draft', 'Persiapan', 'Berjalan', 'Selesai') DEFAULT 'Draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $sql3 = "CREATE TABLE IF NOT EXISTS osim_proposal (
            id INT AUTO_INCREMENT PRIMARY KEY,
            judul VARCHAR(255) NOT NULL,
            latar_belakang TEXT,
            tujuan TEXT,
            waktu_tempat TEXT,
            panitia_json LONGTEXT,
            jadwal_json LONGTEXT,
            rab_json LONGTEXT,
            status ENUM('draft', 'diajukan', 'disetujui', 'revisi', 'ditolak') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $sql4 = "CREATE TABLE IF NOT EXISTS osim_settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT
        )";

        $sql5 = "CREATE TABLE IF NOT EXISTS osim_kandidat (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nomor_urut INT NOT NULL,
            nama_ketua VARCHAR(255) NOT NULL,
            nama_wakil VARCHAR(255) NOT NULL,
            visi TEXT,
            misi TEXT,
            foto VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $sql6 = "CREATE TABLE IF NOT EXISTS osim_suara (
            id INT AUTO_INCREMENT PRIMARY KEY,
            kandidat_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $sql7 = "CREATE TABLE IF NOT EXISTS osim_pemilih_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            siswa_id INT NOT NULL,
            waktu DATETIME NOT NULL,
            terbaca INT DEFAULT 0
        )";

        $this->db->exec($sql1);
        $this->db->exec($sql2);
        $this->db->exec($sql3);
        $this->db->exec($sql4);
        $this->db->exec($sql5);
        $this->db->exec($sql6);
        $this->db->exec($sql7);
    }

    // --- HELPER UNTUK MENGAMBIL SETTING ---
    private function getSettings() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM osim_settings");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    // --- MODUL PENGATURAN ---
    public function pengaturan() {
        $settings = $this->getSettings();
        $page_title = "Pengaturan E-Proposal OSIM";
        include __DIR__ . '/../../resources/views/osim/pengaturan.php';
    }

    public function simpanPengaturan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $stmt = $this->db->prepare("INSERT INTO osim_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            foreach ($_POST as $key => $value) {
                $stmt->execute([$key, $value]);
            }
        }
        redirect('/osim/pengaturan?msg=success');
    }

    public function dashboard() {
        $stats = [
            'total_pengurus' => 0,
            'proker_aktif' => 0,
            'proker_selesai' => 0
        ];

        try {
            $stats['total_pengurus'] = $this->db->query("SELECT COUNT(*) FROM osim_pengurus")->fetchColumn();
            $stats['proker_aktif'] = $this->db->query("SELECT COUNT(*) FROM osim_proker WHERE status IN ('Persiapan', 'Berjalan')")->fetchColumn();
            $stats['proker_selesai'] = $this->db->query("SELECT COUNT(*) FROM osim_proker WHERE status = 'Selesai'")->fetchColumn();
        } catch (\Exception $e) {
            // ignore
        }

        $page_title = "Dashboard OSIM";
        include __DIR__ . '/../../resources/views/osim/dashboard.php';
    }

    // --- MODUL KEPENGURUSAN ---
    public function pengurus() {
        $pengurus = $this->db->query("SELECT o.*, s.nama, s.nis, s.kelas_id, k.nama_kelas 
                                      FROM osim_pengurus o 
                                      JOIN siswa s ON o.siswa_id = s.id 
                                      LEFT JOIN kelas k ON s.kelas_id = k.id 
                                      ORDER BY o.created_at DESC")->fetchAll();
        $siswa_list = $this->db->query("SELECT id, nama, nis FROM siswa WHERE status = 'Aktif' ORDER BY nama ASC")->fetchAll();
        $page_title = "Data Pengurus OSIM";
        include __DIR__ . '/../../resources/views/osim/pengurus.php';
    }

    public function simpanPengurus() {
        $id = $_POST['id'] ?? '';
        $siswa_id = $_POST['siswa_id'] ?? '';
        $jabatan = $_POST['jabatan'] ?? '';
        $periode = $_POST['periode'] ?? '';

        if ($id) {
            $stmt = $this->db->prepare("UPDATE osim_pengurus SET siswa_id = ?, jabatan = ?, periode = ? WHERE id = ?");
            $stmt->execute([$siswa_id, $jabatan, $periode, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO osim_pengurus (siswa_id, jabatan, periode) VALUES (?, ?, ?)");
            $stmt->execute([$siswa_id, $jabatan, $periode]);
        }
        redirect('/osim/pengurus');
    }

    public function hapusPengurus() {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM osim_pengurus WHERE id = ?");
            $stmt->execute([$id]);
        }
        redirect('/osim/pengurus');
    }

    // --- MODUL PROGRAM KERJA (PROKER) ---
    public function proker() {
        $proker = $this->db->query("SELECT * FROM osim_proker ORDER BY created_at DESC")->fetchAll();
        $page_title = "Program Kerja OSIM";
        include __DIR__ . '/../../resources/views/osim/proker.php';
    }

    public function simpanProker() {
        $id = $_POST['id'] ?? '';
        $nama_kegiatan = $_POST['nama_kegiatan'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';
        $tanggal = $_POST['tanggal_pelaksanaan'] ?? '';
        $status = $_POST['status'] ?? 'Draft';

        if ($id) {
            $stmt = $this->db->prepare("UPDATE osim_proker SET nama_kegiatan=?, deskripsi=?, tanggal_pelaksanaan=?, status=? WHERE id=?");
            $stmt->execute([$nama_kegiatan, $deskripsi, $tanggal, $status, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO osim_proker (nama_kegiatan, deskripsi, tanggal_pelaksanaan, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_kegiatan, $deskripsi, $tanggal, $status]);
        }
        redirect('/osim/proker');
    }

    public function hapusProker() {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM osim_proker WHERE id = ?");
            $stmt->execute([$id]);
        }
        redirect('/osim/proker');
    }

    // --- MODUL E-PROPOSAL ---
    public function proposal() {
        $proposal = $this->db->query("SELECT * FROM osim_proposal ORDER BY created_at DESC")->fetchAll();
        $page_title = "E-Proposal OSIM";
        include __DIR__ . '/../../resources/views/osim/proposal.php';
    }

    public function proposalTemplates() {
        $page_title = "Pilih Template Proposal";
        include __DIR__ . '/../../resources/views/osim/proposal_templates.php';
    }

    public function proposalCreate() {
        $type = $_GET['type'] ?? 'bebas';
        $settings = $this->getSettings(); // Ambil settings untuk live preview
        
        // Ambil data pengurus untuk dropdown pilihan panitia
        $pengurus = $this->db->query("SELECT o.id, o.jabatan, s.nama FROM osim_pengurus o JOIN siswa s ON o.siswa_id = s.id ORDER BY s.nama ASC")->fetchAll();
        
        $page_title = "Editor E-Proposal OSIM";
        include __DIR__ . '/../../resources/views/osim/proposal_create.php';
    }

    public function simpanProposal() {
        $id = $_POST['id'] ?? '';
        $judul = $_POST['judul'] ?? 'Proposal Tanpa Judul';
        $latar_belakang = $_POST['latar_belakang'] ?? ''; // Nanti kalau ada tab Latar Belakang
        $tujuan = $_POST['tujuan'] ?? ''; // Nanti kalau ada tab Tujuan
        $waktu_tempat = $_POST['waktu_tempat'] ?? '';
        
        // Data Dinamis dari Editor
        $panitia_json = $_POST['panitia_json'] ?? '[]';
        $jadwal_json = $_POST['jadwal_json'] ?? '[]';
        $rab_json = $_POST['rab_json'] ?? '[]';
        
        $status = $_POST['status'] ?? 'draft';

        if ($id) {
            $stmt = $this->db->prepare("UPDATE osim_proposal SET judul=?, latar_belakang=?, tujuan=?, waktu_tempat=?, panitia_json=?, jadwal_json=?, rab_json=?, status=? WHERE id=?");
            $stmt->execute([$judul, $latar_belakang, $tujuan, $waktu_tempat, $panitia_json, $jadwal_json, $rab_json, $status, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Draft berhasil diperbarui', 'id' => $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO osim_proposal (judul, latar_belakang, tujuan, waktu_tempat, panitia_json, jadwal_json, rab_json, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $latar_belakang, $tujuan, $waktu_tempat, $panitia_json, $jadwal_json, $rab_json, $status]);
            echo json_encode(['status' => 'success', 'message' => 'Draft baru berhasil disimpan', 'id' => $this->db->lastInsertId()]);
        }
        exit;
    }

    public function hapusProposal() {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM osim_proposal WHERE id = ?");
            $stmt->execute([$id]);
        }
        redirect('/osim/proposal');
    }

    // --- MODUL E-VOTING ---
    public function evotingDashboard() {
        $settings = $this->getSettings();
        $status_evoting = $settings['evoting_status'] ?? 'closed';
        $status_kotak = $settings['evoting_kotak'] ?? 'closed';

        $total_suara = $this->db->query("SELECT COUNT(*) FROM osim_suara")->fetchColumn();
        $total_pemilih = $this->db->query("SELECT COUNT(*) FROM osim_pemilih_log")->fetchColumn();
        $total_kandidat = $this->db->query("SELECT COUNT(*) FROM osim_kandidat")->fetchColumn();

        $kandidat = $this->db->query("
            SELECT k.*, (SELECT COUNT(*) FROM osim_suara s WHERE s.kandidat_id = k.id) as perolehan 
            FROM osim_kandidat k 
            WHERE k.status = 'aktif'
            ORDER BY k.nomor_urut ASC
        ")->fetchAll();

        $page_title = "Dashboard E-Voting OSIM";
        include __DIR__ . '/../../resources/views/osim/evoting_dashboard.php';
    }

    public function evotingSetStatus() {
        $key = $_GET['key'] ?? '';
        $val = $_GET['val'] ?? '';
        if (in_array($key, ['evoting_status', 'evoting_kotak']) && in_array($val, ['open', 'closed'])) {
            $stmt = $this->db->prepare("INSERT INTO osim_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$key, $val]);
        }
        redirect('/osim/evoting');
    }

    public function evotingReset() {
        $this->db->exec("TRUNCATE TABLE osim_suara");
        $this->db->exec("TRUNCATE TABLE osim_pemilih_log");
        redirect('/osim/evoting?msg=reset_success');
    }

    public function evotingKandidat() {
        $kandidat = $this->db->query("SELECT * FROM osim_kandidat ORDER BY nomor_urut ASC")->fetchAll();
        $page_title = "Kandidat E-Voting OSIM";
        include __DIR__ . '/../../resources/views/osim/evoting_kandidat.php';
    }

    public function simpanKandidat() {
        $id = $_POST['id'] ?? '';
        $nomor_urut = $_POST['nomor_urut'] ?? 0;
        $nama_ketua = $_POST['nama_ketua'] ?? '';
        $nama_wakil = $_POST['nama_wakil'] ?? '';
        $visi = $_POST['visi'] ?? '';
        $misi = $_POST['misi'] ?? '';
        
        $foto_lama = $_POST['foto_lama'] ?? '';
        $foto = $foto_lama;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_dir = __DIR__ . '/../../public/uploads/osim/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $filename = 'kandidat_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $filename)) {
                $foto = $filename;
            }
        }

        if ($id) {
            $stmt = $this->db->prepare("UPDATE osim_kandidat SET nomor_urut=?, nama_ketua=?, nama_wakil=?, visi=?, misi=?, foto=? WHERE id=?");
            $stmt->execute([$nomor_urut, $nama_ketua, $nama_wakil, $visi, $misi, $foto, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO osim_kandidat (nomor_urut, nama_ketua, nama_wakil, visi, misi, foto) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nomor_urut, $nama_ketua, $nama_wakil, $visi, $misi, $foto]);
        }
        redirect('/osim/evoting/kandidat');
    }

    public function toggleKandidat() {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $this->db->prepare("UPDATE osim_kandidat SET status = IF(status = 'aktif', 'nonaktif', 'aktif') WHERE id = ?");
            $stmt->execute([$id]);
        }
        redirect('/osim/evoting/kandidat');
    }

    public function hapusKandidat() {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM osim_kandidat WHERE id = ?");
            $stmt->execute([$id]);
        }
        redirect('/osim/evoting/kandidat');
    }
}
