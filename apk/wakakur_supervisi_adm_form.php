<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
if (!$guru_id) {
    header("Location: wakakur_supervisi_adm.php");
    exit;
}

// Get current year
$stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
$tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

// Get guru info
$stmtGuru = $core_db->prepare("SELECT id, nama, nip, foto FROM guru WHERE id = ?");
$stmtGuru->execute([$guru_id]);
$guru = $stmtGuru->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    header("Location: wakakur_supervisi_adm.php");
    exit;
}

// Get existing supervisi
$stmtSup = $core_db->prepare("SELECT * FROM kurikulum_supervisi_administrasi WHERE tahun_ajaran_id = ? AND guru_id = ?");
$stmtSup->execute([$tahun_ajaran_id, $guru_id]);
$supervisi = $stmtSup->fetch(PDO::FETCH_ASSOC);

$instrumen = [];
if ($supervisi && !empty($supervisi['instrumen_json'])) {
    $instrumen = json_decode($supervisi['instrumen_json'], true);
}

// Get uploaded berkas
$stmtBerkas = $core_db->prepare("SELECT id, kategori, mapel_nama, kelas_nama, nama_berkas, file_path, status_review FROM guru_perangkat WHERE guru_id = ?");
$stmtBerkas->execute([$guru_id]);
$berkas_list = $stmtBerkas->fetchAll(PDO::FETCH_ASSOC);

$berkas_by_category = [
    'cp' => [],
    'atp' => [],
    'modul' => [],
    'kktp' => [],
    'prota' => [],
    'promes' => []
];

foreach ($berkas_list as $b) {
    if ($b['kategori'] == 'Capaian Pembelajaran (CP)') $berkas_by_category['cp'][] = $b;
    else if ($b['kategori'] == 'Alur Tujuan Pembelajaran (ATP)') $berkas_by_category['atp'][] = $b;
    else if ($b['kategori'] == 'Modul Ajar' || $b['kategori'] == 'Modul Ajar & KKTP') $berkas_by_category['modul'][] = $b;
    else if ($b['kategori'] == 'KKTP') $berkas_by_category['kktp'][] = $b;
    else if ($b['kategori'] == 'Program Tahunan (Prota)') $berkas_by_category['prota'][] = $b;
    else if ($b['kategori'] == 'Program Semester (Promes)') $berkas_by_category['promes'][] = $b;
}

$stmtMapel = $siakad_db->prepare("SELECT DISTINCT mp.id as mapel_id, mp.nama_mapel, k.id as kelas_id, k.nama_kelas FROM jadwal_pelajaran jp JOIN mata_pelajaran mp ON mp.id = jp.mata_pelajaran_id JOIN kelas k ON k.id = jp.kelas_id WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? ORDER BY mp.nama_mapel, k.nama_kelas");
$stmtMapel->execute([$guru_id, $tahun_ajaran_id]);
$mapelList = $stmtMapel->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Form Evaluasi Adm';
include 'header.php';

