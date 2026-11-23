<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$guru_nama = $_SESSION['nama'] ?? ($_SESSION['guru_nama'] ?? 'Pengawas CBT');

// Ambil daftar ruang ujian aktif
$ruangList = [];
try {
    $stmtR = $core_db->query("SELECT id, kode_ruang, nama_ruang, lokasi FROM cbt_ruang ORDER BY kode_ruang ASC");
    if ($stmtR) {
        $ruangList = $stmtR->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (\Exception $e) {}

// Ambil info sesi / jadwal hari ini
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$curHari = $hariIndo[date('l')] ?? 'Senin';
$todayDate = date('Y-m-d');

$jadwalHariIni = [];
try {
    $stmtJ = $core_db->prepare("
        SELECT g.*, u.nama_ujian, s.jam_mulai, s.jam_selesai, s.label_sesi
        FROM cbt_jadwal_grid g
        LEFT JOIN cbt_ujian u ON g.ujian_id = u.id
        LEFT JOIN cbt_jam_slot s ON (g.jam_slot = s.label_sesi OR g.jam_slot = s.urutan)
        WHERE (g.tanggal = ? OR (g.tanggal IS NULL AND g.hari = ?))
        ORDER BY s.jam_mulai ASC, g.id ASC
    ");
    $stmtJ->execute([$todayDate, $curHari]);
    $jadwalHariIni = $stmtJ->fetchAll(PDO::FETCH_ASSOC);
} catch (\Exception $e) {}

$page_title = 'Scan Absensi CBT 1 Pintu';
include 'header.php';
?>

<div class="header" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
    <div class="header-top">
        <div class="greeting">
            <p><i class="fa-solid fa-qrcode"></i> Single-Door Gate Scan</p>
            <h1>Absensi CBT Siswa</h1>
        </div>
        <div class="avatar" style="color: #7c3aed; background: white;" onclick="history.back()">
            <i class="fa-solid fa-arrow-left"></i>
        </div>
    </div>
</div>

<div class="main-content" style="padding-bottom: 90px;">
    <!-- Info Banner Sesi Hari Ini -->
    <div style="background: white; border-radius: 14px; padding: 14px 16px; margin-bottom: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="font-size: 0.78rem; font-weight: 800; color: #4338ca; text-transform: uppercase;">
                <i class="fa-solid fa-calendar-day"></i> <?= $curHari ?>, <?= date('d M Y') ?>
            </div>
            <div id="liveClock" style="font-size: 0.82rem; font-weight: 900; color: #0f172a; font-family: monospace;">
                <?= date('H:i:s') ?> WIB
            </div>
        </div>
        <div style="font-size: 0.75rem; color: #64748b;">
            Petugas: <b><?= htmlspecialchars($guru_nama) ?></b> • Scan kartu siswa reguler maupun kelas jauh otomatis diarahkan ke mapel dan jam sesi aktif saat ini.
        </div>
    </div>

    <!-- Pilih Ruang Ujian (Opsional) -->
    <div style="background: white; border-radius: 14px; padding: 12px 15px; margin-bottom: 14px; border: 1px solid #e2e8f0;">
        <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 4px; text-transform: uppercase;">
            <i class="fa-solid fa-door-open" style="color: #7c3aed;"></i> Lokasi / Ruang Ujian:
        </label>
        <select id="selectRuang" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.85rem; font-weight: 700; color: #1e293b; background: #f8fafc; outline: none;">
            <option value="Gerbang Utama / Pintu Masuk">-- Gerbang Utama / Pintu Masuk 1 Pintu --</option>
            <?php foreach ($ruangList as $r): ?>
                <option value="<?= htmlspecialchars($r['nama_ruang']) ?>"><?= htmlspecialchars($r['kode_ruang']) ?> - <?= htmlspecialchars($r['nama_ruang']) ?> (<?= htmlspecialchars($r['lokasi'] ?: 'Madrasah') ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Scanner Box -->
    <div style="background: #0f172a; border-radius: 16px; overflow: hidden; position: relative; margin-bottom: 14px; box-shadow: 0 8px 24px rgba(15,23,42,0.25);">
        <div id="qr-reader" style="width: 100%; min-height: 270px; background: #000;"></div>
        
        <div id="scanOverlay" style="position: absolute; top: 12px; left: 12px; right: 12px; display: flex; justify-content: space-between; align-items: center; pointer-events: none; z-index: 10;">
            <span style="background: rgba(15,23,42,0.85); color: #4ade80; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; border: 1px solid rgba(74,222,128,0.4); display: flex; align-items: center; gap: 5px;">
                <span style="width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: pulse 1.5s infinite;"></span>
                KAMERA AKTIF
            </span>
            <button type="button" id="btnToggleTorch" onclick="toggleCameraSwitch()" style="pointer-events: auto; background: rgba(15,23,42,0.85); color: white; border: 1px solid rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 10px; font-size: 0.72rem; cursor: pointer;">
                <i class="fa-solid fa-camera-rotate"></i> Ganti Kamera
            </button>
        </div>
    </div>

    <!-- Input Manual Barcode / NIS -->
    <div style="background: white; border-radius: 14px; padding: 14px 15px; margin-bottom: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.03);">
        <label style="font-size: 0.72rem; font-weight: 800; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">
            <i class="fa-solid fa-keyboard" style="color: #2563eb;"></i> Atau Ketik / Scanner Barcode USB:
        </label>
        <div style="display: flex; gap: 8px;">
            <input type="text" id="manualCode" placeholder="Scan Barcode / NIS / No. Peserta..." autofocus style="flex: 1; padding: 11px 13px; border-radius: 10px; border: 1.5px solid #cbd5e1; font-size: 0.92rem; font-weight: 700; color: #0f172a; outline: none;" onkeydown="if(event.key==='Enter') submitManualCode()">
            <button type="button" onclick="submitManualCode()" style="background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%); color: white; border: none; padding: 10px 18px; border-radius: 10px; font-weight: 800; font-size: 0.85rem; cursor: pointer; white-space: nowrap; box-shadow: 0 4px 10px rgba(124,58,237,0.3);">
                <i class="fa-solid fa-check"></i> Absen
            </button>
        </div>
    </div>

    <!-- Live Result Popup Card -->
    <div id="resultCard" style="display: none; background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; border: 2px solid #86efac; box-shadow: 0 8px 20px rgba(34,197,94,0.15); animation: popIn 0.3s ease;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
            <div id="resIcon" style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div id="resStatusBadge" style="font-size: 0.68rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; display: inline-block; background: #dcfce7; color: #15803d; margin-bottom: 2px;">
                    BERHASIL TERABSEN
                </div>
                <h3 id="resNama" style="margin: 0; font-size: 1.05rem; font-weight: 900; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    Nama Siswa
                </h3>
                <div id="resSub" style="font-size: 0.76rem; color: #64748b; font-weight: 600;">
                    NIS: 12345 • Kelas X E.1
                </div>
            </div>
        </div>
        <div style="background: #f8fafc; border-radius: 10px; padding: 10px; font-size: 0.78rem; border: 1px solid #f1f5f9; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <div>
                <span style="color: #64748b; display: block; font-size: 0.68rem; font-weight: 600;">Mata Pelajaran:</span>
                <b id="resMapel" style="color: #1e293b;">-</b>
            </div>
            <div>
                <span style="color: #64748b; display: block; font-size: 0.68rem; font-weight: 600;">Sesi & Jam:</span>
                <b id="resSesi" style="color: #1e293b;">-</b>
            </div>
        </div>
    </div>

    <!-- Live Log Kehadiran Hari Ini -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 0 4px;">
        <h3 style="margin: 0; font-size: 0.92rem; font-weight: 900; color: #0f172a;">
            <i class="fa-solid fa-list-check" style="color: #7c3aed;"></i> Siswa Baru Terabsen
        </h3>
        <span id="logCount" style="font-size: 0.72rem; color: #7c3aed; font-weight: 800; background: #ede9fe; padding: 2px 8px; border-radius: 20px;">
            Live Feed
        </span>
    </div>

    <div id="logContainer" style="display: flex; flex-direction: column; gap: 8px;">
        <div style="text-align: center; padding: 25px 15px; background: white; border-radius: 14px; border: 1px dashed #cbd5e1; color: #94a3b8; font-size: 0.8rem;">
            <i class="fa-solid fa-barcode" style="font-size: 1.8rem; opacity: 0.3; margin-bottom: 6px; display: block;"></i>
            Arahkan kamera ke barcode/QR pada kartu ujian siswa untuk mulai absensi.
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let html5QrCode = null;
    let isProcessing = false;
    let currentCameraId = null;
    let availableCameras = [];
    let currentCameraIndex = 0;

    // Beep Audio Generator
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(success = true) {
        try {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            
            if (success) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.15);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, audioCtx.currentTime); // A3
                gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                osc.start(audioCtx.currentTime);
                osc.stop(audioCtx.currentTime + 0.3);
            }
        } catch (e) {}
    }

    // Jam Digital Live
    setInterval(() => {
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + 
                        String(now.getMinutes()).padStart(2, '0') + ':' + 
                        String(now.getSeconds()).padStart(2, '0') + ' WIB';
        const el = document.getElementById('liveClock');
        if (el) el.innerText = timeStr;
    }, 1000);

    // Initialize Camera Scanner
    function initScanner() {
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                availableCameras = devices;
                // Pilih kamera belakang jika ada
                let backCam = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('belakang'));
                currentCameraId = backCam ? backCam.id : devices[0].id;
                currentCameraIndex = devices.indexOf(backCam || devices[0]);

                startScanning(currentCameraId);
            } else {
                console.warn("Tidak ada kamera terdeteksi");
            }
        }).catch(err => {
            console.error("Gagal mendapatkan kamera:", err);
        });
    }

    function startScanning(cameraId) {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                runScannerInstance(cameraId);
            }).catch(() => {
                runScannerInstance(cameraId);
            });
        } else {
            runScannerInstance(cameraId);
        }
    }

    function runScannerInstance(cameraId) {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 15,
            qrbox: { width: 250, height: 180 },
            aspectRatio: 1.333334
        };

        html5QrCode.start(
            cameraId,
            config,
            (decodedText) => {
                onScanSuccess(decodedText);
            },
            (errorMessage) => {
                // scanning frame error ignore
            }
        ).catch(err => {
            console.error("Start scanning error:", err);
        });
    }

    function toggleCameraSwitch() {
        if (availableCameras.length > 1) {
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            currentCameraId = availableCameras[currentCameraIndex].id;
            startScanning(currentCameraId);
        } else {
            alert("Hanya 1 kamera yang terdeteksi pada perangkat ini.");
        }
    }

    function onScanSuccess(code) {
        if (isProcessing) return;
        isProcessing = true;
        processCode(code);
    }

    function submitManualCode() {
        const inp = document.getElementById('manualCode');
        const code = inp.value.trim();
        if (!code) {
            inp.focus();
            return;
        }
        if (isProcessing) return;
        isProcessing = true;
        processCode(code);
        inp.value = '';
    }

    function processCode(code) {
        const ruang = document.getElementById('selectRuang').value;

        fetch('/cbt/scan/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                code: code,
                ruang_nama: ruang
            })
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch(e) {
                return { success: false, message: 'Respon server: ' + (text ? text.replace(/<[^>]*>?/gm, '').substring(0, 160) : 'Koneksi terputus') };
            }
        })
        .then(res => {
            if (res.success) {
                if (res.already) {
                    playBeep(false);
                    alert("⚠️ SISWA SUDAH SCAN SEBELUMNYA!\n\n" + (res.siswa ? res.siswa.nama : 'Siswa') + " (" + (res.siswa ? res.siswa.kelas_nama : '-') + ")\nSudah tercatat hadir pada mapel " + (res.mapel_nama || '-') + " (" + (res.jam_slot || '') + ") pukul " + (res.waktu || '') + " WIB.\n\nBelum waktunya ganti jam/sesi ujian!");
                } else {
                    playBeep(true);
                    showResult(true, res);
                    addLogItem(true, res);
                }
            } else {
                playBeep(false);
                showResult(false, res);
            }
        })
        .catch(err => {
            playBeep(false);
            showResult(false, { message: 'Terjadi kesalahan jaringan atau server.' });
        })
        .finally(() => {
            setTimeout(() => {
                isProcessing = false;
            }, 1200); // jeda anti-dobel scan
        });
    }

    function showResult(success, data) {
        const card = document.getElementById('resultCard');
        const icon = document.getElementById('resIcon');
        const badge = document.getElementById('resStatusBadge');
        const nama = document.getElementById('resNama');
        const sub = document.getElementById('resSub');
        const mapel = document.getElementById('resMapel');
        const sesi = document.getElementById('resSesi');

        card.style.display = 'block';

        if (success) {
            card.style.borderColor = '#86efac';
            card.style.boxShadow = '0 8px 20px rgba(34,197,94,0.15)';
            icon.style.background = '#ecfdf5';
            icon.style.color = '#10b981';
            icon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
            badge.style.background = '#dcfce7';
            badge.style.color = '#15803d';
            badge.innerText = data.already ? 'SUDAH TERABSEN SEBELUMNYA' : 'HADIR TERCATAT';

            nama.innerText = data.siswa ? data.siswa.nama : 'Siswa';
            sub.innerText = 'NIS: ' + (data.siswa ? data.siswa.nis : '-') + ' • Kelas ' + (data.siswa ? data.siswa.kelas_nama : '-');
            mapel.innerText = data.mapel_nama || 'Semua Mapel Sesi Ini';
            sesi.innerText = (data.jam_slot || 'Sesi Aktif') + ' (' + (data.waktu || 'WIB') + ')';
        } else {
            card.style.borderColor = '#fca5a5';
            card.style.boxShadow = '0 8px 20px rgba(239,68,68,0.15)';
            icon.style.background = '#fef2f2';
            icon.style.color = '#ef4444';
            icon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
            badge.style.background = '#fee2e2';
            badge.style.color = '#b91c1c';
            badge.innerText = 'ABSENSI GAGAL';

            nama.innerText = 'Gagal Memproses';
            sub.innerText = data.message || 'Data tidak ditemukan';
            mapel.innerText = '-';
            sesi.innerText = '-';
        }
    }

    function addLogItem(success, data) {
        const container = document.getElementById('logContainer');
        if (container.children.length === 1 && container.innerText.includes('Arahkan kamera')) {
            container.innerHTML = '';
        }

        const item = document.createElement('div');
        item.style.cssText = 'background: white; border-radius: 12px; padding: 10px 14px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02); animation: popIn 0.25s ease;';
        
        const now = new Date();
        const timeNow = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

        item.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 800;">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: #1e293b;">${data.siswa ? data.siswa.nama : 'Siswa'}</div>
                    <div style="font-size: 0.72rem; color: #64748b;">${data.siswa ? data.siswa.kelas_nama : ''} • Mapel: <b>${data.mapel_nama || '-'}</b></div>
                </div>
            </div>
            <div style="text-align: right;">
                <span style="background: #ecfdf5; color: #059669; font-weight: 800; font-size: 0.68rem; padding: 2px 7px; border-radius: 12px;">${timeNow} WIB</span>
            </div>
        `;

        container.insertBefore(item, container.firstChild);

        // Batasi 10 item terbaru
        if (container.children.length > 10) {
            container.removeChild(container.lastChild);
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        initScanner();
    });
</script>

<style>
@keyframes popIn {
    0% { transform: scale(0.95); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.3); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}
#qr-reader video {
    border-radius: 14px;
    object-fit: cover !important;
}
</style>

<!-- BOTTOM NAVIGATION FOOTER PENGAWAS CBT -->
<div class="bottom-nav" style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; border-top: 1px solid #e2e8f0; box-shadow: 0 -4px 20px rgba(0,0,0,0.06); display: flex; justify-content: space-around; align-items: center; min-height: 68px; padding-top: 6px; padding-bottom: calc(14px + env(safe-area-inset-bottom)); z-index: 9999; background: #ffffff;">
    <a href="guru_dashboard.php" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b;">
        <i class="fa-solid fa-house" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Beranda</span>
    </a>
    <a href="cbt_scan.php" class="nav-item active" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #4f46e5;">
        <i class="fa-solid fa-qrcode" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Scan Absen</span>
    </a>
    <a href="guru_kelas_jauh.php?tab=nilai" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #64748b;">
        <i class="fa-solid fa-chart-line" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Hasil Nilai</span>
    </a>
    <a href="javascript:void(0)" onclick="confirmLogoutPengawas()" class="nav-item" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: #ef4444;">
        <i class="fa-solid fa-arrow-right-from-bracket" style="font-size: 1.25rem; margin-bottom: 4px;"></i>
        <span style="font-size: 0.72rem; font-weight: 700;">Keluar</span>
    </a>
</div>

<script src="<?= ($baseFolder ? $baseFolder : '') ?>/public/assets/js/sweetalert2.all.min.js"></script>
<script>
function confirmLogoutPengawas() {
    Swal.fire({
        title: 'Keluar Pengawas?',
        text: 'Apakah Bapak/Ibu pengawas yakin ingin logout dari sesi scan CBT ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-arrow-right-from-bracket"></i> Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        background: '#ffffff',
        customClass: {
            popup: 'swal2-border-radius'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}
</script>

</div> <!-- .mobile-wrapper -->
</body>
</html>
