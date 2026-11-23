<?php
$pageTitle = 'Scan Presensi Pengawas Ruang CBT';
$active_menu = 'pengawas_scan';
?>
<!-- Local Offline Assets -->
<link rel="stylesheet" href="<?php echo base_url('public/assets/css/all.min.css'); ?>">
<script src="<?php echo base_url('public/assets/js/sweetalert2.all.min.js'); ?>"></script>
<script src="<?php echo base_url('public/assets/js/html5-qrcode.min.js'); ?>"></script>

<style>
/* Modern Scanner Layout & Styling */
@keyframes scanLaser {
    0% { top: 6%; opacity: 0.3; }
    50% { top: 92%; opacity: 1; }
    100% { top: 6%; opacity: 0.3; }
}

.scanner-camera-wrapper {
    position: relative;
    width: 100%;
    max-width: 460px;
    margin: 0 auto;
    border-radius: 20px;
    overflow: hidden;
    background: #0f172a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    border: 3px solid rgba(59, 130, 246, 0.4);
}

.scanner-viewfinder {
    position: absolute;
    inset: 0;
    pointer-events: none;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.scanner-box-frame {
    position: relative;
    width: 250px;
    height: 250px;
    border: 2px dashed rgba(59, 130, 246, 0.6);
    border-radius: 18px;
    box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55);
}

.scanner-corner {
    position: absolute;
    width: 24px;
    height: 24px;
    border-color: #38bdf8;
    border-style: solid;
}
.scanner-corner.tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 12px; }
.scanner-corner.tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 12px; }
.scanner-corner.bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 12px; }
.scanner-corner.br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 12px; }

.scanner-laser {
    position: absolute;
    left: 8px;
    right: 8px;
    height: 3px;
    background: linear-gradient(90deg, transparent, #38bdf8, #0ea5e9, #38bdf8, transparent);
    box-shadow: 0 0 14px #38bdf8, 0 0 6px #0ea5e9;
    border-radius: 2px;
    animation: scanLaser 2.2s infinite ease-in-out;
}

.swal2-popup.cbt-swal {
    border-radius: 24px !important;
    padding: 1.75rem !important;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
}

.quick-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f1f5f9;
    color: #334155;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid #cbd5e1;
}

