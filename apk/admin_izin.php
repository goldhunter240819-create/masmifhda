<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];
$nama = $_SESSION['nama'] ?? 'Guru';

// Verify Admin Izin Access
$stmt_akses = $core_db->prepare("SELECT app_code FROM apk_akses_guru WHERE guru_id = ? AND app_code = 'admin_izin'");
$stmt_akses->execute([$guru_id]);
if (!$stmt_akses->fetchColumn()) {
    echo "<h1>Akses Ditolak</h1>";
    exit;
}

// Map english day to indonesian
$hari_map = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$hari_ini = $hari_map[date('l')];
$tgl_ini = date('Y-m-d');

// Handle Post Actions (Approve, Reject, Lapor Alpa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if (in_array($action, ['approve', 'reject']) && isset($_POST['ajuan_id'])) {
        $ajuan_id = intval($_POST['ajuan_id']);
        $status_baru = ($action === 'approve') ? 'Disetujui' : 'Ditolak';

        try {
            $stmt_upd = $siakad_db->prepare("UPDATE ajuan_izin_guru SET status = ? WHERE id = ?");
            $stmt_upd->execute([$status_baru, $ajuan_id]);
            
            // Notification logic
            $stmt_guru = $siakad_db->prepare("SELECT a.guru_id, a.tanggal_izin, a.keterangan, g.user_id FROM ajuan_izin_guru a JOIN aula_db.guru g ON a.guru_id = g.id WHERE a.id = ?");
            $stmt_guru->execute([$ajuan_id]);
            $g_info = $stmt_guru->fetch(PDO::FETCH_ASSOC);
            
            if ($g_info && $status_baru === 'Disetujui') {
                $guru_id_izin = $g_info['guru_id'];
                $tgl_izin = $g_info['tanggal_izin'];
                $keterangan = $g_info['keterangan'];
                
                $status_absen = 'Izin';
                if (stripos($keterangan, 'Sakit') !== false) {
                    $status_absen = 'Sakit';
                } elseif (stripos($keterangan, 'Dinas') !== false) {
                    $status_absen = 'Dinas Luar';
                }

                $cek_absen = $siakad_db->prepare("SELECT id FROM absensi_guru WHERE guru_id = ? AND tanggal = ?");
                $cek_absen->execute([$guru_id_izin, $tgl_izin]);
                if ($row_abs = $cek_absen->fetch()) {
                    $siakad_db->prepare("UPDATE absensi_guru SET status = ? WHERE id = ?")->execute([$status_absen, $row_abs['id']]);
                } else {
                    $siakad_db->prepare("INSERT INTO absensi_guru (guru_id, tanggal, status, created_at) VALUES (?, ?, ?, NOW())")->execute([$guru_id_izin, $tgl_izin, $status_absen]);
                }
            }
            
            if ($g_info && !empty($g_info['user_id'])) {
                $tgl_indo = date('d-m-Y', strtotime($g_info['tanggal_izin']));
                sendOneSignalNotification(
                    (string)$g_info['user_id'], 
                    "Status Izin Anda", 
                    "Pengajuan izin Anda untuk tanggal {$tgl_indo} telah {$status_baru} oleh Admin."
                );
            }
            header("Location: admin_izin.php?msg=success");
            exit;
        } catch (Exception $e) {
            $error = "Gagal memproses pengajuan.";
        }
    } 
    elseif ($action === 'lapor_alpa' && isset($_POST['alpa_guru_id'])) {
        $alpa_guru_id = intval($_POST['alpa_guru_id']);
        if ($alpa_guru_id > 0) {
            // Cek apakah sudah ada izin/alpa hari ini
            $stmt_cek = $siakad_db->prepare("SELECT id FROM ajuan_izin_guru WHERE guru_id = ? AND tanggal_izin = ?");
            $stmt_cek->execute([$alpa_guru_id, $tgl_ini]);
            if (!$stmt_cek->fetchColumn()) {
                $stmt_ins = $siakad_db->prepare("INSERT INTO ajuan_izin_guru (guru_id, tanggal_izin, keterangan, status, tugas_mapel) VALUES (?, ?, 'Alpa (Dilaporkan oleh Piket)', 'Disetujui', '[]')");
                $stmt_ins->execute([$alpa_guru_id, $tgl_ini]);
                header("Location: admin_izin.php?msg=alpa_success");
                exit;
            } else {
                header("Location: admin_izin.php?msg=alpa_exists");
                exit;
            }
        }
    }
}