$foto_url = !empty($guru['foto']) ? (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/public/uploads/guru/" . $guru['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($guru['nama']) . '&background=f1f5f9&color=475569';
?>
<style>
.form-group { margin-bottom: 15px; }
.form-label { display: block; margin-bottom: 6px; font-weight: 700; color: #1e293b; font-size: 0.85rem; }
.form-select, .form-input { 
    width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #cbd5e1; 
    font-size: 0.9rem; font-family: inherit; background: #f8fafc; color: #1e293b;
    outline: none;
}
.form-select:focus, .form-input:focus { border-color: #0ea5e9; background: #fff; }
.card-item {
    background: #fff; padding: 15px; border-radius: 12px; margin-bottom: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
}
.score-radio {
    display: flex; gap: 8px; margin-top: 8px;
}
.score-radio label {
    flex: 1; text-align: center; background: #f1f5f9; padding: 8px 0; border-radius: 6px;
    font-weight: 700; font-size: 0.8rem; color: #64748b; cursor: pointer; border: 1px solid transparent;
}
.score-radio input[type="radio"] { display: none; }
.score-radio input[type="radio"]:checked + label {
    background: #0ea5e915; border-color: #0ea5e9; color: #0ea5e9;
}
</style>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Form Evaluasi</p>
            <h1 style="font-size: 1.1rem;">Supervisi Administrasi</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.location.href='wakakur_supervisi_adm.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; position: relative; z-index: 10; padding: 15px; display: flex; align-items: center; gap: 15px; background: white; border-radius: 12px; margin-bottom: 20px;">
        <img src="<?= $foto_url ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
        <div>
            <div style="font-weight: 800; color: #1e293b;"><?= htmlspecialchars($guru['nama']) ?></div>
            <div style="font-size: 0.8rem; color: #64748b;">NIP: <?= empty($guru['nip']) ? '-' : $guru['nip'] ?></div>
        </div>
    </div>

    <form id="formSupervisi">
        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
        
        <div class="card-item">
            <div class="form-group">
                <label class="form-label">Tanggal Supervisi</label>
                <input type="date" name="tanggal_supervisi" class="form-input" value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
            </div>
        </div>

        <div class="card-item">
            <h3 style="font-size: 0.95rem; font-weight: 800; margin: 0 0 10px 0; color: #334155;"><i class="fa-solid fa-list-check" style="color: #0ea5e9; margin-right: 5px;"></i> Daftar Mengajar</h3>
            <?php if(empty($mapelList)): ?>
                <div style="font-size: 0.8rem; color: #64748b; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px dashed #cbd5e1; text-align: center;">Belum ada jadwal mengajar</div>
            <?php else: ?>
                <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem; color: #475569; display: flex; flex-direction: column; gap: 6px;">
                    <?php foreach($mapelList as $m): ?>
                        <li><?= htmlspecialchars($m['nama_mapel']) ?> <span style="font-weight: 700; color: #0284c7;">(<?= htmlspecialchars($m['nama_kelas']) ?>)</span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <h3 style="font-size: 0.95rem; font-weight: 800; margin: 20px 0 10px 5px; color: #334155;">Instrumen Kelengkapan</h3>
        
        <?php 
        $items = [
            'cp' => 'Capaian Pembelajaran (CP)',
            'atp' => 'Alur Tujuan Pembelajaran (ATP)',
            'modul' => 'Modul Ajar',
            'kktp' => 'KKTP',
            'prota' => 'Program Tahunan',
            'promes' => 'Program Semester',
            'absen' => 'Buku Daftar Hadir Siswa',
            'jurnal' => 'Buku Jurnal Mengajar',
            'daftar_nilai' => 'Buku Daftar Nilai'
        ];
        
        foreach($items as $key => $label): 
            $val = $instrumen[$key] ?? '';
        ?>
        <div class="card-item">
            <div style="font-weight: 700; font-size: 0.85rem; color: #1e293b;"><?= $label ?></div>
            
            <?php if (in_array($key, ['cp', 'atp', 'modul', 'kktp', 'prota', 'promes'])): ?>
                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 5px;">
                    <?php if(empty($mapelList)): ?>
                        <div style="font-size: 0.75rem; color: #64748b;">Belum ada jadwal mengajar</div>
                    <?php else: ?>
                        <?php foreach($mapelList as $m): 
                            $group_key = $m['nama_mapel'] . ' - Kelas ' . $m['nama_kelas'];
                            $berkas_for_mapel = [];
                            foreach($berkas_by_category[$key] as $b) {
                                $k = $b['kelas_nama'] ?? 'Umum';
                                $map = $b['mapel_nama'] ?? '-';
                                if ($map . ' - Kelas ' . $k === $group_key) {
                                    $berkas_for_mapel[] = $b;
                                }
                            }
                            
                            $bg_header = empty($berkas_for_mapel) ? '#ef4444' : '#f8fafc';
                            $text_header = empty($berkas_for_mapel) ? '#ffffff' : '#475569';
                            $icon_header = empty($berkas_for_mapel) ? '#ffffff' : '#64748b';
                            $bg_body = empty($berkas_for_mapel) ? '#fff5f5' : 'white';
                            $border_color = empty($berkas_for_mapel) ? '#ef4444' : '#e2e8f0';
                        ?>
                            <div style="margin-bottom: 5px; background: <?= $bg_body ?>; border: 1px solid <?= $border_color ?>; border-radius: 8px; overflow: hidden;">
                                <div style="background: <?= $bg_header ?>; padding: 6px 10px; font-size: 0.75rem; font-weight: 700; color: <?= $text_header ?>; border-bottom: 1px solid <?= $border_color ?>;">
                                    <i class="fa-solid fa-folder-open" style="color: <?= $icon_header ?>; margin-right: 4px;"></i> <?= htmlspecialchars($group_key) ?>
                                </div>
                                <div style="padding: 8px; display: flex; flex-direction: column; gap: 5px;">
                                    <?php if(empty($berkas_for_mapel)): ?>
                                        <div style="font-size: 0.7rem; color: #ef4444; background: white; padding: 6px 10px; border-radius: 6px; border: 1px solid #fecaca; display: inline-block; align-self: flex-start;">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Berkas belum diupload
                                        </div>
                                    <?php else: ?>
                                        <?php foreach($berkas_for_mapel as $b): ?>
                                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f0f9ff; padding: 6px 10px; border-radius: 6px; border: 1px solid #bae6fd;">
                                                <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                                                    <div style="display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.75rem; color: #0284c7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        <i class="fa-solid fa-file-pdf"></i> <?= htmlspecialchars($b['nama_berkas']) ?>
                                                    </div>
                                                </div>
                                                <a href="pdf_viewer.php?type=perangkat&id=<?= $b['id'] ?>" style="background: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 10px;">
                                                    <i class="fa-solid fa-eye"></i> Lihat
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php 
                        // Tambahan untuk berkas 'Umum' atau mapel yang tidak ada di jadwal
                        $berkas_lainnya = [];
                        foreach($berkas_by_category[$key] as $b) {
                            $k = $b['kelas_nama'] ?? 'Umum';
                            $map = $b['mapel_nama'] ?? '-';
                            $b_key = $map . ' - Kelas ' . $k;
                            
                            $found = false;
                            foreach($mapelList as $m) {
                                if ($m['nama_mapel'] . ' - Kelas ' . $m['nama_kelas'] === $b_key) {
                                    $found = true; break;
                                }
                            }
                            if(!$found) $berkas_lainnya[] = $b;
                        }
                        if(!empty($berkas_lainnya)):
                        ?>
                            <div style="margin-bottom: 5px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <div style="background: #f8fafc; padding: 6px 10px; font-size: 0.75rem; font-weight: 700; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                    <i class="fa-solid fa-folder-open" style="color: #64748b; margin-right: 4px;"></i> Lainnya / Umum
                                </div>
                                <div style="padding: 8px; display: flex; flex-direction: column; gap: 5px;">
                                    <?php foreach($berkas_lainnya as $b): ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; background: #f0f9ff; padding: 6px 10px; border-radius: 6px; border: 1px solid #bae6fd;">
                                            <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                                                <div style="display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.75rem; color: #0284c7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <i class="fa-solid fa-file-pdf"></i> <?= htmlspecialchars($b['nama_berkas']) ?> <span style="font-size: 0.65rem; color: #64748b; font-weight: 400;">(<?= htmlspecialchars(($b['kelas_nama'] ?? 'Umum') . ' - ' . ($b['mapel_nama'] ?? '-')) ?>)</span>
                                                </div>
                                            </div>
                                            <a href="pdf_viewer.php?type=perangkat&id=<?= $b['id'] ?>" style="background: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 10px;">
                                                <i class="fa-solid fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($key == 'jurnal'): ?>
                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 5px;">
                    <?php if(empty($mapelList)): ?>
                        <div style="font-size: 0.75rem; color: #64748b;">Belum ada jadwal mengajar</div>
                    <?php else: ?>
                        <?php foreach($mapelList as $m): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f0fdf4; padding: 6px 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: #16a34a;">
                                    <i class="fa-solid fa-book-open"></i> Jurnal - <?= htmlspecialchars($m['nama_mapel'] . ' (' . $m['nama_kelas'] . ')') ?>
                                </div>
                                <a href="wakakur_supervisi_jurnal_tabel.php?guru_id=<?= $guru_id ?>&mapel_id=<?= $m['mapel_id'] ?>&kelas_id=<?= $m['kelas_id'] ?>" target="_blank" style="background: #dcfce7; color: #16a34a; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 10px;">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($key == 'absen'): ?>
                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 5px;">
                    <?php if(empty($mapelList)): ?>
                        <div style="font-size: 0.75rem; color: #64748b;">Belum ada jadwal mengajar</div>
                    <?php else: ?>
                        <?php foreach($mapelList as $m): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f0f9ff; padding: 6px 10px; border-radius: 6px; border: 1px solid #bae6fd;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: #0284c7;">
                                    <i class="fa-solid fa-users"></i> Absen - <?= htmlspecialchars($m['nama_mapel'] . ' (' . $m['nama_kelas'] . ')') ?>
                                </div>
                                <a href="wakakur_supervisi_absen_tabel.php?guru_id=<?= $guru_id ?>&mapel_id=<?= $m['mapel_id'] ?>&kelas_id=<?= $m['kelas_id'] ?>" target="_blank" style="background: #e0f2fe; color: #0284c7; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 10px;">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php elseif ($key == 'daftar_nilai'): ?>
                <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 5px;">
                    <?php if(empty($mapelList)): ?>
                        <div style="font-size: 0.75rem; color: #64748b;">Belum ada jadwal mengajar</div>
                    <?php else: ?>
                        <?php foreach($mapelList as $m): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #fffbeb; padding: 6px 10px; border-radius: 6px; border: 1px solid #fde68a;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; color: #d97706;">
                                    <i class="fa-solid fa-star"></i> Nilai - <?= htmlspecialchars($m['nama_mapel'] . ' (' . $m['nama_kelas'] . ')') ?>
                                </div>
                                <a href="wakakur_supervisi_nilai_tabel.php?guru_id=<?= $guru_id ?>&mapel_id=<?= $m['mapel_id'] ?>&kelas_id=<?= $m['kelas_id'] ?>" target="_blank" style="background: #fef3c7; color: #d97706; padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: 10px;">
                                    <i class="fa-solid fa-eye"></i> Lihat
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="score-radio">
                <?php for($i=4; $i>=1; $i--): ?>
                    <input type="radio" name="instrumen[<?= $key ?>]" id="i_<?= $key ?>_<?= $i ?>" value="<?= $i ?>" <?= $val == $i ? 'checked' : '' ?>>
                    <label for="i_<?= $key ?>_<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="card-item" style="margin-top: 20px;">
            <div class="form-group">
                <label class="form-label">Catatan Supervisi</label>
                <textarea name="catatan" class="form-input" rows="4" style="resize:none;" placeholder="Berikan catatan..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Rencana Tindak Lanjut</label>
                <select name="tindak_lanjut" class="form-select">
                    <option value="">-- Pilih --</option>
                    <?php 
                    $tls = ['Apresiasi / Dipertahankan', 'Pembinaan Internal (Kepsek/Waka)', 'Teguran Lisan / Tertulis', 'Diikutsertakan KKG / Diklat'];
                    $ct = $supervisi['tindak_lanjut'] ?? '';
                    foreach($tls as $t): ?>
                        <option value="<?= $t ?>" <?= $ct === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="padding-bottom: 30px;">
            <button type="submit" id="btnSubmit" style="width:100%; padding:15px; border-radius:12px; background: #0ea5e9; color:white; font-weight:800; font-size:1rem; border:none; margin-top: 10px;">
                <i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('formSupervisi').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin" style="margin-right: 8px;"></i> Menyimpan...';
    
    const formData = new FormData(this);
    
    fetch('api_wakakur_supervisi_adm_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'wakakur_supervisi_adm.php';
            });
        } else {
            Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi';
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Gagal menghubungi server', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan Supervisi';
    });
});
</script>

<?php include 'footer_guru.php'; ?>