/* Standard Elite Header Buttons */
.e-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    border: none;
    line-height: 1.2;
}
.e-btn-primary {
    background: #ffffff;
    color: #059669;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.9);
}
.e-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    background: #f8fafc;
    color: #047857;
}
.e-btn-glass {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.e-btn-glass:hover {
    background: rgba(255, 255, 255, 0.3);
    color: #ffffff;
    transform: translateY(-2px);
}
</style>

<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Header Banner -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fa-solid fa-id-badge"></i> KHUSUS KOMPUTER PROKTOR
                        </div>
                        <h1><i class="fa-solid fa-user-check" style="color: #10b981;"></i> Scan Presensi Pengawas Ruang</h1>
                        <p>Titik presensi resmi bapak/ibu pengawas ujian CBT per sesi dan ruang. Data tersimpan real-time untuk validasi berita acara dan monitoring kehadiran pengawas.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/jadwal') : '/cbt/pengawas/jadwal' ?>" class="e-btn e-btn-glass">
                            <i class="fa-solid fa-calendar-days"></i> Matriks Jadwal
                        </a>
                        <a href="<?= function_exists('base_url') ? base_url('cbt/pengawas/rekap') : '/cbt/pengawas/rekap' ?>" class="e-btn e-btn-primary">
                            <i class="fa-solid fa-clipboard-check"></i> Rekap Presensi Pengawas
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Container -->
            <div style="max-width: 1300px; margin: 1.5rem auto; padding: 0 1.5rem;">

                <!-- Status Live Widget & Barcode Input Card -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    
                    <!-- Scanner Card (Gun / Keyboard Input) -->
                    <div style="background: #ffffff; border-radius: 20px; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-barcode" style="color: #2563eb;"></i> Barcode Gun / Input Cepat
                            </h3>
                            <span class="quick-chip" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; color: #10b981;"></i> Standby Scan
                            </span>
                        </div>

                        <!-- Session & Room Settings for this Scan -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 4px;">Sesi Ujian:</label>
                                <select id="scanSesi" style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                                    <?php foreach ($slotList as $sl): ?>
                                        <option value="<?= htmlspecialchars($sl['label_sesi']) ?>" <?= ($sl['label_sesi'] === $activeSesi) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sl['label_sesi']) ?> (<?= substr($sl['jam_mulai'], 0, 5) ?> - <?= substr($sl['jam_selesai'], 0, 5) ?> WIB)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 4px;">Ruang Ujian (Opsional):</label>
                                <select id="scanRuang" style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                                    <option value="0">-- Auto-Deteksi dari Jadwal --</option>
                                    <?php foreach ($ruangList as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nama_ruang']) ?> (<?= htmlspecialchars($r['kode_ruang']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Form Barcode Gun -->
                        <form id="formBarcodeGun" onsubmit="event.preventDefault(); submitPresensiManual();" style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.78rem; font-weight: 800; color: #475569; margin-bottom: 4px;">
                                Arahkan Barcode Scanner Gun ke Kartu Pengawas / NIP:
                            </label>
                            <div style="position: relative;">
                                <input type="text" id="barcodeGunInput" placeholder="Scan Barcode / Masukkan NIP / Nama Pengawas lalu Tekan Enter..." 
                                       autocomplete="off" autofocus
                                       style="width: 100%; padding: 12px 42px 12px 14px; border-radius: 12px; border: 2px solid #3b82f6; font-size: 0.95rem; font-weight: 700; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);">
                                <button type="submit" style="position: absolute; right: 8px; top: 8px; bottom: 8px; padding: 0 14px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 800; cursor: pointer; transition: 0.2s;">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                            <small style="display: block; margin-top: 6px; font-size: 0.72rem; color: #64748b;">
                                <i class="fa-solid fa-circle-info"></i> Support format: NIP Guru, ID Guru, Kode Barcode, atau nama pengawas.
                            </small>
                        </form>

                        <!-- Quick Dropdown Check-in -->
                        <div style="background: #f8fafc; border-radius: 12px; padding: 12px; border: 1px solid #e2e8f0;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #475569; margin-bottom: 6px;">
                                Atau Pilih Langsung dari Daftar Dewan Guru:
                            </label>
                            <div style="display: flex; gap: 8px;">
                                <select id="selectGuruManual" style="flex: 1; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.82rem; color: #0f172a;">
                                    <option value="">-- Pilih Nama Pengawas --</option>
                                    <?php foreach ($guruList as $g): ?>
                                        <option value="<?= $g['nip'] ?: $g['id'] ?>"><?= htmlspecialchars($g['nama']) ?> (NIP: <?= htmlspecialchars($g['nip'] ?: '-') ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" onclick="submitQuickGuru()" style="padding: 8px 14px; background: #059669; color: white; border: none; border-radius: 8px; font-weight: 800; font-size: 0.8rem; cursor: pointer;">
                                    <i class="fa-solid fa-check"></i> Hadir
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Camera QR Scanner Card -->
                    <div style="background: #ffffff; border-radius: 20px; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-camera" style="color: #10b981;"></i> Scan dengan Kamera Laptop / Webcam
                            </h3>
                            <button type="button" id="btnToggleCamera" onclick="toggleCamera()" style="padding: 6px 14px; border-radius: 10px; background: #0f172a; color: white; border: none; font-size: 0.78rem; font-weight: 800; cursor: pointer;">
                                <i class="fa-solid fa-video"></i> Aktifkan Kamera
                            </button>
                        </div>

                        <!-- Camera Viewport -->
                        <div class="scanner-camera-wrapper" style="flex: 1; min-height: 260px; display: flex; align-items: center; justify-content: center;">
                            <div id="cameraReader" style="width: 100%;"></div>
                            <div id="cameraPlaceholder" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                <i class="fa-solid fa-video-slash" style="font-size: 3rem; margin-bottom: 10px; color: #475569;"></i>
                                <div style="font-weight: 800; font-size: 0.95rem; color: #f8fafc;">Kamera Belum Aktif</div>
                                <div style="font-size: 0.78rem; margin-top: 4px;">Klik tombol "Aktifkan Kamera" di atas untuk memindai kartu QR Code pengawas.</div>
                            </div>
                            <div class="scanner-viewfinder" id="scannerLaserOverlay" style="display: none;">
                                <div class="scanner-box-frame">
                                    <div class="scanner-corner tl"></div>
                                    <div class="scanner-corner tr"></div>
                                    <div class="scanner-corner bl"></div>
                                    <div class="scanner-corner br"></div>
                                    <div class="scanner-laser"></div>
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; margin-top: 10px; font-size: 0.75rem; color: #64748b;">
                            <i class="fa-solid fa-qrcode"></i> Kompatibel dengan QR Code Kartu Guru Resmi & Barcode ID.
                        </div>
                    </div>

                </div>

                <!-- Live Log Table of Today's Proctors -->
                <div style="background: #ffffff; border-radius: 20px; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-list-check" style="color: #10b981;"></i> Daftar Kehadiran Pengawas Hari Ini (<?= htmlspecialchars($curHari) ?>, <?= date('d M Y') ?>)
                            </h3>
                            <p style="margin: 4px 0 0; font-size: 0.8rem; color: #64748b;">Data presensi terverifikasi yang langsung masuk ke laporan honorarium sesi ini.</p>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span class="quick-chip" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; font-size: 0.82rem; padding: 6px 14px;">
                                <i class="fa-solid fa-user-check"></i> <strong id="countHadirToday"><?= $totalHadirToday ?></strong> Pengawas Hadir
                            </span>
                        </div>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 800; text-transform: uppercase; font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <th style="padding: 12px 14px;">No</th>
                                    <th style="padding: 12px 14px;">Nama Pengawas</th>
                                    <th style="padding: 12px 14px;">NIP / ID</th>
                                    <th style="padding: 12px 14px;">Sesi Ujian</th>
                                    <th style="padding: 12px 14px;">Ruang Ujian</th>
                                    <th style="padding: 12px 14px;">Waktu Presensi</th>
                                    <th style="padding: 12px 14px;">Metode</th>
                                    <th style="padding: 12px 14px; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="logTableBody">
                                <?php if (empty($recentPresensi)): ?>
                                    <tr id="emptyRow">
                                        <td colspan="8" style="padding: 3rem 1rem; text-align: center; color: #94a3b8;">
                                            <i class="fa-regular fa-clipboard" style="font-size: 2.5rem; margin-bottom: 10px; color: #cbd5e1;"></i>
                                            <div style="font-weight: 700; font-size: 0.95rem;">Belum ada pengawas yang presensi hari ini.</div>
                                            <div style="font-size: 0.78rem; margin-top: 4px;">Silakan arahkan barcode scanner gun atau scan QR kartu pengawas di atas.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentPresensi as $i => $row): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 12px 14px; font-weight: 800; color: #64748b;"><?= $i + 1 ?></td>
                                            <td style="padding: 12px 14px;">
                                                <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;"><?= htmlspecialchars($row['guru_nama']) ?></div>
                                            </td>
                                            <td style="padding: 12px 14px; font-family: monospace; font-weight: 700; color: #475569;"><?= htmlspecialchars($row['guru_nip'] ?: '-') ?></td>
                                            <td style="padding: 12px 14px;">
                                                <span class="quick-chip" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                                                    <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($row['sesi_label']) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px 14px;">
                                                <span class="quick-chip" style="background: #f0fdf4; color: #15803d; border-color: #bbf7d0;">
                                                    <i class="fa-solid fa-door-open"></i> <?= htmlspecialchars($row['ruang_nama']) ?>
                                                </span>
                                            </td>
                                            <td style="padding: 12px 14px; font-weight: 800; color: #0f172a;">
                                                <?= date('H:i:s', strtotime($row['waktu_scan'])) ?> WIB
                                            </td>
                                            <td style="padding: 12px 14px; font-size: 0.75rem; color: #64748b;">
                                                <span class="quick-chip"><?= htmlspecialchars($row['metode_scan']) ?></span>
                                            </td>
                                            <td style="padding: 12px 14px; text-align: center;">
                                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 800;">
                                                    <i class="fa-solid fa-circle-check"></i> Hadir
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
// Web Audio API Synthesizer for Audio Feedback
function playSound(type) {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (type === 'success') {
            // High double beep
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
            osc.frequency.setValueAtTime(1174.66, audioCtx.currentTime + 0.08); // D6
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.22);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.22);
        } else if (type === 'already') {
            // Warning chime
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.3);
        } else {
            // Error buzz
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.35);
        }
    } catch (e) {}
}

