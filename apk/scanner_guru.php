<?php
require_once 'koneksi.php';
if (!isset($_SESSION['guru_id'])) {
    header("Location: login.php");
    exit;
}
try {
    $stmt_scan = $core_db->prepare("SELECT id FROM absen_akses_scanner WHERE guru_id = ? AND akses_guru = 1");
    $stmt_scan->execute([$_SESSION['guru_id']]);
    if (!$stmt_scan->fetch()) {
        header("Location: aplikasi_saya.php");
        exit;
    }
} catch (Exception $e) {}

$settings_suara = [
    'teks_suara_masuk' => 'Halo [nama], Anda berhasil absen masuk.',
    'teks_suara_pulang' => 'Terima kasih [nama], hati-hati di jalan.',
    'teks_suara_sholat' => 'Halo [nama], Anda berhasil absen sholat.',
    'teks_suara_gagal' => 'Maaf [nama], Anda sudah absen sebelumnya.'
];
try {
    $res_suara = $core_db->query("SELECT kunci, nilai FROM absensi_pengaturan_guru WHERE kunci IN ('teks_suara_masuk', 'teks_suara_pulang', 'teks_suara_sholat', 'teks_suara_gagal')")->fetchAll();
    foreach ($res_suara as $s) {
        if (!empty($s['nilai'])) {
            $settings_suara[$s['kunci']] = $s['nilai'];
        }
    }
} catch (Exception $e) {}

$page_title = 'Scanner Guru';
include 'header.php';

// Fetch all guru for manual input
$guruList = [];
try {
    $stmt_guru = $core_db->query("SELECT id, nama, jenis_kelamin FROM guru ORDER BY nama ASC");
    $guruList = $stmt_guru->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}
?>

<div class="header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
    <div class="header-top">
        <div class="greeting">
            <p>Absensi Guru V2</p>
            <h1>Scanner QR</h1>
        </div>
        <div class="avatar" style="color: #0ea5e9; background: white;" onclick="window.location.href='aplikasi_saya.php'">
            <i class="fa-solid fa-xmark"></i>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="data-card" style="margin-top: -2.5rem; z-index: 10; padding: 1rem; padding-bottom: 1.5rem;">
        
        <div style="text-align: center; margin-bottom: 15px;">
            <div id="statusBadge" style="display: inline-flex; align-items: center; gap: 8px; background: #e0f2fe; color: #0284c7; padding: 8px 16px; border-radius: 100px; font-weight: 700; font-size: 0.85rem; border: 1px solid #bae6fd;">
                <i data-lucide="camera" style="width: 16px; height: 16px;"></i> Mempersiapkan Kamera...
            </div>
        </div>

        <!-- Scanner Container -->
        <div style="position: relative; width: 100%; min-height: 300px; border-radius: 16px; overflow: hidden; background: #000; border: 2px solid #e2e8f0; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
            <div id="reader" style="width: 100%; min-height: 300px;"></div>
            <div id="scanFrame" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 2; transition: 0.3s; box-shadow: inset 0 0 0 4px transparent;"></div>
        </div>
        
        <div style="display: flex; gap: 10px; margin-bottom: 0;">
            <button id="flipBtn" class="btn-primary" style="flex: 1; margin-bottom: 0;">
                <i class="fa-solid fa-camera-rotate"></i> Putar Kamera
            </button>
            <button id="audioBtn" class="btn-dark" style="flex: 1; margin-bottom: 0; background: #16a34a; color: white;">
                <i class="fa-solid fa-volume-high"></i> Tes Suara
            </button>
        </div>
    </div>
</div>

<!-- Feedback Popup (SweetAlert) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<style>
    /* Styling adjustments for reader */
    #reader video { border-radius: 14px; object-fit: cover; }
    #reader__dashboard_section_csr span { color: #fff !important; }
</style>

