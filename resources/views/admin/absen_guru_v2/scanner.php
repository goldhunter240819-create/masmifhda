<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo isset($title) ? $title : 'Scanner Absensi V2'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/plus-jakarta-sans.css'); ?>">
    <script src="<?php echo base_url('public/assets/js/lucide.min.js'); ?>"></script>
    <script src="<?php echo base_url('public/assets/js/html5-qrcode.min.js'); ?>"></script>
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0f172a; color: white; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
        
        /* HEADER */
        .scanner-header {
            background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px);
            padding: 15px 20px; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.05); z-index: 10;
        }
        .header-title { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.1rem; }
        .header-title i { color: #0ea5e9; }
        .btn-close {
            background: rgba(255,255,255,0.1); border: none; color: white;
            width: 36px; height: 36px; border-radius: 10px; display: flex;
            align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;
        }
        .btn-close:hover { background: #ef4444; }

        /* SCANNER AREA */
        .scanner-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; padding: 20px; }
        .scanner-container { width: 100%; max-width: 500px; position: relative; }
        
        #reader { width: 100%; border-radius: 20px; overflow: hidden; background: #000; box-shadow: 0 25px 50px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.1); }
        
        /* Decorative frame */
        .scan-frame {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: 2;
        }
        .scan-frame::before {
            content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 250px; height: 250px; border: 2px solid rgba(14, 165, 233, 0.5); border-radius: 20px;
            box-shadow: 0 0 0 4000px rgba(15, 23, 42, 0.85); /* Darken everything outside the box */
            transition: all 0.3s;
        }
        .scan-frame.success::before { border-color: #0ea5e9; background: rgba(16,185,129,0.2); }
        .scan-frame.error::before { border-color: #ef4444; background: rgba(239,68,68,0.2); }

        .scan-line {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -125px);
            width: 250px; height: 3px; background: #0ea5e9; box-shadow: 0 0 10px #0ea5e9;
            animation: scan 2s infinite linear; z-index: 3; display: none;
        }
        .scanning .scan-line { display: block; }

        @keyframes scan {
            0% { transform: translate(-50%, -120px); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translate(-50%, 120px); opacity: 0; }
        }

        /* STATUS BADGE */
        .status-badge {
            position: absolute; top: -50px; left: 50%; transform: translateX(-50%);
            background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);
            padding: 8px 16px; border-radius: 100px; font-size: 0.8rem; font-weight: 700;
            display: flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
        }
        .status-badge.wait { color: #94a3b8; }
        .status-badge.scanning { color: #38bdf8; border-color: rgba(56, 189, 248, 0.3); }
        .status-badge.scanning i { animation: spin 2s infinite linear; }
        
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* RESULT POPUP */
        .result-popup {
            position: fixed; bottom: 30px; left: 50%; transform: translate(-50%, 100px);
            background: white; color: #0f172a; padding: 20px; border-radius: 20px;
            width: 90%; max-width: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            display: flex; align-items: center; gap: 15px; opacity: 0; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 20;
        }
        .result-popup.show { transform: translate(-50%, 0); opacity: 1; }
        
        .result-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .result-icon.success { background: #dcfce7; color: #15803d; }
        .result-icon.error { background: #fee2e2; color: #b91c1c; }
        
        .result-info { flex: 1; min-width: 0; }
        .result-title { font-weight: 800; font-size: 1.1rem; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .result-desc { font-size: 0.8rem; color: #64748b; }

        /* Camera Selection */
        .controls { position: absolute; bottom: 20px; left: 0; right: 0; display: flex; justify-content: center; gap: 10px; z-index: 10; }
        .cam-btn { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer; backdrop-filter: blur(5px); display: flex; align-items: center; gap: 8px; transition: 0.2s; }
        .cam-btn:hover { background: rgba(255,255,255,0.2); }
        .cam-btn.active { background: #0ea5e9; border-color: #0ea5e9; }

    </style>
</head>
<body>

    <div class="scanner-header">
        <div class="header-title">
            <i data-lucide="scan-line"></i>
            Aula System Scanner V2
        </div>
        <button class="btn-close" onclick="window.close() || (window.location.href="<?php echo base_url('admin/absen_guru_v2'); ?>")">
            <i data-lucide="x"></i>
        </button>
    </div>

    <div class="scanner-wrapper" id="wrapper">
        <div class="scanner-container">
            
            <div class="status-badge wait" id="statusBadge">
                <i data-lucide="camera"></i> Mempersiapkan Kamera...
            </div>

            <div id="reader"></div>
            
            <div class="scan-frame" id="scanFrame">
                <div class="scan-line"></div>
            </div>

        </div>
        
        <div class="controls">
            <button class="cam-btn" id="flipBtn"><i data-lucide="refresh-cw"></i> Putar Kamera</button>
        </div>
    </div>

    <!-- Feedback Popup -->
    <div class="result-popup" id="resultPopup">
        <div class="result-icon success" id="resultIcon">
            <i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>
        </div>
        <div class="result-info">
            <div class="result-title" id="resultTitle">Nama Guru</div>
            <div class="result-desc" id="resultDesc">Berhasil dicatat hadir.</div>
        </div>
    </div>

    <!-- Audio Beep (Base64 so it works offline easily) -->
    <audio id="beepSound" preload="auto">
        <source src="data:audio/mp3;base64,//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq" type="audio/mpeg">
    </audio>
    <!-- Fallback if above dummy base64 beep doesn't work, we generate one with Web Audio API -->

    <script>
        lucide.createIcons();

        // Web Audio API Beep Generator (100% offline, no assets needed)
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

        // DOM Elements
        const statusBadge = document.getElementById('statusBadge');
        const scanFrame = document.getElementById('scanFrame');
        const resultPopup = document.getElementById('resultPopup');
        const resultIcon = document.getElementById('resultIcon');
        const resultTitle = document.getElementById('resultTitle');
        const resultDesc = document.getElementById('resultDesc');
        const flipBtn = document.getElementById('flipBtn');

        let isProcessing = false;
        let html5QrCode = null;
        let currentFacingMode = "environment";

        function showResult(title, desc, isSuccess) {
            resultTitle.innerText = title;
            resultDesc.innerText = desc;
            
            resultIcon.className = 'result-icon ' + (isSuccess ? 'success' : 'error');
            resultIcon.innerHTML = isSuccess ? '<i data-lucide="check-circle" style="width:28px;height:28px;"></i>' : '<i data-lucide="x-circle" style="width:28px;height:28px;"></i>';
            scanFrame.className = 'scan-frame ' + (isSuccess ? 'success' : 'error');
            
            lucide.createIcons();
            resultPopup.classList.add('show');
            
            playBeep(isSuccess ? 'success' : 'error');

            setTimeout(() => {
                resultPopup.classList.remove('show');
                scanFrame.className = 'scan-frame';
                isProcessing = false; // Allow next scan
                statusBadge.innerHTML = '<i data-lucide="loader-2"></i> Scanning...';
                lucide.createIcons();
            }, 2500); // Wait 2.5s before next scan
        }

        function handleScanSuccess(decodedText) {
            if(isProcessing) return; // Prevent multiple scans at once
            isProcessing = true;
            
            statusBadge.innerHTML = '<i data-lucide="upload-cloud"></i> Sinkronisasi...';
            lucide.createIcons();

            // Send to backend
            fetch('/api/absen_guru_v2/scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nis: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    showResult(data.data.nama, data.data.kelas + ' â€¢ ' + data.data.status, true);
                } else {
                    showResult("Gagal", data.message, false);
                }
            })
            .catch(err => {
                showResult("Error Jaringan", "Tidak dapat terhubung ke server.", false);
            });
        }

        function initScanner() {
            if(html5QrCode) {
                html5QrCode.stop().then(() => {
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
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                handleScanSuccess,
                (errorMessage) => {
                    // ignore continuous errors
                }
            ).then(() => {
                document.getElementById('wrapper').classList.add('scanning');
                statusBadge.className = 'status-badge scanning';
                statusBadge.innerHTML = '<i data-lucide="loader-2"></i> Mengawasi QR...';
                lucide.createIcons();
            }).catch((err) => {
                statusBadge.className = 'status-badge wait';
                statusBadge.innerHTML = '<i data-lucide="alert-triangle"></i> Kamera Gagal Akses';
                lucide.createIcons();
                alert("Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.");
            });
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initScanner();
        });

        // Flip Camera
        flipBtn.addEventListener('click', () => {
            currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
            initScanner();
        });

    </script>
</body>
</html>


