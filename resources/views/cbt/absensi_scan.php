<?php
$pageTitle = 'Scanner Absensi CBT 1 Pintu';
$active_menu = 'absensi_cbt';
?>
<!-- SweetAlert2 Modern -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Animasi Laser Scanner & Corner Borders */
@keyframes scanLaserLine {
    0% { top: 8%; opacity: 0.2; }
    50% { top: 88%; opacity: 1; }
    100% { top: 8%; opacity: 0.2; }
}

@keyframes pulseGlow {
    0%, 100% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.03); opacity: 1; }
}

@keyframes popIn {
    0% { transform: scale(0.96); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

.scanner-viewfinder {
    position: absolute;
    inset: 0;
    pointer-events: none;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 15;
}

.scanner-box-frame {
    position: relative;
    width: 260px;
    height: 260px;
    border: 2px dashed rgba(56, 189, 248, 0.45);
    border-radius: 20px;
    box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.45);
}

.scanner-corner {
    position: absolute;
    width: 28px;
    height: 28px;
    border-color: #38bdf8;
    border-style: solid;
}
.scanner-corner.tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 12px; }
.scanner-corner.tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 12px; }
.scanner-corner.bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 12px; }
.scanner-corner.br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 12px; }

.scanner-laser {
    position: absolute;
    left: 10px;
    right: 10px;
    height: 3px;
    background: linear-gradient(90deg, transparent, #38bdf8, #0ea5e9, #38bdf8, transparent);
    box-shadow: 0 0 14px #38bdf8, 0 0 6px #0ea5e9;
    border-radius: 2px;
    animation: scanLaserLine 2.2s infinite ease-in-out;
}

/* Custom SweetAlert2 Styling */
.swal2-popup.cbt-swal {
    border-radius: 24px !important;
    padding: 1.75rem !important;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif !important;
}
.swal2-title.cbt-swal-title {
    font-size: 1.35rem !important;
    font-weight: 900 !important;
    color: #0f172a !important;
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
                        <div class="header-badge">SINGLE-DOOR SMART ATTENDANCE</div>
                        <h1><i data-lucide="qr-code"></i> Scanner Absensi CBT 1 Pintu</h1>
                        <p>Satu titik absensi otomatis untuk seluruh siswa dan mapel. Mendukung scan Kartu Pelajar Siakad (QR Token resmi), Kartu CBT Kelas Jauh, maupun Barcode NIS.</p>
                    </div>
                    <div class="header-actions-premium" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="<?php echo base_url('cbt/absensi'); ?>" class="e-btn e-btn-glass">
                            <i data-lucide="clipboard-check"></i> Rekap Kehadiran Siswa
                        </a>
                        <a href="<?php echo base_url('cbt/jadwal'); ?>" class="e-btn e-btn-blue-light">
                            <i data-lucide="calendar"></i> Cek Jadwal Hari Ini
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grid Scanner & Live Log -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">

                <!-- Kolom Kiri: Kamera Scanner & Input Barcode -->
                <div class="card" style="padding: 1.5rem; border-radius: 20px; border: 1.5px solid #cbd5e1; background: #ffffff;">
                    <div style="font-weight: 800; font-size: 1rem; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="camera" style="color: #2563eb;"></i> Kamera Pemindai Kartu
                        </span>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" id="btnSwitchCam" onclick="switchCamera()" style="display: none; background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 8px; cursor: pointer;">
                                <i data-lucide="refresh-cw" style="width: 12px; height: 12px; display: inline-block; vertical-align: -2px;"></i> Ganti Kamera
                            </button>
                            <span id="scanStatusBadge" style="font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 3px 10px; border-radius: 100px;">
                                ● Kamera Siap
                            </span>
                        </div>
                    </div>

                    <!-- Area Kamera QR / Barcode -->
                    <div style="position: relative; background: #0f172a; border-radius: 16px; overflow: hidden; min-height: 290px; display: flex; align-items: center; justify-content: center;">
                        <div id="reader" style="width: 100%; border: none;"></div>
                        
                        <!-- Viewfinder Laser Overlay -->
                        <div class="scanner-viewfinder" id="scannerViewfinder">
                            <div class="scanner-box-frame">
                                <div class="scanner-corner tl"></div>
                                <div class="scanner-corner tr"></div>
                                <div class="scanner-corner bl"></div>
                                <div class="scanner-corner br"></div>
                                <div class="scanner-laser"></div>
                            </div>
                        </div>

                        <!-- Placeholder Sebelum Aktif -->
                        <div id="cameraPlaceholder" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; background: rgba(15, 23, 42, 0.95); z-index: 20;">
                            <div style="width: 64px; height: 64px; border-radius: 20px; background: rgba(56, 189, 248, 0.15); display: flex; align-items: center; justify-content: center; margin-bottom: 14px; animation: pulseGlow 2s infinite;">
                                <i data-lucide="scan" style="width: 36px; height: 36px; color: #38bdf8;"></i>
                            </div>
                            <div style="font-size: 1rem; font-weight: 900; letter-spacing: -0.3px;">Arahkan Kartu Pelajar ke Kamera</div>
                            <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 4px;">Mendukung QR Token Kartu Pelajar & Barcode NIS</div>
                            <button type="button" onclick="startCameraScanner()" class="e-btn e-btn-primary" style="margin-top: 16px; background: #2563eb; padding: 10px 22px; font-weight: 800;">
                                <i data-lucide="video"></i> Aktifkan Kamera Pemindai
                            </button>
                        </div>
                    </div>

                    <!-- Manual Input / Barcode Gun -->
                    <div style="margin-top: 16px;">
                        <label class="label-sm" style="font-weight: 800; color: #334155;">Input Manual / Gun Barcode Scanner:</label>
                        <form id="formManualScan" onsubmit="handleManualSubmit(event)" style="display: flex; gap: 8px;">
                            <input type="text" id="inpScanCode" class="input-sm" placeholder="Scan barcode kartu atau ketik NIS / Token..." autofocus autocomplete="off" style="flex: 1; font-weight: 800; font-family: monospace; font-size: 0.95rem;">
                            <button type="submit" class="e-btn e-btn-primary" id="btnSubmitManual" style="background: #2563eb; padding: 0 20px; font-weight: 800;">
                                Absen Siswa
                            </button>
                        </form>
                    </div>

                    <!-- Peringatan Sistem 1 Pintu Otomatis -->
                    <div style="margin-top: 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 14px; font-size: 0.78rem; color: #166534; line-height: 1.45;">
                        <i data-lucide="shield-check" style="width: 15px; height: 15px; display: inline-block; vertical-align: -2px; margin-right: 4px; color: #16a34a;"></i>
                        <strong>Smart Multi-Token Gate:</strong> Mengenali otomatis QR Kartu Pelajar resmi Siakad (format <code>TOKEN:...</code>), Kartu CBT Kelas Jauh, dan Barcode NIS fisik madrasah.
                    </div>
                </div>

                <!-- Kolom Kanan: Hasil Scan Terakhir & Log Realtime -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                    <!-- Kartu Notifikasi Siswa Terakhir Diabsen -->
                    <div class="card" id="cardSuccessScan" style="display: none; padding: 1.5rem; border-radius: 20px; border: 2px solid #86efac; background: #f0fdf4; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div id="lblScanIconBox" style="width: 58px; height: 58px; border-radius: 16px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 900; flex-shrink: 0; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
                                ✓
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.72rem; font-weight: 900; color: #15803d; letter-spacing: 1px; text-transform: uppercase;" id="lblScanStatusBadge">
                                    BERHASIL ABSENSI CBT
                                </div>
                                <h3 id="lblScanNamaSiswa" style="font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 2px 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Nama Siswa
                                </h3>
                                <div style="font-size: 0.82rem; color: #334155; font-weight: 700; display: flex; gap: 10px; flex-wrap: wrap;">
                                    <span>NIS: <b id="lblScanNis" style="font-family: monospace;">-</b></span>
                                    <span>&bull;</span>
                                    <span>Kelas: <b id="lblScanKelas">-</b></span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed rgba(134, 239, 172, 0.8); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                            <div>
                                <span style="color: #64748b;">Mata Pelajaran:</span>
                                <b id="lblScanMapel" style="color: #0f172a; margin-left: 4px;">-</b>
                            </div>
                            <div>
                                <span style="color: #64748b;">Sesi:</span>
                                <span id="lblScanSesi" style="background: #2563eb; color: white; padding: 3px 10px; border-radius: 6px; font-weight: 800; font-size: 0.75rem; margin-left: 4px;">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Log Kehadiran Realtime Hari Ini -->
                    <div class="card" style="flex: 1; border-radius: 20px; border: 1px solid #e2e8f0; background: white; overflow: hidden; display: flex; flex-direction: column; min-height: 380px;">
                        <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="history" style="color: #64748b; width: 16px; height: 16px;"></i> Siswa Sudah Absen Hari Ini
                            </div>
                            <span id="lblTotalAbsenToday" style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; padding: 3px 10px; border-radius: 100px; font-weight: 800;">
                                <?php echo count($recentAbsen); ?> Siswa
                            </span>
                        </div>

                        <div id="listRecentAbsen" style="padding: 10px 14px; overflow-y: auto; max-height: 380px; flex: 1;">
                            <?php if (empty($recentAbsen)): ?>
                                <div id="emptyAbsenMsg" style="text-align: center; padding: 40px 10px; color: #94a3b8; font-size: 0.85rem;">
                                    <i data-lucide="user-check" style="width: 38px; height: 38px; margin-bottom: 8px; opacity: 0.4; display: inline-block;"></i>
                                    <div>Belum ada siswa yang melakukan absensi hari ini. Silakan mulai scan kartu siswa.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentAbsen as $ra): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 10px; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;"><?php echo htmlspecialchars($ra['nama_siswa']); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                                <span style="font-weight: 700; color: #0284c7;"><?php echo htmlspecialchars($ra['kelas_nama']); ?></span> &bull; NIS: <span style="font-family: monospace; font-weight: 700;"><?php echo htmlspecialchars($ra['nis']); ?></span> &bull; <?php echo htmlspecialchars($ra['mapel_nama']); ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span style="font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 6px;">
                                                <?php echo date('H:i', strtotime($ra['waktu_scan'])); ?> WIB
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Script HTML5 QR Code Library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let html5QrCode = null;
let isScanning = false;
let lastScanCode = '';
let lastScanTime = 0;
let availableCameras = [];
let currentCameraIndex = 0;

// Audio Context Web Audio API untuk feedback suara tanpa file eksternal
let audioCtx = null;
function getAudioContext() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

// 1. Suara Sukses: Chime nada ganda yang elegan (G5 -> C6)
function playSuccessChime() {
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;
        
        // Nada 1: G5 (784Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(784, now);
        gain1.gain.setValueAtTime(0.28, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.12);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.12);

        // Nada 2: C6 (1046.5Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(1046.5, now + 0.08);
        gain2.gain.setValueAtTime(0.32, now + 0.08);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.08);
        osc2.stop(now + 0.35);

        if (navigator.vibrate) navigator.vibrate(80);
    } catch(e) {}
}

