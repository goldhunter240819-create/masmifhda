<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = $_SESSION['guru_id'];

// Cek Wali Kelas
$stmt_ta = $core_db->query("SELECT id, name FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
$ta_aktif = $stmt_ta->fetch(PDO::FETCH_ASSOC);
$ta_id = $ta_aktif['id'] ?? 0;
$ta_name = $ta_aktif['name'] ?? '';

$stmt_wk = $core_db->prepare("SELECT gt.id, gt.keterangan as nama_kelas, k.id as kelas_id FROM guru_tugas gt JOIN kelas k ON (gt.keterangan = CONCAT('Kelas ', k.nama_kelas) OR gt.keterangan = k.nama_kelas) WHERE gt.guru_id = :gid AND gt.tugas = 'Wali Kelas' LIMIT 1");
$stmt_wk->execute(['gid' => $guru_id]);
$wali_kelas = $stmt_wk->fetch(PDO::FETCH_ASSOC);

if (!$wali_kelas) {
    header("Location: akses_ditolak.php");
    exit;
}

$kelas_id = $wali_kelas['kelas_id'] ?? 0;
$nama_kelas_wk = $wali_kelas['nama_kelas'];

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $struktur = [
        'ketua' => $_POST['ketua'] ?? '',
        'wakil_ketua' => $_POST['wakil_ketua'] ?? '',
        'sekretaris' => $_POST['sekretaris'] ?? '',
        'bendahara' => $_POST['bendahara'] ?? ''
    ];

    $jadwal_piket = [
        'Senin' => $_POST['piket_senin'] ?? [],
        'Selasa' => $_POST['piket_selasa'] ?? [],
        'Rabu' => $_POST['piket_rabu'] ?? [],
        'Kamis' => $_POST['piket_kamis'] ?? [],
        'Jumat' => $_POST['piket_jumat'] ?? [],
        'Sabtu' => $_POST['piket_sabtu'] ?? []
    ];

    $cetak_info = [
        'tempat' => $_POST['cetak_tempat'] ?? 'Lampung Barat',
        'tanggal' => $_POST['cetak_tanggal'] ?? date('Y-m-d')
    ];

    $data_to_save = [
        'struktur' => json_encode($struktur),
        'jadwal_piket' => json_encode($jadwal_piket),
        'cetak_info' => json_encode($cetak_info)
    ];

    foreach ($data_to_save as $key => $value) {
        $stmt = $core_db->prepare("INSERT INTO kelas_meta (kelas_id, tahun_ajaran_id, meta_key, meta_value) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)");
        $stmt->execute([$kelas_id, $ta_id, $key, $value]);
    }
    
    $success_msg = "Pengaturan Program Wali Kelas berhasil disimpan!";
}

// Ambil meta saat ini
$stmt = $core_db->prepare("SELECT meta_key, meta_value FROM kelas_meta WHERE kelas_id = ? AND tahun_ajaran_id = ?");
$stmt->execute([$kelas_id, $ta_id]);
$metas = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

$struktur = isset($metas['struktur']) ? json_decode($metas['struktur'], true) : ['ketua'=>'', 'wakil_ketua'=>'', 'sekretaris'=>'', 'bendahara'=>''];
$jadwal_piket = isset($metas['jadwal_piket']) ? json_decode($metas['jadwal_piket'], true) : [];
$cetak_info = isset($metas['cetak_info']) ? json_decode($metas['cetak_info'], true) : ['tempat'=>'Lampung Barat', 'tanggal'=>date('Y-m-d')];

// Ambil daftar siswa kelas ini
$stmt_siswa = $core_db->prepare("SELECT id, nama FROM siswa WHERE kelas_id = ? AND status = 'Aktif' ORDER BY nama ASC");
$stmt_siswa->execute([$kelas_id]);
$siswas = $stmt_siswa->fetchAll(PDO::FETCH_ASSOC);

$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