// Fetch all pengajuan (for Persetujuan Tab)
$sql = "
    SELECT a.*, g.nama as nama_guru 
    FROM ajuan_izin_guru a
    JOIN aula_db.guru g ON a.guru_id = g.id
    ORDER BY FIELD(a.status, 'Pending') DESC, a.tanggal_izin DESC
";
$stmt = $siakad_db->query($sql);
$ajuan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Inval Tasks Today
$sql_inval = "
    SELECT j.*, m.nama_mapel, k.nama_kelas, g.nama as nama_guru, a.tugas_mapel, a.keterangan as alasan_izin
    FROM jadwal_pelajaran j
    JOIN mata_pelajaran m ON j.mata_pelajaran_id = m.id
    JOIN kelas k ON j.kelas_id = k.id
    JOIN aula_db.guru g ON j.guru_id = g.id
    JOIN ajuan_izin_guru a ON a.guru_id = j.guru_id AND a.tanggal_izin = ? AND (a.status = 'Disetujui' OR a.status = 'Pending')
    WHERE j.hari = ?
    ORDER BY j.jam_mulai ASC
";
$stmt_inval = $siakad_db->prepare($sql_inval);
$stmt_inval->execute([$tgl_ini, $hari_ini]);
$raw_inval_list = $stmt_inval->fetchAll(PDO::FETCH_ASSOC);

$grouped_inval = [];
foreach ($raw_inval_list as $inv) {
    $key = $inv['guru_id'] . '_' . $inv['kelas_id'] . '_' . $inv['mata_pelajaran_id'];
    if (!isset($grouped_inval[$key])) {
        $grouped_inval[$key] = $inv;
    } else {
        if (strtotime($inv['jam_selesai']) > strtotime($grouped_inval[$key]['jam_selesai'])) {
            $grouped_inval[$key]['jam_selesai'] = $inv['jam_selesai'];
        }
        if (strtotime($inv['jam_mulai']) < strtotime($grouped_inval[$key]['jam_mulai'])) {
            $grouped_inval[$key]['jam_mulai'] = $inv['jam_mulai'];
        }
    }
}
$inval_list = array_values($grouped_inval);
usort($inval_list, function($a, $b) {
    return strtotime($a['jam_mulai']) - strtotime($b['jam_mulai']);
});

