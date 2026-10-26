<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) { header("Location: login.php"); exit; }

$logged_in_guru_id = $_SESSION['guru_id'];
$is_kamad = false;
$is_wakakur = false;

// Cek hak akses
$stmt_akses = $core_db->prepare("SELECT is_kamad FROM guru WHERE id = :id LIMIT 1");
$stmt_akses->execute(['id' => $logged_in_guru_id]);
$logged_in_guru = $stmt_akses->fetch(PDO::FETCH_ASSOC);

if ($logged_in_guru && $logged_in_guru['is_kamad'] == 1) {
    $is_kamad = true;
} else {
    $stmt_w = $core_db->prepare("SELECT id FROM guru_tugas WHERE guru_id = ? AND (tugas LIKE '%Kurikulum%' OR tugas LIKE '%Waka Kurikulum%') LIMIT 1");
    $stmt_w->execute([$logged_in_guru_id]);
    if ($stmt_w->fetch()) {
        $is_wakakur = true;
    }
}

if (!$is_kamad && !$is_wakakur) {
    echo "<script>alert('Akses Ditolak'); window.location.href='aplikasi_saya.php';</script>";
    exit;
}

$guru_id = isset($_GET['guru_id']) ? (int)$_GET['guru_id'] : 0;
if (!$guru_id) {
    header("Location: wakakur_supervisi_kunjungan.php");
    exit;
}

$stmt_inst = $core_db->query("SELECT tahun_ajaran_id FROM institusi LIMIT 1");
$tahun_ajaran_id = $stmt_inst->fetchColumn() ?: 1;

$stmtGuru = $core_db->prepare("SELECT id, nama, nip, foto FROM guru WHERE id = ?");
$stmtGuru->execute([$guru_id]);
$guru = $stmtGuru->fetch(PDO::FETCH_ASSOC);

if (!$guru) {
    echo "Guru tidak ditemukan."; exit;
}

// Get existing supervisi
$stmtSup = $core_db->prepare("SELECT * FROM kurikulum_supervisi_kelas WHERE tahun_ajaran_id = ? AND guru_id = ?");
$stmtSup->execute([$tahun_ajaran_id, $guru_id]);
$supervisi = $stmtSup->fetch(PDO::FETCH_ASSOC);

$instrumen = [];
if ($supervisi && !empty($supervisi['instrumen_json'])) {
    $instrumen = json_decode($supervisi['instrumen_json'], true);
}

