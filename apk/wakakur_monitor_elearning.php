<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$apk_settings_file = __DIR__ . '/apk_settings.json';
if (file_exists($apk_settings_file)) {
    $apk_settings = json_decode(file_get_contents($apk_settings_file), true);
    if (isset($apk_settings['elearning_enabled']) && !$apk_settings['elearning_enabled']) {
        die("<html><body style='font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#475569;'>
            <div style='background:white;padding:30px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.05);display:inline-block;max-width:400px;'>
                <h2 style='color:#eab308;margin-bottom:10px;'>Fitur Sedang Dipelihara</h2>
                <p>Maaf, fitur E-Learning (Tugas Daring) sedang dinonaktifkan sementara oleh Admin.</p>
                <button onclick='window.history.back()' style='margin-top:20px;background:#3b82f6;color:white;border:none;padding:10px 20px;border-radius:8px;cursor:pointer;'>Kembali</button>
            </div>
        </body></html>");
    }
}

$hari_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$tanggal_filter = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tanggal_filter)) {
    $tanggal_filter = date('Y-m-d');
}
$hari_ini = $hari_indo[date('w', strtotime($tanggal_filter))];

$jadwal = [];
$classes = [];
try {
    $core_db_name = $core_db->query("SELECT database()")->fetchColumn();

    // Ambil tahun ajaran aktif
    $activeTahunId = 1;
    $activeSemester = 1;
    $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
        $activeSemester = $thn['semester'];
    }

    // Fetch classes for dropdown
    $stmt_classes = $core_db->query("SELECT id, nama_kelas FROM kelas ORDER BY tingkat ASC, nama_kelas ASC");
    $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);

    // Build query with filter
    $query = "
        SELECT j.id as jadwal_id, j.jam_mulai, j.jam_selesai, j.mata_pelajaran_id as mapel_id, 
               m.nama_mapel, k.nama_kelas, g.nama as nama_guru, g.no_hp as hp_guru,
               td.id as tugas_id, td.judul
        FROM jadwal_pelajaran j 
        LEFT JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id 
        LEFT JOIN {$core_db_name}.kelas k ON j.kelas_id = k.id 
        LEFT JOIN {$core_db_name}.guru g ON j.guru_id = g.id 
        LEFT JOIN tugas_daring td ON td.kelas_id = j.kelas_id 
                                  AND td.mata_pelajaran_id = j.mata_pelajaran_id 
                                  AND td.tanggal = :tanggal
        WHERE j.hari = :hari AND j.tahun_ajaran_id = :tahun_ajaran_id AND j.semester = :semester
    ";
    
    $params = [
        ':hari' => $hari_ini, 
        ':tanggal' => $tanggal_filter,
        ':tahun_ajaran_id' => $activeTahunId,
        ':semester' => $activeSemester
    ];
    
    if ($filter_kelas > 0) {
        $query .= " AND j.kelas_id = :kelas_id";
        $params[':kelas_id'] = $filter_kelas;
        
        $query .= " ORDER BY j.jam_mulai ASC, k.tingkat ASC, k.nama_kelas ASC";
        
        $stmt = $siakad_db->prepare($query);
        $stmt->execute($params);
        $raw_jadwal = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group consecutive same-subject periods so we don't have multiple rows for the same task
        $grouped = [];
        foreach ($raw_jadwal as $jdwl) {
            $group_key = $jdwl['mapel_id'] . '_' . $jdwl['nama_kelas'] . '_' . $jdwl['nama_guru'];
            if (!isset($grouped[$group_key])) {
                $grouped[$group_key] = $jdwl;
            } else {
                if (strtotime($jdwl['jam_selesai']) > strtotime($grouped[$group_key]['jam_selesai'])) {
                    $grouped[$group_key]['jam_selesai'] = $jdwl['jam_selesai'];
                }
            }
        }
        $jadwal = array_values($grouped);
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$page_title = 'Monitor E-Learning';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Waka Kurikulum</p>
            <h1>Monitor E-Learning</h1>
        </div>
        <div class="avatar" style="color: #10b981; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 20px; min-height: 70vh;">
        
        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px; margin-top: 0;">
            Pantau status pengisian tugas daring/mandiri oleh guru pada tanggal <?= date('d M Y', strtotime($tanggal_filter)) ?> (<?= $hari_ini ?>) sesuai jadwal KBM.
        </p>

        <!-- Filter Tanggal & Kelas -->
        <div style="margin-bottom: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;">
            <form method="GET" action="" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>" onchange="this.form.submit()" style="flex: 1; min-width: 130px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #334155; background: white; outline: none; cursor: pointer;">
                
                <select name="kelas_id" onchange="this.form.submit()" style="flex: 2; min-width: 150px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; color: #334155; background: white; appearance: none; outline: none; cursor: pointer;">
                    <option value="0">Pilih Kelas...</option>
                    <?php foreach ($classes as $kls): ?>
                        <option value="<?= $kls['id'] ?>" <?= $filter_kelas == $kls['id'] ? 'selected' : '' ?>>
                            Kelas <?= htmlspecialchars($kls['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="background: #10b981; color: white; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px; flex-shrink: 0;">
                    <i class="fa-solid fa-filter"></i>
                </div>
            </form>
        </div>

        <?php if ($filter_kelas == 0): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-hand-pointer" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;">Pilih Kelas</h4>
                <p style="margin: 0; font-size: 0.9rem;">Silakan pilih kelas terlebih dahulu untuk melihat daftar monitoring.</p>
            </div>
        <?php elseif (empty($jadwal)): ?>
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;">Tidak Ada Jadwal</h4>
                <p style="margin: 0; font-size: 0.9rem;">Belum ada jadwal KBM pada hari ini untuk kelas yang dipilih.</p>
            </div>
        <?php else: ?>
            <?php foreach ($jadwal as $j): ?>
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); position: relative; overflow: hidden;">
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: <?= $j['tugas_id'] ? '#10b981' : '#ef4444' ?>;"></div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 3px; letter-spacing: 0.5px;">KELAS <?= htmlspecialchars($j['nama_kelas']) ?></div>
                            <div style="font-size: 1.05rem; font-weight: bold; color: #1e293b;"><?= htmlspecialchars($j['nama_mapel']) ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; color: white; background: #64748b; padding: 3px 8px; border-radius: 20px; font-weight: 600; display: inline-block;">
                                <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 0.7rem; font-weight: bold;">
                            <?= substr(htmlspecialchars($j['nama_guru']), 0, 2) ?>
                        </div>
                        <div style="font-size: 0.85rem; font-weight: 600; color: #475569;">
                            <?= htmlspecialchars($j['nama_guru']) ?>
                        </div>
                    </div>

                    <div style="background: <?= $j['tugas_id'] ? '#ecfdf5' : '#fef2f2' ?>; border: 1px dashed <?= $j['tugas_id'] ? '#6ee7b7' : '#fca5a5' ?>; padding: 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <?php if ($j['tugas_id']): ?>
                                <div style="font-size: 0.8rem; font-weight: 700; color: #10b981; margin-bottom: 3px;">
                                    <i class="fa-solid fa-circle-check"></i> Sudah Mengisi
                                </div>
                                <div style="font-size: 0.8rem; color: #059669; font-style: italic;">
                                    "<?= htmlspecialchars($j['judul']) ?>"
                                </div>
                            <?php else: ?>
                                <div style="font-size: 0.8rem; font-weight: 700; color: #ef4444; margin-bottom: 3px;">
                                    <i class="fa-solid fa-circle-xmark"></i> Belum Mengisi
                                </div>
                                <div style="font-size: 0.75rem; color: #b91c1c;">
                                    Belum ada tugas e-learning hari ini.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$j['tugas_id'] && !empty($j['hp_guru'])): 
                            // Format HP to standard format (replace starting 0 with 62)
                            $hp = preg_replace('/^0/', '62', $j['hp_guru']);
                            // Cek format minimal biar gk ngaco kalau format nomernya salah
                            if (strlen($hp) >= 10 && is_numeric($hp)) {
                                $wa_msg = urlencode("Assalamu'alaikum Bapak/Ibu " . $j['nama_guru'] . ", mohon izin mengingatkan untuk mengisi tugas E-Learning pada kelas " . $j['nama_kelas'] . " mata pelajaran " . $j['nama_mapel'] . " hari ini. Terima kasih.");
                        ?>
                            <a href="https://wa.me/<?= $hp ?>?text=<?= $wa_msg ?>" target="_blank" style="background: #25d366; color: white; padding: 8px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-brands fa-whatsapp" style="font-size: 1rem;"></i> Tegur via WA
                            </a>
                        <?php 
                            }
                        endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php include 'footer_guru.php'; ?>