// Fetch all gurus for Alpa Dropdown
$stmt_gurus = $core_db->query("SELECT id, nama FROM guru ORDER BY nama ASC");
$all_gurus = $stmt_gurus->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Admin Izin & Inval';
include 'header.php';
?>
<style>
    .card-ajuan, .inval-card {
        background: var(--card-bg, #ffffff);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        border: 1px solid var(--border-color);
    }
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px dashed var(--border-color);
    }
    .guru-name { font-size: 1.05rem; font-weight: 800; color: var(--text-main); }
    .tanggal { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
    .badge-status { padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; }
    .keterangan-box { background: #f8fafc; padding: 12px; border-radius: 8px; font-size: 0.85rem; color: var(--text-main); margin-bottom: 15px; border-left: 3px solid #cbd5e1; }
    .tugas-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 10px; color: var(--text-main); }
    .tugas-item { background: #f1f5f9; padding: 10px 15px; border-radius: 8px; margin-bottom: 8px; }
    .tugas-mapel-name { font-weight: 700; font-size: 0.85rem; color: var(--primary); margin-bottom: 3px; }
    .tugas-text { font-size: 0.8rem; color: var(--text-main); line-height: 1.4; }
    .action-buttons { display: flex; gap: 10px; margin-top: 15px; }
    .btn-action { padding: 10px; border-radius: 10px; font-weight: bold; font-size: 0.85rem; cursor: pointer; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; width: 100%; }
    .btn-reject { background: #fee2e2; color: #ef4444; }
    .btn-approve { background: #d1fae5; color: #10b981; }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
    .data-card { background: var(--card-bg, #ffffff); border-radius: 20px; padding: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; }

    /* Tabs */
    .tabs-container { display: flex; background: #f1f5f9; border-radius: 12px; padding: 5px; margin-bottom: 20px; }
    .tab-btn { flex: 1; text-align: center; padding: 10px; font-weight: 700; font-size: 0.9rem; color: #64748b; border-radius: 8px; cursor: pointer; transition: 0.2s; }
    .tab-btn.active { background: white; color: #f59e0b; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .btn-inval { background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; text-decoration: none; display: inline-block; }
</style>
    
<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding-bottom: 2.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Admin Delegasi</p>
            <h1>Izin & Inval Piket</h1>
        </div>
        <a href="aplikasi_saya.php" class="avatar" style="color: #f59e0b; background: white; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] == 'success'): ?>
            <div style="background: #d1fae5; color: #059669; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; text-align: center;">
                Aksi berhasil disimpan!
            </div>
            <?php elseif ($_GET['msg'] == 'alpa_success'): ?>
            <div style="background: #d1fae5; color: #059669; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; text-align: center;">
                Guru berhasil dilaporkan Alpa hari ini!
            </div>
            <?php elseif ($_GET['msg'] == 'alpa_exists'): ?>
            <div style="background: #fee2e2; color: #ef4444; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; text-align: center;">
                Guru tersebut sudah tercatat Izin/Alpa hari ini.
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="tabs-container">
            <div class="tab-btn active" onclick="switchTab('persetujuan')">Persetujuan</div>
            <div class="tab-btn" onclick="switchTab('inval')">Tugas Inval</div>
        </div>

        <div id="tab-persetujuan" class="tab-content active">
            <?php if (count($ajuan_list) > 0): ?>
                <?php foreach ($ajuan_list as $a): 
                    $badge_class = 'badge-pending';
                    if ($a['status'] == 'Disetujui') $badge_class = 'badge-disetujui';
                    if ($a['status'] == 'Ditolak') $badge_class = 'badge-ditolak';
                    
                    $tugas_arr = json_decode($a['tugas_mapel'], true);
                ?>
                <div class="card-ajuan">
                    <div class="card-header">
                        <div>
                            <div class="guru-name"><?= htmlspecialchars($a['nama_guru']) ?></div>
                            <div class="tanggal"><i class="fa-regular fa-calendar" style="margin-right: 5px;"></i> <?= date('d M Y', strtotime($a['tanggal_izin'])) ?></div>
                        </div>
                        <div class="badge-status <?= $badge_class ?>"><?= $a['status'] ?></div>
                    </div>

                    <div class="keterangan-box">
                        <span style="font-weight: 700;">Alasan:</span> <?= htmlspecialchars($a['keterangan']) ?>
                    </div>

                    <div class="tugas-section">
                        <div class="tugas-title" onclick="toggleTugas(this)" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 0;">
                            <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main);"><i class="fa-solid fa-list-check" style="color:var(--primary); margin-right: 5px;"></i> Rincian Tugas / Materi Kelas</div>
                            <i class="fa-solid fa-chevron-down toggle-icon" style="transition: transform 0.3s; color: var(--text-muted); font-size: 0.8rem;"></i>
                        </div>
                        <div class="tugas-content" style="display: none; padding-top: 10px;">
                        <?php if (is_array($tugas_arr) && count($tugas_arr) > 0): ?>
                            <?php foreach ($tugas_arr as $t): ?>
                                <div class="tugas-item">
                                    <div class="tugas-mapel-name"><?= htmlspecialchars($t['kelas']) ?> - <?= htmlspecialchars($t['mapel']) ?></div>
                                    <div class="tugas-text">
                                        <?= !empty($t['tugas']) ? nl2br(htmlspecialchars($t['tugas'])) : '<i>Tidak ada instruksi khusus.</i>' ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="tugas-item" style="text-align: center; color: var(--text-muted); font-style: italic;">
                                Tidak ada jadwal mapel di hari ini.
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($a['status'] == 'Pending'): ?>
                    <div class="action-buttons">
                        <form method="POST" style="flex: 1; display: flex;" onsubmit="confirmSubmit(event, this, 'Tolak pengajuan izin ini?');">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="ajuan_id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn-action btn-reject"><i class="fa-solid fa-xmark"></i> Tolak</button>
                        </form>
                        <form method="POST" style="flex: 1; display: flex;" onsubmit="confirmSubmit(event, this, 'Setujui pengajuan izin ini?');">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="ajuan_id" value="<?= $a['id'] ?>">
                            <button type="submit" class="btn-action btn-approve"><i class="fa-solid fa-check"></i> Setujui</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 5px;">Belum ada pengajuan izin</h3>
                    <p style="font-size: 0.9rem;">Daftar pengajuan izin guru akan muncul di sini.</p>
                </div>
            <?php endif; ?>
        </div>

        <div id="tab-inval" class="tab-content">
            
            <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <h4 style="margin: 0 0 10px 0; color: #b45309; font-size: 0.9rem;"><i class="fa-solid fa-triangle-exclamation"></i> Laporkan Guru Alpa (Tidak Masuk Tanpa Izin)</h4>
                <form method="POST" style="display: flex; gap: 10px;">
                    <input type="hidden" name="action" value="lapor_alpa">
                    <select name="alpa_guru_id" required style="flex: 1; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                        <option value="">Pilih Guru...</option>
                        <?php foreach($all_gurus as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" style="background: #ef4444; color: white; border: none; padding: 10px 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">Laporkan</button>
                </form>
            </div>

            <h3 style="font-size: 1.05rem; margin-bottom: 15px; color: #1e293b; border-bottom: 2px solid #f1f5f9; padding-bottom: 5px;">Tugas Kelas (Inval) Hari Ini</h3>
            
            <?php if (count($inval_list) > 0): ?>
                <?php 
                $last_guru = '';
                foreach ($inval_list as $inv): 
                    if ($last_guru != $inv['nama_guru']): 
                        $last_guru = $inv['nama_guru'];
                ?>
                    <div style="background: #f8fafc; padding: 8px 15px; border-radius: 8px; margin-bottom: 10px; margin-top: 15px; font-weight: bold; color: #475569; font-size: 0.9rem;">
                        <i class="fa-solid fa-user-xmark"></i> Guru Berhalangan: <?= htmlspecialchars($inv['nama_guru']) ?>
                    </div>
                <?php endif; 
                
                // Cari tugas spesifik untuk kelas ini dari JSON tugas_mapel
                $tugas_arr = json_decode($inv['tugas_mapel'], true);
                $tugas_spesifik = "";
                if (is_array($tugas_arr)) {
                    foreach ($tugas_arr as $t) {
                        if ($t['kelas'] == $inv['nama_kelas'] && $t['mapel'] == $inv['nama_mapel']) {
                            $tugas_spesifik = $t['tugas'];
                        }
                    }
                }
                
                // Cek apakah jurnal sudah diisi (sudah ada yang mengambil inval)
                $stmt_cek_jurnal = $siakad_db->prepare("SELECT materi FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal = ?");
                $stmt_cek_jurnal->execute([$inv['guru_id'], $inv['kelas_id'], $inv['mata_pelajaran_id'], $tgl_ini]);
                $jurnal_filled = $stmt_cek_jurnal->fetch(PDO::FETCH_ASSOC);
                
                $inval_oleh = '';
                if ($jurnal_filled) {
                    // Ekstrak nama pengambil inval dari teks materi jika ada (Inval oleh: Nama)
                    if (preg_match('/\(Inval oleh:\s*(.*?)\)/', $jurnal_filled['materi'], $matches)) {
                        $inval_oleh = $matches[1];
                    } else {
                        $inval_oleh = 'Guru Inval (Selesai)';
                    }
                }
                ?>
                <div class="inval-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <div>
                            <div style="font-weight: 800; color: #0ea5e9; font-size: 1.05rem;"><?= htmlspecialchars($inv['nama_mapel']) ?></div>
                            <div style="color: #64748b; font-size: 0.85rem; margin-top: 2px;">
                                <i class="fa-solid fa-users"></i> Kelas <?= htmlspecialchars($inv['nama_kelas']) ?> | Jam: <?= date('H:i', strtotime($inv['jam_mulai'])) ?> - <?= date('H:i', strtotime($inv['jam_selesai'])) ?>
                            </div>
                        </div>
                        
                        <?php if ($inval_oleh): ?>
                            <div style="background: #ecfdf5; color: #059669; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; border: 1px solid #a7f3d0;">
                                <i class="fa-solid fa-check-circle"></i> Selesai oleh:<br><span style="font-size:0.8rem;"><?= htmlspecialchars($inval_oleh) ?></span>
                            </div>
                        <?php else: ?>
                            <a href="jurnal_mengajar.php?inval=1&target_guru_id=<?= $inv['guru_id'] ?>&pre_kelas=<?= $inv['kelas_id'] ?>&pre_mapel=<?= $inv['mata_pelajaran_id'] ?>&tugas=<?= urlencode($tugas_spesifik) ?>" class="btn-inval">
                                <i class="fa-solid fa-person-chalkboard"></i> Masuk Kelas
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($tugas_spesifik)): ?>
                        <div style="background: #f0f9ff; border: 1px dashed #bae6fd; border-radius: 8px; font-size: 0.8rem; color: #0369a1; margin-top: 10px;">
                            <div onclick="toggleTugas(this)" style="cursor: pointer; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <strong><i class="fa-solid fa-list-check" style="margin-right: 5px;"></i> Tugas dari guru:</strong>
                                <i class="fa-solid fa-chevron-down toggle-icon" style="transition: transform 0.3s; color: #7dd3fc; font-size: 0.8rem;"></i>
                            </div>
                            <div class="tugas-content" style="display: none; padding: 0 10px 10px 10px;">
                                <?= nl2br(htmlspecialchars($tugas_spesifik)) ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="background: #f1f5f9; padding: 8px; border-radius: 8px; font-size: 0.75rem; color: #94a3b8; font-style: italic; margin-top: 10px;">
                            Tidak ada pesan tugas.
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-mug-hot" style="font-size: 3rem; margin-bottom: 15px; color: #cbd5e1;"></i>
                    <h3 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 5px;">Tidak ada kelas kosong</h3>
                    <p style="font-size: 0.9rem;">Semua kelas dilaporkan aman hari ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </div> <!-- end .main-content -->

    <script>
        function toggleTugas(el) {
            let content = el.nextElementSibling;
            let icon = el.querySelector('.toggle-icon');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }
        
        function confirmSubmit(event, form, message) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    container: 'my-swal-container',
                    popup: 'modern-toast'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            if (tab === 'persetujuan') {
                document.querySelectorAll('.tab-btn')[0].classList.add('active');
                document.getElementById('tab-persetujuan').classList.add('active');
            } else {
                document.querySelectorAll('.tab-btn')[1].classList.add('active');
                document.getElementById('tab-inval').classList.add('active');
            }
        }
    </script>
    <?php include 'footer_guru.php'; ?>