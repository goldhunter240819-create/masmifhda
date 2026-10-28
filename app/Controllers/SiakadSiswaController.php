<?php

namespace App\Controllers;

use App\Core\Database;

class SiakadSiswaController {

    public function siswa() {
        $db = Database::connect('core');
        $selected_kelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $active_year_id = $active_year ? intval($active_year['id']) : 1;
        
        $siswas = [];
        if ($selected_kelas !== '') {
            // Match riwayat by year NAME (covers both Ganjil & Genap of same school year)
            $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
            if ($selected_kelas !== 'all') {
                $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($selected_kelas);
            }

            $siswas = $db->query("SELECT s.*, u.username, u.plain_password, 
                                 COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas,
                                 COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id
                                 FROM siswa s 
                                 LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                     AND rks.tahun_ajaran_id IN (
                                         SELECT id FROM tahun_ajaran WHERE name = '$active_year_name'
                                     )
                                 LEFT JOIN users u ON s.user_id = u.id 
                                 LEFT JOIN kelas k ON s.kelas_id = k.id 
                                 LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                                 $where
                                 ORDER BY s.nama ASC")->fetchAll();
                                 
            // Add qr_token for preview
            foreach ($siswas as &$s) {
                $s['qr_token'] = "TOKEN:" . $s['user_id'] . ":" . substr(md5($s['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
            }
            unset($s);
        }
        
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

        $title = "Manajemen Data Siswa - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function formTambahSiswa() {
        $db = Database::connect('core');
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $siswa = null; // Mode tambah baru
        $title = "Tambah Siswa Baru - SIAKAD";
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function editSiswa($id) {
        $db = Database::connect('core');
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $siswa = $db->query("SELECT s.*, u.username, u.plain_password FROM siswa s JOIN users u ON s.user_id = u.id WHERE s.id = " . intval($id))->fetch();
        if (!$siswa) { redirect('/siakad/siswa'); exit; }
        $title = "Edit Data Siswa - " . $siswa['nama'];
        ob_start();
        include __DIR__ . '/../../resources/views/siakad/siswa_form.php';
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function addSiswa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Handle Foto
            $foto_name = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "siswa_" . time() . "." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/siswa/" . $foto_name);
            }

            $db = Database::connect('core');
            
            try {
                $db->beginTransaction();
                
                // 1. Create User in core_db
                $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                $check = $db->prepare("SELECT id FROM users WHERE username = ?");
                $check->execute([$random_user]);
                while ($check->fetch()) {
                    $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    $check->execute([$random_user]);
                }
                
                $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                while ($random_pass === $random_user) {
                    $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                }
                
                $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, status, plain_password) VALUES (?, ?, 3, 'active', ?)");
                $stmt->execute([$random_user, password_hash($random_pass, PASSWORD_BCRYPT), $random_pass]);
                $userId = $db->lastInsertId();
                
                $active_year = \App\Core\AcademicYear::current();
                $active_year_name = $active_year ? $active_year['name'] : '2025/2026';

                // 2. Create Siswa Profile with ALL fields (KK Standard)
                $sql = "INSERT INTO siswa (user_id, nama, nis, nisn, no_kk, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, gol_darah, pendidikan_terakhir, status_perkawinan, no_paspor, no_kitas, kewarganegaraan, anak_ke, status_keluarga, kelas_id, alamat, rt, rw, desa, kecamatan, kota, provinsi, kode_pos, nama_ayah, pekerjaan_ayah, nama_ibu, pekerjaan_ibu, penghasilan_ortu, no_hp_ortu, sekolah_asal, no_ijazah, nama_wali, pekerjaan_wali, no_hp_wali, foto, tahun_ajaran) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $userId, 
                    $data['nama'] ?? '', 
                    $data['nis'] ?? '', 
                    $data['nisn'] ?? '', 
                    $data['no_kk'] ?? '', 
                    $data['nik'] ?? '', 
                    $data['tempat_lahir'] ?? '', 
                    !empty($data['tanggal_lahir']) ? $data['tanggal_lahir'] : null, 
                    $data['jenis_kelamin'] ?? 'L', 
                    $data['agama'] ?? '', 
                    $data['gol_darah'] ?? '', 
                    $data['pendidikan_terakhir'] ?? '', 
                    $data['status_perkawinan'] ?? '', 
                    $data['no_paspor'] ?? '', 
                    $data['no_kitas'] ?? '', 
                    $data['kewarganegaraan'] ?? '', 
                    $data['anak_ke'] ?? '', 
                    $data['status_keluarga'] ?? '',
                    !empty($data['kelas_id']) ? $data['kelas_id'] : null, 
                    $data['alamat'] ?? '', 
                    $data['rt'] ?? '', 
                    $data['rw'] ?? '', 
                    $data['desa'] ?? '', 
                    $data['kecamatan'] ?? '', 
                    $data['kota'] ?? '', 
                    $data['provinsi'] ?? '', 
                    $data['kode_pos'] ?? '', 
                    $data['nama_ayah'] ?? '', 
                    $data['pekerjaan_ayah'] ?? '', 
                    $data['nama_ibu'] ?? '', 
                    $data['pekerjaan_ibu'] ?? '',
                    $data['penghasilan_ortu'] ?? '', 
                    $data['no_hp_ortu'] ?? '', 
                    $data['sekolah_asal'] ?? '', 
                    $data['no_ijazah'] ?? '', 
                    $data['nama_wali'] ?? '',
                    $data['pekerjaan_wali'] ?? '', 
                    $data['no_hp_wali'] ?? '', 
                    $foto_name, 
                    $active_year_name
                ]);
                $siswaId = $db->lastInsertId();
                
                // Add to riwayat_kelas_siswa
                if ($active_year && $data['kelas_id']) {
                    $riwayat = $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, 1)");
                    $riwayat->execute([$siswaId, $data['kelas_id'], $active_year['id']]);
                }
                
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                die("Gagal: " . $e->getMessage());
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        $kelas_id = !empty($data['kelas_id']) ? $data['kelas_id'] : null;
        $redir = '/siakad/siswa?msg=created' . ($kelas_id ? '&kelas_id=' . $kelas_id : '');
        redirect($redir);
    }


    public function importSiswa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            $global_kelas_id = !empty($_POST['kelas_id']) ? intval($_POST['kelas_id']) : null;
            $db = Database::connect('core');
            
            // Map existing classes for smart-matching
            $allKelas = $db->query("SELECT id, nama_kelas FROM kelas")->fetchAll();
            $kelasMap = [];
            foreach ($allKelas as $k) {
                $kelasMap[strtolower(trim($k['nama_kelas']))] = $k['id'];
            }
            
            foreach ($lines as $line) {
                $cols = explode("\t", trim($line));
                if (count($cols) < 2) continue;
                
                $nis = trim($cols[0]);
                $nama = trim($cols[1]);
                $jk = isset($cols[2]) ? trim($cols[2]) : 'L';
                $tempat = isset($cols[3]) ? trim($cols[3]) : '';
                $tgl = isset($cols[4]) ? trim($cols[4]) : null;
                $nama_kelas_import = isset($cols[5]) ? trim($cols[5]) : '';
                
                if (empty($nis) || empty($nama)) continue;

                // Determine final class ID
                $final_kelas_id = $global_kelas_id;
                if (!empty($nama_kelas_import)) {
                    $norm = strtolower(trim($nama_kelas_import));
                    if (isset($kelasMap[$norm])) {
                        $final_kelas_id = $kelasMap[$norm];
                    } else {
                        // Create new class on the fly
                        $stmt = $db->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
                        $stmt->execute([trim($nama_kelas_import)]);
                        $new_id = $db->lastInsertId();
                        $kelasMap[$norm] = $new_id;
                        $final_kelas_id = $new_id;
                    }
                }

                try {
                    $db->beginTransaction();
                    $check = $db->prepare("SELECT id FROM siswa WHERE nis = ?");
                    $check->execute([$nis]);
                    if ($check->fetch()) { $db->rollBack(); continue; }

                    $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    $checkUser = $db->prepare("SELECT id FROM users WHERE username = ?");
                    $checkUser->execute([$random_user]);
                    while ($checkUser->fetch()) {
                        $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                        $checkUser->execute([$random_user]);
                    }

                    $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    while ($random_pass === $random_user) {
                        $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    }

                    $stmt = $db->prepare("INSERT INTO users (username, password_hash, role_id, status, plain_password) VALUES (?, ?, 3, 'active', ?)");
                    $stmt->execute([$random_user, password_hash($random_pass, PASSWORD_BCRYPT), $random_pass]);
                    $userId = $db->lastInsertId();
                    
                    $active_year = \App\Core\AcademicYear::current();
                    $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
                    $stmt = $db->prepare("INSERT INTO siswa (user_id, nama, nis, jenis_kelamin, tempat_lahir, tanggal_lahir, kelas_id, tahun_ajaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$userId, $nama, $nis, $jk, $tempat, $tgl, $final_kelas_id, $active_year_name]);
                    $siswaId = $db->lastInsertId();
                    
                    if ($active_year && $final_kelas_id) {
                        $riwayat = $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, 1)");
                        $riwayat->execute([$siswaId, $final_kelas_id, $active_year['id']]);
                    }
                    
                    $db->commit();
                } catch (\Exception $e) { $db->rollBack(); }
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        redirect('/siakad/siswa');
    }

    public function updateSiswaBulk() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paste_data'])) {
            $data_raw = trim($_POST['paste_data']);
            $lines = explode("\n", $data_raw);
            $db = Database::connect('core');
            
            // Map existing classes for smart-matching
            $allKelas = $db->query("SELECT id, nama_kelas FROM kelas")->fetchAll();
            $kelasMap = [];
            foreach ($allKelas as $k) {
                $kelasMap[strtolower(trim($k['nama_kelas']))] = $k['id'];
            }
            
            foreach ($lines as $line) {
                $cols = explode("\t", trim($line));
                if (count($cols) < 2) continue;
                $qr_token_raw = trim($cols[0]);
                $nis = isset($cols[1]) ? trim($cols[1]) : '';
                $nisn = isset($cols[2]) ? trim($cols[2]) : '';
                if ($nisn !== '' && is_numeric($nisn) && strlen($nisn) < 10) {
                    $nisn = str_pad($nisn, 10, '0', STR_PAD_LEFT);
                }
                $nik = isset($cols[3]) ? trim($cols[3]) : '';
                $nama = isset($cols[4]) ? trim($cols[4]) : '';
                $jk = isset($cols[5]) ? trim($cols[5]) : 'L';
                $tempat = isset($cols[6]) ? trim($cols[6]) : '';
                $tgl = isset($cols[7]) ? trim($cols[7]) : null;
                $nama_kelas_import = isset($cols[8]) ? trim($cols[8]) : '';
                $alamat = isset($cols[9]) ? trim($cols[9]) : '';
                
                // Parse date if it's DD/MM/YYYY from Excel
                if ($tgl && strpos($tgl, '/') !== false) {
                    $d_parts = explode('/', $tgl);
                    if (count($d_parts) === 3) {
                        if (strlen($d_parts[2]) === 4) {
                            // DD/MM/YYYY -> YYYY-MM-DD
                            $tgl = $d_parts[2] . '-' . str_pad($d_parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($d_parts[0], 2, '0', STR_PAD_LEFT);
                        } else if (strlen($d_parts[0]) === 4) {
                            // YYYY/MM/DD -> YYYY-MM-DD
                            $tgl = $d_parts[0] . '-' . str_pad($d_parts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($d_parts[2], 2, '0', STR_PAD_LEFT);
                        }
                    }
                }
                
                // Validate QR Token Format
                if (strpos($qr_token_raw, 'TOKEN:') !== 0) continue;
                
                $parts = explode(':', $qr_token_raw);
                if (count($parts) < 3) continue;
                
                $user_id = intval($parts[1]);
                $hash = $parts[2];
                
                $expected_hash = substr(md5($user_id . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                if ($hash !== $expected_hash) continue; // Invalid token
                
                if (empty($nama)) continue;

                // Determine final class ID
                $final_kelas_id = null;
                if (!empty($nama_kelas_import)) {
                    $norm = strtolower(trim($nama_kelas_import));
                    if (isset($kelasMap[$norm])) {
                        $final_kelas_id = $kelasMap[$norm];
                    } else {
                        // Create new class on the fly
                        $stmt = $db->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
                        $stmt->execute([trim($nama_kelas_import)]);
                        $new_id = $db->lastInsertId();
                        $kelasMap[$norm] = $new_id;
                        $final_kelas_id = $new_id;
                    }
                }

                try {
                    $db->beginTransaction();
                    
                    // Cek eksistensi siswa by user_id
                    $check = $db->prepare("SELECT id, kelas_id FROM siswa WHERE user_id = ?");
                    $check->execute([$user_id]);
                    $siswa_exist = $check->fetch();
                    
                    if ($siswa_exist) {
                        $siswaId = $siswa_exist['id'];
                        
                        // Update Data Profil
                        $update_sql = "UPDATE siswa SET nama = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat = ?";
                        $params = [$nama, $jk, $tempat, $tgl, $alamat];
                        
                        if (!empty($nis)) {
                            $update_sql .= ", nis = ?";
                            $params[] = $nis;
                        }
                        
                        if (!empty($nisn)) {
                            $update_sql .= ", nisn = ?";
                            $params[] = $nisn;
                        }
                        
                        if (!empty($nik)) {
                            $update_sql .= ", nik = ?";
                            $params[] = $nik;
                        }
                        
                        // Update Kelas jika berubah
                        if ($final_kelas_id && $siswa_exist['kelas_id'] != $final_kelas_id) {
                            $update_sql .= ", kelas_id = ?";
                            $params[] = $final_kelas_id;
                            
                            // Update riwayat kelas aktif
                            $active_year = \App\Core\AcademicYear::current();
                            if ($active_year) {
                                // Cek apakah riwayat kelas_id sudah ada di tahun aktif
                                $checkRiwayat = $db->prepare("SELECT id FROM riwayat_kelas_siswa WHERE siswa_id = ? AND tahun_ajaran_id = ?");
                                $checkRiwayat->execute([$siswaId, $active_year['id']]);
                                if ($checkRiwayat->fetch()) {
                                    $updRiwayat = $db->prepare("UPDATE riwayat_kelas_siswa SET kelas_id = ? WHERE siswa_id = ? AND tahun_ajaran_id = ?");
                                    $updRiwayat->execute([$final_kelas_id, $siswaId, $active_year['id']]);
                                } else {
                                    $insRiwayat = $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, 1)");
                                    $insRiwayat->execute([$siswaId, $final_kelas_id, $active_year['id']]);
                                }
                            }
                        }
                        
                        $update_sql .= " WHERE id = ?";
                        $params[] = $siswaId;
                        
                        $stmt = $db->prepare($update_sql);
                        $stmt->execute($params);
                    }
                    
                    $db->commit();
                } catch (\Exception $e) { $db->rollBack(); }
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        redirect('/siakad/siswa');
    }


    public function updateSiswa() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $id = $data['id'];
            
            $db = Database::connect('core');
            $siswa = $db->query("SELECT * FROM siswa WHERE id = $id")->fetch();

            // Handle Foto
            $foto_name = $siswa['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                // Hapus foto lama
                if ($siswa['foto']) {
                    @unlink(__DIR__ . "/../../public/uploads/siswa/" . $siswa['foto']);
                }
                
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = "siswa_" . time() . "." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/siswa/" . $foto_name);
            }

            $sql = "UPDATE siswa SET 
                    nama = ?, nis = ?, nisn = ?, no_kk = ?, nik = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, 
                    agama = ?, gol_darah = ?, pendidikan_terakhir = ?, status_perkawinan = ?, no_paspor = ?, 
                    no_kitas = ?, kewarganegaraan = ?, anak_ke = ?, status_keluarga = ?, kelas_id = ?, 
                    alamat = ?, rt = ?, rw = ?, desa = ?, kecamatan = ?, kota = ?, provinsi = ?, kode_pos = ?, 
                    nama_ayah = ?, pekerjaan_ayah = ?, nama_ibu = ?, pekerjaan_ibu = ?, 
                    penghasilan_ortu = ?, no_hp_ortu = ?, sekolah_asal = ?, no_ijazah = ?, 
                    nama_wali = ?, pekerjaan_wali = ?, no_hp_wali = ?, foto = ? 
                    WHERE id = ?";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $data['nama'] ?? '', 
                $data['nis'] ?? '',
                $data['nisn'] ?? '', 
                $data['no_kk'] ?? '', 
                $data['nik'] ?? '', 
                $data['tempat_lahir'] ?? '', 
                !empty($data['tanggal_lahir']) ? $data['tanggal_lahir'] : null, 
                $data['jenis_kelamin'] ?? 'L', 
                $data['agama'] ?? '', 
                $data['gol_darah'] ?? '', 
                $data['pendidikan_terakhir'] ?? '', 
                $data['status_perkawinan'] ?? '',
                $data['no_paspor'] ?? '', 
                $data['no_kitas'] ?? '', 
                $data['kewarganegaraan'] ?? '', 
                $data['anak_ke'] ?? '', 
                $data['status_keluarga'] ?? '', 
                !empty($data['kelas_id']) ? $data['kelas_id'] : null, 
                $data['alamat'] ?? '', 
                $data['rt'] ?? '', 
                $data['rw'] ?? '', 
                $data['desa'] ?? '', 
                $data['kecamatan'] ?? '', 
                $data['kota'] ?? '', 
                $data['provinsi'] ?? '', 
                $data['kode_pos'] ?? '', 
                $data['nama_ayah'] ?? '', 
                $data['pekerjaan_ayah'] ?? '', 
                $data['nama_ibu'] ?? '', 
                $data['pekerjaan_ibu'] ?? '', 
                $data['penghasilan_ortu'] ?? '', 
                $data['no_hp_ortu'] ?? '', 
                $data['sekolah_asal'] ?? '', 
                $data['no_ijazah'] ?? '', 
                $data['nama_wali'] ?? '', 
                $data['pekerjaan_wali'] ?? '', 
                $data['no_hp_wali'] ?? '', 
                $foto_name, 
                $id
            ]);
        }
        \App\Services\DataSyncService::runSiswaSync();
        $kelas_id = !empty($data['kelas_id']) ? $data['kelas_id'] : null;
        $redir = '/siakad/siswa?msg=updated' . ($kelas_id ? '&kelas_id=' . $kelas_id : '');
        redirect($redir);
    }


