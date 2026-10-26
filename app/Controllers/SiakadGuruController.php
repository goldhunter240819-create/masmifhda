<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadGuruController {

    public function guru() {
        $db = Database::connect('core');
        $gurus = $db->query("SELECT g.*, u.username, u.plain_password FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll();
        foreach ($gurus as &$g) {
            $g['qr_token'] = "TOKEN:" . $g['user_id'] . ":" . substr(md5($g['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
        }
        unset($g);
        
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        $title = "Manajemen Data Guru - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function addGuru() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nip = $_POST['nip'];
            $nik = $_POST['nik'];
            $nama = $_POST['nama'];
            $tempat_lahir = $_POST['tempat_lahir'];
            $tanggal_lahir = $_POST['tanggal_lahir'];
            $jenis_kelamin = $_POST['jenis_kelamin'];
            $alamat = $_POST['alamat'];
            $no_hp = $_POST['no_hp'];
            $email = $_POST['email'];
            $jabatan = $_POST['jabatan'];
            $status_kepegawaian = $_POST['status_kepegawaian'] ?? 'Honorer';
            $is_kamad = isset($_POST['is_kamad']) ? 1 : 0;
            
            $rt = $_POST['rt'] ?? '';
            $rw = $_POST['rw'] ?? '';
            $desa = $_POST['desa'] ?? '';
            $kecamatan = $_POST['kecamatan'] ?? '';
            $kota = $_POST['kota'] ?? '';
            $provinsi = $_POST['provinsi'] ?? '';
            $kode_pos = $_POST['kode_pos'] ?? '';
            $npk = $_POST['npk'] ?? '';
            $npwp = $_POST['npwp'] ?? '';
            $no_rekening = $_POST['no_rekening'] ?? '';
            $nrg = $_POST['nrg'] ?? '';
            $no_peserta_sertifikasi = $_POST['no_peserta_sertifikasi'] ?? '';
            $no_sertifikat = $_POST['no_sertifikat'] ?? '';
            $tgl_sertifikat = !empty($_POST['tgl_sertifikat']) ? $_POST['tgl_sertifikat'] : null;
            $jenjang_sertifikat = $_POST['jenjang_sertifikat'] ?? '';
            $mapel_sertifikat = $_POST['mapel_sertifikat'] ?? '';
            
            if (empty($nik)) {
                die("Gagal: NIK wajib diisi untuk dijadikan Username!");
            }
            $user_baru = $nik;
            
            $pass_baru = $nik;
            // Handle Foto
            $foto_name = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "guru_" . time() . "." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/guru/" . $foto_name);
            }

            $db = Database::connect('core');
            
            try {
                $db->beginTransaction();
                
                // 1. Create User in core_db with Random Credentials
                $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, plain_password) VALUES (?, ?, 2, ?)");
                $stmt->execute([$user_baru, password_hash($pass_baru, PASSWORD_BCRYPT), $pass_baru]);
                $userId = $db->lastInsertId();
                
                // 2. Create Guru Profile with all fields
                $stmt = $db->prepare("INSERT INTO guru (user_id, nama, nip, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_hp, email, jabatan, foto, is_kamad, status_kepegawaian, rt, rw, desa, kecamatan, kota, provinsi, kode_pos, npk, npwp, no_rekening, nrg, no_peserta_sertifikasi, no_sertifikat, tgl_sertifikat, jenjang_sertifikat, mapel_sertifikat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $nama, $nip, $nik, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $email, $jabatan, $foto_name, $is_kamad, $status_kepegawaian, $rt, $rw, $desa, $kecamatan, $kota, $provinsi, $kode_pos, $npk, $npwp, $no_rekening, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat]);
                
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                die("Gagal menambahkan guru: " . $e->getMessage());
            }
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
    }

    public function updateFotoGuru() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $db = Database::connect('core');
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $old = $db->query("SELECT foto FROM guru WHERE id = " . intval($id))->fetch();
                    if ($old && $old['foto']) @unlink(__DIR__ . "/../../public/uploads/guru/" . $old['foto']);
                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto_name = "guru_" . time() . "." . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/guru/" . $foto_name);
                    $stmt = $db->prepare("UPDATE guru SET foto = ? WHERE id = ?");
                    $stmt->execute([$foto_name, $id]);
                    echo json_encode(['success' => true, 'foto' => $foto_name]);
                    exit;
                }
                echo json_encode(['success' => false, 'message' => 'Upload gagal']);
            } catch (\Exception $e) { echo json_encode(['success' => false, 'message' => $e->getMessage()]); }
            exit;
        }
    }


    public function updateGuru() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $nik = $_POST['nik'];
            $nip = $_POST['nip'] ?? '';
            $nama = $_POST['nama'];
            $tempat_lahir = $_POST['tempat_lahir'];
            $tanggal_lahir = $_POST['tanggal_lahir'];
            $jenis_kelamin = $_POST['jenis_kelamin'];
            $alamat = $_POST['alamat'];
            $no_hp = $_POST['no_hp'];
            $email = $_POST['email'];
            $jabatan = $_POST['jabatan'];
            $status_kepegawaian = $_POST['status_kepegawaian'] ?? 'Honorer';
            $is_kamad = isset($_POST['is_kamad']) ? 1 : 0;
            
            $rt = $_POST['rt'] ?? '';
            $rw = $_POST['rw'] ?? '';
            $desa = $_POST['desa'] ?? '';
            $kecamatan = $_POST['kecamatan'] ?? '';
            $kota = $_POST['kota'] ?? '';
            $provinsi = $_POST['provinsi'] ?? '';
            $kode_pos = $_POST['kode_pos'] ?? '';
            $npk = $_POST['npk'] ?? '';
            $npwp = $_POST['npwp'] ?? '';
            $no_rekening = $_POST['no_rekening'] ?? '';
            $nrg = $_POST['nrg'] ?? '';
            $no_peserta_sertifikasi = $_POST['no_peserta_sertifikasi'] ?? '';
            $no_sertifikat = $_POST['no_sertifikat'] ?? '';
            $tgl_sertifikat = !empty($_POST['tgl_sertifikat']) ? $_POST['tgl_sertifikat'] : null;
            $jenjang_sertifikat = $_POST['jenjang_sertifikat'] ?? '';
            $mapel_sertifikat = $_POST['mapel_sertifikat'] ?? '';
            
            $db = Database::connect('core');
            $guru = $db->query("SELECT * FROM guru WHERE id = $id")->fetch();
            
            // Handle Foto
            $foto_name = $guru['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "guru_" . time() . "." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/guru/" . $foto_name);
            }

            $stmt = $db->prepare("UPDATE guru SET nama = ?, nik = ?, nip = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, email = ?, jabatan = ?, foto = ?, is_kamad = ?, status_kepegawaian = ?, rt = ?, rw = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kode_pos = ?, npk = ?, npwp = ?, no_rekening = ?, nrg = ?, no_peserta_sertifikasi = ?, no_sertifikat = ?, tgl_sertifikat = ?, jenjang_sertifikat = ?, mapel_sertifikat = ? WHERE id = ?");
            $stmt->execute([$nama, $nik, $nip, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $email, $jabatan, $foto_name, $is_kamad, $status_kepegawaian, $rt, $rw, $desa, $kecamatan, $kota, $provinsi, $kode_pos, $npk, $npwp, $no_rekening, $nrg, $no_peserta_sertifikasi, $no_sertifikat, $tgl_sertifikat, $jenjang_sertifikat, $mapel_sertifikat, $id]);

            if (!empty($nik) && $guru['user_id']) {
                $stmtUser = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmtUser->execute([trim($nik), $guru['user_id']]);
            }
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
    }


    public function deleteGuru($id) {
        $db = Database::connect('core');
        $guru = $db->query("SELECT * FROM guru WHERE id = $id")->fetch();
        if ($guru) {
            $db->beginTransaction();
            try {
                $db->exec("DELETE FROM users WHERE id = " . $guru['user_id']);
                $db->exec("DELETE FROM guru WHERE id = $id");
                $db->commit();
            } catch (\Exception $e) { $db->rollBack(); }
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
    }

    public function deleteGuruBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect('core');
            $ids = array_map('intval', $_POST['ids']);
            $ids_str = implode(',', $ids);
            
            $gurus = $db->query("SELECT user_id, foto FROM guru WHERE id IN ($ids_str)")->fetchAll();
            $userIds = [];
            foreach ($gurus as $g) {
                if ($g['user_id']) $userIds[] = $g['user_id'];
                if ($g['foto'] && file_exists(__DIR__ . '/../../public/uploads/guru/' . $g['foto'])) {
                    unlink(__DIR__ . '/../../public/uploads/guru/' . $g['foto']);
                }
            }
            
            try {
                $db->beginTransaction();
                $db->exec("DELETE FROM guru WHERE id IN ($ids_str)");
                if (!empty($userIds)) {
                    $uIdsStr = implode(',', $userIds);
                    $db->exec("DELETE FROM users WHERE id IN ($uIdsStr)");
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
            }
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
        exit;
    }

    public function cetakKartuMassal() {
        $db = Database::connect('core');
        // Ambil SEMUA data guru beserta username & password
        $gurus = $db->query("SELECT g.*, u.username, u.plain_password FROM guru g JOIN users u ON g.user_id = u.id ORDER BY g.nama ASC")->fetchAll();
        foreach ($gurus as &$g) {
            $g['qr_token'] = "TOKEN:" . $g['user_id'] . ":" . substr(md5($g['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
        }
        unset($g);
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_cetak_kartu_view.php';
        echo ob_get_clean();
        exit;
    }

    public function cetakKartuBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect('core');
            $ids = array_map('intval', $_POST['ids']);
            $ids_str = implode(',', $ids);
            
            // Ambil data guru beserta username & password
            $gurus = $db->query("SELECT g.*, u.username, u.plain_password FROM guru g JOIN users u ON g.user_id = u.id WHERE g.id IN ($ids_str) ORDER BY g.nama ASC")->fetchAll();
            foreach ($gurus as &$g) {
                $g['qr_token'] = "TOKEN:" . $g['user_id'] . ":" . substr(md5($g['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
            }
            unset($g);
            $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
            
            ob_start();
            include __DIR__ . '/../../resources/views/siakad/guru_cetak_kartu_view.php';
            echo ob_get_clean();
            exit;
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
        exit;
    }

    public function importGuru() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            $db = Database::connect('core');
            
            $success = 0;
            $failed = 0;
            
            foreach ($lines as $line) {
                $cols = explode("\t", trim($line));
                if (count($cols) < 2) continue; // Minimal NIK dan Nama
                
                $nik = trim($cols[0]);
                $nama = trim($cols[1]);
                $jabatan = isset($cols[2]) ? trim($cols[2]) : 'Guru';
                $email = isset($cols[3]) ? trim($cols[3]) : '';
                
                if (empty($nik) || empty($nama)) { $failed++; continue; }

                try {
                    $db->beginTransaction();
                    $check = $db->prepare("SELECT id FROM users WHERE username = ?");
                    $check->execute([$nik]);
                    if ($check->fetch()) { $db->rollBack(); $failed++; continue; }

                    $user_baru = $nik;
                    $pass_baru = $nik;

                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, plain_password) VALUES (?, ?, 2, ?)");
                    $stmt->execute([$user_baru, password_hash($pass_baru, PASSWORD_BCRYPT), $pass_baru]);
                    $userId = $db->lastInsertId();
                    
                    $stmt = $db->prepare("INSERT INTO guru (user_id, nama, nik, jabatan, email) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $nama, $nik, $jabatan, $email]);
                    $db->commit();
                    $success++;
                } catch (\Exception $e) { $db->rollBack(); $failed++; }
            }
        }
        \App\Services\DataSyncService::runGuruSync();
        redirect('/siakad/guru');
    }


    public function exportGuru() {
        $db = Database::connect('core');
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'public';
        
        $gurus = $db->query("SELECT * FROM guru ORDER BY nama ASC")->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak Data Guru - <?php echo $inst['nama_sekolah']; ?></title>
            <style>
                body { font-family: 'Times New Roman', serif; padding: 40px; color: #000; }
                .kop-surat { display: flex; align-items: center; border-bottom: 4px double #000; padding-bottom: 15px; margin-bottom: 30px; }
                .kop-surat img { width: 90px; height: 90px; object-fit: contain; }
                .kop-text { text-align: center; flex: 1; }
                .kop-text h1 { margin: 0; font-size: 1.6rem; text-transform: uppercase; }
                .kop-text h2 { margin: 5px 0; font-size: 1.2rem; }
                .kop-text p { margin: 2px 0; font-size: 0.85rem; font-style: italic; }
                
                h3 { text-align: center; text-transform: uppercase; margin-bottom: 25px; }
                
                table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
                th, td { border: 1px solid #000; padding: 8px 10px; }
                th { background: #f0f0f0; }
                
                .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
                .ttd { text-align: center; width: 250px; }
                
                @media print {
                    @page { margin: 2cm; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body onload="window.print()">
            <div class="kop-surat">
                <?php if($inst['logo']): ?>
                    <img src="/public/uploads/logo/<?php echo $inst['logo']; ?>">
                <?php endif; ?>
                <div class="kop-text">
                    <h1>YAYASAN PENDIDIKAN <?php echo $inst['nama']; ?></h1>
                    <h2>MADRASAH IBTIDAIYAH (MI) <?php echo $inst['nama']; ?></h2>
                    <p><?php echo $inst['alamat']; ?>, <?php echo $inst['kecamatan']; ?>, <?php echo $inst['kota']; ?></p>
                    <p>Website: <?php echo $inst['website']; ?> | Email: <?php echo $inst['email']; ?></p>
                </div>
            </div>

            <h3>DAFTAR DATA GURU & TENAGA KEPENDIDIKAN</h3>
            <p style="font-size: 0.8rem; margin-bottom: 10px;">Jenis Laporan: <b><?php echo $mode == 'full' ? 'Lengkap (Internal)' : 'Standar (Publik)'; ?></b></p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>NIP / Username</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <?php if($mode == 'full'): ?>
                            <th>NIK</th>
                            <th>No. HP</th>
                            <th>Email</th>
                        <?php endif; ?>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($gurus as $g): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><?php echo $g['nip']; ?></td>
                        <td><b><?php echo $g['nama']; ?></b></td>
                        <td><?php echo $g['jabatan']; ?></td>
                        <?php if($mode == 'full'): ?>
                            <td><?php echo $g['nik'] ?: '-'; ?></td>
                            <td><?php echo $g['no_hp'] ?: '-'; ?></td>
                            <td><?php echo $g['email'] ?: '-'; ?></td>
                        <?php endif; ?>
                        <td style="font-size: 0.8rem;"><?php echo $g['alamat']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <div class="ttd">
                    <p><?php echo $inst['kota']; ?>, <?php echo date('d F Y'); ?></p>
                    <p>Kepala Madrasah,</p>
                    <div style="height: 80px;"></div>
                    <p><b><?php echo $inst['nama_kepala']; ?></b></p>
                    <p>NIP. <?php echo $inst['nip_kepala']; ?></p>
                </div>
            </div>
        </body>
        </html>
        <?php
    }


    public function mengajar() {
        $dbSiakad = Database::connect('siakad');
        $dbCore = Database::connect('core');
        
        $selected_kelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : null;
        
        $kelas = $dbCore->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $gurus = $dbCore->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $mapels = $dbSiakad->query("SELECT * FROM mapel ORDER BY nama_mapel ASC")->fetchAll();
        
        // Fetch existing assignments for the selected class and active academic year
        $assignments = [];
        $activeYear = \App\Core\AcademicYear::current();
        $thn_id = $activeYear['id'];
        $smt = $activeYear['semester'];
        
        if ($selected_kelas) {
            $existing = $dbSiakad->prepare("SELECT mapel_id, guru_id FROM mengajar WHERE kelas_id = ? AND tahun_ajaran_id = ? AND semester = ?");
            $existing->execute([$selected_kelas, $thn_id, $smt]);
            foreach ($existing->fetchAll() as $row) {
                $assignments[$row['mapel_id']] = $row['guru_id'];
            }
        }

        $title = "Penugasan Mengajar Per Kelas - SIAKAD";
        ob_start();
        ?>
<div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/siakad/sidebar.php'; ?>
            <div style="flex: 1; padding: 1.5rem;">
            <?php include __DIR__ . '/../../resources/views/siakad/_siakad_header.php'; ?>
                <div class="z-page-head-row">
                    <div>
                        <h1><i data-lucide="book-open"></i> Penugasan Mengajar</h1>
                        <p>Atur guru pengampu mata pelajaran untuk setiap kelas.</p>
                    </div>
                </div>

                <!-- Filter Kelas -->
                <div style="background: #fff; padding: 0.75rem 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0; margin-bottom: 1.25rem;">
                    <form action="" method="GET" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                        <div style="flex: 1; display: flex; align-items: center; gap: 12px;">
                            <label style="color: #64748b; font-size: 0.82rem; font-weight: 700; white-space: nowrap;">Pilih Kelas:</label>
                            <select name="kelas_id" onchange="this.form.submit()" style="background: #fff; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 6px 12px; font-weight: 700; color: #0284c7; font-size: 0.85rem; outline: none; width: 240px; cursor: pointer;">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach($kelas as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                        KELAS <?php echo $k['nama_kelas']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if($selected_kelas): ?>
                            <div>
                                <span style="background: #f0f9ff; color: #0284c7; border: 1px solid #bbf7d0; padding: 4px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                    <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i> Mode Edit Aktif
                                </span>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if($selected_kelas): ?>
                <div style="background: #fff; padding: 0; overflow: hidden; border-radius: 14px; border: 1px solid #e2e8f0;">
                    <form action="/siakad/guru/mengajar/update" method="POST">
                        <input type="hidden" name="kelas_id" value="<?php echo $selected_kelas; ?>">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #f1f5f9;">
                                    <th style="padding: 0.55rem 0.75rem; width: 150px; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Kode Mapel</th>
                                    <th style="padding: 0.55rem 0.75rem; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Nama Mata Pelajaran</th>
                                    <th style="padding: 0.55rem 0.75rem; width: 350px; font-size: 0.65rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Guru Pengampu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    // Group mapels by kategori
                                    $grouped_mapels = [];
                                    foreach($mapels as $m) {
                                        $kat = $m['kategori'] ?: 'Umum';
                                        $grouped_mapels[$kat][] = $m;
                                    }
                                ?>
                                <?php foreach($grouped_mapels as $kategori => $maps): ?>
                                    <tr>
                                        <td colspan="3" style="background: #f8fafc; color: #0f172a; padding: 0.65rem 0.75rem; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; border-top: 1px solid #e2e8f0;">
                                            <i data-lucide="folder" style="width: 14px; height: 14px; display: inline-block; vertical-align: text-bottom; margin-right: 5px; color: #0284c7;"></i>
                                            KATEGORI: <?php echo $kategori; ?>
                                        </td>
                                    </tr>
                                    <?php foreach($maps as $m): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;">
                                        <td style="padding: 0.45rem 0.75rem;">
                                            <span style="background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;">
                                                <?php echo $m['kode_mapel']; ?>
                                            </span>
                                        </td>
                                        <td style="padding: 0.45rem 0.75rem; font-weight: 700; color: #1e293b; font-size: 0.88rem;">
                                            <?php echo $m['nama_mapel']; ?>
                                        </td>
                                        <td style="padding: 0.45rem 0.75rem;">
                                            <select name="guru[<?php echo $m['id']; ?>]" style="width: 100%; padding: 6px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; font-weight: 500; background: #fff; outline: none; transition: 0.2s;">
                                                <option value="">-- Pilih Guru --</option>
                                                <?php foreach($gurus as $g): ?>
                                                    <option value="<?php echo $g['id']; ?>" <?php echo (isset($assignments[$m['id']]) && $assignments[$m['id']] == $g['id']) ? 'selected' : ''; ?>>
                                                        <?php echo $g['nama']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div style="padding: 1.25rem; background: #f8fafc; text-align: right; border-top: 1px solid #f1f5f9;">
                            <button type="submit" class="z-btn z-btn-primary">
                                <i data-lucide="save"></i> SIMPAN PENUGASAN
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3.5rem 2rem; background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;">
                        <i data-lucide="mouse-pointer-2" style="width: 36px; height: 36px; color: #94a3b8; margin-bottom: 0.75rem;"></i>
                        <h3 style="color: #64748b; font-size: 1.05rem; font-weight: 700; margin: 0 0 4px 0;">Silakan pilih kelas terlebih dahulu</h3>
                        <p style="color: #94a3b8; font-size: 0.8rem; margin: 0;">Data mata pelajaran akan muncul setelah kelas dipilih.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>lucide.createIcons();</script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function updateMengajar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guru']) && isset($_POST['kelas_id'])) {
            $db = Database::connect('siakad');
            $kelas_id = $_POST['kelas_id'];
            
            $activeYear = \App\Core\AcademicYear::current();
            $thn_id = $activeYear['id'];
            $smt = $activeYear['semester'];
            
            foreach ($_POST['guru'] as $mapel_id => $guru_id) {
                $guru_id = !empty($guru_id) ? $guru_id : null;
                if ($guru_id) {
                    $cek = $db->prepare("SELECT id FROM mengajar WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
                    $cek->execute([$kelas_id, $mapel_id, $thn_id, $smt]);
                    if ($cek->fetch()) {
                        $stmt = $db->prepare("UPDATE mengajar SET guru_id = ? WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
                        $stmt->execute([$guru_id, $kelas_id, $mapel_id, $thn_id, $smt]);
                    } else {
                        $stmt = $db->prepare("INSERT INTO mengajar (kelas_id, mapel_id, guru_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$kelas_id, $mapel_id, $guru_id, $thn_id, $smt]);
                    }
                } else {
                    $stmt = $db->prepare("DELETE FROM mengajar WHERE kelas_id = ? AND mapel_id = ? AND tahun_ajaran_id = ? AND semester = ?");
                    $stmt->execute([$kelas_id, $mapel_id, $thn_id, $smt]);
                }
            }
        }
        $redirect = isset($_POST['kelas_id']) ? '?kelas_id=' . $_POST['kelas_id'] : '';
        redirect('/siakad/guru/mengajar' . $redirect);
    }

    public function tugasTambahan() {
        $db = Database::connect('core');
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $gurus = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC")->fetchAll();
        $tasks = $db->query("SELECT gt.*, g.nama FROM guru_tugas gt JOIN guru g ON gt.guru_id = g.id WHERE gt.tahun_ajaran = '$active_year_name' AND gt.tugas != 'Wali Kelas' AND gt.tugas != 'Pembina Ekstrakurikuler' ORDER BY gt.id DESC")->fetchAll();
        $kelas = $db->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $tahun_ajarans = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();

        $title = "Tugas Tambahan Guru - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/guru_tugas.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function saveTugasTambahan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::connect('core');
            $guru_id = $_POST['guru_id'];
            $tugas = $_POST['tugas'];
            $keterangan = $_POST['keterangan'];
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            $tahun_ajaran = isset($_POST['tahun_ajaran']) ? $_POST['tahun_ajaran'] : $active_year_name;

            $stmt = $db->prepare("INSERT INTO guru_tugas (guru_id, tugas, keterangan, tahun_ajaran) VALUES (?, ?, ?, ?)");
            $stmt->execute([$guru_id, $tugas, $keterangan, $tahun_ajaran]);
        }
        redirect('/siakad/guru/tugas');
    }

    public function deleteTugasTambahan($id) {
        $db = Database::connect('core');
        $db->prepare("DELETE FROM guru_tugas WHERE id = ?")->execute([$id]);
        redirect('/siakad/guru/tugas');
    }

}



