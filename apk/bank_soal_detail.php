<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}

$guru_id = (int)$_SESSION['guru_id'];
$paket_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['paket_id']) ? (int)$_GET['paket_id'] : 0);

$db = $siakad_db ?: $core_db;

// Ambil data paket
$stmtP = $db->prepare("SELECT * FROM bs_paket WHERE id = ?");
$stmtP->execute([$paket_id]);
$paket = $stmtP->fetch(PDO::FETCH_ASSOC);

if (!$paket) {
    header("Location: bank_soal.php");
    exit;
}

$isLocked = !empty($paket['is_locked']);
$topik_id = (int)$paket['topik_id'];

// Ambil info topik
$stmtT = $db->prepare("SELECT * FROM bs_topik WHERE id = ?");
$stmtT->execute([$topik_id]);
$topik = $stmtT->fetch(PDO::FETCH_ASSOC);

$message = '';
$msg_type = '';

// Proses Simpan / Edit / Hapus Soal dari HP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isLocked) {
        $message = 'Paket soal ini sedang dikunci oleh Kurikulum/Admin. Anda tidak dapat menambah atau mengedit soal.';
        $msg_type = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        $soal_id = (int)($_POST['soal_id'] ?? 0);

        if ($action === 'delete' && $soal_id > 0) {
            $stmtDel = $db->prepare("DELETE FROM bs_soal WHERE id = ? AND paket_id = ?");
            if ($stmtDel->execute([$soal_id, $paket_id])) {
                $message = 'Butir soal berhasil dihapus!';
                $msg_type = 'success';
            } else {
                $message = 'Gagal menghapus butir soal.';
                $msg_type = 'error';
            }
        } elseif ($action === 'save') {
            $tipe_soal = $_POST['tipe_soal'] ?? 'sec_pg';
            $pertanyaan = trim($_POST['pertanyaan'] ?? '');
            $opsi_a = trim($_POST['opsi_a'] ?? '');
            $opsi_b = trim($_POST['opsi_b'] ?? '');
            $opsi_c = trim($_POST['opsi_c'] ?? '');
            $opsi_d = trim($_POST['opsi_d'] ?? '');
            $opsi_e = trim($_POST['opsi_e'] ?? '');
            $kunci_jawaban = trim($_POST['kunci_jawaban'] ?? '');
            $skor = !empty($_POST['skor_maksimal']) ? (float)$_POST['skor_maksimal'] : 2.00;
            $tingkat = $_POST['tingkat_kesulitan'] ?? 'Sedang';

            if (!empty($pertanyaan)) {
                if ($soal_id > 0) {
                    // Update
                    $stmtUp = $db->prepare("
                        UPDATE bs_soal 
                        SET tipe_soal = ?, tingkat_kesulitan = ?, skor_maksimal = ?, pertanyaan = ?, 
                            opsi_a = ?, opsi_b = ?, opsi_c = ?, opsi_d = ?, opsi_e = ?, kunci_jawaban = ?
                        WHERE id = ? AND paket_id = ?
                    ");
                    $stmtUp->execute([
                        $tipe_soal, $tingkat, $skor, $pertanyaan,
                        $opsi_a, $opsi_b, $opsi_c, $opsi_d, $opsi_e, $kunci_jawaban,
                        $soal_id, $paket_id
                    ]);
                    $message = 'Butir soal berhasil diperbarui!';
                    $msg_type = 'success';
                } else {
                    // Tambah baru
                    $maxNo = (int)$db->query("SELECT MAX(nomor_urut) FROM bs_soal WHERE paket_id = {$paket_id}")->fetchColumn();
                    $nomor_urut = $maxNo + 1;

                    $stmtIns = $db->prepare("
                        INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmtIns->execute([
                        $topik_id, $paket_id, $paket['mapel_nama'], $paket['kelas_nama'], $nomor_urut,
                        $tipe_soal, $tingkat, $skor, $pertanyaan,
                        $opsi_a, $opsi_b, $opsi_c, $opsi_d, $opsi_e, $kunci_jawaban
                    ]);
                    $message = 'Butir soal baru berhasil ditambahkan!';
                    $msg_type = 'success';
                }
            } else {
                $message = 'Pertanyaan soal tidak boleh kosong.';
                $msg_type = 'error';
            }
        }
    }
}