// 2. Suara Peringatan / Sudah Absen: Double blip
function playAlreadyBeep() {
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;
        [0, 0.09].forEach((delay) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(659.25, now + delay);
            gain.gain.setValueAtTime(0.22, now + delay);
            gain.gain.exponentialRampToValueAtTime(0.001, now + delay + 0.07);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now + delay);
            osc.stop(now + delay + 0.07);
        });
        if (navigator.vibrate) navigator.vibrate([60, 40, 60]);
    } catch(e) {}
}

// 3. Suara Peringatan / Siswa Tidak Ditemukan: Buzzer nada rendah
function playErrorBuzzer() {
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(220, now);
        osc.frequency.exponentialRampToValueAtTime(150, now + 0.3);
        gain.gain.setValueAtTime(0.35, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.3);

        if (navigator.vibrate) navigator.vibrate([120, 60, 200]);
    } catch(e) {}
}

// Inisialisasi Kamera Pemindai
function startCameraScanner() {
    document.getElementById('cameraPlaceholder').style.display = 'none';
    document.getElementById('scannerViewfinder').style.display = 'flex';
    document.getElementById('scanStatusBadge').innerHTML = '● Membuka Kamera...';
    document.getElementById('scanStatusBadge').style.background = '#fef3c7';
    document.getElementById('scanStatusBadge').style.color = '#b45309';

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            availableCameras = cameras;
            if (cameras.length > 1) {
                document.getElementById('btnSwitchCam').style.display = 'inline-block';
            }
            // Pilih kamera belakang jika ada
            let preferredCamId = cameras[0].id;
            const backCam = cameras.find(c => c.label.toLowerCase().includes('back') || c.label.toLowerCase().includes('belakang') || c.label.toLowerCase().includes('rear'));
            if (backCam) preferredCamId = backCam.id;

            runCameraWithId(preferredCamId);
        } else {
            runCameraWithId({ facingMode: "environment" });
        }
    }).catch(err => {
        runCameraWithId({ facingMode: "environment" });
    });
}