let isProcessingScan = false;
let html5QrScanner = null;
let isCameraActive = false;

function submitPresensiAjax(kode, metode = 'BARCODE_GUN') {
    if (!kode || isProcessingScan) return;
    isProcessingScan = true;

    const sesi = document.getElementById('scanSesi').value;
    const ruangId = document.getElementById('scanRuang').value;

    const postUrl = '<?= function_exists('base_url') ? base_url('cbt/pengawas/scan/process') : '/cbt/pengawas/scan/process' ?>';

    fetch(postUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            kode: kode,
            sesi_label: sesi,
            ruang_id: ruangId,
            metode: metode
        })
    })
    .then(res => res.json())
    .then(data => {
        isProcessingScan = false;
        const inputField = document.getElementById('barcodeGunInput');
        if (inputField) {
            inputField.value = '';
            inputField.focus();
        }

        if (data.success) {
            if (data.already_scanned) {
                playSound('already');
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Presensi!',
                    html: `
                        <div style="text-align: center;">
                            <div style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">${data.pengawas.nama}</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">NIP: ${data.pengawas.nip}</div>
                            <div style="background: #f1f5f9; padding: 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; color: #334155;">
                                Ruang: <strong>${data.pengawas.ruang}</strong><br>
                                Waktu Scan: <strong>${data.pengawas.waktu}</strong> (${data.pengawas.sesi})
                            </div>
                        </div>
                    `,
                    timer: 3500,
                    showConfirmButton: false,
                    customClass: { popup: 'cbt-swal' }
                });
            } else {
                playSound('success');
                prependLogRow(data.pengawas, metode);
                Swal.fire({
                    icon: 'success',
                    title: 'Presensi Berhasil!',
                    html: `
                        <div style="text-align: center;">
                            <div style="font-size: 1.2rem; font-weight: 900; color: #059669; margin-bottom: 4px;">${data.pengawas.nama}</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 12px;">NIP: ${data.pengawas.nip}</div>
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; color: #065f46;">
                                <i class="fa-solid fa-door-open"></i> Bertugas di: <strong>${data.pengawas.ruang}</strong><br>
                                <i class="fa-regular fa-clock"></i> Sesi: <strong>${data.pengawas.sesi}</strong> (${data.pengawas.waktu})
                            </div>
                        </div>
                    `,
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: { popup: 'cbt-swal' }
                });
            }
        } else {
            playSound('error');
            Swal.fire({
                icon: 'error',
                title: 'Gagal Presensi',
                text: data.message || 'Data pengawas tidak terdaftar!',
                timer: 3500,
                showConfirmButton: false,
                customClass: { popup: 'cbt-swal' }
            });
        }
    })
    .catch(err => {
        isProcessingScan = false;
        playSound('error');
        console.error(err);
    });
}

