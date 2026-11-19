<?php
$pageTitle = 'Scanner Absensi CBT 1 Pintu';
$active_menu = 'absensi_cbt';
?>
<div class="siakad-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="z-main">
        <?php include __DIR__ . '/header.php'; ?>

        <div class="z-scroll" style="padding: 0 0 3rem 0;">

            <!-- Header -->
            <div class="elite-page-header">
                <div class="header-bg-shapes"></div>
                <div class="header-content-inner">
                    <div class="header-text-premium">
                        <div class="header-badge">SINGLE-DOOR SMART ATTENDANCE</div>
                        <h1><i data-lucide="qr-code"></i> Scanner Absensi CBT 1 Pintu</h1>
                        <p>Satu titik absensi otomatis untuk seluruh siswa dan mapel. Siswa otomatis diarahkan ke mata pelajaran dan sesi kelasnya sesuai jadwal hari ini.</p>
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
                        <span id="scanStatusBadge" style="font-size: 0.72rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 100px;">
                            ● Kamera Siap
                        </span>
                    </div>

                    <!-- Area Kamera QR / Barcode -->
                    <div style="position: relative; background: #0f172a; border-radius: 16px; overflow: hidden; min-height: 280px; display: flex; align-items: center; justify-content: center;">
                        <div id="reader" style="width: 100%;"></div>
                        <div id="cameraPlaceholder" style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; background: rgba(15, 23, 42, 0.9);">
                            <i data-lucide="scan" style="width: 50px; height: 50px; color: #38bdf8; margin-bottom: 12px; animation: pulse 2s infinite;"></i>
                            <div style="font-size: 0.95rem; font-weight: 800;">Arahkan Kartu Pelajar Siswa ke Kamera</div>
                            <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 4px;">Mendukung QR Code & Barcode NIS</div>
                            <button type="button" onclick="startCameraScanner()" class="e-btn e-btn-primary" style="margin-top: 14px; background: #2563eb;">
                                <i data-lucide="video"></i> Aktifkan Kamera
                            </button>
                        </div>
                    </div>

                    <!-- Manual Input / Barcode Gun -->
                    <div style="margin-top: 16px;">
                        <label class="label-sm" style="font-weight: 800;">Input Manual / Gun Barcode Scanner:</label>
                        <form id="formManualScan" onsubmit="handleManualSubmit(event)" style="display: flex; gap: 8px;">
                            <input type="text" id="inpScanCode" class="input-sm" placeholder="Scan barcode kartu atau ketik NIS siswa..." autofocus autocomplete="off" style="flex: 1; font-weight: 800; font-family: monospace;">
                            <button type="submit" class="e-btn e-btn-primary" style="background: #2563eb; padding: 0 18px;">
                                Absen Siswa
                            </button>
                        </form>
                    </div>

                    <!-- Peringatan Sistem 1 Pintu Otomatis -->
                    <div style="margin-top: 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 10px 14px; font-size: 0.78rem; color: #1e40af; line-height: 1.45;">
                        <i data-lucide="info" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px; margin-right: 4px;"></i>
                        <strong>Sistem 1 Pintu Cerdas:</strong> Siswa dari kelas mana pun yang scan di pintu ini otomatis masuk ke mapel dan sesinya masing-masing sesuai jam jadwal aktif saat ini.
                    </div>
                </div>

                <!-- Kolom Kanan: Hasil Scan Terakhir & Log Realtime -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                    <!-- Kartu Notifikasi Siswa Terakhir Diabsen -->
                    <div class="card" id="cardSuccessScan" style="display: none; padding: 1.5rem; border-radius: 20px; border: 2px solid #86efac; background: #f0fdf4;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="width: 56px; height: 56px; border-radius: 14px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0;">
                                ✓
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.72rem; font-weight: 900; color: #15803d; letter-spacing: 1px; text-transform: uppercase;" id="lblScanStatusBadge">
                                    BERHASIL ABSENSI CBT
                                </div>
                                <h3 id="lblScanNamaSiswa" style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin: 2px 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    Nama Siswa
                                </h3>
                                <div style="font-size: 0.8rem; color: #334155; font-weight: 700; display: flex; gap: 10px; flex-wrap: wrap;">
                                    <span>NIS: <b id="lblScanNis">-</b></span>
                                    <span>&bull;</span>
                                    <span>Kelas: <b id="lblScanKelas">-</b></span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #86efac; display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem;">
                            <div>
                                <span style="color: #64748b;">Mata Pelajaran:</span>
                                <b id="lblScanMapel" style="color: #0f172a;">-</b>
                            </div>
                            <div>
                                <span style="color: #64748b;">Sesi:</span>
                                <span id="lblScanSesi" style="background: #2563eb; color: white; padding: 2px 8px; border-radius: 6px; font-weight: 800;">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Log Kehadiran Realtime Hari Ini -->
                    <div class="card" style="flex: 1; border-radius: 20px; border: 1px solid #e2e8f0; background: white; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="padding: 1rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="history" style="color: #64748b;"></i> Siswa Sudah Absen Hari Ini
                            </div>
                            <span id="lblTotalAbsenToday" style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; padding: 2px 8px; border-radius: 100px; font-weight: 800;">
                                <?php echo count($recentAbsen); ?> Siswa
                            </span>
                        </div>

                        <div id="listRecentAbsen" style="padding: 12px 14px; overflow-y: auto; max-height: 380px; flex: 1;">
                            <?php if (empty($recentAbsen)): ?>
                                <div id="emptyAbsenMsg" style="text-align: center; padding: 30px 10px; color: #94a3b8; font-size: 0.82rem;">
                                    Belum ada siswa yang melakukan absensi hari ini. Silakan mulai scan kartu siswa.
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentAbsen as $ra): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-bottom: 1px solid #f1f5f9;">
                                        <div>
                                            <div style="font-weight: 800; font-size: 0.85rem; color: #0f172a;"><?php echo htmlspecialchars($ra['nama_siswa']); ?></div>
                                            <div style="font-size: 0.72rem; color: #64748b;">
                                                <?php echo htmlspecialchars($ra['kelas_nama']); ?> &bull; NIS: <?php echo htmlspecialchars($ra['nis']); ?> &bull; <?php echo htmlspecialchars($ra['mapel_nama']); ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span style="font-size: 0.7rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px;">
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

