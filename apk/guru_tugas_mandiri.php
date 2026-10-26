<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

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

$guru_id = $_SESSION['guru_id'];

if (isset($_POST['simpan_tugas'])) {
    $id_kelas = $_POST['id_kelas'];
    $id_mapel = $_POST['id_mapel'];
    $tanggal = $_POST['tanggal'];
    $judul = $_POST['judul'];
    $instruksi = $_POST['instruksi'];

    $batas_waktu = !empty($_POST['batas_waktu']) ? $_POST['batas_waktu'] : null;

    try {
        $stmt_in = $siakad_db->prepare("INSERT INTO tugas_daring (guru_id, kelas_id, mata_pelajaran_id, tanggal, batas_waktu, judul, instruksi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_in->execute([$guru_id, $id_kelas, $id_mapel, $tanggal, $batas_waktu, $judul, $instruksi]);
        $success_msg = "Tugas berhasil dikirim ke siswa!";
        
        // Auto-Insert Jurnal jika hari ini adalah Daring
        $stmt_cek_daring = $siakad_db->prepare("SELECT id FROM kalender_akademik WHERE tipe = 'daring' AND ? BETWEEN tanggal_mulai AND tanggal_selesai LIMIT 1");
        $stmt_cek_daring->execute([$tanggal]);
        if ($stmt_cek_daring->fetch()) {
            // Cek apakah jurnal sudah ada
            $stmt_cek_jurnal = $siakad_db->prepare("SELECT id FROM jurnal_guru WHERE guru_id = ? AND kelas_id = ? AND mapel_id = ? AND tanggal = ?");
            $stmt_cek_jurnal->execute([$guru_id, $id_kelas, $id_mapel, $tanggal]);
            if (!$stmt_cek_jurnal->fetch()) {
                $materi_jurnal = "Pembelajaran Daring: " . $judul;
                $ket_jurnal = "Di-generate otomatis dari E-Learning";
                $stmt_jurnal = $siakad_db->prepare("INSERT INTO jurnal_guru (guru_id, kelas_id, mapel_id, tanggal, materi, keterangan) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_jurnal->execute([$guru_id, $id_kelas, $id_mapel, $tanggal, $materi_jurnal, $ket_jurnal]);
            }
        }

        // Notifikasi ke Siswa/Wali
        $stmt_s = $core_db->prepare("SELECT id FROM siswa WHERE kelas_id = ?");
        $stmt_s->execute([$id_kelas]);
        $siswa_list = $stmt_s->fetchAll(PDO::FETCH_ASSOC);

        $target_users = [];
        foreach ($siswa_list as $s) {
            $target_users[] = 'siswa_' . $s['id'];
        }

        if (!empty($target_users)) {
            // Dapatkan nama mapel dan kelas
            $stmt_mapel = $siakad_db->prepare("SELECT nama_mapel FROM mata_pelajaran WHERE id = ?");
            $stmt_mapel->execute([$id_mapel]);
            $nm_mapel = $stmt_mapel->fetchColumn() ?: 'Mapel';

            // Nama guru sudah diset kalau kita ambil dari tabel guru, tapi lebih cepat kalau dari session atau db
            $stmt_g = $core_db->prepare("SELECT nama FROM guru WHERE id = ?");
            $stmt_g->execute([$guru_id]);
            $nm_guru = $stmt_g->fetchColumn() ?: 'Guru Anda';

            $title_notif = "Tugas Baru: " . $nm_mapel;
            $msg_notif = "Ada tugas daring baru dari " . $nm_guru . ". Silakan dicek di aplikasi!";
            
            // Asumsi sendOneSignalNotification tersedia dari koneksi.php
            if (function_exists('sendOneSignalNotification')) {
                sendOneSignalNotification($target_users, $title_notif, $msg_notif);
            }
        }

    } catch (Exception $e) {
        $error_msg = "Gagal menyimpan tugas: " . $e->getMessage();
    }
}

if (isset($_POST['hapus_tugas'])) {
    $hapus_id = $_POST['hapus_id'];
    try {
        $stmt_hapus = $siakad_db->prepare("DELETE FROM tugas_daring WHERE id = ? AND guru_id = ?");
        $stmt_hapus->execute([$hapus_id, $guru_id]);
        $success_msg = "Tugas berhasil dihapus!";
    } catch (Exception $e) {
        $error_msg = "Gagal menghapus tugas: " . $e->getMessage();
    }
}
// Ambil tahun ajaran aktif
$activeTahunId = 1;
$activeSemester = 1;
try {
    $stmtTahun = $core_db->query("SELECT id, semester FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
    if ($thn = $stmtTahun->fetch()) {
        $activeTahunId = $thn['id'];
        $activeSemester = $thn['semester'];
    }
} catch (Exception $e) {}

// Ambil data Kelas yang diajar
$kelas_list = [];
$mapel_list = [];
try {
    $stmt_k = $siakad_db->prepare("
        SELECT DISTINCT k.id, k.nama_kelas, k.tingkat
        FROM (
            SELECT kelas_id FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT kelas_id FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN aula_db.kelas k ON k.id = combined.kelas_id
        ORDER BY k.tingkat, k.nama_kelas
    ");
    $stmt_k->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $kelas_list = $stmt_k->fetchAll(PDO::FETCH_ASSOC);

    $stmt_m = $siakad_db->prepare("
        SELECT DISTINCT mp.id, mp.nama_mapel
        FROM (
            SELECT mapel_id as mapel FROM mengajar WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
            UNION
            SELECT mata_pelajaran_id as mapel FROM jadwal_pelajaran WHERE guru_id = ? AND tahun_ajaran_id = ? AND semester = ?
        ) combined
        JOIN mata_pelajaran mp ON mp.id = combined.mapel
        ORDER BY mp.nama_mapel
    ");
    $stmt_m->execute([$guru_id, $activeTahunId, $activeSemester, $guru_id, $activeTahunId, $activeSemester]);
    $mapel_list = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$riwayat_tugas = [];
try {
    $stmt_riw = $siakad_db->prepare("
        SELECT td.*, k.nama_kelas, m.nama_mapel 
        FROM tugas_daring td
        JOIN aula_db.kelas k ON td.kelas_id = k.id
        JOIN mata_pelajaran m ON td.mata_pelajaran_id = m.id
        WHERE td.guru_id = ?
        ORDER BY td.created_at DESC
        LIMIT 20
    ");
    $stmt_riw->execute([$guru_id]);
    $riwayat_tugas = $stmt_riw->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$page_title = 'Tugas Daring / Mandiri';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
    <div class="header-top">
        <a href="profil_guru.php" style="color: white; text-decoration: none; font-size: 1.2rem;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting">
            <p>Penugasan Siswa</p>
            <h1>Tugas Daring</h1>
        </div>
        <div class="avatar" style="color: #ef4444;">
            <i class="fa-solid fa-laptop-file"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        
        <div style="display:flex; background:#f1f5f9; padding: 5px; border-radius:12px; margin-bottom: 20px;">
            <div id="tab-input" onclick="switchTab('input')" style="flex:1; text-align:center; padding: 10px; background:#fff; border-radius:10px; color:#0ea5e9; font-weight:700; cursor:pointer; box-shadow:0 2px 5px rgba(0,0,0,0.05); transition: 0.2s;">Input Baru</div>
            <div id="tab-riwayat" onclick="switchTab('riwayat')" style="flex:1; text-align:center; padding: 10px; color:#64748b; font-weight:700; cursor:pointer; transition: 0.2s; border-radius:10px;">Riwayat Tugas</div>
        </div>

        <?php if (isset($success_msg)): ?>
            <div style="background: #ecfdf5; color: #10b981; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #a7f3d0;">
                <i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div style="background: #fef2f2; color: #ef4444; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 5px;"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <!-- CONTENT INPUT -->
        <div id="content-input">
            <h3 class="section-title">Buat Tugas Baru</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Silakan isi form di bawah ini untuk memberikan tugas kepada siswa.</p>

            <form action="" method="POST" id="form-tambah-tugas" onsubmit="return submitTugas(this);">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Pilih Kelas</label>
                    <select name="id_kelas" id="id_kelas" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Mata Pelajaran</label>
                    <select name="id_mapel" id="id_mapel" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                        <option value="">-- Pilih Mapel --</option>
                        <?php foreach ($mapel_list as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars(isset($m['nama_mapel']) ? $m['nama_mapel'] : 'Unknown Mapel') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Tanggal Tugas</label>
                            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Batas Waktu Pengumpulan</label>
                            <input type="date" name="batas_waktu" required style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Judul Tugas</label>
                    <input type="text" name="judul" required placeholder="Contoh: Latihan Soal Bab 1" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 0.9rem; color: #475569; margin-bottom: 5px;">Instruksi Tugas</label>
                    <textarea name="instruksi" required rows="5" placeholder="Tuliskan instruksi tugas secara detail..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                </div>

                <button type="submit" name="simpan_tugas" id="btn-submit" style="width: 100%; background: #ef4444; color: white; border: none; padding: 14px; border-radius: 8px; font-size: 1rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);">Kirim Tugas ke Siswa</button>
            </form>
        </div>

        <!-- CONTENT RIWAYAT -->
        <div id="content-riwayat" style="display:none;">
            <h3 class="section-title">Riwayat Tugas Saya</h3>
            <?php if (empty($riwayat_tugas)): ?>
                <div style="text-align: center; padding: 30px 10px; color: #94a3b8;">
                    <i class="fa-solid fa-clock-rotate-left" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p style="margin: 0; font-size: 0.9rem;">Belum ada tugas yang dikirim.</p>
                </div>
            <?php else: ?>
                <?php foreach ($riwayat_tugas as $r): ?>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div>
                                <div style="font-size: 0.75rem; color: #0ea5e9; font-weight: 700; margin-bottom: 3px;"><?= htmlspecialchars($r['nama_kelas']) ?> &bull; <?= htmlspecialchars($r['nama_mapel']) ?></div>
                                <div style="font-size: 1rem; font-weight: bold; color: #1e293b;"><?= htmlspecialchars($r['judul']) ?></div>
                            </div>
                            <div style="font-size: 0.7rem; color: #64748b; text-align: right;">
                                <div style="margin-bottom: 3px;"><i class="fa-regular fa-calendar" style="margin-right: 3px;"></i><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                                <?php if (!empty($r['batas_waktu'])): ?>
                                    <div style="color: #ef4444; font-weight: 600;"><i class="fa-regular fa-clock" style="margin-right: 3px;"></i>Batas: <?= date('d M Y', strtotime($r['batas_waktu'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="font-size: 0.85rem; color: #475569; line-height: 1.5; background: white; padding: 10px; border-radius: 8px; border: 1px dashed #cbd5e1; white-space: pre-wrap;"><?= htmlspecialchars($r['instruksi']) ?></div>
                        <div style="display: flex; gap: 10px; margin-top: 12px; justify-content: flex-end;">
                            <a href="guru_tugas_mandiri_edit.php?id=<?= $r['id'] ?>" style="background: #e0f2fe; color: #0284c7; padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            <form action="" method="POST" onsubmit="confirmDelete(event, this);" style="margin: 0;">
                                <input type="hidden" name="hapus_id" value="<?= $r['id'] ?>">
                                <button type="submit" name="hapus_tugas" style="background: #fee2e2; color: #ef4444; border: none; padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    let tInput = document.getElementById('tab-input');
    let tRiwayat = document.getElementById('tab-riwayat');
    let cInput = document.getElementById('content-input');
    let cRiwayat = document.getElementById('content-riwayat');

    if(tab === 'input') {
        tInput.style.background = '#fff';
        tInput.style.color = '#0ea5e9';
        tInput.style.boxShadow = '0 2px 5px rgba(0,0,0,0.05)';
        
        tRiwayat.style.background = 'transparent';
        tRiwayat.style.color = '#64748b';
        tRiwayat.style.boxShadow = 'none';

        cInput.style.display = 'block';
        cRiwayat.style.display = 'none';
    } else {
        tRiwayat.style.background = '#fff';
        tRiwayat.style.color = '#0ea5e9';
        tRiwayat.style.boxShadow = '0 2px 5px rgba(0,0,0,0.05)';
        
        tInput.style.background = 'transparent';
        tInput.style.color = '#64748b';
        tInput.style.boxShadow = 'none';

        cRiwayat.style.display = 'block';
        cInput.style.display = 'none';
    }
}

function submitTugas(form) {
    let btn = document.getElementById('btn-submit');
    // Change button state
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
    btn.style.background = '#94a3b8';
    btn.style.cursor = 'not-allowed';
    btn.style.boxShadow = 'none';
    
    // We cannot just return true, otherwise it prevents double click but if we disable it, the form won't submit the button's name (which PHP relies on).
    // So we append a hidden input to simulate the button press.
    let hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'simpan_tugas';
    hidden.value = '1';
    form.appendChild(hidden);
    
    // Use setTimeout to allow the form to submit before disabling the button
    setTimeout(() => {
        btn.disabled = true;
    }, 10);
    
    return true;
}

function confirmDelete(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus Tugas?',
        text: "Tugas yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            let hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'hapus_tugas';
            hidden.value = '1';
            form.appendChild(hidden);
            form.submit();
        }
    });
}
</script>

<?php include 'footer_guru.php'; ?>
