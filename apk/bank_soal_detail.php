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
        } elseif ($action === 'copy') {
            $srcPaketId = (int)($_POST['source_paket_id'] ?? 0);
            $mode = $_POST['mode'] ?? 'append';
            if ($srcPaketId > 0 && $srcPaketId !== $paket_id) {
                $stmtSrc = $db->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
                $stmtSrc->execute([$srcPaketId]);
                $srcSoals = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($srcSoals)) {
                    if ($mode === 'overwrite') {
                        $db->prepare("DELETE FROM bs_soal WHERE paket_id = ?")->execute([$paket_id]);
                        $startNo = 0;
                    } else {
                        $startNo = (int)$db->query("SELECT MAX(nomor_urut) FROM bs_soal WHERE paket_id = {$paket_id}")->fetchColumn();
                    }
                    $stmtIns = $db->prepare("
                        INSERT INTO bs_soal (topik_id, paket_id, mapel_nama, kelas_nama, nomor_urut, tipe_soal, tingkat_kesulitan, skor_maksimal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, kunci_jawaban)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $cCount = 0;
                    foreach ($srcSoals as $s) {
                        $startNo++;
                        $stmtIns->execute([
                            $topik_id, $paket_id, $paket['mapel_nama'], $paket['kelas_nama'], $startNo,
                            $s['tipe_soal'], $s['tingkat_kesulitan'] ?? 'Sedang', $s['skor_maksimal'] ?? 2.00,
                            $s['pertanyaan'], $s['opsi_a'], $s['opsi_b'], $s['opsi_c'], $s['opsi_d'], $s['opsi_e'], $s['kunci_jawaban']
                        ]);
                        $cCount++;
                    }
                    $message = "Berhasil menduplikasi dan menyalin {$cCount} butir soal ke paket ini!";
                    $msg_type = 'success';
                } else {
                    $message = 'Paket sumber tidak memiliki butir soal.';
                    $msg_type = 'error';
                }
            }
        }
    }
}