// Ambil seluruh butir soal
$stmtS = $db->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
$stmtS->execute([$paket_id]);
$soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Kelola Soal: ' . $paket['mapel_nama'];
include 'header.php';
?>

<style>
    .badge-soal-tipe {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .tipe-pg { background: #e0f2fe; color: #0284c7; }
    .tipe-pgk { background: #f3e8ff; color: #7e22ce; }
    .tipe-essay { background: #ecfdf5; color: #047857; }
    .tipe-bs { background: #fef3c7; color: #b45309; }

    .opsi-box-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 5px;
        font-size: 0.8rem;
    }
    .opsi-box-item.is-correct {
        background: #ecfdf5;
        border-color: #86efac;
        color: #166534;
        font-weight: 700;
    }
    .modal-soal-hp {
        position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px); z-index: 9999; display: none;
        align-items: flex-end; justify-content: center;
    }
    .modal-soal-hp.open { display: flex; }
    .modal-content-hp {
        background: #ffffff; width: 100%; max-height: 90vh;
        border-radius: 20px 20px 0 0; padding: 20px;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.15); overflow-y: auto;
    }
</style>

<!-- Header Top Mobile -->
<div class="header" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
    <div class="header-top">
        <a href="bank_soal.php" style="color: white; font-size: 1.2rem; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="greeting" style="margin-left: 14px;">
            <p><?= htmlspecialchars($topik['nama_topik'] ?? 'Topik Ujian') ?></p>
            <h1><?= htmlspecialchars($paket['mapel_nama']) ?></h1>
        </div>
        <div class="avatar" style="color: #0284c7; background: white;">
            <i class="fa-solid fa-file-lines"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; position: relative;">

        <!-- Alert Pesan Flash -->
        <?php if (!empty($message)): ?>
            <div style="padding: 12px 14px; border-radius: 10px; margin-bottom: 14px; font-size: 0.82rem; font-weight: 700; display: flex; align-items: center; gap: 8px; <?= $msg_type === 'success' ? 'background:#ecfdf5; border:1px solid #86efac; color:#166534;' : 'background:#fef2f2; border:1px solid #fecaca; color:#991b1b;' ?>">
                <i class="fa-solid <?= $msg_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Info Bar Paket -->
        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px 16px; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <span style="font-size: 0.82rem; font-weight: 800; background: #e0e7ff; color: #4338ca; padding: 3px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fa-solid fa-graduation-cap"></i> Kelas: <?= htmlspecialchars($paket['kelas_nama'] ?: 'Semua Kelas') ?>
                </span>
                <?php if ($isLocked): ?>
                    <span style="font-size: 0.72rem; font-weight: 800; background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 12px; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-lock"></i> Terkunci (Final)
                    </span>
                <?php else: ?>
                    <span style="font-size: 0.72rem; font-weight: 800; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 12px; border: 1px solid #a7f3d0;">
                        <i class="fa-solid fa-lock-open"></i> Terbuka (Bisa Diedit)
                    </span>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #64748b; margin-top: 8px;">
                <div>Total Soal: <strong style="color: #0f172a; font-size: 0.95rem;"><?= count($soalList) ?></strong> Butir</div>
                <a href="/bank_soal/daftar_soal?paket_id=<?= $paket_id ?>" target="_blank" style="color: #0284c7; font-weight: 700; text-decoration: none; font-size: 0.75rem; display: flex; align-items: center; gap: 4px;">
                    Versi Desktop <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>

        <!-- Tombol Tambah Soal (Hanya jika belum dikunci) -->
        <?php if (!$isLocked): ?>
            <button type="button" onclick="openModalTambah()" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 16px;">
                <i class="fa-solid fa-plus-circle"></i> Tambah Butir Soal Baru
            </button>
        <?php else: ?>
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.78rem; color: #92400e; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-lock" style="color: #d97706;"></i>
                Soal telah dikunci oleh Kurikulum. Guru hanya dapat melihat butir soal (mode baca).
            </div>
        <?php endif; ?>

        <!-- Daftar Butir Soal -->
        <h4 style="margin: 0 0 12px 0; font-size: 0.88rem; color: #475569; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
            Daftar Butir Soal (<?= count($soalList) ?>)
        </h4>

        <?php if (empty($soalList)): ?>
            <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 14px; border: 1.5px dashed #cbd5e1; color: #94a3b8;">
                <i class="fa-solid fa-file-circle-question" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                <h4 style="margin: 0 0 4px; color: #475569; font-size: 0.95rem; font-weight: 800;">Belum Ada Butir Soal</h4>
                <p style="margin: 0; font-size: 0.78rem;">Tekan tombol "Tambah Butir Soal Baru" di atas untuk mulai memasukkan soal.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($soalList as $idx => $s): 
                    $tipe = $s['tipe_soal'] ?: 'sec_pg';
                    $tipeBadge = 'tipe-pg'; $tipeLabel = 'Pilihan Ganda';
                    if ($tipe === 'sec_pgk') { $tipeBadge = 'tipe-pgk'; $tipeLabel = 'PG Kompleks'; }
                    elseif ($tipe === 'sec_essay') { $tipeBadge = 'tipe-essay'; $tipeLabel = 'Esai / Uraian'; }
                    elseif ($tipe === 'sec_bs') { $tipeBadge = 'tipe-bs'; $tipeLabel = 'Benar / Salah'; }

                    $kunci = strtoupper(trim($s['kunci_jawaban'] ?? ''));
                ?>
                <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 24px; height: 24px; border-radius: 6px; background: #0284c7; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">
                                <?= $idx + 1 ?>
                            </span>
                            <span class="badge-soal-tipe <?= $tipeBadge ?>">
                                <?= $tipeLabel ?>
                            </span>
                        </div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b;">
                            Skor: <strong style="color: #0f172a;"><?= $s['skor_maksimal'] ?></strong>
                        </div>
                    </div>

                    <!-- Pertanyaan -->
                    <div style="font-size: 0.88rem; color: #0f172a; line-height: 1.45; font-weight: 600; margin-bottom: 10px;">
                        <?= nl2br(htmlspecialchars($s['pertanyaan'])) ?>
                    </div>

                    <!-- Opsi Jawaban (Jika PG) -->
                    <?php if ($tipe === 'sec_pg' || $tipe === 'sec_pgk'): ?>
                        <div style="margin-bottom: 10px;">
                            <?php 
                            $opsi = ['A' => $s['opsi_a'], 'B' => $s['opsi_b'], 'C' => $s['opsi_c'], 'D' => $s['opsi_d'], 'E' => $s['opsi_e']];
                            foreach ($opsi as $huruf => $teks):
                                if (empty($teks)) continue;
                                $isKunci = (strpos($kunci, $huruf) !== false);
                            ?>
                                <div class="opsi-box-item <?= $isKunci ? 'is-correct' : '' ?>">
                                    <span style="font-weight: 800; min-width: 18px;"><?= $huruf ?>.</span>
                                    <span style="flex: 1;"><?= htmlspecialchars($teks) ?></span>
                                    <?php if ($isKunci): ?>
                                        <i class="fa-solid fa-circle-check" style="color: #16a34a; font-size: 0.85rem;"></i>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Kunci Esai / Lainnya -->
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px 10px; font-size: 0.78rem; margin-bottom: 10px; color: #166534;">
                            <strong>Kunci / Panduan Jawaban:</strong> <?= htmlspecialchars($s['kunci_jawaban'] ?: '-') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons (Hanya jika belum dikunci) -->
                    <?php if (!$isLocked): ?>
                        <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                            <button type="button" onclick='openModalEdit(<?= json_encode($s) ?>)' style="padding: 6px 12px; font-size: 0.75rem; font-weight: 700; background: #e0f2fe; color: #0284c7; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form method="POST" onsubmit="return confirm('Hapus butir soal nomor <?= $idx + 1 ?> ini?')" style="margin: 0;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="soal_id" value="<?= $s['id'] ?>">
                                <button type="submit" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 700; background: #fee2e2; color: #ef4444; border: none; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Modal Form Tambah / Edit Soal dari HP -->
<div id="modalFormSoal" class="modal-soal-hp" onclick="if(event.target===this) closeModalSoal()">
    <div class="modal-content-hp">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h3 id="modalTitle" style="margin: 0; font-size: 1.05rem; font-weight: 900; color: #0f172a;">Tambah Butir Soal</h3>
            <button type="button" onclick="closeModalSoal()" style="background: none; border: none; font-size: 1.3rem; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="soal_id" id="form_soal_id" value="0">

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; color: #334155;">Tipe / Bentuk Soal</label>
                <select name="tipe_soal" id="form_tipe_soal" onchange="handleTipeChange(this.value)" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem;">
                    <option value="sec_pg">Pilihan Ganda (PG)</option>
                    <option value="sec_pgk">PG Kompleks (Multi-Jawaban)</option>
                    <option value="sec_essay">Essay / Uraian</option>
                    <option value="sec_bs">Benar / Salah</option>
                </select>
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; color: #334155;">Pertanyaan Soal <span style="color:#ef4444;">*</span></label>
                <textarea name="pertanyaan" id="form_pertanyaan" rows="4" required placeholder="Tuliskan teks pertanyaan soal..." style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; resize: vertical;"></textarea>
            </div>

            <div id="section_opsi_pg">
                <div style="margin-bottom: 6px;">
                    <label style="font-size: 0.72rem; font-weight: 700; color: #64748b;">Pilihan Opsi Jawaban:</label>
                </div>
                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px;">
                    <input type="text" name="opsi_a" id="form_opsi_a" placeholder="Opsi A" style="padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                    <input type="text" name="opsi_b" id="form_opsi_b" placeholder="Opsi B" style="padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                    <input type="text" name="opsi_c" id="form_opsi_c" placeholder="Opsi C" style="padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                    <input type="text" name="opsi_d" id="form_opsi_d" placeholder="Opsi D" style="padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                    <input type="text" name="opsi_e" id="form_opsi_e" placeholder="Opsi E" style="padding: 7px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.82rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; color: #334155;">Kunci Jawaban <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kunci_jawaban" id="form_kunci" required placeholder="Contoh: A atau A,B,C" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 4px; color: #334155;">Skor Bobot</label>
                    <input type="number" step="0.5" name="skor_maksimal" id="form_skor" value="2.0" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem;">
                </div>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="button" onclick="closeModalSoal()" style="flex: 1; padding: 11px 0; border-radius: 10px; border: 1.5px solid #cbd5e1; background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="flex: 2; padding: 11px 0; border-radius: 10px; border: none; background: #0ea5e9; color: white; font-weight: 800; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);">
                    <i class="fa-solid fa-check"></i> Simpan Butir Soal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambah() {
    document.getElementById('modalTitle').textContent = 'Tambah Butir Soal Baru';
    document.getElementById('form_soal_id').value = '0';
    document.getElementById('form_tipe_soal').value = 'sec_pg';
    document.getElementById('form_pertanyaan').value = '';
    document.getElementById('form_opsi_a').value = '';
    document.getElementById('form_opsi_b').value = '';
    document.getElementById('form_opsi_c').value = '';
    document.getElementById('form_opsi_d').value = '';
    document.getElementById('form_opsi_e').value = '';
    document.getElementById('form_kunci').value = '';
    document.getElementById('form_skor').value = '2.0';
    handleTipeChange('sec_pg');
    document.getElementById('modalFormSoal').classList.add('open');
}

function openModalEdit(data) {
    document.getElementById('modalTitle').textContent = 'Edit Butir Soal #' + data.nomor_urut;
    document.getElementById('form_soal_id').value = data.id;
    document.getElementById('form_tipe_soal').value = data.tipe_soal || 'sec_pg';
    document.getElementById('form_pertanyaan').value = data.pertanyaan || '';
    document.getElementById('form_opsi_a').value = data.opsi_a || '';
    document.getElementById('form_opsi_b').value = data.opsi_b || '';
    document.getElementById('form_opsi_c').value = data.opsi_c || '';
    document.getElementById('form_opsi_d').value = data.opsi_d || '';
    document.getElementById('form_opsi_e').value = data.opsi_e || '';
    document.getElementById('form_kunci').value = data.kunci_jawaban || '';
    document.getElementById('form_skor').value = data.skor_maksimal || '2.0';
    handleTipeChange(data.tipe_soal || 'sec_pg');
    document.getElementById('modalFormSoal').classList.add('open');
}

function closeModalSoal() {
    document.getElementById('modalFormSoal').classList.remove('open');
}

function handleTipeChange(tipe) {
    const secOpsi = document.getElementById('section_opsi_pg');
    if (tipe === 'sec_essay' || tipe === 'sec_bs') {
        secOpsi.style.display = 'none';
    } else {
        secOpsi.style.display = 'block';
    }
}
</script>

<div style="height: 80px;"></div>

<?php include 'footer_guru.php'; ?>