function submitPresensiManual() {
    const input = document.getElementById('barcodeGunInput');
    const val = input.value.trim();
    if (val) {
        submitPresensiAjax(val, 'BARCODE_GUN');
    }
}

function submitQuickGuru() {
    const select = document.getElementById('selectGuruManual');
    const val = select.value.trim();
    if (val) {
        submitPresensiAjax(val, 'MANUAL_PILIH');
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Pilih Pengawas',
            text: 'Silakan pilih nama pengawas terlebih dahulu dari daftar!',
            confirmButtonColor: '#0284c7'
        });
    }
}

function prependLogRow(pengawas, metode) {
    const tbody = document.getElementById('logTableBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    const countElem = document.getElementById('countHadirToday');
    if (countElem) {
        countElem.innerText = parseInt(countElem.innerText || '0') + 1;
    }

    const tr = document.createElement('tr');
    tr.style.borderBottom = '1px solid #f1f5f9';
    tr.style.background = '#f0fdf4';
    tr.style.transition = 'background 0.5s';

    tr.innerHTML = `
        <td style="padding: 12px 14px; font-weight: 800; color: #059669;"><i class="fa-solid fa-star" style="color: #10b981;"></i></td>
        <td style="padding: 12px 14px;">
            <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;">${pengawas.nama}</div>
        </td>
        <td style="padding: 12px 14px; font-family: monospace; font-weight: 700; color: #475569;">${pengawas.nip}</td>
        <td style="padding: 12px 14px;">
            <span class="quick-chip" style="background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe;">
                <i class="fa-regular fa-clock"></i> ${pengawas.sesi}
            </span>
        </td>
        <td style="padding: 12px 14px;">
            <span class="quick-chip" style="background: #f0fdf4; color: #15803d; border-color: #bbf7d0;">
                <i class="fa-solid fa-door-open"></i> ${pengawas.ruang}
            </span>
        </td>
        <td style="padding: 12px 14px; font-weight: 800; color: #059669;">
            ${pengawas.waktu}
        </td>
        <td style="padding: 12px 14px; font-size: 0.75rem; color: #64748b;">
            <span class="quick-chip">${metode}</span>
        </td>
        <td style="padding: 12px 14px; text-align: center;">
            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 9999px; background: #dcfce7; color: #166534; font-size: 0.75rem; font-weight: 800;">
                <i class="fa-solid fa-circle-check"></i> Hadir
            </span>
        </td>
    `;

    tbody.insertBefore(tr, tbody.firstChild);

    setTimeout(() => {
        tr.style.background = 'transparent';
    }, 2500);
}

// Camera Scanner Handling
function toggleCamera() {
    const btn = document.getElementById('btnToggleCamera');
    const placeholder = document.getElementById('cameraPlaceholder');
    const laserOverlay = document.getElementById('scannerLaserOverlay');

    if (isCameraActive) {
        if (html5QrScanner) {
            html5QrScanner.stop().then(() => {
                isCameraActive = false;
                btn.innerHTML = '<i class="fa-solid fa-video"></i> Aktifkan Kamera';
                btn.style.background = '#0f172a';
                placeholder.style.display = 'block';
                laserOverlay.style.display = 'none';
            });
        }
    } else {
        html5QrScanner = new Html5Qrcode("cameraReader");
        const config = { fps: 12, qrbox: { width: 220, height: 220 } };

        html5QrScanner.start({ facingMode: "environment" }, config, (decodedText) => {
            if (!isProcessingScan) {
                submitPresensiAjax(decodedText, 'KAMERA_QR');
            }
        }).then(() => {
            isCameraActive = true;
            btn.innerHTML = '<i class="fa-solid fa-video-slash"></i> Matikan Kamera';
            btn.style.background = '#dc2626';
            placeholder.style.display = 'none';
            laserOverlay.style.display = 'flex';
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Akses Kamera Gagal',
                text: 'Tidak dapat mengakses kamera: ' + err,
                confirmButtonColor: '#0284c7'
            });
        });
    }
}

// Keep Focus on Barcode Gun Input
document.addEventListener('click', function(e) {
    const input = document.getElementById('barcodeGunInput');
    if (input && !['INPUT', 'SELECT', 'BUTTON', 'A'].includes(e.target.tagName)) {
        input.focus();
    }
});
</script>