// Ambil seluruh butir soal
$stmtS = $db->prepare("SELECT * FROM bs_soal WHERE paket_id = ? ORDER BY nomor_urut ASC, id ASC");
$stmtS->execute([$paket_id]);
$soalList = $stmtS->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar paket sumber untuk modal salin soal
$srcPaketList = [];
try {
    $stmtSrcP = $db->prepare("
        SELECT p.id, p.mapel_nama, p.kelas_nama, p.kelas_sasaran, t.jenis,
               (SELECT COUNT(*) FROM bs_soal WHERE paket_id = p.id) as jml_soal
        FROM bs_paket p
        JOIN bs_topik t ON p.topik_id = t.id
        WHERE (SELECT COUNT(*) FROM bs_soal WHERE paket_id = p.id) > 0 AND p.id != ?
        ORDER BY (p.mapel_nama = ?) DESC, t.id DESC, p.kelas_nama ASC
    ");
    $stmtSrcP->execute([$paket_id, $paket['mapel_nama']]);
    $srcPaketList = $stmtSrcP->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

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
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 100000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px 16px;
        box-sizing: border-box;
    }
    .modal-soal-hp.open { display: flex; }
    .modal-content-hp {
        background: #ffffff;
        width: 100%;
        max-width: 540px;
        max-height: 85vh;
        border-radius: 20px;
        padding: 22px 20px 24px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow-y: auto;
        position: relative;
        margin: auto;
        box-sizing: border-box;
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
                    <i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($paket['kelas_nama'] ?: 'Kelas X') ?>
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

            <?php if (!empty($paket['kelas_sasaran'])): ?>
                <div style="font-size: 0.74rem; color: #64748b; font-weight: 700; margin-bottom: 6px;">
                    Sasaran: <strong style="color: #0f172a;"><?= htmlspecialchars($paket['kelas_sasaran']) ?></strong>
                </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #64748b; margin-top: 8px;">
                <div>Total Soal: <strong style="color: #0f172a; font-size: 0.95rem;"><?= count($soalList) ?></strong> Butir</div>
                <a href="/bank_soal/daftar_soal?paket_id=<?= $paket_id ?>" target="_blank" style="color: #0284c7; font-weight: 700; text-decoration: none; font-size: 0.75rem; display: flex; align-items: center; gap: 4px;">
                    Versi Desktop <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>

        <!-- Tombol Tambah Soal & Salin Soal (Hanya jika belum dikunci) -->
        <?php if (!$isLocked): ?>
            <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                <button type="button" onclick="openModalTambah()" style="flex: 1.2; padding: 11px 8px; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 0.82rem; cursor: pointer; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3); display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Soal Baru
                </button>
                <button type="button" onclick="openModalCopyApk()" style="flex: 1; padding: 11px 8px; background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; border-radius: 12px; font-weight: 800; font-size: 0.82rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="fa-solid fa-copy"></i> Salin Soal
                </button>
            </div>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
            <div>
                <h3 id="modalTitle" style="margin: 0; font-size: 1.05rem; font-weight: 900; color: #0f172a;">Tambah Butir Soal</h3>
                <span style="font-size: 0.72rem; color: #64748b;">Kelola teks soal, opsi pilihan, dan kunci jawaban</span>
            </div>
            <button type="button" onclick="closeModalSoal()" style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; border: none; font-size: 1.2rem; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="soal_id" id="form_soal_id" value="0">

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; color: #334155;">Tipe / Bentuk Soal</label>
                <select name="tipe_soal" id="form_tipe_soal" onchange="handleTipeChange(this.value)" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 700; background: #fff;">
                    <option value="sec_pg">Pilihan Ganda (PG)</option>
                    <option value="sec_pgk">PG Kompleks (Multi-Jawaban)</option>
                    <option value="sec_essay">Essay / Uraian</option>
                    <option value="sec_bs">Benar / Salah</option>
                </select>
            </div>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; color: #334155;">Pertanyaan Soal <span style="color:#ef4444;">*</span></label>
                <textarea name="pertanyaan" id="form_pertanyaan" rows="4" required placeholder="Tuliskan teks pertanyaan soal..." style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; resize: vertical; box-sizing: border-box;"></textarea>
            </div>

            <div id="section_opsi_pg">
                <div style="margin-bottom: 6px;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #475569;">Pilihan Opsi Jawaban:</label>
                </div>
                <div style="display: flex; flex-direction: column; gap: 7px; margin-bottom: 12px;">
                    <input type="text" name="opsi_a" id="form_opsi_a" placeholder="Opsi A" style="padding: 9px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; box-sizing: border-box;">
                    <input type="text" name="opsi_b" id="form_opsi_b" placeholder="Opsi B" style="padding: 9px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; box-sizing: border-box;">
                    <input type="text" name="opsi_c" id="form_opsi_c" placeholder="Opsi C" style="padding: 9px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; box-sizing: border-box;">
                    <input type="text" name="opsi_d" id="form_opsi_d" placeholder="Opsi D" style="padding: 9px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; box-sizing: border-box;">
                    <input type="text" name="opsi_e" id="form_opsi_e" placeholder="Opsi E" style="padding: 9px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; color: #334155;">Kunci Jawaban <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="kunci_jawaban" id="form_kunci" required placeholder="Contoh: A atau A,B,C" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; color: #334155;">Skor Bobot</label>
                    <input type="number" step="0.5" name="skor_maksimal" id="form_skor" value="2.0" style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 700; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; padding-top: 10px;">
                <button type="button" onclick="closeModalSoal()" style="flex: 1; padding: 12px 0; border-radius: 12px; border: 1.5px solid #cbd5e1; background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="flex: 2; padding: 12px 0; border-radius: 12px; border: none; background: #0284c7; color: white; font-weight: 800; font-size: 0.9rem; cursor: pointer; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
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

    function openModalCopyApk() {
        const modal = document.getElementById('modalCopyApk');
        if (modal) modal.classList.add('open');
    }
</script>

<!-- Modal Copy Soal Mobile -->
<div id="modalCopyApk" class="modal-soal-hp" onclick="if(event.target === this) this.classList.remove('open')">
    <div class="modal-content-hp">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-copy" style="color: #16a34a;"></i> Salin Soal dari Paket Lain
            </h3>
            <button type="button" onclick="document.getElementById('modalCopyApk').classList.remove('open')" style="background: none; border: none; font-size: 1.1rem; color: #94a3b8; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <?php if (empty($srcPaketList)): ?>
            <div style="padding: 24px 10px; text-align: center; color: #64748b;">
                <i class="fa-solid fa-inbox" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 8px;"></i>
                <div style="font-weight: 800; font-size: 0.9rem; color: #334155;">Belum Ada Paket Sumber</div>
                <div style="font-size: 0.75rem; margin-top: 4px;">Belum ada paket mapel lain yang terisi butir soal untuk disalin.</div>
            </div>
        <?php else: ?>
            <form method="POST" action="" onsubmit="return confirm('Salin butir soal dari paket sumber terpilih ke paket ini?')">
                <input type="hidden" name="action" value="copy">
                
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Pilih Paket Sumber: *</label>
                    <select name="source_paket_id" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.82rem; font-weight: 600; background: #fff;">
                        <?php foreach($srcPaketList as $src): ?>
                            <option value="<?= $src['id'] ?>">
                                [<?= htmlspecialchars($src['jenis'] ?: 'SAS') ?>] <?= htmlspecialchars($src['mapel_nama']) ?> - <?= htmlspecialchars($src['kelas_nama']) ?> (<?= $src['jml_soal'] ?> Soal)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Metode Penyalinan:</label>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.8rem; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="mode" value="append" checked style="margin-top: 2px;">
                            <div>
                                <strong>Tambahkan (Append)</strong>
                                <div style="font-size: 0.72rem; color: #64748b;">Soal baru ditaruh di nomor lanjutan.</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 0.8rem; padding: 8px 10px; border: 1.5px solid #fee2e2; border-radius: 8px; cursor: pointer; background: #fff5f5;">
                            <input type="radio" name="mode" value="overwrite" style="margin-top: 2px;">
                            <div>
                                <strong style="color: #b91c1c;">Gantikan Seluruhnya (Overwrite)</strong>
                                <div style="font-size: 0.72rem; color: #ef4444;">Hapus soal lama di paket ini, ganti dengan paket sumber.</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; margin-top: 14px;">
                    <button type="button" onclick="document.getElementById('modalCopyApk').classList.remove('open')" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; font-size: 0.82rem; cursor: pointer;">Batal</button>
                    <button type="submit" style="flex: 1; padding: 10px; border-radius: 8px; border: none; background: #16a34a; color: #fff; font-weight: 800; font-size: 0.82rem; cursor: pointer;">Salin Sekarang</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</script>

<div style="height: 80px;"></div>

<?php include 'footer_guru.php'; ?>
