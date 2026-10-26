<?php

namespace App\Controllers;

use App\Core\Database;

class KurikulumMonitoringJurnalController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Hanya Admin/Kepala Madrasah yang bisa melihat Jurnal Global di SIAKAD
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 99])) {
            redirect('/login');
            exit;
        }

        if (isset($_GET['cetak']) && $_GET['cetak'] == 1) {
            $this->cetak();
            exit;
        }

        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
        $filter_tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-d');
        $filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_opsi_cetak = isset($_GET['opsi_cetak']) ? $_GET['opsi_cetak'] : 'buku_jurnal_kelas';

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel")->fetchAll();
        } catch (\Exception $e) {
            try {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            } catch (\Exception $e) {}
        }
        
        // Merge dengan mapel dari db_core untuk antisipasi data terpisah
        try {
            $coreMapelList = $db_core->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            foreach($coreMapelList as $cm) {
                $exists = false;
                foreach($mapelList as $ml) {
                    if($ml['id'] == $cm['id']) { $exists = true; break; }
                }
                if(!$exists) {
                    $mapelList[] = $cm;
                }
            }
        } catch (\Exception $e) {}
        $mapelMap = [];
        foreach($mapelList as $m) $mapelMap[$m['id']] = $m['nama_mapel'];
        
        $kelasMap = [];
        foreach($kelasList as $k) $kelasMap[$k['id']] = $k['nama_kelas'];
        
        $guruList = $db_core->query("SELECT id, nama FROM guru ORDER BY nama")->fetchAll();
        $guruMap = [];
        foreach($guruList as $g) $guruMap[$g['id']] = $g['nama'];

        $is_filtered = isset($_GET['tanggal_mulai']) || isset($_GET['kelas_id']) || isset($_GET['guru_id']);
        $jurnal_global = false;
        
        if ($is_filtered) {

        // Ambil Jurnal Global yang ada
        $query = "SELECT MIN(j.id) as id, j.guru_id, j.kelas_id, j.mapel_id, j.tanggal, j.materi, j.keterangan, MIN(j.created_at) as created_at FROM jurnal_guru j WHERE j.tanggal >= :tgl_mulai AND j.tanggal <= :tgl_sampai";
        $params = [
            'tgl_mulai' => $filter_tanggal_mulai,
            'tgl_sampai' => $filter_tanggal_sampai
        ];
        
        if ($filter_kelas > 0) {
            $query .= " AND j.kelas_id = :kid";
            $params['kid'] = $filter_kelas;
        }
        if ($filter_guru > 0) {
            $query .= " AND j.guru_id = :gid";
            $params['gid'] = $filter_guru;
        }
        
        $query .= " GROUP BY j.guru_id, j.kelas_id, j.mapel_id, j.tanggal, j.materi, j.keterangan ORDER BY j.tanggal DESC, id DESC";

        $stmt = $db_siakad->prepare($query);
        $stmt->execute($params);
        $jurnal_db = $stmt->fetchAll();
        
        $jurnal_map = [];
        foreach($jurnal_db as $jd) {
            $key = $jd['tanggal'].'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
            $jurnal_map[$key] = $jd;
        }
        
        // Ambil jadwal aktif
        $active_tahun_id = 1;
        $active_semester = 1;
        try {
            $stmtTahun = $db_core->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            if ($thn = $stmtTahun->fetch()) {
                $active_tahun_id = $thn['id'];
                $active_semester = $thn['semester'];
            }
        } catch (\Exception $e) {}
        
        $jadwal_query = "SELECT * FROM jadwal_pelajaran WHERE tahun_ajaran_id = :ta AND semester = :sm";
        $j_params = ['ta' => $active_tahun_id, 'sm' => $active_semester];
        if ($filter_kelas > 0) {
            $jadwal_query .= " AND kelas_id = :kid";
            $j_params['kid'] = $filter_kelas;
        }
        if ($filter_guru > 0) {
            $jadwal_query .= " AND guru_id = :gid";
            $j_params['gid'] = $filter_guru;
        }
        $stmt_j = $db_siakad->prepare($jadwal_query);
        $stmt_j->execute($j_params);
        $jadwals = $stmt_j->fetchAll();
        
        $temp_jadwal = [];
        foreach($jadwals as $jdw) {
            $key_j = $jdw['hari'] . '_' . $jdw['kelas_id'] . '_' . $jdw['jam_mulai'];
            if(!isset($temp_jadwal[$key_j]) || $jdw['id'] > $temp_jadwal[$key_j]['id']) {
                $temp_jadwal[$key_j] = $jdw;
            }
        }
        
        $jadwal_by_hari = [];
        foreach($temp_jadwal as $jdw) {
            $hari = $jdw['hari']; // Senin, Selasa, dst
            if(!isset($jadwal_by_hari[$hari])) $jadwal_by_hari[$hari] = [];
            $jadwal_by_hari[$hari][] = $jdw;
        }
        
        $hari_indo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        
        $jurnal_global = [];
        
        // Fetch Kalender Akademik for this range
        $stmt_k = $db_siakad->prepare("SELECT * FROM kalender_akademik WHERE (tanggal_mulai <= :ts AND tanggal_selesai >= :tm)");
        $stmt_k->execute(['tm' => $filter_tanggal_mulai, 'ts' => $filter_tanggal_sampai]);
        $kalenders = $stmt_k->fetchAll();
        $kalender_map = [];
        foreach($kalenders as $kal) {
            $curr = strtotime($kal['tanggal_mulai']);
            $end = strtotime($kal['tanggal_selesai']);
            while($curr <= $end) {
                $d = date('Y-m-d', $curr);
                $kalender_map[$d] = $kal;
                $curr = strtotime('+1 day', $curr);
            }
        }
        
        // Loop tanggal dari mulai sampai akhir
        $current_date = strtotime($filter_tanggal_mulai);
        $end_date = strtotime($filter_tanggal_sampai);
        
        while ($current_date <= $end_date) {
            $tgl_str = date('Y-m-d', $current_date);
            $hari_inggris = date('l', $current_date);
            $hari_id = $hari_indo[$hari_inggris];
            
            // Jurnal asli pada tanggal ini (yang tidak ada di jadwal, misal extra)
            $added_keys = [];
            $real_counts = [];
            foreach($jurnal_db as $jd) {
                if ($jd['tanggal'] == $tgl_str) {
                    $k = $tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
                    if (!isset($real_counts[$k])) $real_counts[$k] = 0;
                    $real_counts[$k]++;
                }
            }
            
            foreach($jurnal_db as $jd) {
                if ($jd['tanggal'] == $tgl_str) {
                    // Tambahkan nama untuk view
                    $jd['nama_guru'] = isset($guruMap[$jd['guru_id']]) ? $guruMap[$jd['guru_id']] : 'Guru ID '.$jd['guru_id'];
                    $jd['nama_kelas'] = isset($kelasMap[$jd['kelas_id']]) ? $kelasMap[$jd['kelas_id']] : 'Kelas ID '.$jd['kelas_id'];
                    $jd['nama_mapel'] = isset($mapelMap[$jd['mapel_id']]) ? $mapelMap[$jd['mapel_id']] : 'Mapel ID '.$jd['mapel_id'];
                    
                    $jam_mulai = '99:99:99';
                    if (isset($jadwal_by_hari[$hari_id])) {
                        foreach($jadwal_by_hari[$hari_id] as $jdw) {
                            if ($jdw['kelas_id'] == $jd['kelas_id'] && $jdw['mata_pelajaran_id'] == $jd['mapel_id'] && $jdw['guru_id'] == $jd['guru_id']) {
                                $jam_mulai = $jdw['jam_mulai'];
                                break;
                            }
                        }
                    }
                    $jd['jam_mulai'] = $jam_mulai;
                    
                    $k = $tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
                    $jd['is_duplicate'] = ($real_counts[$k] > 1);
                    
                    $jurnal_global[] = $jd;
                    $added_keys[$tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id']] = true;
                }
            }
            
            // Periksa jadwal pada hari ini
            $is_libur_or_kegiatan = false;
            $is_pulang_awal = false;
            $jam_mulai_kosong = null;
            
            $kal_info = "";
            $kal_tipe = "";
            if(isset($kalender_map[$tgl_str])) {
                $kal_tipe = $kalender_map[$tgl_str]['tipe'];
                if($kal_tipe == 'libur' || $kal_tipe == 'kegiatan') {
                    $is_libur_or_kegiatan = true;
                    $kal_info = $kalender_map[$tgl_str]['keterangan'];
                } else if ($kal_tipe == 'pulang_awal') {
                    $is_pulang_awal = true;
                    $jam_mulai_kosong = $kalender_map[$tgl_str]['jam_mulai_kosong'];
                    $kal_info = $kalender_map[$tgl_str]['keterangan'];
                }
            }

            if (!$is_libur_or_kegiatan && isset($jadwal_by_hari[$hari_id])) {
                foreach($jadwal_by_hari[$hari_id] as $jdw) {
                    // Cek Bypass Pulang Awal
                    if ($is_pulang_awal && $jam_mulai_kosong) {
                        if ($jdw['jam_mulai'] >= $jam_mulai_kosong) {
                            $key = $tgl_str.'_'.$jdw['kelas_id'].'_'.$jdw['mata_pelajaran_id'].'_'.$jdw['guru_id'];
                            if (!isset($added_keys[$key])) {
                                $jurnal_global[] = [
                                    'id' => 0,
                                    'guru_id' => $jdw['guru_id'],
                                    'kelas_id' => $jdw['kelas_id'],
                                    'mapel_id' => $jdw['mata_pelajaran_id'],
                                    'tanggal' => $tgl_str,
                                    'jam_mulai' => $jdw['jam_mulai'],
                                    'materi' => 'Pulang Dipercepat: ' . $kal_info,
                                    'keterangan' => '',
                                    'created_at' => $tgl_str.' 00:00:00',
                                    'nama_guru' => isset($guruMap[$jdw['guru_id']]) ? $guruMap[$jdw['guru_id']] : 'Guru ID '.$jdw['guru_id'],
                                    'nama_kelas' => isset($kelasMap[$jdw['kelas_id']]) ? $kelasMap[$jdw['kelas_id']] : 'Kelas ID '.$jdw['kelas_id'],
                                    'nama_mapel' => isset($mapelMap[$jdw['mata_pelajaran_id']]) ? $mapelMap[$jdw['mata_pelajaran_id']] : 'Mapel ID '.$jdw['mata_pelajaran_id'],
                                    'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0],
                                    'is_pulang_awal' => true
                                ];
                                $added_keys[$key] = true;
                            }
                            continue; // Skip, tidak wajib isi normal
                        }
                    }
                    
                    $key = $tgl_str.'_'.$jdw['kelas_id'].'_'.$jdw['mata_pelajaran_id'].'_'.$jdw['guru_id'];
                    if (!isset($added_keys[$key])) {
                        // Belum ada jurnal, tambahkan dummy
                        $jurnal_global[] = [
                            'id' => 0,
                            'guru_id' => $jdw['guru_id'],
                            'kelas_id' => $jdw['kelas_id'],
                            'mapel_id' => $jdw['mata_pelajaran_id'],
                            'tanggal' => $tgl_str,
                            'jam_mulai' => $jdw['jam_mulai'],
                            'materi' => '', // Tanda belum mengisi
                            'keterangan' => '',
                            'created_at' => $tgl_str.' 00:00:00',
                            'nama_guru' => isset($guruMap[$jdw['guru_id']]) ? $guruMap[$jdw['guru_id']] : 'Guru ID '.$jdw['guru_id'],
                            'nama_kelas' => isset($kelasMap[$jdw['kelas_id']]) ? $kelasMap[$jdw['kelas_id']] : 'Kelas ID '.$jdw['kelas_id'],
                            'nama_mapel' => isset($mapelMap[$jdw['mata_pelajaran_id']]) ? $mapelMap[$jdw['mata_pelajaran_id']] : 'Mapel ID '.$jdw['mata_pelajaran_id'],
                            'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0]
                        ];
                        $added_keys[$key] = true; // prevent duplicate dummies if multiple jam
                    }
                }
            } else if ($is_libur_or_kegiatan && isset($jadwal_by_hari[$hari_id])) {
                if ($kal_tipe == 'libur') {
                    $key_libur = $tgl_str.'_libur';
                    if (!isset($added_keys[$key_libur])) {
                        $jurnal_global[] = [
                            'id' => 0,
                            'guru_id' => 0,
                            'kelas_id' => 0,
                            'mapel_id' => 0,
                            'tanggal' => $tgl_str,
                            'jam_mulai' => '00:00:00',
                            'materi' => 'Libur: ' . $kal_info,
                            'keterangan' => '',
                            'created_at' => $tgl_str.' 00:00:00',
                            'nama_guru' => '-',
                            'nama_kelas' => '-',
                            'nama_mapel' => '-',
                            'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0],
                            'is_libur' => true
                        ];
                        $added_keys[$key_libur] = true;
                    }
                } else if ($kal_tipe == 'kegiatan') {
                    $key_libur = $tgl_str.'_kegiatan';
                    if (!isset($added_keys[$key_libur])) {
                        $jurnal_global[] = [
                            'id' => 0,
                            'guru_id' => 0,
                            'kelas_id' => 0,
                            'mapel_id' => 0,
                            'tanggal' => $tgl_str,
                            'jam_mulai' => '00:00:00',
                            'materi' => 'Kegiatan: ' . $kal_info,
                            'keterangan' => '',
                            'created_at' => $tgl_str.' 00:00:00',
                            'nama_guru' => '-',
                            'nama_kelas' => '-',
                            'nama_mapel' => '-',
                            'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0],
                            'is_libur' => true
                        ];
                        $added_keys[$key_libur] = true;
                    }
                }
            }
            $current_date = strtotime("+1 day", $current_date);
        }
        
        // Sort descending array based on tanggal and id
        usort($jurnal_global, function($a, $b) {
            if ($a['tanggal'] == $b['tanggal']) {
                if ($a['kelas_id'] == $b['kelas_id']) {
                    return strcmp($a['jam_mulai'], $b['jam_mulai']);
                }
                return $a['kelas_id'] - $b['kelas_id'];
            }
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        } // End is_filtered


        // Ambil data absensi_siswa untuk jurnal yang ditampilkan
        $absen_map = [];
        $all_siswa_ids = [];
        $absen_records = [];

        if (!empty($jurnal_global)) {
            $absen_query = "SELECT a.siswa_id, a.kelas_id, a.tanggal, a.status 
                            FROM absensi_siswa a 
                            WHERE a.tanggal >= :tgl_mulai AND a.tanggal <= :tgl_sampai";
            $absen_params = [
                'tgl_mulai' => $filter_tanggal_mulai,
                'tgl_sampai' => $filter_tanggal_sampai
            ];
            if ($filter_kelas > 0) {
                $absen_query .= " AND a.kelas_id = :kid";
                $absen_params['kid'] = $filter_kelas;
            }
            $stmt_a = $db_siakad->prepare($absen_query);
            $stmt_a->execute($absen_params);
            $absen_records = $stmt_a->fetchAll();

            foreach($absen_records as $ar) {
                if (!empty($ar['siswa_id'])) {
                    $all_siswa_ids[] = $ar['siswa_id'];
                }
            }
        }

        // Ambil nama siswa dari core_db secara bulk
        $siswa_map = [];
        if (!empty($all_siswa_ids)) {
            $unique_siswa_ids = array_unique(array_filter($all_siswa_ids));
            if (!empty($unique_siswa_ids)) {
                $inSiswa = implode(',', array_map('intval', $unique_siswa_ids));
                $siswas = $db_core->query("SELECT id, nama FROM siswa WHERE id IN ($inSiswa)")->fetchAll();
                foreach($siswas as $s) {
                    $siswa_map[$s['id']] = $s['nama'];
                }
            }
        }

        // Kelompokkan absensi per tanggal & kelas_id
        foreach($absen_records as $ar) {
            $key = $ar['tanggal'] . '_' . $ar['kelas_id'];
            if (!isset($absen_map[$key])) {
                $absen_map[$key] = [
                    'hadir' => [],
                    'izin' => [],
                    'sakit' => [],
                    'alpa' => [],
                    'total' => 0
                ];
            }
            
            $st = trim($ar['status']);
            $st_lower = strtolower($st);
            $nama_siswa = isset($siswa_map[$ar['siswa_id']]) ? $siswa_map[$ar['siswa_id']] : 'Siswa #'.$ar['siswa_id'];
            
            $siswa_item = [
                'id' => $ar['siswa_id'],
                'nama' => $nama_siswa,
                'status' => $st
            ];
            
            if (in_array($st_lower, ['hadir', 'h'])) {
                $absen_map[$key]['hadir'][] = $siswa_item;
            } elseif (in_array($st_lower, ['izin', 'i'])) {
                $absen_map[$key]['izin'][] = $siswa_item;
            } elseif (in_array($st_lower, ['sakit', 's'])) {
                $absen_map[$key]['sakit'][] = $siswa_item;
            } else { // alpa, alpha, a
                $absen_map[$key]['alpa'][] = $siswa_item;
            }
            $absen_map[$key]['total']++;
        }

        // Ambil data guru dari core_db secara bulk
        $guruMap = [];
        if (is_array($jurnal_global)) {
            $guru_ids = array_unique(array_column($jurnal_global, 'guru_id'));
            if (!empty($guru_ids)) {
                $inClause = implode(',', array_map('intval', $guru_ids));
                $gurus = $db_core->query("SELECT id, nama FROM guru WHERE id IN ($inClause)")->fetchAll();
                foreach($gurus as $g) {
                    $guruMap[$g['id']] = $g['nama'];
                }
            }

            foreach($jurnal_global as &$j) {
                $j['nama_guru'] = isset($guruMap[$j['guru_id']]) ? $guruMap[$j['guru_id']] : 'Guru ID '.$j['guru_id'];
                $j['nama_kelas'] = isset($kelasMap[$j['kelas_id']]) ? $kelasMap[$j['kelas_id']] : 'Kelas ID '.$j['kelas_id'];
                $j['nama_mapel'] = isset($mapelMap[$j['mapel_id']]) ? $mapelMap[$j['mapel_id']] : 'Mapel ID '.$j['mapel_id'];

                $key = $j['tanggal'] . '_' . $j['kelas_id'];
                if (isset($absen_map[$key])) {
                    $j['total_absen'] = $absen_map[$key]['total'];
                    $j['absen_detail'] = $absen_map[$key];
                } else {
                    $j['total_absen'] = 0;
                    $j['absen_detail'] = [
                        'hadir' => [],
                        'izin' => [],
                        'sakit' => [],
                        'alpa' => [],
                        'total' => 0
                    ];
                }
            }
            unset($j);
        }
        include __DIR__ . '/../../resources/views/admin/kurikulum_monitoring_jurnal.php';
    }

    private function cetak() {
        $db_core = Database::connect('core');
        $db_siakad = Database::connect('siakad');

        $filter_tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
        $filter_tanggal_sampai = isset($_GET['tanggal_sampai']) ? $_GET['tanggal_sampai'] : date('Y-m-t');
        $filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
        $filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;
        $filter_opsi_cetak = isset($_GET['opsi_cetak']) ? $_GET['opsi_cetak'] : 'buku_jurnal_kelas';

        $kelasList = $db_core->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat, nama_kelas")->fetchAll();
        
        $mapelList = [];
        try {
            $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mata_pelajaran ORDER BY nama_mapel")->fetchAll();
        } catch (\Exception $e) {
            try {
                $mapelList = $db_siakad->query("SELECT id, nama_mapel FROM mapel ORDER BY nama_mapel")->fetchAll();
            } catch (\Exception $e) {}
        }
        
        // Merge dengan mapel dari db_core untuk antisipasi data terpisah
        try {
            $coreMapelList = $db_core->query("SELECT id, nama_mapel FROM mapel")->fetchAll();
            foreach($coreMapelList as $cm) {
                $exists = false;
                foreach($mapelList as $ml) {
                    if($ml['id'] == $cm['id']) { $exists = true; break; }
                }
                if(!$exists) {
                    $mapelList[] = $cm;
                }
            }
        } catch (\Exception $e) {}
        $mapelMap = [];
        foreach($mapelList as $m) $mapelMap[$m['id']] = $m['nama_mapel'];
        
        $kelasMap = [];
        foreach($kelasList as $k) $kelasMap[$k['id']] = $k['nama_kelas'];
        
        $guruList = $db_core->query("SELECT id, nama FROM guru ORDER BY nama")->fetchAll();
        $guruMap = [];
        foreach($guruList as $g) $guruMap[$g['id']] = $g['nama'];

        // Ambil Jurnal Global (tanpa detail siswa per nama untuk cetak)
        $query = "SELECT MIN(j.id) as id, j.guru_id, j.kelas_id, j.mapel_id, j.tanggal, j.materi, j.keterangan, MIN(j.created_at) as created_at FROM jurnal_guru j WHERE j.tanggal >= :tgl_mulai AND j.tanggal <= :tgl_sampai";
        $params = [
            'tgl_mulai' => $filter_tanggal_mulai,
            'tgl_sampai' => $filter_tanggal_sampai
        ];
        
        if ($filter_kelas > 0) {
            $query .= " AND j.kelas_id = :kid";
            $params['kid'] = $filter_kelas;
        }
        if ($filter_guru > 0) {
            $query .= " AND j.guru_id = :gid";
            $params['gid'] = $filter_guru;
        }
        
        $query .= " GROUP BY j.guru_id, j.kelas_id, j.mapel_id, j.tanggal, j.materi, j.keterangan ORDER BY j.tanggal ASC, j.kelas_id ASC";

        $stmt = $db_siakad->prepare($query);
        $stmt->execute($params);
        $jurnal_db = $stmt->fetchAll();
        
        $jurnal_map = [];
        foreach($jurnal_db as $jd) {
            $key = $jd['tanggal'].'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
            $jurnal_map[$key] = $jd;
        }
        
        // Ambil jadwal aktif
        $active_tahun_id = 1;
        $active_semester = 1;
        try {
            $stmtTahun = $db_core->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
            if ($thn = $stmtTahun->fetch()) {
                $active_tahun_id = $thn['id'];
                $active_semester = $thn['semester'];
            }
        } catch (\Exception $e) {}
        
        $jadwal_query = "SELECT * FROM jadwal_pelajaran WHERE tahun_ajaran_id = :ta AND semester = :sm";
        $j_params = ['ta' => $active_tahun_id, 'sm' => $active_semester];
        if ($filter_kelas > 0) {
            $jadwal_query .= " AND kelas_id = :kid";
            $j_params['kid'] = $filter_kelas;
        }
        if ($filter_guru > 0) {
            $jadwal_query .= " AND guru_id = :gid";
            $j_params['gid'] = $filter_guru;
        }
        $stmt_j = $db_siakad->prepare($jadwal_query);
        $stmt_j->execute($j_params);
        $jadwals = $stmt_j->fetchAll();
        
        $temp_jadwal = [];
        foreach($jadwals as $jdw) {
            $key_j = $jdw['hari'] . '_' . $jdw['kelas_id'] . '_' . $jdw['jam_mulai'];
            if(!isset($temp_jadwal[$key_j]) || $jdw['id'] > $temp_jadwal[$key_j]['id']) {
                $temp_jadwal[$key_j] = $jdw;
            }
        }
        
        $jadwal_by_hari = [];
        foreach($temp_jadwal as $jdw) {
            $hari = $jdw['hari'];
            if(!isset($jadwal_by_hari[$hari])) $jadwal_by_hari[$hari] = [];
            $jadwal_by_hari[$hari][] = $jdw;
        }
        
        $hari_indo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        
        $jurnal_global = [];
        
        $stmt_k = $db_siakad->prepare("SELECT * FROM kalender_akademik WHERE (tanggal_mulai <= :ts AND tanggal_selesai >= :tm)");
        $stmt_k->execute(['tm' => $filter_tanggal_mulai, 'ts' => $filter_tanggal_sampai]);
        $kalenders = $stmt_k->fetchAll();
        $kalender_map = [];
        foreach($kalenders as $kal) {
            $curr = strtotime($kal['tanggal_mulai']);
            $end = strtotime($kal['tanggal_selesai']);
            while($curr <= $end) {
                $d = date('Y-m-d', $curr);
                $kalender_map[$d] = $kal;
                $curr = strtotime('+1 day', $curr);
            }
        }
        
        $current_date = strtotime($filter_tanggal_mulai);
        $end_date = strtotime($filter_tanggal_sampai);
        
        while ($current_date <= $end_date) {
            $tgl_str = date('Y-m-d', $current_date);
            $hari_inggris = date('l', $current_date);
            $hari_id = $hari_indo[$hari_inggris];
            
            $added_keys = [];
            $real_counts = [];
            foreach($jurnal_db as $jd) {
                if ($jd['tanggal'] == $tgl_str) {
                    $k = $tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
                    if (!isset($real_counts[$k])) $real_counts[$k] = 0;
                    $real_counts[$k]++;
                }
            }
            
            foreach($jurnal_db as $jd) {
                if ($jd['tanggal'] == $tgl_str) {
                    $jd['nama_guru'] = isset($guruMap[$jd['guru_id']]) ? $guruMap[$jd['guru_id']] : 'Guru ID '.$jd['guru_id'];
                    $jd['nama_kelas'] = isset($kelasMap[$jd['kelas_id']]) ? $kelasMap[$jd['kelas_id']] : 'Kelas ID '.$jd['kelas_id'];
                    $jd['nama_mapel'] = isset($mapelMap[$jd['mapel_id']]) ? $mapelMap[$jd['mapel_id']] : 'Mapel ID '.$jd['mapel_id'];
                    
                    $jam_mulai = '99:99:99';
                    if (isset($jadwal_by_hari[$hari_id])) {
                        foreach($jadwal_by_hari[$hari_id] as $jdw) {
                            if ($jdw['kelas_id'] == $jd['kelas_id'] && $jdw['mata_pelajaran_id'] == $jd['mapel_id'] && $jdw['guru_id'] == $jd['guru_id']) {
                                $jam_mulai = $jdw['jam_mulai'];
                                break;
                            }
                        }
                    }
                    $jd['jam_mulai'] = $jam_mulai;
                    
                    $k = $tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id'];
                    $jd['is_duplicate'] = ($real_counts[$k] > 1);
                    
                    $jurnal_global[] = $jd;
                    $added_keys[$tgl_str.'_'.$jd['kelas_id'].'_'.$jd['mapel_id'].'_'.$jd['guru_id']] = true;
                }
            }
            
            $is_libur_or_kegiatan = false;
            $is_pulang_awal = false;
            $jam_mulai_kosong = null;
            $kal_info = "";
            $kal_tipe = "";
            if(isset($kalender_map[$tgl_str])) {
                $kal_tipe = $kalender_map[$tgl_str]['tipe'];
                if($kal_tipe == 'libur' || $kal_tipe == 'kegiatan') {
                    $is_libur_or_kegiatan = true;
                    $kal_info = $kalender_map[$tgl_str]['keterangan'];
                } else if ($kal_tipe == 'pulang_awal') {
                    $is_pulang_awal = true;
                    $jam_mulai_kosong = $kalender_map[$tgl_str]['jam_mulai_kosong'];
                    $kal_info = $kalender_map[$tgl_str]['keterangan'];
                }
            }

            if (!$is_libur_or_kegiatan && isset($jadwal_by_hari[$hari_id])) {
                foreach($jadwal_by_hari[$hari_id] as $jdw) {
                    // Cek Bypass Pulang Awal
                    if ($is_pulang_awal && $jam_mulai_kosong) {
                        if ($jdw['jam_mulai'] >= $jam_mulai_kosong) {
                            $key = $tgl_str.'_'.$jdw['kelas_id'].'_'.$jdw['mata_pelajaran_id'].'_'.$jdw['guru_id'];
                            if (!isset($added_keys[$key])) {
                                $jurnal_global[] = [
                                    'id' => 0,
                                    'guru_id' => $jdw['guru_id'],
                                    'kelas_id' => $jdw['kelas_id'],
                                    'mapel_id' => $jdw['mata_pelajaran_id'],
                                    'tanggal' => $tgl_str,
                                    'jam_mulai' => $jdw['jam_mulai'],
                                    'materi' => 'Pulang Dipercepat: ' . $kal_info,
                                    'keterangan' => '',
                                    'created_at' => $tgl_str.' 00:00:00',
                                    'nama_guru' => isset($guruMap[$jdw['guru_id']]) ? $guruMap[$jdw['guru_id']] : 'Guru ID '.$jdw['guru_id'],
                                    'nama_kelas' => isset($kelasMap[$jdw['kelas_id']]) ? $kelasMap[$jdw['kelas_id']] : 'Kelas ID '.$jdw['kelas_id'],
                                    'nama_mapel' => isset($mapelMap[$jdw['mata_pelajaran_id']]) ? $mapelMap[$jdw['mata_pelajaran_id']] : 'Mapel ID '.$jdw['mata_pelajaran_id'],
                                    'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0],
                                    'is_pulang_awal' => true
                                ];
                                $added_keys[$key] = true;
                            }
                            continue; // Skip, tidak wajib isi normal
                        }
                    }
                    
                    $key = $tgl_str.'_'.$jdw['kelas_id'].'_'.$jdw['mata_pelajaran_id'].'_'.$jdw['guru_id'];
                    if (!isset($added_keys[$key])) {
                        $jurnal_global[] = [
                            'id' => 0,
                            'guru_id' => $jdw['guru_id'],
                            'kelas_id' => $jdw['kelas_id'],
                            'mapel_id' => $jdw['mata_pelajaran_id'],
                            'tanggal' => $tgl_str,
                            'jam_mulai' => $jdw['jam_mulai'],
                            'materi' => '', 
                            'keterangan' => '',
                            'created_at' => $tgl_str.' 00:00:00',
                            'nama_guru' => isset($guruMap[$jdw['guru_id']]) ? $guruMap[$jdw['guru_id']] : 'Guru ID '.$jdw['guru_id'],
                            'nama_kelas' => isset($kelasMap[$jdw['kelas_id']]) ? $kelasMap[$jdw['kelas_id']] : 'Kelas ID '.$jdw['kelas_id'],
                            'nama_mapel' => isset($mapelMap[$jdw['mata_pelajaran_id']]) ? $mapelMap[$jdw['mata_pelajaran_id']] : 'Mapel ID '.$jdw['mata_pelajaran_id'],
                            'absen_detail' => ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpa'=>0,'total'=>0]
                        ];
                        $added_keys[$key] = true; 
                    }
                }
            }
            $current_date = strtotime("+1 day", $current_date);
        }
        
        // Sort ascending for print
        usort($jurnal_global, function($a, $b) {
            if ($a['tanggal'] == $b['tanggal']) {
                if ($a['kelas_id'] == $b['kelas_id']) {
                    return strcmp($a['jam_mulai'], $b['jam_mulai']);
                }
                return $a['kelas_id'] - $b['kelas_id'];
            }
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });
        
        file_put_contents('debug_jumat.txt', print_r($jurnal_global, true));

        $absen_map = [];
        if (!empty($jurnal_global)) {
            $absen_query = "SELECT a.kelas_id, a.tanggal, a.status 
                            FROM absensi_siswa a 
                            WHERE a.tanggal >= :tgl_mulai AND a.tanggal <= :tgl_sampai";
            $absen_params = [
                'tgl_mulai' => $filter_tanggal_mulai,
                'tgl_sampai' => $filter_tanggal_sampai
            ];
            if ($filter_kelas > 0) {
                $absen_query .= " AND a.kelas_id = :kid";
                $absen_params['kid'] = $filter_kelas;
            }
            $stmt_a = $db_siakad->prepare($absen_query);
            $stmt_a->execute($absen_params);
            $absen_records = $stmt_a->fetchAll();

            foreach($absen_records as $ar) {
                $key = $ar['tanggal'] . '_' . $ar['kelas_id'];
                if (!isset($absen_map[$key])) {
                    $absen_map[$key] = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                }
                $st = strtolower(trim($ar['status']));
                if (in_array($st, ['hadir', 'h'])) $absen_map[$key]['hadir']++;
                elseif (in_array($st, ['izin', 'i'])) $absen_map[$key]['izin']++;
                elseif (in_array($st, ['sakit', 's'])) $absen_map[$key]['sakit']++;
                else $absen_map[$key]['alpa']++;
            }
        }

        foreach($jurnal_global as &$j) {
            $key = $j['tanggal'] . '_' . $j['kelas_id'];
            if (isset($absen_map[$key]) && $j['id'] > 0) {
                $j['absen_detail'] = $absen_map[$key];
            } elseif(!isset($j['absen_detail'])) {
                $j['absen_detail'] = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            }
        }
        unset($j);

        $nama_kelas_cetak = "Semua Kelas";
        if ($filter_kelas > 0 && isset($kelasMap[$filter_kelas])) {
            $nama_kelas_cetak = $kelasMap[$filter_kelas];
        }

        $inst = $db_core->query("SELECT * FROM institusi LIMIT 1")->fetch();

        include __DIR__ . '/../../resources/views/siakad/cetak_jurnal_global.php';
    }
}