function startCameraScanner() {
    document.getElementById('cameraPlaceholder').style.display = 'none';

    html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        (decodedText, decodedResult) => {
            const now = Date.now();
            if (decodedText === lastScanCode && (now - lastScanTime < 3000)) {
                return; // Debounce 3 detik untuk barcode yang sama
            }
            lastScanCode = decodedText;
            lastScanTime = now;

            submitAbsenCode(decodedText);
        },
        (errorMessage) => {
            // parse error, ignore
        }
    ).catch((err) => {
        alert("Gagal mengakses kamera: " + err);
        document.getElementById('cameraPlaceholder').style.display = 'flex';
    });
}

function handleManualSubmit(e) {
    e.preventDefault();
    const code = document.getElementById('inpScanCode').value.trim();
    if (!code) return;

    submitAbsenCode(code);
    document.getElementById('inpScanCode').value = '';
}

function submitAbsenCode(code) {
    const formData = new FormData();
    formData.append('code', code);

    fetch('<?php echo base_url("cbt/scan/process"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Tampilkan kartu sukses
            const card = document.getElementById('cardSuccessScan');
            card.style.display = 'block';

            if (data.already) {
                card.style.background = '#fffbeb';
                card.style.borderColor = '#fde68a';
                document.getElementById('lblScanStatusBadge').textContent = 'SUDAH TERABSEN SEBELUMNYA';
                document.getElementById('lblScanStatusBadge').style.color = '#b45309';
            } else {
                card.style.background = '#f0fdf4';
                card.style.borderColor = '#86efac';
                document.getElementById('lblScanStatusBadge').textContent = 'BERHASIL ABSENSI CBT (' + data.status.toUpperCase() + ')';
                document.getElementById('lblScanStatusBadge').style.color = '#15803d';
            }

            document.getElementById('lblScanNamaSiswa').textContent = data.siswa.nama;
            document.getElementById('lblScanNis').textContent = data.siswa.nis;
            document.getElementById('lblScanKelas').textContent = data.siswa.kelas_nama;
            document.getElementById('lblScanMapel').textContent = data.mapel;
            document.getElementById('lblScanSesi').textContent = data.sesi;

            // Tambahkan ke log daftar realtime di kanan jika baru absen
            if (!data.already) {
                const emptyMsg = document.getElementById('emptyAbsenMsg');
                if (emptyMsg) emptyMsg.style.display = 'none';

                const list = document.getElementById('listRecentAbsen');
                const row = document.createElement('div');
                row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-bottom: 1px solid #f1f5f9; animation: slideDown 0.3s;';
                row.innerHTML = `
                    <div>
                        <div style="font-weight: 800; font-size: 0.85rem; color: #0f172a;">${data.siswa.nama}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">
                            ${data.siswa.kelas_nama} &bull; NIS: ${data.siswa.nis} &bull; ${data.mapel}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.7rem; font-weight: 800; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px;">
                            ${data.waktu} WIB
                        </span>
                    </div>
                `;
                list.insertBefore(row, list.firstChild);
            }

            // Suara beep notifikasi audio
            playBeepSound();
        } else {
            alert('⚠️ ' + data.message);
        }
    })
    .catch(err => {
        alert('Gagal memproses absensi: ' + err);
    });
}

function playBeepSound() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(800, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } catch(e) {}
}
</script>