// Get mapel_id dan kelas_id yang diajarkan guru ini
$stmtMapel = $siakad_db->prepare("SELECT DISTINCT mp.id as mapel_id, mp.nama_mapel, k.id as kelas_id, k.nama_kelas FROM jadwal_pelajaran jp JOIN mata_pelajaran mp ON mp.id = jp.mata_pelajaran_id JOIN kelas k ON k.id = jp.kelas_id WHERE jp.guru_id = ? AND jp.tahun_ajaran_id = ? ORDER BY mp.nama_mapel, k.nama_kelas");
$stmtMapel->execute([$guru_id, $tahun_ajaran_id]);
$mapelList = $stmtMapel->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Form Supervisi Kunjungan Kelas';
include 'header.php';
?>
<style>
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #475569; font-size: 0.9rem; }
    .form-input { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
    .form-input:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1); }
    
    .supervisi-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.85rem; }
    .supervisi-table th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; text-align: center; font-weight: 700; color: #334155; }
    .supervisi-table td { border: 1px solid #e2e8f0; padding: 10px; vertical-align: top; }
    .supervisi-table .section-title { font-weight: 700; background: #f1f5f9; }
    
    .score-radio { display: flex; gap: 8px; width: 100%; margin-top: 10px; }
    .score-radio input[type="radio"] { display: none; }
    .score-radio label { 
        flex: 1; padding: 10px 0; text-align: center;
        border: 2px solid #cbd5e1; border-radius: 8px; cursor: pointer; color: #64748b; font-weight: 700; font-size: 1.1rem;
        background: white; transition: all 0.2s ease;
    }
    .score-radio label:hover { border-color: #94a3b8; }
    
    /* Dynamic Colors for 1, 2, 3, 4 */
    .score-radio input[value="1"]:checked + label { background: #ef4444; color: white; border-color: #ef4444; }
    .score-radio input[value="2"]:checked + label { background: #f97316; color: white; border-color: #f97316; }
    .score-radio input[value="3"]:checked + label { background: #eab308; color: white; border-color: #eab308; }
    .score-radio input[value="4"]:checked + label { background: #22c55e; color: white; border-color: #22c55e; }
    
    .sticky-bottom { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px; box-shadow: 0 -4px 6px -1px rgba(0,0,0,0.05); z-index: 100; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; max-width: 500px; margin: 0 auto; }
</style>

<div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
    <div class="header-top">
        <div class="greeting">
            <p><?= htmlspecialchars($guru['nama']) ?></p>
            <h1 style="font-size: 1.1rem;">Supervisi Kelas</h1>
        </div>
        <div class="avatar" style="color: #d97706; background: white;" onclick="window.location.href='wakakur_supervisi_kunjungan.php'">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 100px;">
    <form id="formSupervisi">
        <input type="hidden" name="guru_id" value="<?= $guru_id ?>">
        <div class="data-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-top: -20px; position: relative; z-index: 10;">
            <div class="form-group">
                <label class="form-label">Tanggal Supervisi</label>
                <input type="date" name="tanggal_supervisi" class="form-input" value="<?= $supervisi['tanggal_supervisi'] ?? date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Mata Pelajaran - Kelas</label>
                <select name="mapel_kelas" class="form-input" required>
                    <option value="">-- Pilih Mapel & Kelas --</option>
                    <?php 
                    $selected_mk = '';
                    if ($supervisi && !empty($supervisi['mapel_id']) && !empty($supervisi['kelas_id'])) {
                        $selected_mk = $supervisi['mapel_id'] . '-' . $supervisi['kelas_id'];
                    }
                    foreach($mapelList as $m): 
                        $val = $m['mapel_id'] . '-' . $m['kelas_id'];
                    ?>
                        <option value="<?= $val ?>" <?= $val == $selected_mk ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nama_mapel']) ?> - <?= htmlspecialchars($m['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="background: #fffbeb; border: 1px solid #fcd34d; padding: 12px; border-radius: 8px; font-size: 0.8rem; color: #b45309; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> Pilih skor <b>1, 2, 3, atau 4</b> pada setiap aspek yang diamati (4 = Sangat Baik, 1 = Kurang).
            </div>
            
            <table class="supervisi-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="90%">Aspek yang diamati & Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- KEGIATAN AWAL -->
                    <tr>
                        <td class="section-title">A</td>
                        <td class="section-title">Kegiatan Awal</td>
                    </tr>
                    <tr>
                        <td align="center">1</td>
                        <td>
                            <b>Kegiatan apersepsi</b><br>
                            a. Menyebutkan tujuan pembelajaran<br>
                            b. Memberikan contoh-contoh dalam kehidupan sehari-hari<br>
                            c. Manfaat belajar materi yang disampaikan. (KBC)
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v1 = $instrumen[1] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[1]" id="i_1_<?= $i ?>" value="<?= $i ?>" <?= $v1 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_1_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">2</td>
                        <td>
                            <b>Memahami</b><br>
                            Guru memberikan stimulus untuk membangkitkan rasa ingin tahu peserta didik dan mulai mengidentifikasi topik yang akan dieksplorasi (Berkesadaran dan Bermakna)/KBC
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v2 = $instrumen[2] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[2]" id="i_2_<?= $i ?>" value="<?= $i ?>" <?= $v2 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_2_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- KEGIATAN INTI -->
                    <tr>
                        <td class="section-title">B</td>
                        <td class="section-title">Kegiatan Inti</td>
                    </tr>
                    <tr>
                        <td align="center">3</td>
                        <td>
                            <b>Memahami</b><br>
                            Peserta didik mengidentifikasi dan merumuskan pertanyaan yang akan dijawab dalam proses inkuiri dan guru membimbing peserta didik untuk menyusun hipotesis atau dugaan awal.<br>
                            (Berkesadaran dan Bermakna) / KBC
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v3 = $instrumen[3] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[3]" id="i_3_<?= $i ?>" value="<?= $i ?>" <?= $v3 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_3_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">4</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Peserta didik mengumpulkan informasi dari berbagai sumber, seperti eksperimen, observasi, wawancara, atau literatur dan mencari pola atau hubungan antar konsep.<br>
                            (Bermakna, Mengembirakan)
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v4 = $instrumen[4] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[4]" id="i_4_<?= $i ?>" value="<?= $i ?>" <?= $v4 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_4_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">5</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Peserta didik mengorganisasi dan menafsirkan data yang telah dikumpulkan, menggunakan berbagai teknik analisis.<br>
                            (Bermakna, Mengembirakan) / KBC
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v5 = $instrumen[5] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[5]" id="i_5_<?= $i ?>" value="<?= $i ?>" <?= $v5 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_5_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">6</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Peserta didik menyusun kesimpulan berdasarkan bukti dan hasil analisis, serta menjelaskan temuan dan menghubungkannya dengan konsep atau teori yang relevan.<br>
                            (Bermakna, Mengembirakan)
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v6 = $instrumen[6] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[6]" id="i_6_<?= $i ?>" value="<?= $i ?>" <?= $v6 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_6_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">7</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Peserta didik menyampaikan hasil temuannya melalui presentasi, laporan tertulis, atau diskusi kelas.<br>
                            (Bermakna, Mengembirakan) / KBC
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v7 = $instrumen[7] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[7]" id="i_7_<?= $i ?>" value="<?= $i ?>" <?= $v7 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_7_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- KEGIATAN PENUTUP -->
                    <tr>
                        <td class="section-title">C</td>
                        <td class="section-title">Kegiatan Penutup</td>
                    </tr>
                    <tr>
                        <td align="center">8</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Melakukan refleksi terhadap proses pembelajaran: apa yang telah dipelajari, kesulitan yang dihadapi, dan bagaimana meningkatkan pemahaman lebih lanjut, serta guru memberikan umpan balik konstruktif untuk memperkaya pengalaman belajar peserta didik.<br>
                            (Berkesadaran, Bermakna)
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v8 = $instrumen[8] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[8]" id="i_8_<?= $i ?>" value="<?= $i ?>" <?= $v8 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_8_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">9</td>
                        <td>
                            <b>Mengaplikasi</b><br>
                            Peserta didik menerapkan hasil pembelajaran dalam konteks nyata atau proyek lanjutan dan mendorong eksplorasi lebih lanjut untuk memperdalam pemahaman konsep.<br>
                            (Berkesadaran, Bermakna)
                            <div class="score-radio" style="margin-top: 15px; justify-content: flex-start;">
                                <?php $v9 = $instrumen[9] ?? ''; ?>
                                <?php for($i=4; $i>=1; $i--): ?>
                                    <input type="radio" name="instrumen[9]" id="i_9_<?= $i ?>" value="<?= $i ?>" <?= $v9 == $i ? 'checked' : '' ?> onchange="calculate()">
                                    <label for="i_9_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            
            <div style="background: #f1f5f9; padding: 10px; border-radius: 8px; font-size: 0.75rem; color: #64748b; margin-top: 15px; margin-bottom: 15px; text-align: center;">
                <b>Keterangan:</b> Amat baik (A): 90-100 | Baik (B): 80-90 | Cukup (C): 70-80 | Kurang (K): ≤ 70
            </div>

            <div id="scoreCard" style="background: #f1f5f9; border: 2px solid #cbd5e1; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; margin-bottom: 5px; opacity: 0.8;">Nilai Akhir (Skor/36*100)</div>
                <div style="display: flex; justify-content: center; align-items: baseline; gap: 10px;">
                    <div id="txtNilaiAkhir" style="font-size: 2.5rem; font-weight: 800;">0</div>
                    <div id="txtKetercapaian" style="font-size: 1.2rem; font-weight: 700; opacity: 0.9;">-</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Supervisi</label>
                <textarea name="catatan" class="form-input" rows="4" style="resize:none;" placeholder="Berikan catatan perbaikan atau apresiasi..."><?= htmlspecialchars($supervisi['catatan'] ?? '') ?></textarea>
            </div>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px dashed #cbd5e1; display: flex; justify-content: space-between; align-items: center;">
                <div style="flex: 1;">
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Status Form</div>
                    <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem;" id="txtStatusComplete">Mengisi...</div>
                </div>
                <button type="button" id="btnSubmit" style="background: #0ea5e9; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.2s;">
                    <i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan
                </button>
            </div>
            
        </div>
    </form>
</div>

<script>
function calculate() {
    let totalScore = 0;
    let filled = 0;
    for(let i=1; i<=9; i++) {
        let radios = document.getElementsByName('instrumen['+i+']');
        for(let r of radios) {
            if(r.checked) {
                totalScore += parseInt(r.value);
                filled++;
            }
        }
    }
    
    let maxScore = 36;
    let finalScore = (totalScore / maxScore) * 100;
    let ketercapaian = '-';
    
    let cardBg = '#f1f5f9';
    let cardBorder = '#cbd5e1';
    let cardColor = '#475569';
    
    if (finalScore >= 90) {
        ketercapaian = '(A) Amat Baik';
        cardBg = '#f0fdf4'; cardBorder = '#22c55e'; cardColor = '#15803d'; // Green
    }
    else if (finalScore >= 80) {
        ketercapaian = '(B) Baik';
        cardBg = '#fefce8'; cardBorder = '#eab308'; cardColor = '#a16207'; // Yellow
    }
    else if (finalScore >= 70) {
        ketercapaian = '(C) Cukup';
        cardBg = '#fff7ed'; cardBorder = '#f97316'; cardColor = '#c2410c'; // Orange
    }
    else if (finalScore > 0) {
        ketercapaian = '(K) Kurang';
        cardBg = '#fef2f2'; cardBorder = '#ef4444'; cardColor = '#b91c1c'; // Red
    }
    
    let scoreCard = document.getElementById('scoreCard');
    scoreCard.style.background = cardBg;
    scoreCard.style.borderColor = cardBorder;
    scoreCard.style.color = cardColor;
    
    document.getElementById('txtNilaiAkhir').innerText = (totalScore > 0) ? finalScore.toFixed(1) : '0';
    document.getElementById('txtKetercapaian').innerText = ketercapaian;
    
    let statusText = document.getElementById('txtStatusComplete');
    if (filled == 9) {
        statusText.innerText = "Siap Disimpan (9/9)";
        statusText.style.color = "#16a34a";
    } else {
        statusText.innerText = `Belum Lengkap (${filled}/9)`;
        statusText.style.color = "#ef4444";
    }
}

// init
calculate();

document.getElementById('btnSubmit').addEventListener('click', function(e) {
    // Validasi mapel
    let mk = document.getElementsByName('mapel_kelas')[0].value;
    if(!mk) {
        alert("Pilih Mata Pelajaran & Kelas terlebih dahulu!");
        return;
    }
    
    let filled = 0;
    for(let i=1; i<=9; i++) {
        let radios = document.getElementsByName('instrumen['+i+']');
        let is_filled = false;
        for(let r of radios) { if(r.checked) is_filled = true; }
        if(is_filled) filled++;
    }
    
    if(filled < 9) {
        if(!confirm("Anda belum mengisi semua 9 aspek penilaian. Apakah Anda yakin ingin menyimpan apa adanya?")) {
            return;
        }
    }
    
    let btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    
    let form = document.getElementById('formSupervisi');
    let formData = new FormData(form);
    
    fetch('api_wakakur_supervisi_kunjungan_save.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if(res.status == 'success') {
            alert("Supervisi Kunjungan Kelas berhasil disimpan!");
            window.location.href = 'wakakur_supervisi_kunjungan.php';
        } else {
            alert(res.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan';
        }
    })
    .catch(err => {
        alert("Terjadi kesalahan jaringan.");
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-save" style="margin-right: 8px;"></i> Simpan';
    });
});
</script>
<?php include 'footer_guru.php'; ?>