    public function deleteSiswa($id) {
        $db = Database::connect('core');
        $siswa = $db->query("SELECT * FROM siswa WHERE id = " . intval($id))->fetch();
        $kelas_id = $siswa ? $siswa['kelas_id'] : null;
        if ($siswa) {
            $db->beginTransaction();
            try {
                if ($siswa['foto'] && file_exists(__DIR__ . '/../../public/uploads/siswa/' . $siswa['foto'])) {
                    @unlink(__DIR__ . '/../../public/uploads/siswa/' . $siswa['foto']);
                }
                if ($siswa['user_id']) {
                    $db->exec("DELETE FROM users WHERE id = " . $siswa['user_id']);
                }
                $db->exec("DELETE FROM siswa WHERE id = " . intval($id));
                $db->commit();
            } catch (\Exception $e) { $db->rollBack(); }
        }
        \App\Services\DataSyncService::runSiswaSync();
        $redir = '/siakad/siswa?msg=deleted' . ($kelas_id ? '&kelas_id=' . $kelas_id : '');
        redirect($redir);
    }

    public function deleteSiswaBulk() {
        $kelas_id = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect('core');
            $ids = array_map('intval', $_POST['ids']);
            $ids_str = implode(',', $ids);
            
            $siswas = $db->query("SELECT user_id, foto, kelas_id FROM siswa WHERE id IN ($ids_str)")->fetchAll();
            $userIds = [];
            foreach ($siswas as $s) {
                if (!$kelas_id && !empty($s['kelas_id'])) $kelas_id = $s['kelas_id'];
                if ($s['user_id']) $userIds[] = $s['user_id'];
                if ($s['foto'] && file_exists(__DIR__ . '/../../public/uploads/siswa/' . $s['foto'])) {
                    @unlink(__DIR__ . '/../../public/uploads/siswa/' . $s['foto']);
                }
            }
            
            try {
                $db->beginTransaction();
                $db->exec("DELETE FROM siswa WHERE id IN ($ids_str)");
                if (!empty($userIds)) {
                    $uIdsStr = implode(',', $userIds);
                    $db->exec("DELETE FROM users WHERE id IN ($uIdsStr)");
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        $redir = '/siakad/siswa?msg=deleted_bulk' . ($kelas_id ? '&kelas_id=' . $kelas_id : '');
        redirect($redir);
        exit;
    }

    public function resetPasswordBulk() {
        $kelas_id = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $db = Database::connect('core');
            $ids = array_map('intval', $_POST['ids']);
            $ids_str = implode(',', $ids);
            $siswas = $db->query("SELECT id, user_id, kelas_id FROM siswa WHERE id IN ($ids_str)")->fetchAll();
            
            try {
                $db->beginTransaction();
                foreach ($siswas as $s) {
                    if (!$kelas_id && !empty($s['kelas_id'])) $kelas_id = $s['kelas_id'];
                    // Generate random username
                    $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    $check = $db->prepare("SELECT id FROM users WHERE username = ?");
                    $check->execute([$random_user]);
                    while ($check->fetch()) {
                        $random_user = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                        $check->execute([$random_user]);
                    }
                    
                    // Generate random password
                    $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    while ($random_pass === $random_user) {
                        $random_pass = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                    }

                    if ($s['user_id']) {
                        $update = $db->prepare("UPDATE users SET username = ?, password_hash = ?, plain_password = ? WHERE id = ?");
                        $update->execute([$random_user, password_hash($random_pass, PASSWORD_BCRYPT), $random_pass, $s['user_id']]);
                    } else {
                        // Create new user for student
                        $insert = $db->prepare("INSERT INTO users (username, password_hash, plain_password, role_id, status) VALUES (?, ?, ?, 3, 'active')");
                        $insert->execute([$random_user, password_hash($random_pass, PASSWORD_BCRYPT), $random_pass]);
                        $new_user_id = $db->lastInsertId();
                        
                        $update_siswa = $db->prepare("UPDATE siswa SET user_id = ? WHERE id = ?");
                        $update_siswa->execute([$new_user_id, $s['id']]);
                    }
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                error_log("Error in resetPasswordBulk: " . $e->getMessage());
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        $redir = '/siakad/siswa?msg=reset_success' . ($kelas_id ? '&kelas_id=' . $kelas_id : '');
        redirect($redir);
        exit;
    }


    public function exportSiswa() {
        $db = Database::connect('core');
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'public';
        $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : null;
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_id = $active_year ? $active_year['id'] : 1;
        
        $active_year_name_export = $active_year ? $active_year['name'] : '';
        
        $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name_export')";
        if ($kelas_id) { $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($kelas_id); }
        
        $siswas = $db->query("SELECT s.*, 
                             COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                             FROM siswa s 
                             LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                 AND rks.tahun_ajaran_id IN (
                                     SELECT id FROM tahun_ajaran WHERE name = '$active_year_name_export'
                                 )
                             LEFT JOIN kelas k ON s.kelas_id = k.id 
                             LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                             $where ORDER BY s.nama ASC")->fetchAll();
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();

        if ($mode == 'excel') {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Data_Siswa_" . date('Ymd_His') . ".xls");
            
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>QR Token (JANGAN DIUBAH)</th>";
            echo "<th>NIS</th>";
            echo "<th>NISN</th>";
            echo "<th>NIK</th>";
            echo "<th>Nama Lengkap</th>";
            echo "<th>JK (L/P)</th>";
            echo "<th>Tempat Lahir</th>";
            echo "<th>Tgl Lahir (YYYY-MM-DD)</th>";
            echo "<th>Kelas Asal</th>";
            echo "<th>Alamat</th>";
            echo "</tr>";
            
            foreach($siswas as $s) {
                $qr_token = "TOKEN:" . $s['user_id'] . ":" . substr(md5($s['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                echo "<tr>";
                echo "<td>" . $qr_token . "</td>";
                echo "<td>" . $s['nis'] . "</td>";
                echo "<td>" . $s['nisn'] . "</td>";
                echo "<td>" . $s['nik'] . "</td>";
                echo "<td>" . htmlspecialchars($s['nama']) . "</td>";
                echo "<td>" . $s['jenis_kelamin'] . "</td>";
                echo "<td>" . htmlspecialchars($s['tempat_lahir']) . "</td>";
                echo "<td>" . $s['tanggal_lahir'] . "</td>";
                echo "<td>" . $s['nama_kelas'] . "</td>";
                echo "<td>" . htmlspecialchars($s['alamat']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit;
        }

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cetak Data Siswa - <?php echo $inst['nama']; ?></title>
            <style>
                body { font-family: 'Times New Roman', serif; padding: 40px; color: #000; }
                .kop-surat { display: flex; align-items: center; border-bottom: 4px double #000; padding-bottom: 15px; margin-bottom: 30px; }
                .kop-surat img { width: 90px; height: 90px; object-fit: contain; }
                .kop-text { text-align: center; flex: 1; }
                .kop-text h1 { margin: 0; font-size: 1.6rem; text-transform: uppercase; }
                .kop-text h2 { margin: 5px 0; font-size: 1.2rem; }
                .kop-text p { margin: 2px 0; font-size: 0.85rem; font-style: italic; }
                
                h3 { text-align: center; text-transform: uppercase; margin-bottom: 25px; }
                
                table { width: 100%; border-collapse: collapse; font-size: 0.7rem; }
                th, td { border: 1px solid #000; padding: 4px 6px; }
                th { background: #f0f0f0; }
                
                .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
                .ttd { text-align: center; width: 250px; }
                
                @media print {
                    @page { size: landscape; margin: 1cm; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <div class="no-print" style="text-align:center; margin-bottom: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; font-size: 1rem; background: #059669; color: white; border: none; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                    Cetak Laporan
                </button>
            </div>
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

            <h3>DAFTAR DATA SISWA <?php echo $kelas_id ? "KELAS " . $siswas[0]['nama_kelas'] : "SELURUH KELAS"; ?></h3>
            <p style="font-size: 0.8rem; margin-bottom: 10px;">Jenis Laporan: <b><?php echo $mode == 'full' ? 'Lengkap (Internal)' : 'Standar (Publik)'; ?></b></p>

            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">No</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th style="width: 25px;">JK</th>
                        <th>Tempat, Tgl Lahir</th>
                        <th>Nama Ayah</th>
                        <th>Pekerjaan Ayah</th>
                        <th>Nama Ibu</th>
                        <th>Pekerjaan Ibu</th>
                        <th>Nama Wali</th>
                        <th>Pekerjaan Wali</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($siswas as $s): ?>
                    <tr>
                        <td align="center"><?php echo $no++; ?></td>
                        <td><?php echo $s['nis'] ?: '-'; ?></td>
                        <td><?php echo $s['nisn'] ?: '-'; ?></td>
                        <td><?php echo $s['nik'] ?: '-'; ?></td>
                        <td><b><?php echo $s['nama']; ?></b></td>
                        <td align="center"><?php echo $s['jenis_kelamin'] == 'Laki-Laki' ? 'L' : ($s['jenis_kelamin'] == 'Perempuan' ? 'P' : $s['jenis_kelamin']); ?></td>
                        <td><?php echo $s['tempat_lahir']; ?>, <?php echo $s['tanggal_lahir']; ?></td>
                        <td><?php echo $s['nama_ayah'] ?: '-'; ?></td>
                        <td><?php echo $s['pekerjaan_ayah'] ?: '-'; ?></td>
                        <td><?php echo $s['nama_ibu'] ?: '-'; ?></td>
                        <td><?php echo $s['pekerjaan_ibu'] ?: '-'; ?></td>
                        <td><?php echo $s['nama_wali'] ?: '-'; ?></td>
                        <td><?php echo $s['pekerjaan_wali'] ?: '-'; ?></td>
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


    public function naikKelas() {
        $db = Database::connect('core');
        $kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $tahun_ajaran = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_id = $active_year ? $active_year['id'] : 1;
        $active_year_name = $active_year ? $active_year['name'] : '-';

        $from_kelas = isset($_GET['from_kelas']) ? $_GET['from_kelas'] : null;
        $from_tahun = isset($_GET['from_tahun']) ? $_GET['from_tahun'] : null;
        $to_kelas = isset($_GET['to_kelas']) ? $_GET['to_kelas'] : null;
        
        $from_tingkat = null;
        if($from_kelas) {
            foreach($kelas as $k) {
                if ($k['id'] == $from_kelas) {
                    $from_tingkat = $k['tingkat'];
                    break;
                }
            }
        }
        
        $siswas = [];
        if($from_kelas && $from_tahun) {
            $siswas = $db->prepare("SELECT * FROM siswa WHERE kelas_id = ? AND tahun_ajaran = ? AND status IN ('Aktif', 'Alumni') ORDER BY nama ASC");
            $siswas->execute([intval($from_kelas), $from_tahun]);
            $siswas = $siswas->fetchAll();
        }

        $target_siswas = [];
        if($to_kelas && $to_kelas !== 'ALUMNI') {
            $target_siswas = $db->prepare("SELECT * FROM siswa WHERE kelas_id = ? AND tahun_ajaran = ? AND status = 'Aktif' ORDER BY nama ASC");
            $target_siswas->execute([intval($to_kelas), $active_year_name]);
            $target_siswas = $target_siswas->fetchAll();
        }

        $title = "Proses Naik Kelas - SIAKAD";
        ob_start();
        ?>
<div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/siakad/sidebar.php'; ?>
            <div style="flex: 1; padding: 1.5rem;">
            <?php include __DIR__ . '/../../resources/views/siakad/_siakad_header.php'; ?>
                <div class="z-page-head-row">
                    <div>
                        <h1><i data-lucide="trending-up"></i> Sistem Kenaikan Kelas</h1>
                        <p>Pindahkan siswa terpilih antar jenjang atau urungkan pemindahan secara sinkron.</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <!-- LEFT COLUMN: KELAS ASAL -->
                    <div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; border-top: 4px solid #0ea5e9; display: flex; flex-direction: column;">
                        <div style="padding: 1.25rem; border-bottom: 1px dashed #e2e8f0;">
                            <h4 style="margin: 0 0 0.75rem; font-size: 0.9rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                <span style="background: #0ea5e9; color: white; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">1</span>
                                Pilih Kelas Asal
                            </h4>
                            <form action="" method="GET" id="form-filter-asal">
                                <input type="hidden" name="to_kelas" value="<?php echo htmlspecialchars($to_kelas ?? ''); ?>">
                                <select name="from_tahun" style="width: 100%; padding: 6px 12px; margin-bottom: 8px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #0f172a; font-size: 0.85rem; outline: none; background: #fff; cursor: pointer;" onchange="this.form.submit();">
                                    <option value="">-- Pilih Tahun Ajaran Asal --</option>
                                    <?php 
                                    $unique_tahuns = array_unique(array_column($tahun_ajaran, 'name'));
                                    foreach($unique_tahuns as $t_name): 
                                        if ($t_name === $active_year_name) continue; 
                                    ?>
                                        <option value="<?php echo $t_name; ?>" <?php echo $from_tahun == $t_name ? 'selected' : ''; ?>><?php echo $t_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="from_kelas" onchange="if(this.form.from_tahun.value != '') this.form.submit(); else alert('Pilih tahun ajaran asal dulu');" style="width: 100%; padding: 6px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #0f172a; font-size: 0.85rem; outline: none; background: #fff; cursor: pointer;">
                                    <option value="">-- Pilih Kelas Asal --</option>
                                    <?php foreach($kelas as $k): ?>
                                        <option value="<?php echo $k['id']; ?>" <?php echo $from_kelas == $k['id'] ? 'selected' : ''; ?>>KELAS <?php echo $k['nama_kelas']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        
                        <?php if($from_kelas): ?>
                        <div style="padding: 0.75rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 800; color: #0284c7; font-size: 0.78rem;">
                                <input type="checkbox" id="select-all-asal" style="width: 14px; height: 14px;"> Pilih Semua
                            </label>
                            <span style="font-weight: 700; color: #475569; font-size: 0.82rem;">Siswa (<?php echo count($siswas); ?>)</span>
                        </div>
                        <div style="padding: 1rem; flex: 1; overflow-y: auto; max-height: 400px; background: #f8fafc;">
                            <form id="form-naik" action="/siakad/siswa/proses-naik" method="POST">
                                <input type="hidden" name="from_kelas" value="<?php echo $from_kelas; ?>">
                                <input type="hidden" name="from_tahun" value="<?php echo htmlspecialchars($from_tahun ?? ''); ?>">
                                <input type="hidden" name="to_kelas" id="hidden_to_kelas" value="<?php echo htmlspecialchars($to_kelas ?? ''); ?>">
                                
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <?php if(empty($siswas)): ?>
                                        <div style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 2rem 0;">Tidak ada siswa di kelas ini</div>
                                    <?php endif; ?>
                                    <?php foreach($siswas as $s): ?>
                                        <label style="border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; padding: 8px 10px; background: white; cursor: pointer;">
                                            <input type="checkbox" name="student_ids[]" value="<?php echo $s['id']; ?>" class="student-checkbox-asal" style="width: 15px; height: 15px; margin-right: 10px; accent-color: #0284c7;">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; background: #e2e8f0; margin-right: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px solid #cbd5e1;">
                                                <?php if(!empty($s['foto'])): ?>
                                                    <img src="/public/uploads/siswa/<?php echo htmlspecialchars($s['foto']); ?>" alt="foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<i data-lucide=\'user\' style=\'width:18px;height:18px;\'></i>'; lucide.createIcons();">
                                                <?php else: ?>
                                                    <i data-lucide="user" style="width:18px;height:18px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.8rem; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($s['nama']); ?></div>
                                                <div style="font-size: 0.7rem; color: #94a3b8; font-family: monospace;">NIS: <?php echo htmlspecialchars($s['nis']); ?></div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        </div>
                        <div style="padding: 1rem; border-top: 1px solid #e2e8f0; text-align: right;">
                            <button type="button" class="z-btn z-btn-primary" <?php echo (empty($siswas) || !$to_kelas) ? 'disabled' : ''; ?> onclick="ZNS.confirm('Naikkan siswa yang dipilih ke Kelas Tujuan?', function() { document.getElementById('form-naik').submit(); }, 'Naik Kelas', 'Ya, Proses')" style="padding: 8px 16px; border-radius: 8px; font-size: 0.85rem;">Naik Kelas <i data-lucide="arrow-right" style="width: 16px; height: 16px; margin-left: 5px; vertical-align: middle;"></i></button>
                        </div>
                        <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.85rem;">Silakan pilih Kelas Asal terlebih dahulu</div>
                        <?php endif; ?>
                    </div>

                    <!-- RIGHT COLUMN: KELAS TUJUAN -->
                    <div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; border-top: 4px solid #0284c7; display: flex; flex-direction: column;">
                        <div style="padding: 1.25rem; border-bottom: 1px dashed #e2e8f0;">
                            <h4 style="margin: 0 0 0.75rem; font-size: 0.9rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                <span style="background: #0284c7; color: white; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">2</span>
                                Tujuan: TP <?php echo $active_year_name; ?>
                            </h4>

                            <?php if($from_tingkat): ?>
                            <div style="margin-bottom: 12px; padding: 10px; background: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #0284c7; margin-bottom: 6px;">Filter Tingkat Tujuan (Asal: <?php echo htmlspecialchars($from_tingkat); ?>)</div>
                                <div style="display: flex; gap: 12px; font-size: 0.8rem; font-weight: 600; color: #475569;">
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="radio" name="tingkat_filter" value="all" checked onchange="filterTujuan()" style="accent-color: #0284c7;"> Semua</label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="radio" name="tingkat_filter" value="sama" onchange="filterTujuan()" style="accent-color: #0284c7;"> Tingkat Sama</label>
                                    <label style="cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="radio" name="tingkat_filter" value="beda" onchange="filterTujuan()" style="accent-color: #0284c7;"> Tingkat Beda (Naik)</label>
                                </div>
                            </div>
                            <?php endif; ?>

                            <form action="" method="GET" id="form-filter-tujuan">
                                <input type="hidden" name="from_tahun" value="<?php echo htmlspecialchars($from_tahun ?? ''); ?>">
                                <input type="hidden" name="from_kelas" value="<?php echo htmlspecialchars($from_kelas ?? ''); ?>">
                                <select name="to_kelas" id="to_kelas_select" onchange="document.getElementById('hidden_to_kelas') && (document.getElementById('hidden_to_kelas').value = this.value); this.form.submit();" style="width: 100%; padding: 6px 12px; margin-bottom: 8px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #0f172a; font-size: 0.85rem; outline: none; background: #fff; cursor: pointer;">
                                    <option value="" data-tingkat="">-- Pilih Kelas Tujuan --</option>
                                    <option value="ALUMNI" data-tingkat="ALUMNI" style="color: #dc2626; font-weight: 800;" <?php echo $to_kelas === 'ALUMNI' ? 'selected' : ''; ?>>Setel Sebagai ALUMNI (LULUS)</option>
                                    <?php foreach($kelas as $k): ?>
                                        <option value="<?php echo $k['id']; ?>" data-tingkat="<?php echo htmlspecialchars($k['tingkat'] ?? ''); ?>" <?php echo $to_kelas == $k['id'] ? 'selected' : ''; ?>>KELAS <?php echo $k['nama_kelas']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        
                        <?php if($to_kelas && $to_kelas !== 'ALUMNI'): ?>
                        <div style="padding: 0.75rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 800; color: #0284c7; font-size: 0.78rem;">
                                <input type="checkbox" id="select-all-tujuan" style="width: 14px; height: 14px;"> Pilih Semua
                            </label>
                            <span style="font-weight: 700; color: #475569; font-size: 0.82rem;">Siswa (<?php echo count($target_siswas); ?>)</span>
                        </div>
                        <div style="padding: 1rem; flex: 1; overflow-y: auto; max-height: 400px; background: #f8fafc;">
                            <form id="form-urungkan" action="/siakad/siswa/urungkan-naik" method="POST">
                                <input type="hidden" name="from_kelas" value="<?php echo $from_kelas; ?>">
                                <input type="hidden" name="from_tahun" value="<?php echo htmlspecialchars($from_tahun ?? ''); ?>">
                                <input type="hidden" name="to_kelas" value="<?php echo htmlspecialchars($to_kelas ?? ''); ?>">
                                
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <?php if(empty($target_siswas)): ?>
                                        <div style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 2rem 0;">Belum ada siswa di kelas tujuan</div>
                                    <?php endif; ?>
                                    <?php foreach($target_siswas as $s): ?>
                                        <label style="border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; padding: 8px 10px; background: white; cursor: pointer;">
                                            <input type="checkbox" name="student_ids[]" value="<?php echo $s['id']; ?>" class="student-checkbox-tujuan" style="width: 15px; height: 15px; margin-right: 10px; accent-color: #0284c7;">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; background: #e2e8f0; margin-right: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px solid #cbd5e1;">
                                                <?php if(!empty($s['foto'])): ?>
                                                    <img src="/public/uploads/siswa/<?php echo htmlspecialchars($s['foto']); ?>" alt="foto" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.outerHTML='<i data-lucide=\'user\' style=\'width:18px;height:18px;\'></i>'; lucide.createIcons();">
                                                <?php else: ?>
                                                    <i data-lucide="user" style="width:18px;height:18px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.8rem; font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($s['nama']); ?></div>
                                                <div style="font-size: 0.7rem; color: #94a3b8; font-family: monospace;">NIS: <?php echo htmlspecialchars($s['nis']); ?></div>
                                            </div>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        </div>
                        <div style="padding: 1rem; border-top: 1px solid #e2e8f0; text-align: left;">
                            <button type="button" class="z-btn" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 700;" <?php echo (empty($target_siswas) || !$from_kelas) ? 'disabled' : ''; ?> onclick="ZNS.confirm('Urungkan kenaikan kelas siswa ini kembali ke kelas asalnya?', function() { document.getElementById('form-urungkan').submit(); }, 'Urungkan', 'Ya, Urungkan')"><i data-lucide="arrow-left" style="width: 16px; height: 16px; margin-right: 5px; vertical-align: middle;"></i> Urungkan</button>
                        </div>
                        <?php elseif($to_kelas === 'ALUMNI'): ?>
                        <div style="padding: 2rem; text-align: center; color: #0f172a; font-size: 0.85rem;">
                            <i data-lucide="graduation-cap" style="width: 48px; height: 48px; color: #0284c7; margin-bottom: 15px;"></i>
                            <div style="font-weight: 800;">Mode ALUMNI</div>
                            <p style="color: #64748b; margin-top: 5px;">Siswa yang disetel sebagai Alumni akan lulus dan dinonaktifkan dari sistem kelas reguler.</p>
                        </div>
                        <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.85rem;">Silakan pilih Kelas Tujuan</div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <script>
            var fromTingkat = "<?php echo htmlspecialchars($from_tingkat ?? ''); ?>";
            function filterTujuan() {
                var filterElem = document.querySelector('input[name="tingkat_filter"]:checked');
                if(!filterElem) return;
                var filter = filterElem.value;
                var select = document.getElementById('to_kelas_select');
                var options = select.querySelectorAll('option');
                
                options.forEach(function(opt) {
                    if (opt.value === '' || opt.value === 'ALUMNI') {
                        opt.style.display = '';
                        return;
                    }
                    var optTingkat = opt.getAttribute('data-tingkat');
                    if (filter === 'all') {
                        opt.style.display = '';
                    } else if (filter === 'sama') {
                        opt.style.display = (optTingkat === fromTingkat) ? '' : 'none';
                    } else if (filter === 'beda') {
                        opt.style.display = (optTingkat !== fromTingkat) ? '' : 'none';
                    }
                });
            }
            
            var saAsal = document.getElementById('select-all-asal');
            if (saAsal) {
                saAsal.addEventListener('change', function() {
                    var checkboxes = document.querySelectorAll('.student-checkbox-asal');
                    for (var i = 0; i < checkboxes.length; i++) checkboxes[i].checked = this.checked;
                });
            }
            var saTujuan = document.getElementById('select-all-tujuan');
            if (saTujuan) {
                saTujuan.addEventListener('change', function() {
                    var checkboxes = document.querySelectorAll('.student-checkbox-tujuan');
                    for (var i = 0; i < checkboxes.length; i++) checkboxes[i].checked = this.checked;
                });
            }
            lucide.createIcons();
        </script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }


    public function prosesNaik() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_kelas']) && !empty($_POST['student_ids'])) {
            $to = $_POST['to_kelas'];
            $student_ids = $_POST['student_ids'];
            $ids_str = implode(',', array_map('intval', $student_ids));
            
            $db = Database::connect('core');
            $active_year = \App\Core\AcademicYear::current();
            $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
            
            $active_year_id = $active_year ? intval($active_year['id']) : 1;
            
            if ($to === 'ALUMNI') {
                // Tahun lulus = tahun asal siswa (from_tahun), bukan tahun aktif
                $from_tahun_post = isset($_POST['from_tahun']) ? trim($_POST['from_tahun']) : null;
                
                // Cari id tahun ajaran berdasarkan nama from_tahun
                $lulus_year_id = $active_year_id; // default fallback
                if ($from_tahun_post) {
                    $stmt_yr = $db->prepare("SELECT id FROM tahun_ajaran WHERE name = ? ORDER BY is_active DESC LIMIT 1");
                    $stmt_yr->execute([$from_tahun_post]);
                    $found_yr = $stmt_yr->fetchColumn();
                    if ($found_yr) {
                        $lulus_year_id = intval($found_yr);
                    }
                }
                
                $db->exec("UPDATE siswa SET status = 'Alumni', tahun_lulus_id = $lulus_year_id WHERE id IN ($ids_str)");
            } else {
                $to_kelas_id = intval($to);
                
                // Update kelas_id utama dan tahun_ajaran di tabel siswa
                $db->exec("UPDATE siswa SET kelas_id = $to_kelas_id, tahun_ajaran = '$active_year_name' WHERE id IN ($ids_str)");
                
                // Insert atau Update riwayat_kelas_siswa
                $checkStmt = $db->prepare("SELECT id FROM riwayat_kelas_siswa WHERE siswa_id = ? AND tahun_ajaran_id = ? AND semester = 1");
                $insertStmt = $db->prepare("INSERT INTO riwayat_kelas_siswa (siswa_id, kelas_id, tahun_ajaran_id, semester) VALUES (?, ?, ?, 1)");
                $updateStmt = $db->prepare("UPDATE riwayat_kelas_siswa SET kelas_id = ? WHERE id = ?");
                
                foreach ($student_ids as $sid) {
                    $sid_val = intval($sid);
                    $checkStmt->execute([$sid_val, $active_year_id]);
                    $exists = $checkStmt->fetch();
                    
                    if ($exists) {
                        $updateStmt->execute([$to_kelas_id, $exists['id']]);
                    } else {
                        $insertStmt->execute([$sid_val, $to_kelas_id, $active_year_id]);
                    }
                }
            }
        }
        \App\Services\DataSyncService::runSiswaSync();
        
        $redirectUrl = '/siakad/siswa/naik-kelas';
        $params = [];
        if (isset($_POST['from_tahun'])) $params['from_tahun'] = $_POST['from_tahun'];
        if (isset($_POST['from_kelas'])) $params['from_kelas'] = $_POST['from_kelas'];
        if (isset($_POST['to_kelas'])) $params['to_kelas'] = $_POST['to_kelas'];
        
        if (!empty($params)) {
            $redirectUrl .= '?' . http_build_query($params);
        }
        header("Location: $redirectUrl");
    }

    public function urungkanNaik() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from_kelas']) && isset($_POST['from_tahun']) && !empty($_POST['student_ids'])) {
            $from_kelas = intval($_POST['from_kelas']);
            $from_tahun = trim($_POST['from_tahun']);
            $student_ids = $_POST['student_ids'];
            $ids_str = implode(',', array_map('intval', $student_ids));
            
            $db = Database::connect('core');
            $active_year = \App\Core\AcademicYear::current();
            $active_year_id = $active_year ? intval($active_year['id']) : 1;
            
            // Revert back to origin class and year
            $db->exec("UPDATE siswa SET kelas_id = $from_kelas, tahun_ajaran = '$from_tahun', status = 'Aktif' WHERE id IN ($ids_str)");
            
            // Delete history from the active year since we reverted it
            $db->exec("DELETE FROM riwayat_kelas_siswa WHERE siswa_id IN ($ids_str) AND tahun_ajaran_id = $active_year_id AND semester = 1");
            
            \App\Services\DataSyncService::runSiswaSync();
        }
        
        $redirectUrl = '/siakad/siswa/naik-kelas';
        $params = [];
        if (isset($_POST['from_tahun'])) $params['from_tahun'] = $_POST['from_tahun'];
        if (isset($_POST['from_kelas'])) $params['from_kelas'] = $_POST['from_kelas'];
        if (isset($_POST['to_kelas'])) $params['to_kelas'] = $_POST['to_kelas'];
        
        if (!empty($params)) {
            $redirectUrl .= '?' . http_build_query($params);
        }
        header("Location: $redirectUrl");
    }


    public function alumni() {
        $db = Database::connect('core');
        $selected_tahun = isset($_GET['tahun_id']) ? $_GET['tahun_id'] : null;
        
        $years = $db->query("SELECT * FROM tahun_ajaran ORDER BY name DESC")->fetchAll();
        
        $where = "WHERE s.status = 'Alumni'";
        if ($selected_tahun) {
            $where .= " AND s.tahun_lulus_id = " . intval($selected_tahun);
        }

        $siswas = $db->query("SELECT s.*, u.username, k.nama_kelas, ta.name as tahun_lulus 
                             FROM siswa s 
                             JOIN users u ON s.user_id = u.id 
                             LEFT JOIN kelas k ON s.kelas_id = k.id 
                             LEFT JOIN tahun_ajaran ta ON s.tahun_lulus_id = ta.id
                             $where
                             ORDER BY s.nama ASC")->fetchAll();
        
        $title = "Data Alumni Siswa - SIAKAD";
        ob_start();
        ?>
<div class="siakad-container">
            <?php include __DIR__ . '/../../resources/views/siakad/sidebar.php'; ?>
            <div style="flex: 1; padding: 1.5rem;">
            <?php include __DIR__ . '/../../resources/views/siakad/_siakad_header.php'; ?>
                <div class="z-page-head-row">
                    <div>
                        <h1><i data-lucide="graduation-cap"></i> Database Alumni</h1>
                        <p>Daftar siswa yang telah menyelesaikan masa studi.</p>
                    </div>
                </div>

                <!-- Filter Tahun Lulus -->
                <div class="card" style="margin-bottom: 2rem; background: #f8fafc; padding: 1.2rem; border: 1px solid #e2e8f0;">
                    <form action="" method="GET" style="display: flex; align-items: center; gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                            <i data-lucide="graduation-cap" style="width: 18px; color: #94a3b8;"></i>
                            <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">Angkatan Lulus:</span>
                            <select name="tahun_id" onchange="this.form.submit()" class="input-sm" style="max-width: 250px; background: white;">
                                <option value="">Semua Angkatan</option>
                                <?php foreach($years as $y): ?>
                                    <option value="<?php echo $y['id']; ?>" <?php echo $selected_tahun == $y['id'] ? 'selected' : ''; ?>>
                                        TAHUN <?php echo $y['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="font-size: 0.85rem; color: #94a3b8;">
                            Total <b><?php echo count($siswas); ?></b> Alumni Terdata
                        </div>
                    </form>
                </div>

                <div class="card" style="padding: 0; overflow: hidden; border-top: 4px solid #64748b;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #edf2f7;">
                                <th style="padding: 1rem 1.2rem;">Nama Siswa</th>
                                <th style="padding: 1rem 1.2rem;">NIS</th>
                                <th style="padding: 1rem 1.2rem;">Tahun Lulus</th>
                                <th style="padding: 1rem 1.2rem; text-align: center;">Ijazah</th>
                                <th style="padding: 1rem 1.2rem; text-align: center;">SKL</th>
                                <th style="padding: 1rem 1.2rem; text-align: center;">Upload Dok.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($siswas as $s): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem 1.2rem;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; background: #e2e8f0; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                                            <?php if(!empty($s['foto'])): ?>
                                                <img src="/public/uploads/siswa/<?php echo htmlspecialchars($s['foto']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i data-lucide="user" style="width: 16px; height: 16px; color: #94a3b8;"></i>
                                            <?php endif; ?>
                                        </div>
                                        <span style="font-weight: 700;"><?php echo htmlspecialchars($s['nama']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.2rem; font-family: monospace; font-size: 0.82rem; color: #64748b;"><?php echo htmlspecialchars($s['nis']); ?></td>
                                <td style="padding: 1rem 1.2rem;">
                                    <span style="background: #f1f5f9; color: #64748b; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($s['tahun_lulus'] ?: '-'); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.2rem; text-align: center;">
                                    <?php if(!empty($s['scan_ijazah'])): ?>
                                        <a href="/public/uploads/alumni_docs/<?php echo htmlspecialchars($s['scan_ijazah']); ?>" target="_blank" style="color: #16a34a; font-weight: 700; font-size: 0.78rem;"><i data-lucide="file-check" style="width: 14px; height: 14px; margin-right: 3px;"></i>Ada</a>
                                    <?php else: ?>
                                        <span style="color: #cbd5e1; font-size: 0.78rem;"><i data-lucide="file-x" style="width: 14px; height: 14px;"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 1.2rem; text-align: center;">
                                    <?php if(!empty($s['scan_skl'])): ?>
                                        <a href="/public/uploads/alumni_docs/<?php echo htmlspecialchars($s['scan_skl']); ?>" target="_blank" style="color: #16a34a; font-weight: 700; font-size: 0.78rem;"><i data-lucide="file-check" style="width: 14px; height: 14px; margin-right: 3px;"></i>Ada</a>
                                    <?php else: ?>
                                        <span style="color: #cbd5e1; font-size: 0.78rem;"><i data-lucide="file-x" style="width: 14px; height: 14px;"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 1.2rem; text-align: center;">
                                    <button class="z-btn" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 5px 10px; font-size: 0.75rem; font-weight: 700;" onclick="openUploadModal(<?php echo $s['id']; ?>, '<?php echo addslashes($s['nama']); ?>')"><i data-lucide="upload" style="width: 12px; height: 12px;"></i> Upload</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($siswas)): ?>
                                <tr><td colspan="6" style="padding: 4rem; text-align: center; color: #94a3b8;">
                                    <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 1rem;"></i>
                                    <div>Tidak ditemukan data alumni untuk angkatan ini.</div>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MODAL UPLOAD DOKUMEN -->
                <div id="modal-upload" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; max-width: 440px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
                            <div>
                                <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a;">Upload Dokumen Alumni</h3>
                                <p id="modal-nama" style="font-size: 0.82rem; color: #64748b; margin-top: 3px;"></p>
                            </div>
                            <button onclick="closeUploadModal()" style="background: #f1f5f9; border: none; border-radius: 8px; padding: 6px 10px; cursor: pointer; color: #64748b;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
                        </div>
                        <form method="POST" action="/siakad/siswa/upload-dokumen-alumni" enctype="multipart/form-data">
                            <input type="hidden" name="siswa_id" id="modal-siswa-id">
                            <input type="hidden" name="from_tahun" value="<?php echo htmlspecialchars($selected_tahun ?? ''); ?>">
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">ðŸ“œ Scan Ijazah (PDF / JPG / PNG)</label>
                                <input type="file" name="scan_ijazah" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; border: 1.5px dashed #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.82rem;">
                            </div>
                            <div style="margin-bottom: 1.2rem;">
                                <label style="font-size: 0.78rem; font-weight: 700; color: #475569; display: block; margin-bottom: 6px;">ðŸ“„ Scan SKL (PDF / JPG / PNG)</label>
                                <input type="file" name="scan_skl" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; border: 1.5px dashed #e2e8f0; border-radius: 8px; padding: 10px; font-size: 0.82rem;">
                            </div>
                            <div style="font-size: 0.72rem; color: #94a3b8; margin-bottom: 1rem;"><i data-lucide="info" style="width: 12px; height: 12px;"></i> Kosongkan jika tidak ingin mengubah dokumen yang sudah ada. Maks. 5 MB.</div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" onclick="closeUploadModal()" style="flex: 1; padding: 10px; border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer;">Batal</button>
                                <button type="submit" class="z-btn z-btn-primary" style="flex: 2; padding: 10px; border-radius: 8px; font-size: 0.85rem;"><i data-lucide="upload-cloud" style="width: 14px; height: 14px;"></i> Simpan Dokumen</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <script>
            lucide.createIcons();
            function openUploadModal(id, nama) {
                document.getElementById('modal-siswa-id').value = id;
                document.getElementById('modal-nama').textContent = nama;
                document.getElementById('modal-upload').style.display = 'flex';
            }
            function closeUploadModal() {
                document.getElementById('modal-upload').style.display = 'none';
            }
        </script>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../resources/views/siakad/layout.php';
    }

    public function uploadDokumenAlumni() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['siswa_id'])) {
            redirect('/siakad/siswa/alumni');
            exit;
        }
        $siswa_id = intval($_POST['siswa_id']);
        $db = Database::connect('core');
        $updates = [];
        $upload_dir = __DIR__ . '/../../public/uploads/alumni_docs/';

        foreach (['scan_ijazah', 'scan_skl'] as $field) {
            if (!empty($_FILES[$field]['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) continue;
                if ($_FILES[$field]['size'] > 5 * 1024 * 1024) continue;
                $filename = $field . '_' . $siswa_id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES[$field]['tmp_name'], $upload_dir . $filename);
                $updates[$field] = $filename;
            }
        }

        if (!empty($updates)) {
            $set_parts = [];
            foreach ($updates as $col => $val) {
                $set_parts[] = "$col = " . $db->quote($val);
            }
            $db->exec("UPDATE siswa SET " . implode(', ', $set_parts) . " WHERE id = $siswa_id");
        }

        $tahun_param = !empty($_POST['from_tahun']) ? '?tahun_id=' . urlencode($_POST['from_tahun']) : '';
        redirect('/siakad/siswa/alumni' . $tahun_param . '&success=uploaded');
        exit;
    }

    public function cetakKartuMassal() {
        $db = Database::connect('core');
        
        $active_year = \App\Core\AcademicYear::current();
        $active_year_name = $active_year ? $active_year['name'] : '2025/2026';
        
        $active_year_id = $active_year ? intval($active_year['id']) : 1;
        
        $where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
        if (!empty($_GET['id'])) {
            $where = "WHERE s.id = " . intval($_GET['id']);
        } elseif (!empty($_GET['ids'])) {
            $raw_ids = is_array($_GET['ids']) ? $_GET['ids'] : explode(',', $_GET['ids']);
            $ids = array_map('intval', $raw_ids);
            $ids_str = implode(',', $ids);
            $where = "WHERE s.id IN ($ids_str)";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $ids_str = implode(',', $ids);
            $where = "WHERE s.id IN ($ids_str)";
        } else {
            $selected_kelas = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : null;
            if ($selected_kelas && $selected_kelas !== 'all') {
                $where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = " . intval($selected_kelas);
            }
        }

        $siswas = $db->query("SELECT s.*, u.username, u.plain_password, 
                             COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas 
                             FROM siswa s 
                             LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                                 AND rks.tahun_ajaran_id IN (
                                     SELECT id FROM tahun_ajaran WHERE name = '$active_year_name'
                                 )
                             LEFT JOIN users u ON s.user_id = u.id 
                             LEFT JOIN kelas k ON s.kelas_id = k.id 
                             LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                             $where
                             ORDER BY s.nama ASC")->fetchAll();
                             
        $inst = $db->query("SELECT * FROM institusi LIMIT 1")->fetch();
        $semua_kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'horizontal';

        // Template Cetak (Khusus)
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cetak Kartu - <?php echo $inst ? $inst['nama'] : 'Madrasah'; ?></title>
            <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
            <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
            <script src="<?php echo base_url('public/assets/js/qrcode.min.js'); ?>"></script>
            <style>
                body {
                    margin: 0;
                    padding: 20px;
                    background: #f1f5f9;
                    font-family: 'Plus Jakarta Sans', sans-serif;
                }
                .print-controls {
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: white;
                    padding: 8px 15px;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    z-index: 100;
                }
                .btn-print {
                    background: #0f172a;
                    color: white;
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-weight: 700;
                    font-size: 0.8rem;
                    font-family: inherit;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    box-shadow: 0 2px 4px rgba(15,23,42,0.15);
                }
                .card-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 5mm;
                    width: 210mm;
                    margin: 0 auto;
                }
                .id-card-wrapper {
                    display: flex;
                    flex-direction: row; /* Depan Kiri, Belakang Kanan */
                    gap: 5mm;
                    page-break-inside: avoid;
                    margin-bottom: 5mm;
                    justify-content: center;
                    width: 100%;
                }
                
                <?php if ($mode === 'vertical'): ?>
                .id-card-body {
                    box-sizing: border-box;
                    width: 54mm; 
                    height: 85.6mm; 
                    border-radius: 3mm; 
                    overflow: hidden; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
                    position: relative; 
                    border: 1px solid #e2e8f0; 
                    display: flex; 
                    flex-direction: column;
                    background: radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.1) 0%, transparent 60%), radial-gradient(circle at 0% 100%, rgba(2, 132, 199, 0.05) 0%, transparent 50%), white;
                    flex-shrink: 0;
                }
                <?php else: ?>
                .id-card-body {
                    box-sizing: border-box;
                    width: 85.6mm; 
                    height: 54mm; 
                    border-radius: 3mm; 
                    overflow: hidden; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.15); 
                    position: relative; 
                    border: 1px solid #e2e8f0; 
                    display: flex; 
                    flex-direction: column;
                    background: radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.1) 0%, transparent 60%), radial-gradient(circle at 0% 100%, rgba(2, 132, 199, 0.05) 0%, transparent 50%), white;
                    flex-shrink: 0;
                }
                <?php endif; ?>

                @media print {
                    body {
                        background: white;
                        padding: 0;
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .print-controls { display: none; }
                    .card-container {
                        gap: 2mm; /* Rapatkan gap saat print agar muat 5 baris */
                    }
                    .id-card-wrapper {
                        margin-bottom: 2mm;
                        box-shadow: none !important;
                    }
                    .id-card-body {
                        box-shadow: none !important;
                        border: 0.1mm solid #ddd !important; /* Garis panduan potong solid tipis */
                    }
                    @page {
                        size: A4;
                        margin: 10mm; /* Margin standar untuk A4 */
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-controls">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label style="font-size: 0.7rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Filter Kelas:</label>
                    <select onchange="window.location.href='?kelas_id='+this.value+'&mode=<?php echo $mode; ?>'" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 0.8rem; font-weight: 600; color: #0f172a; outline: none; cursor: pointer;">
                        <option value="">Semua Kelas</option>
                        <?php foreach($semua_kelas as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>><?php echo $k['nama_kelas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="border-left: 2px solid #e2e8f0; padding-left: 12px;">
                    <div style="font-weight: 800; color: #0f172a; font-size: 0.85rem;">Total: <?php echo count($siswas); ?> Kartu</div>
                    <div style="font-size: 0.7rem; color: #64748b;">(<?php echo count($siswas) * 2; ?> Sisi) siap cetak.</div>
                </div>
                
                <div style="display: flex; gap: 8px; margin-left: 5px;">
                    <?php if ($mode === 'vertical'): ?>
                        <button class="btn-print" onclick="window.location.href='?kelas_id=<?php echo $selected_kelas; ?>&mode=horizontal'" style="background: #64748b; color: white;">
                            <i data-lucide="layout-template" style="width: 14px; height: 14px;"></i> HORIZONTAL
                        </button>
                    <?php else: ?>
                        <button class="btn-print" onclick="window.location.href='?kelas_id=<?php echo $selected_kelas; ?>&mode=vertical'" style="background: #64748b; color: white;">
                            <i data-lucide="smartphone" style="width: 14px; height: 14px;"></i> VERTIKAL
                        </button>
                    <?php endif; ?>
                    <?php $isThumb = isset($_GET['thumb']) && $_GET['thumb'] == '1'; ?>
                    <button class="btn-print" onclick="window.location.href='?kelas_id=<?php echo $selected_kelas; ?>&mode=<?php echo $mode; ?>&thumb=<?php echo $isThumb ? '0' : '1'; ?>'" style="background: <?php echo $isThumb ? '#f59e0b' : '#64748b'; ?>; color: white;">
                        <i data-lucide="image" style="width: 14px; height: 14px;"></i> VERSI <?php echo $isThumb ? 'ORIGINAL' : 'THUMBNAIL'; ?>
                    </button>
                    <button class="btn-print" onclick="window.print()">
                        <i data-lucide="printer" style="width: 14px; height: 14px;"></i> CETAK A4 SEKARANG
                    </button>
                </div>
            </div>

            <div class="card-container">
                <?php foreach($siswas as $data): ?>
                    <?php 
                        $alamat = '-';
                        if($data['alamat']) {
                            $alamat = $data['alamat'];
                            if($data['desa']) $alamat .= ', ' . $data['desa'];
                        }
                        
                        $tglFormat = '-';
                        if ($data['tanggal_lahir']) {
                            $parts = explode('-', $data['tanggal_lahir']);
                            if (count($parts) === 3 && $data['tanggal_lahir'] !== '0000-00-00') {
                                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                $monthIndex = (int)$parts[1] - 1;
                                $monthStr = isset($months[$monthIndex]) ? $months[$monthIndex] : $parts[1];
                                $tglFormat = $parts[2] . ' ' . $monthStr . ' ' . $parts[0];
                            } else {
                                $tglFormat = $data['tanggal_lahir'];
                            }
                        }
                        
                        $ttl = '-';
                        if($data['tempat_lahir'] || $data['tanggal_lahir']) {
                            $ttl = ($data['tempat_lahir'] ? $data['tempat_lahir'] . ', ' : '') . ($tglFormat !== '-' ? $tglFormat : '');
                        }
                        
                        $useThumb = isset($_GET['thumb']) && $_GET['thumb'] == '1';
                        if ($useThumb) {
                            $foto = $data['foto'] ? '/thumb.php?src=uploads/siswa/' . $data['foto'] . '&w=300&h=300' : 'https://ui-avatars.com/api/?name=' . urlencode($data['nama']) . '&background=065f46&color=fff';
                        } else {
                            $foto = $data['foto'] ? '/uploads/siswa/' . $data['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($data['nama']) . '&background=065f46&color=fff';
                        }
                        
                        $tglCetak = date('d M Y');
                        $kotaCetak = $inst ? $inst['kota'] : 'Jakarta';
                        $namaKepala = $inst ? $inst['nama_kepala'] : 'Kepala Sekolah';
                        $thn = date('Y');
                        
                        // QR TTD: Pengesahan
                        $txtTTD = "Telah disahkan oleh $namaKepala di $kotaCetak pada $tglCetak";
                        $qr_ttd_data = $txtTTD;
                        
                        // QR Belakang: Token Login
                        $nisn_nis = ($data['nisn'] ? $data['nisn'] . '/' : '') . $data['nis'];
                        $qr_token = "TOKEN:" . $data['user_id'] . ":" . substr(md5($data['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
                        $txtSiswa = $qr_token;
                        $qr_absen_data = $txtSiswa;
                        
                        $username = $data['username'] ?: $data['nis'];
                    ?>
                    
                    <div class="id-card-wrapper">
                        <!-- SISI DEPAN -->
                        <div class="id-card-body">
                            <!-- Watermark -->
                            <?php if($inst && $inst['logo']): ?>
                                <?php if ($mode === 'vertical'): ?>
                                    <img src="/uploads/logo/<?php echo $inst['logo']; ?>" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 40mm; height: 40mm; object-fit: contain; opacity: 0.08; z-index: 1;">
                                <?php else: ?>
                                    <img src="/uploads/logo/<?php echo $inst['logo']; ?>" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 35mm; height: 35mm; object-fit: contain; opacity: 0.1; z-index: 1;">
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($mode === 'vertical'): ?>
                                <!-- VERTIKAL SISI DEPAN -->
                                <div style="height: 18mm; background: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1440 320\'%3E%3Cpath fill=\'%23ffffff\' fill-opacity=\'0.25\' d=\'M0,128L48,144C96,160,192,192,288,197.3C384,203,480,181,576,149.3C672,117,768,75,864,64C960,53,1056,75,1152,106.7C1248,139,1344,181,1392,202.7L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z\'%3E%3C/path%3E%3C/svg%3E'), linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%); background-size: cover; background-position: center; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2mm; color: white; border-bottom: 1.5px solid #075985; flex-shrink: 0; z-index: 2; position: relative;">
                                    <?php if($inst && $inst['logo']): ?>
                                        <img src="/uploads/logo/<?php echo $inst['logo']; ?>" style="width: 8mm; height: 8mm; object-fit: contain; margin-bottom: 1mm; flex-shrink: 0;">
                                    <?php else: ?>
                                        <div style="width: 8mm; height: 8mm; display: flex; align-items: center; justify-content: center; margin-bottom: 1mm; flex-shrink: 0;"><i data-lucide="graduation-cap" style="width: 6mm;"></i></div>
                                    <?php endif; ?>
                                    <div style="text-align: center; line-height: 1.2;">
                                        <div style="font-weight: 800; font-size: 8px; letter-spacing: 1px;">KARTU PELAJAR</div>
                                        <div style="font-weight: 700; font-size: 7px; opacity: 0.95; text-transform: uppercase;"><?php echo $inst ? $inst['nama'] : 'GH-SSS ACADEMY'; ?></div>
                                    </div>
                                </div>
                                <div style="flex: 1; padding: 2mm 3mm; display: flex; flex-direction: column; align-items: center; position: relative; z-index: 2;">
                                    <div style="width: 20mm; height: 26mm; border: 1px solid #cbd5e1; border-radius: 1mm; overflow: hidden; padding: 0.8mm; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2mm;">
                                        <img src="<?php echo $foto; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5mm;">
                                    </div>
                                    <div style="width: 100%; text-align: center; margin-bottom: 2mm;">
                                        <div style="font-weight: 800; color: #0f172a; font-size: 9px; line-height: 1.2; margin-bottom: 1mm;"><?php echo htmlspecialchars($data['nama']); ?></div>
                                        <div style="font-weight: 700; color: #475569; font-size: 7px;">NIS: <?php echo htmlspecialchars($data['nis']) . ($data['nisn'] ? ' / ' . htmlspecialchars($data['nisn']) : ''); ?></div>
                                        <div style="font-weight: 700; color: #475569; font-size: 6.5px;"><?php echo $ttl; ?></div>
                                        <div style="font-weight: 700; color: #475569; font-size: 6px; margin-top: 0.5mm; line-height: 1.2; padding: 0 2mm;"><?php echo htmlspecialchars($alamat); ?></div>
                                    </div>
                                    <div style="display: flex; flex-direction: row; align-items: flex-end; justify-content: space-between; width: 100%; margin-top: auto;">
                                        <div style="text-align: left; max-width: 30mm;">
                                            <div style="font-size: 5px; color: #475569; line-height: 1.4;"><?php echo $inst ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?><br>Kepala Madrasah</div>
                                            <div style="font-size: 5.5px; font-weight: 800; color: #0f172a; margin-top: 3.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo $inst ? $inst['nama_kepala'] : 'Kepala Sekolah'; ?></div>
                                            <div style="font-size: 5px; color: #475569; margin-top: 0.5mm;">NIP. <?php echo $inst ? $inst['nip_kepala'] : '-'; ?></div>
                                        </div>
                                        <div id="qr-ttd-<?php echo $data['id']; ?>" style="width: 12mm; height: 12mm; flex-shrink: 0;"></div>
                                        <script>
                                            new QRCode(document.getElementById("qr-ttd-<?php echo $data['id']; ?>"), {
                                                text: "<?php echo addslashes($qr_ttd_data); ?>",
                                                width: 60,
                                                height: 60,
                                                correctLevel : QRCode.CorrectLevel.L
                                            });
                                            setTimeout(() => {
                                                let qrEl = document.getElementById("qr-ttd-<?php echo $data['id']; ?>");
                                                if(qrEl) {
                                                    let img = qrEl.querySelector('img');
                                                    if(img) img.style.width = '100%';
                                                    let canvas = qrEl.querySelector('canvas');
                                                    if(canvas) canvas.style.display = 'none';
                                                }
                                            }, 200);
                                        </script>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- HORIZONTAL SISI DEPAN -->
                                <!-- Header Bar -->
                                <div style="height: 12.5mm; background: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1440 320\'%3E%3Cpath fill=\'%23ffffff\' fill-opacity=\'0.25\' d=\'M0,128L48,144C96,160,192,192,288,197.3C384,203,480,181,576,149.3C672,117,768,75,864,64C960,53,1056,75,1152,106.7C1248,139,1344,181,1392,202.7L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z\'%3E%3C/path%3E%3C/svg%3E'), linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%); background-size: cover; background-position: center; display: flex; align-items: center; padding: 0 4mm; color: white; border-bottom: 1.5px solid #075985; flex-shrink: 0; z-index: 2; position: relative;">
                                    <?php if($inst && $inst['logo']): ?>
                                        <img src="/uploads/logo/<?php echo $inst['logo']; ?>" style="width: 10mm; height: 10mm; object-fit: contain; margin-right: 3mm; flex-shrink: 0;">
                                    <?php else: ?>
                                        <div style="width: 10mm; height: 10mm; display: flex; align-items: center; justify-content: center; margin-right: 3mm; flex-shrink: 0;"><i data-lucide="graduation-cap" style="width: 7.5mm;"></i></div>
                                    <?php endif; ?>
                                    <div style="text-align: left; line-height: 1.2; flex: 1; min-width: 0;">
                                        <div style="font-weight: 800; font-size: 10px; letter-spacing: 1px; white-space: nowrap;">KARTU PELAJAR</div>
                                        <div style="font-weight: 700; font-size: 8.5px; opacity: 0.95; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $inst ? $inst['nama'] : 'GH-SSS ACADEMY'; ?></div>
                                    </div>
                                </div>
                                
                                <!-- Body: Foto besar kiri, Data+QR kanan -->
                                <div style="flex: 1; padding: 2mm 4mm; display: flex; gap: 3mm; position: relative; z-index: 2;">
                                    <!-- KIRI: Foto besar (full tinggi) -->
                                    <div style="width: 22mm; flex-shrink: 0; border: 1px solid #cbd5e1; border-radius: 1mm; overflow: hidden; padding: 0.8mm; background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); align-self: stretch;">
                                        <img src="<?php echo $foto; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5mm;">
                                    </div>
                                    <!-- KANAN: Data atas, QR TTD bawah -->
                                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                        <table style="width: 100%; font-size: 7.5px; border-collapse: collapse; line-height: 1.5;">
                                            <tr>
                                                <td style="font-weight: 700; width: 17mm; color: #475569; vertical-align: top;">Nama</td>
                                                <td style="width: 2mm; vertical-align: top;">:</td>
                                                <td style="vertical-align: top; max-width: 38mm; overflow: hidden; white-space: nowrap;">
                                                    <div class="auto-fit-text" style="font-weight: 800; color: #0f172a; font-size: 8px;"><?php echo htmlspecialchars($data['nama']); ?></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 700; color: #475569; vertical-align: top;">NIS / NISN</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo htmlspecialchars($data['nis']) . ($data['nisn'] ? ' / ' . htmlspecialchars($data['nisn']) : ''); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tempat Lahir</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo $data['tempat_lahir'] ?: '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Tanggal Lahir</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo $tglFormat; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight: 700; color: #475569; vertical-align: top;">Alamat</td>
                                                <td style="vertical-align: top;">:</td>
                                                <td style="font-weight: 700; color: #1e293b; vertical-align: top;"><?php echo htmlspecialchars($alamat); ?></td>
                                            </tr>
                                        </table>
                                        <!-- QR TTD + Pengesahan berdampingan di bawah -->
                                        <div style="display: flex; align-items: flex-end; gap: 2mm;">
                                            <div id="qr-ttd-<?php echo $data['id']; ?>" style="width: 12mm; height: 12mm; flex-shrink: 0;"></div>
                                            <script>
                                                new QRCode(document.getElementById("qr-ttd-<?php echo $data['id']; ?>"), {
                                                    text: "<?php echo addslashes($qr_ttd_data); ?>",
                                                    width: 60,
                                                    height: 60,
                                                    correctLevel : QRCode.CorrectLevel.L
                                                });
                                                setTimeout(() => {
                                                    let qrEl = document.getElementById("qr-ttd-<?php echo $data['id']; ?>");
                                                    if(qrEl) {
                                                        let img = qrEl.querySelector('img');
                                                        if(img) img.style.width = '100%';
                                                        let canvas = qrEl.querySelector('canvas');
                                                        if(canvas) canvas.style.display = 'none';
                                                    }
                                                }, 200);
                                            </script>
                                            <div style="text-align: left;">
                                                <div style="font-size: 5px; color: #475569; line-height: 1.4;"><?php echo $inst ? $inst['kota'] : 'Jakarta'; ?>, <?php echo date('d M Y'); ?><br>Mengetahui, Kepala Madrasah</div>
                                                <div style="font-size: 5.5px; font-weight: 800; color: #0f172a; margin-top: 2.5mm; border-bottom: 0.5px solid #0f172a; display: inline-block; padding-bottom: 0.5px;"><?php echo $inst ? $inst['nama_kepala'] : 'Kepala Sekolah'; ?></div>
                                                <div style="font-size: 5px; color: #475569; margin-top: 0.5mm;">NIP. <?php echo $inst ? $inst['nip_kepala'] : '-'; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div style="height: 1.5mm; background: #0284c7; width: 100%; flex-shrink: 0; position: relative; z-index: 2;"></div>
                        </div>
                        
                        <!-- SISI BELAKANG -->
                        <div class="id-card-body" style="background: white; color: #0f172a; padding: 4mm; align-items: center; justify-content: center; <?php echo $mode === 'vertical' ? 'flex-direction: column;' : ''; ?>">
                            <div style="width: 100%; text-align: center; margin-bottom: 3mm;">
                                <div style="font-weight: 800; font-size: <?php echo $mode === 'vertical' ? '9px' : '10px'; ?>; letter-spacing: 1px; color: #0284c7;">KARTU PELAJAR ELEKTRONIK</div>
                                <div style="font-size: 6px; color: #64748b; margin-top: 1mm;">Gunakan QR Code ini untuk melihat data identitas siswa</div>
                            </div>
                            
                            <div style="background: white; padding: 2mm; border-radius: 2mm; margin-bottom: 3mm; border: 1px solid #cbd5e1;">
                                <div id="qr-absen-<?php echo $data['id']; ?>" style="<?php echo $mode === 'vertical' ? 'width: 30mm; height: 30mm;' : 'width: 25mm; height: 25mm;'; ?>"></div>
                                <script>
                                    new QRCode(document.getElementById("qr-absen-<?php echo $data['id']; ?>"), {
                                        text: <?php echo json_encode($qr_absen_data); ?>,
                                        width: 100,
                                        height: 100,
                                        correctLevel : QRCode.CorrectLevel.M
                                    });
                                    setTimeout(() => {
                                        let qrEl = document.getElementById("qr-absen-<?php echo $data['id']; ?>");
                                        if(qrEl) {
                                            let img = qrEl.querySelector('img');
                                            if(img) img.style.width = '100%';
                                            let canvas = qrEl.querySelector('canvas');
                                            if(canvas) canvas.style.display = 'none';
                                        }
                                    }, 200);
                                </script>
                            </div>
                            

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <script>
                lucide.createIcons();
                // Otomatis buka jendela print setelah gambar selesai dimuat
                window.onload = function() {
                    setTimeout(function(){
                        window.print();
                    }, 1000);
                }
            </script>
        </body>
        </html>
        <?php
    }

    public function updateFotoSiswa() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $db = Database::connect('core');
                
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    // Get old photo to delete
                    $old = $db->query("SELECT foto FROM siswa WHERE id = " . intval($id))->fetch();
                    if ($old && $old['foto']) {
                        @unlink(__DIR__ . "/../../public/uploads/siswa/" . $old['foto']);
                    }

                    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $foto_name = "siswa_" . time() . "." . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . "/../../public/uploads/siswa/" . $foto_name);
                    
                    $stmt = $db->prepare("UPDATE siswa SET foto = ? WHERE id = ?");
                    $stmt->execute([$foto_name, $id]);
                    
                    echo json_encode(['success' => true, 'foto' => $foto_name]);
                    exit;
                }
                echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diunggah']);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }
}