<script>
    lucide.createIcons();

    // Web Audio API Beep Generator (100% offline)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    function playBeep(type = 'success') {
        if(audioCtx.state === 'suspended') audioCtx.resume();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        
        if(type === 'success') {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime); // 800Hz beep
            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.2);
        } else {
            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(300, audioCtx.currentTime); // Lower pitch error
            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
            oscillator.start(audioCtx.currentTime);
            oscillator.stop(audioCtx.currentTime + 0.5);
        }
    }

    const statusBadge = document.getElementById('statusBadge');
    const scanFrame = document.getElementById('scanFrame');
    const flipBtn = document.getElementById('flipBtn');

    let isProcessing = false;
    let html5QrCode = null;
    let currentFacingMode = "environment";

    function showResult(title, desc, isSuccess) {
        scanFrame.style.boxShadow = isSuccess ? 'inset 0 0 0 4px #10b981' : 'inset 0 0 0 4px #ef4444';
        playBeep(isSuccess ? 'success' : 'error');

        Swal.fire({
            html: isSuccess 
                ? `<div style="font-size: 1.5rem; font-weight: 800; margin-bottom: 5px; color: #1e293b;">${title}</div><div style="font-size: 0.95rem; color: #475569;">${desc}</div>` 
                : `<div style="font-size: 1.3rem; font-weight: 800; margin-bottom: 5px; color: #ef4444;">${title}</div><div style="font-size: 0.95rem; color: #ef4444; font-weight: 600;">${desc}</div>`,
            icon: isSuccess ? 'success' : 'error',
            timer: isSuccess ? 2000 : 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top',
            timerProgressBar: true,
            background: isSuccess ? '#ffffff' : '#fef2f2',
            customClass: { popup: 'my-swal-container' }
        }).then(() => {
            scanFrame.style.boxShadow = 'inset 0 0 0 4px transparent';
            isProcessing = false; // Allow next scan
            statusBadge.innerHTML = '<i data-lucide="loader-2" style="width: 16px; height: 16px;"></i> Scanning...';
            lucide.createIcons();
        });
    }

    function handleScanSuccess(decodedText) {
        if(isProcessing) return; // Prevent multiple scans at once
        isProcessing = true;
        
        statusBadge.innerHTML = '<i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i> Sinkronisasi...';
        lucide.createIcons();

        // Send to backend
        fetch('api_scan_guru.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nis: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showResult(data.data.nama, data.data.kelas + ' • ' + data.data.status, true);
                // Text-to-Speech Notif
                if ('speechSynthesis' in window) {
                    let msgLower = data.message.toLowerCase();
                    let isPulang = msgLower.includes('pulang');
                    let isSholat = msgLower.includes('sholat');
                    
                    let templateTeks = "<?php echo addslashes($settings_suara['teks_suara_masuk']); ?>";
                    if(isPulang) templateTeks = "<?php echo addslashes($settings_suara['teks_suara_pulang']); ?>";
                    else if(isSholat) templateTeks = "<?php echo addslashes($settings_suara['teks_suara_sholat']); ?>";
                    
                    let baseNama = data.data.nama.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                    let finalNama = baseNama;
                    if (data.data.jk === 'L' || data.data.jk === 'Laki-Laki' || data.data.jk === 'Laki-laki') {
                        finalNama = 'Bapak ' + baseNama;
                    } else if (data.data.jk === 'P' || data.data.jk === 'Perempuan') {
                        finalNama = 'Ibu ' + baseNama;
                    }
                    let teksFix = templateTeks.replace(/\[nama\]/ig, finalNama);
                    
                    let speechMsg = new SpeechSynthesisUtterance(teksFix);
                    speechMsg.lang = 'id-ID';
                    speechMsg.rate = 1.0;
                    speechMsg.pitch = 1.0;
                    speechMsg.volume = 1.0;
                    window.speechSynthesis.speak(speechMsg);
                }
            } else {
                let errTitle = data.data && data.data.nama ? data.data.nama : "Gagal";
                let errDesc = data.data && data.data.kelas ? data.message + ' (' + data.data.kelas + ')' : data.message;
                showResult(errTitle, errDesc, false);
                
                // Text-to-Speech Notif (Gagal / Sudah Absen)
                if ('speechSynthesis' in window && data.data && data.data.nama) {
                    let templateTeks = "<?php echo addslashes($settings_suara['teks_suara_gagal']); ?>";
                    let baseNama = data.data.nama.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                    let finalNama = baseNama;
                    if (data.data.jk === 'L' || data.data.jk === 'Laki-Laki' || data.data.jk === 'Laki-laki') {
                        finalNama = 'Bapak ' + baseNama;
                    } else if (data.data.jk === 'P' || data.data.jk === 'Perempuan') {
                        finalNama = 'Ibu ' + baseNama;
                    }
                    
                    let teksFix = templateTeks.replace(/\[nama\]/ig, finalNama);
                    let speechMsg = new SpeechSynthesisUtterance(teksFix);
                    speechMsg.lang = 'id-ID';
                    window.speechSynthesis.speak(speechMsg);
                }
            }
        })
        .catch(err => {
            showResult("Error", "Tidak dapat terhubung ke server.", false);
        });
    }

    function initScanner() {
        if(html5QrCode) {
            html5QrCode.stop().then(() => {
                startScanning();
            }).catch(err => {
                startScanning();
            });
        } else {
            html5QrCode = new Html5Qrcode("reader");
            startScanning();
        }
    }

    function startScanning() {
        html5QrCode.start(
            { facingMode: currentFacingMode },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            handleScanSuccess,
            (errorMessage) => { }
        ).then(() => {
            let videoEl = document.querySelector('#reader video');
            if(videoEl) {
                videoEl.style.transform = currentFacingMode === 'user' ? 'scaleX(-1)' : 'scaleX(1)';
            }
            statusBadge.innerHTML = '<i data-lucide="loader-2" style="width: 16px; height: 16px;"></i> Mengawasi QR...';
            statusBadge.style.color = '#0284c7';
            statusBadge.style.background = '#e0f2fe';
            statusBadge.style.borderColor = '#bae6fd';
            lucide.createIcons();
        }).catch((err) => {
            statusBadge.innerHTML = '<i data-lucide="alert-triangle" style="width: 16px; height: 16px;"></i> Kamera Gagal Akses';
            statusBadge.style.color = '#b91c1c';
            statusBadge.style.background = '#fee2e2';
            statusBadge.style.borderColor = '#fecaca';
            lucide.createIcons();
            Swal.fire('Kamera Error', 'Tidak dapat mengakses kamera.', 'error');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('audioBtn').addEventListener('click', function() {
            // Unlock Speech API on Android & Play Test Sound
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let testMsg = new SpeechSynthesisUtterance("Tes suara sistem absensi berhasil.");
                testMsg.lang = 'id-ID';
                window.speechSynthesis.speak(testMsg);
            }
        });

        // Start scanner on load
        initScanner();
    });

    flipBtn.addEventListener('click', () => {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        initScanner();
    });
</script>

<?php include 'footer_guru.php'; ?>