function runCameraWithId(camConfig) {
    if (html5QrCode) {
        try { html5QrCode.stop(); } catch(e) {}
    }

    html5QrCode = new Html5Qrcode("reader");
    const config = { 
        fps: 15, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrCode.start(
        camConfig,
        config,
        (decodedText, decodedResult) => {
            const now = Date.now();
            if (decodedText === lastScanCode && (now - lastScanTime < 2800)) {
                return; // Debounce 2.8s untuk barcode yang sama
            }
            lastScanCode = decodedText;
            lastScanTime = now;

            submitAbsenCode(decodedText);
        },
        (errorMessage) => {
            // Frame error - abaikan
        }
    ).then(() => {
        isScanning = true;
        document.getElementById('scanStatusBadge').innerHTML = '● Kamera Aktif';
        document.getElementById('scanStatusBadge').style.background = '#dcfce7';
        document.getElementById('scanStatusBadge').style.color = '#15803d';
    }).catch((err) => {
        document.getElementById('cameraPlaceholder').style.display = 'flex';
        document.getElementById('scannerViewfinder').style.display = 'none';
        document.getElementById('scanStatusBadge').innerHTML = '● Kamera Offline';
        document.getElementById('scanStatusBadge').style.background = '#fee2e2';
        document.getElementById('scanStatusBadge').style.color = '#b91c1c';

        Swal.fire({
            icon: 'error',
            title: 'Kamera Tidak Dapat Dibuka',
            html: `
                <div style="font-size:0.92rem; color:#475569; line-height:1.5;">
                    Pastikan browser diizinkan mengakses kamera perangkat Anda (Camera Permissions).<br>
                    <span style="font-size:0.78rem; color:#94a3b8; font-family:monospace; margin-top:6px; display:block;">${escapeHtml(String(err))}</span>
                </div>
            `,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Tutup',
            customClass: { popup: 'cbt-swal', title: 'cbt-swal-title' }
        });
    });
}

function switchCamera() {
    if (availableCameras.length <= 1) return;
    currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
    runCameraWithId(availableCameras[currentCameraIndex].id);
}

function handleManualSubmit(e) {
    e.preventDefault();
    const inp = document.getElementById('inpScanCode');
    const code = inp.value.trim();
    if (!code) {
        inp.focus();
        return;
    }

    submitAbsenCode(code);
    inp.value = '';
}

function escapeHtml(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function submitAbsenCode(code) {
    const formData = new FormData();
    formData.append('code', code);

    // Animasi tombol manual jika diklik
    const btnManual = document.getElementById('btnSubmitManual');
    if (btnManual) {
        btnManual.disabled = true;
        btnManual.innerHTML = '<i data-lucide="loader-2" class="spin"></i> Memproses...';
        if (window.lucide) lucide.createIcons();
    }

    fetch('<?php echo base_url("cbt/scan/process"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Tampilkan kartu hasil di sisi kanan
            const card = document.getElementById('cardSuccessScan');
            const iconBox = document.getElementById('lblScanIconBox');
            const badge = document.getElementById('lblScanStatusBadge');
            
            card.style.display = 'block';
            card.style.animation = 'none';
            void card.offsetWidth; // Trigger reflow
            card.style.animation = 'popIn 0.3s ease-out';

            if (data.already) {
                card.style.background = '#fffbeb';
                card.style.borderColor = '#fde68a';
                iconBox.style.background = '#fef3c7';
                iconBox.style.color = '#b45309';
                iconBox.textContent = 'ℹ';
                badge.textContent = 'SUDAH TERABSEN SEBELUMNYA';
                badge.style.color = '#b45309';
                playAlreadyBeep();
            } else {
                card.style.background = '#f0fdf4';
                card.style.borderColor = '#86efac';
                iconBox.style.background = '#dcfce7';
                iconBox.style.color = '#15803d';
                iconBox.textContent = '✓';
                const statusTxt = (data.status || 'HADIR').toUpperCase();
                badge.textContent = 'BERHASIL ABSENSI CBT (' + statusTxt + ')';
                badge.style.color = '#15803d';
                playSuccessChime();
            }

            document.getElementById('lblScanNamaSiswa').textContent = data.siswa.nama;
            document.getElementById('lblScanNis').textContent = data.siswa.nis;
            document.getElementById('lblScanKelas').textContent = data.siswa.kelas_nama;
            document.getElementById('lblScanMapel').textContent = data.mapel || data.mapel_nama || '-';
            document.getElementById('lblScanSesi').textContent = data.sesi || data.jam_slot || 'Sesi 1';

            // Toast Modern SweetAlert2 di pojok kanan atas
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3200,
                timerProgressBar: true,
                customClass: { popup: 'cbt-swal' }
            });

            Toast.fire({
                icon: data.already ? 'info' : 'success',
                title: data.already ? 'Sudah Terabsen' : 'Absensi Berhasil!',
                text: `${data.siswa.nama} (${data.siswa.kelas_nama})`
            });

            // Tambahkan ke log realtime jika absensi baru
            if (!data.already) {
                const emptyMsg = document.getElementById('emptyAbsenMsg');
                if (emptyMsg) emptyMsg.style.display = 'none';

                const list = document.getElementById('listRecentAbsen');
                const row = document.createElement('div');
                row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 10px 10px; border-bottom: 1px solid #f1f5f9; animation: popIn 0.3s ease-out;';
                row.innerHTML = `
                    <div>
                        <div style="font-weight: 800; font-size: 0.88rem; color: #0f172a;">${escapeHtml(data.siswa.nama)}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                            <span style="font-weight: 700; color: #0284c7;">${escapeHtml(data.siswa.kelas_nama)}</span> &bull; NIS: <span style="font-family: monospace; font-weight: 700;">${escapeHtml(data.siswa.nis)}</span> &bull; ${escapeHtml(data.mapel || data.mapel_nama || '')}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 6px;">
                            ${escapeHtml(data.waktu)} WIB
                        </span>
                    </div>
                `;
                list.insertBefore(row, list.firstChild);

                // Update counter total
                const totalEl = document.getElementById('lblTotalAbsenToday');
                if (totalEl) {
                    const currentCount = parseInt(totalEl.innerText) || 0;
                    totalEl.innerText = (currentCount + 1) + ' Siswa';
                }
            }

        } else {
            // Suara Buzzer Peringatan
            playErrorBuzzer();
            lastScanCode = ''; // Reset agar kartu bisa discan ulang jika posisi miring

            // SweetAlert2 Modal Modern yang elegan (Bukan alert jadul browser!)
            Swal.fire({
                icon: 'warning',
                title: 'Siswa Tidak Ditemukan!',
                html: `
                    <div style="font-size: 0.95rem; color: #334155; margin-bottom: 12px; font-weight: 600;">
                        ${escapeHtml(data.message)}
                    </div>
                    <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 12px; padding: 10px 14px; text-align: left; font-size: 0.8rem; color: #475569; line-height: 1.5;">
                        <div style="font-weight: 800; color: #0284c7; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                            💡 Solusi Cepat Pengawas:
                        </div>
                        • Pastikan kartu dicetak dari Siakad atau Kartu Peserta CBT resmi.<br>
                        • Jika barcode buram, gunakan kolom <b>Input Manual NIS</b> di bawah.<br>
                        • Kode yang terbaca: <code style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #0f172a; font-weight: 800;">${escapeHtml(code)}</code>
                    </div>
                `,
                timer: 4000,
                timerProgressBar: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Tutup & Lanjut Scan',
                customClass: { popup: 'cbt-swal', title: 'cbt-swal-title' }
            }).then(() => {
                const inp = document.getElementById('inpScanCode');
                if (inp) inp.focus();
            });
        }
    })
    .catch(err => {
        playErrorBuzzer();
        lastScanCode = '';
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Terputus',
            text: 'Gagal memproses data absensi: ' + err,
            confirmButtonColor: '#2563eb',
            customClass: { popup: 'cbt-swal', title: 'cbt-swal-title' }
        });
    })
    .finally(() => {
        if (btnManual) {
            btnManual.disabled = false;
            btnManual.innerHTML = 'Absen Siswa';
        }
        if (window.lucide) lucide.createIcons();
    });
}
</script>
