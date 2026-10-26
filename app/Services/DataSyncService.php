<?php

namespace App\Services;

use App\Core\Database;

class DataSyncService {

    /**
     * Sinkronisasi data satu siswa atau semua siswa dari SIAKAD ke CBT
     * @param int|null $user_id ID dari tabel users (core_db)
     */
    public static function syncSiswaToCbt($user_id = null) {
        try {
            $dbCore = Database::connect('core');
            $dbCbt  = Database::connect('cbt');

            $query = "
                SELECT s.nama, s.nis, s.tanggal_lahir, s.tempat_lahir, s.kelas_id, s.foto,
                       u.username, u.plain_password, u.password_hash, u.id as user_id,
                       k.nama_kelas, s.status
                FROM siswa s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN kelas k ON s.kelas_id = k.id
                WHERE u.role_id = 3
            ";

            if ($user_id !== null) {
                $query .= " AND u.id = " . intval($user_id);
            }

            $siswaSiakad = $dbCore->query($query)->fetchAll();
            if (empty($siswaSiakad)) return;

            // Ambil mapping grup kelas di CBT
            $cbtGrups = $dbCbt->query("SELECT grup_id, grup_nama FROM cbt_user_grup")->fetchAll();
            $grupMap = array();
            foreach ($cbtGrups as $g) {
                $grupMap[strtolower(trim($g['grup_nama']))] = $g['grup_id'];
            }

            $dbCbt->exec('SET FOREIGN_KEY_CHECKS=0');

            foreach ($siswaSiakad as $s) {
                $username = $s['username'];
                if (empty($username)) continue;

                // Jika status di SIAKAD bukan 'Aktif', maka hapus dari CBT
                if (strtolower($s['status']) !== 'aktif') {
                    $stmt = $dbCbt->prepare("DELETE FROM cbt_user WHERE user_name = ? AND user_level = 1");
                    $stmt->execute([$username]);
                    continue;
                }

                // Pastikan grup kelas ada di CBT
                $grupId = 0;
                if (!empty($s['nama_kelas'])) {
                    $kelasKey = strtolower(trim($s['nama_kelas']));
                    if (!isset($grupMap[$kelasKey])) {
                        try {
                            $stmt = $dbCbt->prepare("INSERT INTO cbt_user_grup (grup_nama) VALUES (?)");
                            $stmt->execute(array(strtoupper(trim($s['nama_kelas']))));
                            $newId = $dbCbt->lastInsertId();
                            $grupMap[$kelasKey] = $newId;
                            $grupId = $newId;
                        } catch (\Exception $e) {
                            $grupId = 0;
                        }
                    } else {
                        $grupId = $grupMap[$kelasKey];
                    }
                }

                $plainPwd  = $s['plain_password'] ? $s['plain_password'] : $username;
                $finalPwd  = $plainPwd; // GHCBT uses plain text
                
                $tempatLahir = $s['tempat_lahir'] ? $s['tempat_lahir'] : '';
                $tglLahir    = $s['tanggal_lahir'] ? $s['tanggal_lahir'] : null;
                $foto        = $s['foto'] ? $s['foto'] : '';

                if (!empty($foto)) {
                    $src = __DIR__ . '/../../public/uploads/siswa/' . $foto;
                    $dst = __DIR__ . '/../../public/GHCBT/public/uploads/siswa/' . $foto;
                    if (file_exists($src)) {
                        if (!is_dir(dirname($dst))) mkdir(dirname($dst), 0777, true);
                        @copy($src, $dst);
                    }
                }

                // Check if exists
                $stmt = $dbCbt->prepare("SELECT user_name FROM cbt_user WHERE user_name = ? AND user_level = 1");
                $stmt->execute([$username]);
                $exists = $stmt->fetch();

                if (!$exists) {
                    $stmt = $dbCbt->prepare("
                        INSERT INTO cbt_user
                            (user_name, user_password, user_firstname, user_grup_id, user_level,
                             user_birth_place, user_birth_date, user_foto, user_login)
                        VALUES (?, ?, ?, ?, 1, ?, ?, ?, 0)
                    ");
                    $stmt->execute(array($username, $finalPwd, $s['nama'], $grupId, $tempatLahir, $tglLahir, $foto));
                } else {
                    $stmt = $dbCbt->prepare("
                        UPDATE cbt_user
                        SET user_firstname   = ?,
                            user_grup_id     = ?,
                            user_birth_place = ?,
                            user_birth_date  = ?,
                            user_foto        = ?,
                            user_password    = ?
                        WHERE user_name = ? AND user_level = 1
                    ");
                    $stmt->execute(array($s['nama'], $grupId, $tempatLahir, $tglLahir, $foto, $finalPwd, $username));
                }
            }
            $dbCbt->exec('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            error_log("DataSyncService Error (syncSiswaToCbt): " . $e->getMessage());
        }
    }

    /**
     * Hapus siswa dari CBT berdasarkan username
     */
    public static function deleteSiswaFromCbt($username) {
        if (empty($username)) return;
        try {
            $dbCbt = Database::connect('cbt');
            $stmt = $dbCbt->prepare("DELETE FROM cbt_user WHERE user_name = ? AND user_level = 1");
            $stmt->execute([$username]);
        } catch (\Exception $e) {
            error_log("DataSyncService Error (deleteSiswaFromCbt): " . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi data satu guru atau semua guru dari SIAKAD ke CBT (tabel user)
     * @param int|null $user_id ID dari tabel users (core_db)
     */
    public static function syncGuruToCbt($user_id = null) {
        try {
            $dbCore = Database::connect('core');
            $dbCbt  = Database::connect('cbt');

            $query = "
                SELECT g.nama, u.username, u.plain_password, u.status
                FROM guru g
                JOIN users u ON g.user_id = u.id
                WHERE u.role_id = 2
            ";

            if ($user_id !== null) {
                $query .= " AND u.id = " . intval($user_id);
            }

            $guruSiakad = $dbCore->query($query)->fetchAll();
            if (empty($guruSiakad)) return;

            foreach ($guruSiakad as $g) {
                $uname = $g['username'];
                if (empty($uname)) continue;

                if (strtolower($g['status']) !== 'active' && strtolower($g['status']) !== 'aktif') {
                    $stmt = $dbCbt->prepare("DELETE FROM user WHERE username = ?");
                    $stmt->execute([$uname]);
                    continue;
                }

                $nama  = $g['nama'];
                $pass  = sha1($g['plain_password'] ?: '123456');

                $stmt = $dbCbt->prepare("SELECT id FROM user WHERE username = ?");
                $stmt->execute([$uname]);
                $existing = $stmt->fetch();

                if ($existing) {
                    $dbCbt->prepare("UPDATE user SET nama = ?, password = ?, level = 'guru' WHERE id = ?")
                          ->execute([$nama, $pass, $existing['id']]);
                } else {
                    $dbCbt->prepare("INSERT INTO user (username, password, nama, level, opsi1, opsi2) VALUES (?, ?, ?, 'guru', 'default', 'default')")
                          ->execute([$uname, $pass, $nama]);
                }
            }
        } catch (\Exception $e) {
            error_log("DataSyncService Error (syncGuruToCbt): " . $e->getMessage());
        }
    }

    /**
     * Hapus guru dari CBT berdasarkan username
     */
    public static function deleteGuruFromCbt($username) {
        if (empty($username)) return;
        try {
            $dbCbt = Database::connect('cbt');
            $stmt = $dbCbt->prepare("DELETE FROM user WHERE username = ?");
            $stmt->execute([$username]);
        } catch (\Exception $e) {
            error_log("DataSyncService Error (deleteGuruFromCbt): " . $e->getMessage());
        }
    }

    public static function runSiswaSync($user_id = null) {
        self::syncSiswaToCbt($user_id);
        self::syncSiswaToAbsen($user_id);
        self::syncSiswaToSiakadDB($user_id);
    }

    public static function runGuruSync($user_id = null) {
        self::syncGuruToCbt($user_id);
        self::syncGuruToAbsen($user_id);
    }

    public static function deleteSiswaSync($username) {
        self::deleteSiswaFromCbt($username);
        self::deleteSiswaFromAbsen($username);
    }

    public static function deleteGuruSync($username) {
        self::deleteGuruFromCbt($username);
        self::deleteGuruFromAbsen($username);
    }

    public static function syncSiswaToAbsen($user_id = null) {
        try {
            $dbCore = Database::connect('core');
            $dbAbsen = Database::connect('absen'); // We need to ensure Database::connect('absen') works

            $query = "SELECT s.nama, s.nis, s.tanggal_lahir, s.tempat_lahir, k.nama_kelas, s.foto, u.username, u.status
                     FROM siswa s
                     JOIN users u ON s.user_id = u.id
                     LEFT JOIN kelas k ON s.kelas_id = k.id
                     WHERE u.role_id = 3";
            if ($user_id !== null) { $query .= " AND u.id = " . intval($user_id); }

            $siswaSiakad = $dbCore->query($query)->fetchAll();
            if (empty($siswaSiakad)) return;

            foreach ($siswaSiakad as $s) {
                if (strtolower($s['status']) !== 'aktif') {
                    self::deleteSiswaFromAbsen($s['username']);
                    continue;
                }
                $kelas = $s['nama_kelas'] ? $s['nama_kelas'] : 'Belum Ada Kelas';
                $stmt = $dbAbsen->prepare("SELECT id FROM users WHERE qr_code = ? AND tipe = 'siswa'");
                $stmt->execute([$s['nis']]);
                $exists = $stmt->fetch();

                if ($exists) {
                    $dbAbsen->prepare("UPDATE users SET nama=?, kelas_mapel=?, foto=? WHERE id=?")
                            ->execute([$s['nama'], $kelas, $s['foto'], $exists['id']]);
                } else {
                    $dbAbsen->prepare("INSERT INTO users (nama, tipe, kelas_mapel, qr_code, foto) VALUES (?, 'siswa', ?, ?, ?)")
                            ->execute([$s['nama'], $kelas, $s['nis'], $s['foto']]);
                }
            }
        } catch (\Exception $e) {
            error_log("DataSyncService Error (syncSiswaToAbsen): " . $e->getMessage());
        }
    }

    public static function syncGuruToAbsen($user_id = null) {
        try {
            $dbCore = Database::connect('core');
            $dbAbsen = Database::connect('absen');

            $query = "SELECT g.nama, g.nip, g.foto, u.username, u.status
                     FROM guru g
                     JOIN users u ON g.user_id = u.id
                     WHERE u.role_id = 2";
            if ($user_id !== null) { $query .= " AND u.id = " . intval($user_id); }

            $guruSiakad = $dbCore->query($query)->fetchAll();
            if (empty($guruSiakad)) return;

            foreach ($guruSiakad as $g) {
                if (strtolower($g['status']) !== 'active' && strtolower($g['status']) !== 'aktif') {
                    self::deleteGuruFromAbsen($g['username']);
                    continue;
                }
                
                $nip = $g['nip'] ? $g['nip'] : $g['username'];
                $stmt = $dbAbsen->prepare("SELECT id FROM users WHERE qr_code = ? AND tipe = 'guru'");
                $stmt->execute([$nip]);
                $exists = $stmt->fetch();

                if ($exists) {
                    $dbAbsen->prepare("UPDATE users SET nama=?, foto=? WHERE id=?")
                            ->execute([$g['nama'], $g['foto'], $exists['id']]);
                } else {
                    $dbAbsen->prepare("INSERT INTO users (nama, tipe, kelas_mapel, qr_code, foto) VALUES (?, 'guru', 'Guru', ?, ?)")
                            ->execute([$g['nama'], $nip, $g['foto']]);
                }
            }
        } catch (\Exception $e) {
            error_log("DataSyncService Error (syncGuruToAbsen): " . $e->getMessage());
        }
    }

    public static function deleteSiswaFromAbsen($username) {
        if (empty($username)) return;
        try {
            $dbCore = Database::connect('core');
            $nisData = $dbCore->prepare("SELECT s.nis FROM siswa s JOIN users u ON s.user_id = u.id WHERE u.username = ?");
            $nisData->execute([$username]);
            $s = $nisData->fetch();
            if($s && $s['nis']) {
                $dbAbsen = Database::connect('absen');
                $dbAbsen->prepare("DELETE FROM users WHERE qr_code = ? AND tipe = 'siswa'")->execute([$s['nis']]);
            }
        } catch (\Exception $e) { }
    }

    public static function deleteGuruFromAbsen($username) {
        if (empty($username)) return;
        try {
            $dbCore = Database::connect('core');
            $nipData = $dbCore->prepare("SELECT g.nip FROM guru g JOIN users u ON g.user_id = u.id WHERE u.username = ?");
            $nipData->execute([$username]);
            $g = $nipData->fetch();
            if($g && $g['nip']) {
                $dbAbsen = Database::connect('absen');
                $dbAbsen->prepare("DELETE FROM users WHERE qr_code = ? AND tipe = 'guru'")->execute([$g['nip']]);
            }
        } catch (\Exception $e) { }
    }

    public static function syncSiswaToSiakadDB($user_id = null) {
        try {
            $dbCore = Database::connect('core');
            $siakad_db = new \PDO("mysql:host=127.0.0.1;dbname=aula_siakad_db;charset=utf8mb4", 'root', '');
            $siakad_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            $query = "SELECT * FROM siswa";
            if ($user_id !== null) { $query .= " WHERE user_id = " . intval($user_id); }
            
            $siswas = $dbCore->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($siswas)) return;
            
            foreach ($siswas as $s) {
                $chk = $siakad_db->prepare("SELECT id FROM siswa WHERE id = ?");
                $chk->execute([$s['id']]);
                if ($chk->fetch()) {
                    $upd = $siakad_db->prepare("UPDATE siswa SET nis = ?, nisn = ?, nama_lengkap = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat = ?, kelas_id = ?, status = ?, agama = ?, desa_kelurahan = ?, kecamatan = ?, kabupaten_kota = ?, provinsi = ?, kode_pos = ?, nama_ayah = ?, pekerjaan_ayah = ?, nama_ibu = ?, pekerjaan_ibu = ?, nama_wali = ?, foto_siswa = ? WHERE id = ?");
                    $upd->execute([ $s['nis'], $s['nisn'], $s['nama'], $s['jenis_kelamin'], $s['tempat_lahir'], $s['tanggal_lahir'], $s['alamat'], $s['kelas_id'], $s['status'], $s['agama'], $s['desa'], $s['kecamatan'], $s['kota'], $s['provinsi'], $s['kode_pos'], $s['nama_ayah'], $s['pekerjaan_ayah'], $s['nama_ibu'], $s['pekerjaan_ibu'], $s['nama_wali'], $s['foto'], $s['id'] ]);
                } else {
                    $ins = $siakad_db->prepare("INSERT INTO siswa (id, nis, nisn, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, kelas_id, status, agama, desa_kelurahan, kecamatan, kabupaten_kota, provinsi, kode_pos, nama_ayah, pekerjaan_ayah, nama_ibu, pekerjaan_ibu, nama_wali, foto_siswa, tahun_masuk) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $ins->execute([ $s['id'], $s['nis'], $s['nisn'], $s['nama'], $s['jenis_kelamin'], $s['tempat_lahir'], $s['tanggal_lahir'], $s['alamat'], $s['kelas_id'], $s['status'], $s['agama'], $s['desa'], $s['kecamatan'], $s['kota'], $s['provinsi'], $s['kode_pos'], $s['nama_ayah'], $s['pekerjaan_ayah'], $s['nama_ibu'], $s['pekerjaan_ibu'], $s['nama_wali'], $s['foto'], $s['tahun_ajaran'] ]);
                }
            }
        } catch (\Exception $e) {}
    }
}