$page_title = 'Program Wali Kelas';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); padding-top: 3.5rem;">
    <div class="header-top">
        <div class="greeting">
            <p>Buku Wali Kelas</p>
            <h1>Program Wali Kelas</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;">
            <i class="fa-solid fa-list-check"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <?php if (isset($success_msg)): ?>
        <script>
            Swal.fire({icon: 'success', title: 'Berhasil', text: '<?= addslashes($success_msg) ?>', confirmButtonColor: '#0ea5e9'});
        </script>
    <?php endif; ?>

    <div class="data-card" style="margin-top: -2.5rem; z-index: 10;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 class="section-title" style="margin: 0;"><i class="fa-solid fa-print" style="color: #0ea5e9; margin-right: 5px;"></i> Dokumen Program</h3>
        </div>
        <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 15px;">Anda dapat mencetak PDF Buku Program Wali Kelas langsung dari sini.</p>
        
        <a href="/siakad/guru/wali-kelas/program/cetak" target="_blank" style="display: block; text-align: center; background: #0ea5e9; color: white; padding: 12px; border-radius: 8px; font-weight: 600; text-decoration: none;">
            <i class="fa-solid fa-file-pdf"></i> Preview & Cetak PDF Program
        </a>
    </div>

    <form method="POST">
        <input type="hidden" name="action" value="save">
        
        <h3 class="section-title" style="margin-top: 25px;"><i class="fa-solid fa-sitemap" style="color: #0ea5e9; margin-right: 5px;"></i> Struktur Organisasi</h3>
        <div style="background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Ketua Kelas</label>
                <select name="ketua" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;" required>
                    <option value="">-- Pilih Ketua --</option>
                    <?php foreach($siswas as $s): ?>
                    <option value="<?= htmlspecialchars($s['nama']) ?>" <?= (($struktur['ketua']??'') == $s['nama']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Wakil Ketua</label>
                <select name="wakil_ketua" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;">
                    <option value="">-- Pilih Wakil Ketua --</option>
                    <?php foreach($siswas as $s): ?>
                    <option value="<?= htmlspecialchars($s['nama']) ?>" <?= (($struktur['wakil_ketua']??'') == $s['nama']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Sekretaris</label>
                <select name="sekretaris" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;" required>
                    <option value="">-- Pilih Sekretaris --</option>
                    <?php foreach($siswas as $s): ?>
                    <option value="<?= htmlspecialchars($s['nama']) ?>" <?= (($struktur['sekretaris']??'') == $s['nama']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Bendahara</label>
                <select name="bendahara" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: #f8fafc;" required>
                    <option value="">-- Pilih Bendahara --</option>
                    <?php foreach($siswas as $s): ?>
                    <option value="<?= htmlspecialchars($s['nama']) ?>" <?= (($struktur['bendahara']??'') == $s['nama']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 class="section-title"><i class="fa-solid fa-broom" style="color: #0ea5e9; margin-right: 5px;"></i> Jadwal Piket</h3>
        <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 15px;">Pilih nama siswa yang piket pada hari-hari berikut. (Bisa pilih lebih dari 1 siswa)</p>
        
        <div style="background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px;">
            <?php foreach($days as $day): 
                $day_lower = strtolower($day);
                $selected_piket = $jadwal_piket[$day] ?? [];
                if (is_string($selected_piket)) $selected_piket = array_map('trim', explode("\n", $selected_piket));
            ?>
            <div style="margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Hari <?= $day ?></label>
                
                <div style="max-height: 150px; overflow-y: auto; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px;">
                    <?php foreach($siswas as $s): 
                        $is_selected = in_array($s['nama'], $selected_piket);
                    ?>
                    <label style="display: flex; align-items: center; margin-bottom: 8px; font-size: 0.85rem; color: #334155;">
                        <input type="checkbox" name="piket_<?= $day_lower ?>[]" value="<?= htmlspecialchars($s['nama']) ?>" <?= $is_selected ? 'checked' : '' ?> style="margin-right: 8px; transform: scale(1.2);">
                        <?= htmlspecialchars($s['nama']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <h3 class="section-title"><i class="fa-solid fa-print" style="color: #0ea5e9; margin-right: 5px;"></i> Pengaturan TTD Cetak</h3>
        <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 15px;">Informasi tempat dan tanggal untuk dicetak pada bagian tanda tangan PDF.</p>
        
        <div style="background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Tempat Cetak</label>
                <input type="text" name="cetak_tempat" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;" value="<?= htmlspecialchars($cetak_info['tempat'] ?? 'Lampung Barat') ?>" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 5px;">Tanggal Cetak</label>
                <input type="date" name="cetak_tanggal" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;" value="<?= htmlspecialchars($cetak_info['tanggal'] ?? date('Y-m-d')) ?>" required>
            </div>
        </div>

        <button type="submit" style="width: 100%; background: #0ea5e9; color: white; border: none; padding: 14px; border-radius: 8px; font-weight: 700; cursor: pointer; margin-bottom: 20px; font-size: 0.95rem; box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.3);">
            <i class="fa-solid fa-save"></i> Simpan Pengaturan
        </button>
    </form>

    <a href="wali_kelas.php" class="btn" style="display: block; text-align: center; background: #f1f5f9; color: #475569; padding: 12px; border-radius: 50px; font-weight: 600; text-decoration: none; margin-bottom: 30px;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Wali Kelas
    </a>
</div>

<?php include 'footer_guru.php'; ?>
