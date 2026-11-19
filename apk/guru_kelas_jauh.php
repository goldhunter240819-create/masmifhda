<?php
require_once 'koneksi.php';

// Cek Auth Login Guru / Admin
if (!isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nama_petugas = $_SESSION['nama'] ?? ($_SESSION['guru_nama'] ?? 'Pengawas Kelas Jauh');
$nip_petugas = $_SESSION['guru_nip'] ?? ($_SESSION['nip'] ?? '-');

// Handle Action AJAX: Buka Kunci Peserta Kelas Jauh
if (isset($_POST['action']) && $_POST['action'] === 'unlock_peserta') {
    header('Content-Type: application/json');
    $peserta_id = (int)($_POST['peserta_id'] ?? 0);
    if ($peserta_id > 0) {
        try {
            $stmtUn = $core_db->prepare("UPDATE cbt_peserta SET status_pengerjaan = 'Mengerjakan', status_kunci = 'Unlocked', pelanggaran_count = 0, is_locked = 0 WHERE id = ?");
            $stmtUn->execute([$peserta_id]);
            echo json_encode(['success' => true, 'message' => 'Status ujian siswa berhasil dibuka kuncinya!']);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
    echo json_encode(['success' => false, 'message' => 'ID peserta tidak valid']);
    exit;
}

// 1. Ambil Data Siswa Kelas Jauh
$totalSiswaKJ = 0;
try {
    $stmtTot = $core_db->query("SELECT COUNT(*) FROM cbt_peserta_khusus");
    $totalSiswaKJ = (int)$stmtTot->fetchColumn();
} catch (\Exception $e) {}

// 2. Ambil Rekap Absensi Siswa Kelas Jauh Hari Ini
$todayDate = date('Y-m-d');
$hadirHariIniKJ = 0;
$listAbsenKJ = [];
try {
    $stmtAbsKJ = $core_db->prepare("
        SELECT a.*, k.nama_siswa as nama_lengkap, k.no_peserta 
        FROM cbt_absensi a
        LEFT JOIN cbt_peserta_khusus k ON (a.nis = k.no_peserta OR a.nis = k.username)
        WHERE (a.tipe_peserta = 'khusus' OR a.kelas_nama LIKE '%Jauh%' OR a.ruang_nama LIKE '%Jauh%')
          AND a.tanggal = ?
        ORDER BY a.waktu_scan DESC
    ");
    $stmtAbsKJ->execute([$todayDate]);
    $listAbsenKJ = $stmtAbsKJ->fetchAll(PDO::FETCH_ASSOC);
    $hadirHariIniKJ = count($listAbsenKJ);
} catch (\Exception $e) {}

// 3. Ambil Daftar Ujian CBT yang Tersedia untuk Kelas Jauh
$ujianListKJ = [];
try {
    $stmtUjn = $core_db->query("
        SELECT id, kode_ujian, nama_ujian, mapel_nama, kelas_nama, kkm, durasi_menit 
        FROM cbt_ujian 
        WHERE status = 'Aktif' 
          AND (kelas_nama = 'Semua Kelas' OR kelas_nama LIKE '%Jauh%' OR kelas_nama = '' OR kelas_nama IS NULL)
        ORDER BY id DESC
    ");
    $ujianListKJ = $stmtUjn->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

// 4. Ambil Hasil Ujian Siswa Kelas Jauh (Filter Ujian)
$selectedUjianId = isset($_GET['ujian_id']) ? (int)$_GET['ujian_id'] : ($ujianListKJ[0]['id'] ?? 0);
$hasilListKJ = [];
$statRata2 = 0;
$statTertinggi = 0;
$statTuntasCount = 0;
$kkmAktif = 75;

if ($selectedUjianId > 0) {
    try {
        // Ambil info KKM ujian aktif
        foreach ($ujianListKJ as $uj) {
            if ($uj['id'] == $selectedUjianId) {
                $kkmAktif = (float)($uj['kkm'] ?? 75);
                break;
            }
        }

        $stmtHsl = $core_db->prepare("
            SELECT p.*, k.nama_siswa as nama_lengkap, k.no_peserta, k.kelas_nama as rombel_kj
            FROM cbt_peserta p
            INNER JOIN cbt_peserta_khusus k ON (p.nis = k.no_peserta OR p.nis = k.username OR p.nama_siswa = k.nama_siswa)
            WHERE p.ujian_id = ?
            ORDER BY p.nilai_akhir DESC, p.nama_siswa ASC
        ");
        $stmtHsl->execute([$selectedUjianId]);
        $hasilListKJ = $stmtHsl->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($hasilListKJ)) {
            $totalNilai = 0;
            foreach ($hasilListKJ as $h) {
                $nilai = (float)($h['nilai_akhir'] ?? 0);
                $totalNilai += $nilai;
                if ($nilai > $statTertinggi) $statTertinggi = $nilai;
                if ($nilai >= $kkmAktif) $statTuntasCount++;
            }
            $statRata2 = round($totalNilai / count($hasilListKJ), 1);
        }
    } catch (\Exception $e) {}
}

$page_title = 'Portal Pengawas - Kelas Jauh CBT';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #059669 0%, #0d9488 100%);">
    <div class="header-top">
        <div class="greeting">
            <p><i class="fa-solid fa-satellite-dish"></i> Portal Mandiri Kelas Jauh</p>
            <h1>Pengawas CBT Kelas Jauh</h1>
        </div>
        <div class="avatar" style="color: #059669; background: white;" onclick="window.location.href='logout.php'">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 90px;">
    <!-- Welcome Info Card -->
    <div style="background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.98rem; color: #0f172a;"><?= htmlspecialchars($nama_petugas) ?></div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 600;">NIP/ID: <?= htmlspecialchars($nip_petugas) ?> • Pengawas Sentral</div>
                </div>
            </div>
            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 0.7rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;">
                CBT KELAS JAUH
            </span>
        </div>

        <!-- Quick Summary 3 Kolom -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; text-align: center;">
            <div style="background: #f8fafc; border-radius: 10px; padding: 10px 6px; border: 1px solid #f1f5f9;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Peserta</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #0f172a;"><?= $totalSiswaKJ ?></div>
            </div>
            <div style="background: #f0fdf4; border-radius: 10px; padding: 10px 6px; border: 1px solid #dcfce7;">
                <div style="font-size: 0.65rem; color: #166534; font-weight: 700; text-transform: uppercase;">Absen Hari Ini</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #15803d;"><?= $hadirHariIniKJ ?></div>
            </div>
            <div style="background: #eff6ff; border-radius: 10px; padding: 10px 6px; border: 1px solid #dbeafe;">
                <div style="font-size: 0.65rem; color: #1e40af; font-weight: 700; text-transform: uppercase;">Paket Soal</div>
                <div style="font-size: 1.25rem; font-weight: 900; color: #2563eb;"><?= count($ujianListKJ) ?></div>
            </div>
        </div>
    </div>

    <!-- Switch Navigation Pills -->
    <div style="display: flex; gap: 8px; margin-bottom: 16px; background: #e2e8f0; padding: 4px; border-radius: 12px;">
        <button type="button" id="tabBtnScan" onclick="switchKJTab('scan')" style="flex: 1; padding: 10px; border: none; border-radius: 10px; font-weight: 800; font-size: 0.82rem; cursor: pointer; background: #059669; color: white; transition: 0.2s;">
            <i class="fa-solid fa-qrcode"></i> Scan Absensi
        </button>
        <button type="button" id="tabBtnNilai" onclick="switchKJTab('nilai')" style="flex: 1; padding: 10px; border: none; border-radius: 10px; font-weight: 800; font-size: 0.82rem; cursor: pointer; background: transparent; color: #475569; transition: 0.2s;">
            <i class="fa-solid fa-square-poll-vertical"></i> Hasil Nilai
        </button>
    </div>

    <!-- ================= TAB 1: SCAN ABSENSI KELAS JAUH ================= -->
    <div id="tabContentScan">
        <!-- Shortcut Masuk Scanner Mobile Full Camera -->
        <a href="cbt_scan.php" style="display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 14px 18px; border-radius: 14px; text-decoration: none; font-weight: 800; font-size: 0.95rem; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(5,150,105,0.3);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-camera" style="font-size: 1.3rem;"></i>
                <div>
                    <div>Buka Kamera Scanner 1 Pintu</div>
                    <div style="font-size: 0.72rem; opacity: 0.85; font-weight: 500;">Scan QR / Barcode kartu ujian otomatis</div>
                </div>
            </div>
            <i class="fa-solid fa-arrow-right"></i>
        </a>

        <!-- Absen Cepat Manual NIS / Username -->
        <div style="background: white; border-radius: 14px; padding: 14px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">
                <i class="fa-solid fa-keyboard" style="color: #059669;"></i> Input Cepat No. Peserta / Username Kelas Jauh:
            </label>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="manualCodeKJ" placeholder="Contoh: KJ-1001 atau username..." style="flex: 1; padding: 11px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.9rem; font-weight: 700; outline: none;" onkeydown="if(event.key==='Enter') submitScanKJ()">
                <button type="button" onclick="submitScanKJ()" style="background: #059669; color: white; border: none; padding: 11px 18px; border-radius: 10px; font-weight: 800; font-size: 0.85rem; cursor: pointer; white-space: nowrap;">
                    <i class="fa-solid fa-check"></i> Absenkan
                </button>
            </div>
            <div id="quickScanMsg" style="margin-top: 8px; font-size: 0.78rem; font-weight: 700; display: none;"></div>
        </div>

        <!-- Live Feed Absensi Hari Ini -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 0.92rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #059669;"></i> Riwayat Kehadiran Hari Ini
            </h3>
            <span style="font-size: 0.7rem; color: #059669; font-weight: 800; background: #ecfdf5; padding: 2px 8px; border-radius: 20px;">
                <?= count($listAbsenKJ) ?> Siswa Hadir
            </span>
        </div>

        <?php if (empty($listAbsenKJ)): ?>
            <div style="text-align: center; padding: 30px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.82rem;">
                <i class="fa-solid fa-user-clock" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
                Belum ada siswa kelas jauh yang terabsen pada hari ini.<br>Gunakan scanner kamera atau input cepat nomor peserta di atas.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($listAbsenKJ as $a): ?>
                    <div style="background: white; border-radius: 12px; padding: 12px 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 38px; height: 38px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.05rem;">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div style="font-weight: 800; font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($a['nama_siswa']) ?></div>
                                <div style="font-size: 0.72rem; color: #64748b;">
                                    No: <b><?= htmlspecialchars($a['nis']) ?></b> • Mapel: <b><?= htmlspecialchars($a['mapel_nama'] ?: 'Umum') ?></b>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="background: #ecfdf5; color: #059669; font-weight: 800; font-size: 0.68rem; padding: 3px 8px; border-radius: 20px;">
                                <?= date('H:i', strtotime($a['waktu_scan'])) ?> WIB
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ================= TAB 2: REKAP HASIL & NILAI KELAS JAUH ================= -->
    <div id="tabContentNilai" style="display: none;">
        <!-- Filter Pilih Paket Ujian -->
        <div style="background: white; border-radius: 14px; padding: 14px; margin-bottom: 14px; border: 1px solid #e2e8f0;">
            <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">
                <i class="fa-solid fa-filter" style="color: #059669;"></i> Pilih Paket Ujian CBT:
            </label>
            <select onchange="window.location.href='guru_kelas_jauh.php?tab=nilai&ujian_id=' + this.value" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.88rem; font-weight: 700; color: #0f172a; outline: none; background: #f8fafc;">
                <?php foreach ($ujianListKJ as $uj): ?>
                    <option value="<?= (int)$uj['id'] ?>" <?= $uj['id'] == $selectedUjianId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($uj['nama_ujian']) ?> (<?= htmlspecialchars($uj['mapel_nama']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Metric Card Hasil Ujian -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 14px;">
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">RATA-RATA</div>
                <div style="font-size: 1.3rem; font-weight: 900; color: #2563eb;"><?= $statRata2 ?></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">TERTINGGI</div>
                <div style="font-size: 1.3rem; font-weight: 900; color: #10b981;"><?= $statTertinggi ?></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 12px 8px; border: 1px solid #e2e8f0; text-align: center;">
                <div style="font-size: 0.65rem; color: #64748b; font-weight: 700;">TUNTAS KKM</div>
                <div style="font-size: 1.3rem; font-weight: 900; color: #059669;"><?= $statTuntasCount ?>/<?= count($hasilListKJ) ?></div>
            </div>
        </div>

        <!-- Daftar Nilai Siswa -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="margin: 0; font-size: 0.92rem; font-weight: 900; color: #0f172a;">
                <i class="fa-solid fa-list-ol" style="color: #059669;"></i> Daftar Nilai Siswa (<?= count($hasilListKJ) ?>)
            </h3>
        </div>

        <?php if (empty($hasilListKJ)): ?>
            <div style="text-align: center; padding: 30px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.82rem;">
                <i class="fa-solid fa-folder-open" style="font-size: 2rem; opacity: 0.3; margin-bottom: 8px; display: block;"></i>
                Belum ada data pengerjaan ujian untuk paket soal ini oleh siswa kelas jauh.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($hasilListKJ as $idx => $h): ?>
                    <?php
                    $nilai = (float)($h['nilai_akhir'] ?? 0);
                    $isTuntas = ($nilai >= $kkmAktif);
                    $isLocked = (!empty($h['is_locked']) || $h['status_pengerjaan'] === 'Terkunci');
                    ?>
                    <div style="background: white; border-radius: 14px; padding: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800;">
                                    <?= $idx + 1 ?>
                                </span>
                                <div>
                                    <h4 style="margin: 0; font-size: 0.92rem; font-weight: 800; color: #0f172a;">
                                        <?= htmlspecialchars($h['nama_siswa']) ?>
                                    </h4>
                                    <div style="font-size: 0.72rem; color: #64748b;">
                                        No. Peserta: <b><?= htmlspecialchars($h['nis']) ?></b> • <?= htmlspecialchars($h['rombel_kj'] ?: 'Kelas Jauh') ?>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.25rem; font-weight: 900; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                                    <?= $nilai ?>
                                </div>
                                <span style="font-size: 0.65rem; font-weight: 800; padding: 2px 7px; border-radius: 10px; background: <?= $isTuntas ? '#ecfdf5' : '#fef2f2' ?>; color: <?= $isTuntas ? '#059669' : '#dc2626' ?>;">
                                    <?= $isTuntas ? 'Tuntas' : 'Remedial' ?>
                                </span>
                            </div>
                        </div>

                        <!-- Info Detail & Tombol Buka Kunci -->
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #f1f5f9; padding-top: 8px; margin-top: 8px; font-size: 0.74rem;">
                            <div style="color: #64748b;">
                                Benar: <b style="color: #059669;"><?= (int)($h['jawaban_benar'] ?? 0) ?></b> • Salah: <b style="color: #dc2626;"><?= (int)($h['jawaban_salah'] ?? 0) ?></b> • Status: <b><?= htmlspecialchars($h['status_pengerjaan'] ?: 'Selesai') ?></b>
                            </div>
                            <?php if ($isLocked || in_array($h['status_pengerjaan'], ['Terkunci', 'Mengerjakan'])): ?>
                                <button type="button" onclick="bukaKunciPeserta(<?= (int)$h['id'] ?>, '<?= htmlspecialchars(addslashes($h['nama_siswa'])) ?>')" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-unlock"></i> Buka Kunci
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function switchKJTab(tab) {
        const btnScan = document.getElementById('tabBtnScan');
        const btnNilai = document.getElementById('tabBtnNilai');
        const contentScan = document.getElementById('tabContentScan');
        const contentNilai = document.getElementById('tabContentNilai');

        if (tab === 'scan') {
            btnScan.style.background = '#059669';
            btnScan.style.color = '#ffffff';
            btnNilai.style.background = 'transparent';
            btnNilai.style.color = '#475569';
            contentScan.style.display = 'block';
            contentNilai.style.display = 'none';
        } else {
            btnNilai.style.background = '#059669';
            btnNilai.style.color = '#ffffff';
            btnScan.style.background = 'transparent';
            btnScan.style.color = '#475569';
            contentNilai.style.display = 'block';
            contentScan.style.display = 'none';
        }
    }

    // Auto switch jika URL memiliki parameter tab=nilai
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'nilai') {
        switchKJTab('nilai');
    }

    function submitScanKJ() {
        const inp = document.getElementById('manualCodeKJ');
        const code = inp.value.trim();
        const msgEl = document.getElementById('quickScanMsg');

        if (!code) {
            inp.focus();
            return;
        }

        msgEl.style.display = 'block';
        msgEl.style.color = '#2563eb';
        msgEl.innerText = 'Memproses absensi...';

        fetch('/cbt/scan/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                code: code,
                ruang_nama: 'Sentral Kelas Jauh'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                if (res.already) {
                    msgEl.style.color = '#dc2626';
                    msgEl.innerText = '⚠️ Siswa sudah scan sebelumnya: ' + (res.siswa ? res.siswa.nama : code) + ' pada jam ' + (res.waktu || '') + ' WIB. Belum waktunya ganti sesi!';
                    alert('⚠️ SISWA SUDAH SCAN SEBELUMNYA!\n\nSiswa ' + (res.siswa ? res.siswa.nama : code) + ' telah terabsen pada jam ' + (res.waktu || '') + ' WIB.\nBelum waktunya ganti sesi/jam ujian!');
                } else {
                    msgEl.style.color = '#059669';
                    msgEl.innerText = '✓ Berhasil: ' + (res.siswa ? res.siswa.nama : code) + ' terabsen pada mapel ' + (res.mapel_nama || '-');
                    inp.value = '';
                    setTimeout(() => { window.location.reload(); }, 1200);
                }
            } else {
                msgEl.style.color = '#dc2626';
                msgEl.innerText = '✗ ' + (res.message || 'Gagal memproses kode');
            }
        })
        .catch(() => {
            msgEl.style.color = '#dc2626';
            msgEl.innerText = 'Terjadi kesalahan jaringan atau server.';
        });
    }

    function bukaKunciPeserta(pesertaId, namaSiswa) {
        if (!confirm('Buka kunci ujian untuk ' + namaSiswa + ' agar dapat melanjutkan ujian?')) return;

        fetch('guru_kelas_jauh.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'unlock_peserta',
                peserta_id: pesertaId
            })
        })
        .then(res => res.json())
        .then(res => {
            alert(res.message);
            if (res.success) {
                window.location.reload();
            }
        })
        .catch(() => {
            alert('Gagal menghubungi server.');
        });
    }
</script>
