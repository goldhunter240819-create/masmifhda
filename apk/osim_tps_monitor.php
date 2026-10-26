<?php
$page_title = "Live Monitor TPS OSIM";
require_once 'koneksi.php';

$total_suara = $core_db->query("SELECT COUNT(*) FROM osim_suara")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../public/assets/images/favicon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../public/assets/css/plus-jakarta-sans.css">
    <script src="../public/assets/js/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            margin: 0; padding: 0;
            color: #fff;
            height: 100vh;
            display: flex; flex-direction: column;
            overflow: hidden;
            background-image: radial-gradient(ellipse at top, #1e3a5f 0%, #0f172a 60%);
        }
        
        /* Animated Background Particles */
        body::before {
            content: '';
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, rgba(56,189,248,0.15), transparent),
                radial-gradient(2px 2px at 40px 70px, rgba(139,92,246,0.1), transparent),
                radial-gradient(2px 2px at 50px 160px, rgba(56,189,248,0.1), transparent),
                radial-gradient(2px 2px at 90px 40px, rgba(16,185,129,0.15), transparent),
                radial-gradient(2px 2px at 130px 80px, rgba(56,189,248,0.1), transparent),
                radial-gradient(2px 2px at 160px 120px, rgba(139,92,246,0.1), transparent);
            background-size: 200px 200px;
            animation: bgFloat 20s linear infinite;
            pointer-events: none; z-index: 0;
        }
        @keyframes bgFloat { from { transform: translateY(0); } to { transform: translateY(-200px); } }

        .header {
            padding: 25px 30px 15px;
            text-align: center;
            z-index: 10;
        }
        .header h1 { margin: 0; font-size: clamp(1.8rem, 5vw, 3rem); font-weight: 900; background: linear-gradient(135deg, #38bdf8, #818cf8, #38bdf8); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-transform: uppercase; letter-spacing: 5px; animation: textShine 4s linear infinite; }
        @keyframes textShine { to { background-position: 200% center; } }
        .header p { margin: 8px 0 0 0; color: #94a3b8; font-size: 1rem; text-transform: uppercase; letter-spacing: 2px; }

        .monitor-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
            z-index: 5;
        }

        /* ========== KOTAK SUARA ========== */
        .kotak-suara {
            position: relative;
            width: 360px; height: 440px; /* Jauh lebih besar */
            background: linear-gradient(145deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6), inset 0 2px 8px rgba(255,255,255,0.6), 0 0 80px rgba(56,189,248,0.1);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding-top: 30px;
            z-index: 5;
            border: 4px solid #94a3b8;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .kotak-suara.opening {
            animation: shakeBox 0.8s ease-in-out;
        }
        @keyframes shakeBox {
            0%, 100% { transform: translateX(0) rotate(0); }
            10% { transform: translateX(-8px) rotate(-2deg); }
            20% { transform: translateX(8px) rotate(2deg); }
            30% { transform: translateX(-12px) rotate(-3deg); }
            40% { transform: translateX(12px) rotate(3deg); }
            50% { transform: translateX(-15px) rotate(-4deg); }
            60% { transform: translateX(15px) rotate(4deg); }
            70% { transform: translateX(-10px) rotate(-2deg); }
            80% { transform: translateX(10px) rotate(2deg); }
            90% { transform: translateX(-5px) rotate(-1deg); }
        }
        .kotak-suara.exploded {
            transform: scale(0);
            opacity: 0;
        }
        
        .kotak-lubang {
            position: absolute;
            top: 35px;
            width: 160px; height: 18px;
            background: #0f172a;
            border-radius: 10px;
            box-shadow: inset 0 5px 10px rgba(0,0,0,0.8);
        }

        .box-label {
            margin-top: 15px; 
            font-weight: 900; 
            color: #475569; 
            letter-spacing: 3px; 
            font-size: 1.1rem;
        }

        /* Digital Counter di dalam kotak */
        .box-counter-display {
            background: #0f172a;
            border-radius: 12px;
            padding: 20px 30px;
            margin-top: 25px;
            border: 3px solid #334155;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.9), 0 5px 15px rgba(0,0,0,0.2);
            text-align: center;
            width: 85%;
        }
        .stats-number {
            font-size: 6.5rem;
            font-weight: 900;
            color: #38bdf8;
            line-height: 1.1;
            font-family: 'Arial Black', Impact, sans-serif;
            text-shadow: 0 0 20px rgba(56, 189, 248, 1), 0 0 40px rgba(56, 189, 248, 0.5);
            -webkit-text-stroke: 1px #fff;
            letter-spacing: -2px;
        }
        .stats-label {
            color: #94a3b8;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 12px;
            font-weight: 900;
        }

        .ballot {
            position: absolute;
            top: -150px;
            width: 130px; height: 170px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: none;
            z-index: 4;
            border: 2px solid #e2e8f0;
        }
        .ballot-inner { padding: 18px; }
        .ballot-line { height: 9px; background: #cbd5e1; margin-bottom: 13px; border-radius: 5px; }
        
        @keyframes insertBallot {
            0% { top: -200px; transform: scale(1); opacity: 0; }
            20% { top: -100px; transform: scale(1); opacity: 1; }
            50% { top: -50px; transform: scale(0.9); opacity: 1; }
            80% { top: 20px; transform: scale(0.8); opacity: 1; height: 170px; }
            100% { top: 60px; transform: scale(0.7); opacity: 0; height: 0px; }
        }

        /* ========== STATS ========== */
        /* Dihapus karena counter dipindah ke dalam kotak */

        /* ========== BUKA KOTAK BUTTON ========== */
        .btn-buka-kotak {
            margin-top: 25px;
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 50%, #f59e0b 100%);
            background-size: 200% auto;
            animation: btnShine 3s ease infinite;
            color: white; border: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 900;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 8px 30px rgba(239, 68, 68, 0.4);
            transition: all 0.3s;
            display: flex; align-items: center; gap: 10px;
            z-index: 10;
            font-family: 'Inter', sans-serif;
        }
        @keyframes btnShine { 0%,100% { background-position: 0% center; } 50% { background-position: 100% center; } }
        .btn-buka-kotak:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 15px 40px rgba(239, 68, 68, 0.5); }
        .btn-buka-kotak:active { transform: translateY(0) scale(0.98); }

        .latest-voter {
            position: fixed;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-size: 1.3rem;
            font-weight: 800;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            opacity: 0;
            transition: opacity 0.5s, transform 0.5s;
            text-align: center;
            white-space: nowrap;
            z-index: 50;
        }
        .latest-voter.show { opacity: 1; transform: translate(-50%, -20px); }
        
        #btnInitAudio {
            position: fixed;
            top: 20px; right: 20px;
            background: rgba(239, 68, 68, 0.9); color: white;
            border: none; padding: 10px 20px;
            border-radius: 50px; cursor: pointer;
            font-weight: 700; z-index: 100;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            display: flex; align-items: center; gap: 8px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        #btnInitAudio:hover { transform: scale(1.05); }
        #btnInitAudio.ready {
            background: rgba(16, 185, 129, 0.9);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        /* ========== HASIL / RESULTS OVERLAY ========== */
        .hasil-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: #0f172a;
            z-index: 200;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .hasil-overlay.active { display: flex; }

        .hasil-header {
            text-align: center;
            padding: 30px 20px 15px;
        }
        .hasil-header h1 {
            margin: 0; font-size: clamp(1.5rem, 4vw, 2.5rem); font-weight: 900;
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #ef4444);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            text-transform: uppercase; letter-spacing: 3px;
        }
        .hasil-header p { color: #94a3b8; margin: 5px 0 0; font-size: 0.9rem; letter-spacing: 1px; }

        .hasil-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 20px;
            flex-wrap: wrap;
        }

        /* ========== CANDIDATE RESULT CARD ========== */
        .hasil-card {
            width: 300px;
            text-align: center;
            opacity: 0;
            transform: translateY(80px) scale(0.5);
            transition: all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .hasil-card.revealed {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .hasil-card.winner {
            transform: translateY(-10px) scale(1.05);
        }
        .hasil-card.winner .hasil-foto-wrapper {
            box-shadow: 0 0 0 5px #fbbf24, 0 0 60px rgba(251, 191, 36, 0.4);
        }
        .hasil-card.winner .winner-crown {
            display: block;
        }

        .hasil-foto-wrapper {
            width: 200px; height: 200px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 5px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            position: relative;
            background: #1e293b;
        }
        .hasil-foto-wrapper img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .winner-crown {
            display: none;
            position: absolute;
            top: -35px; left: 50%; transform: translateX(-50%);
            font-size: 3rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
            z-index: 10;
        }

        .hasil-nomor {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white; font-weight: 900; font-size: 1rem;
            padding: 5px 18px; border-radius: 20px;
            margin-bottom: 10px;
        }
        .hasil-nama { font-size: 1.2rem; font-weight: 900; color: #f1f5f9; margin: 5px 0; }
        .hasil-wakil { font-size: 0.9rem; color: #94a3b8; font-weight: 600; }

        /* ========== SLOT MACHINE COUNTER ========== */
        .slot-counter-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 4px;
        }
        .slot-digit-box {
            width: 45px; height: 65px;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            border: 2px solid rgba(56, 189, 248, 0.3);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.1), inset 0 0 10px rgba(0,0,0,0.5);
        }
        .slot-digit-strip {
            position: absolute;
            top: 0; left: 0; width: 100%;
            transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .slot-digit-num {
            width: 45px; height: 65px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem; font-weight: 900;
            color: #38bdf8;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
        }
        .slot-digit-box::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(15,23,42,0.6) 0%, transparent 30%, transparent 70%, rgba(15,23,42,0.6) 100%);
            pointer-events: none; z-index: 2;
        }

        .hasil-suara-label {
            font-size: 0.8rem; color: #64748b; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
            margin-top: 10px;
        }

        .btn-kembali {
            position: fixed; top: 20px; right: 20px;
            background: rgba(255,255,255,0.1); color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.15);
            padding: 10px 20px; border-radius: 50px;
            cursor: pointer; font-weight: 700; z-index: 210;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .btn-kembali:hover { background: rgba(255,255,255,0.2); color: #fff; }

        /* ========== EXPLOSION PARTICLES ========== */
        @keyframes particleFly {
            0% { transform: translate(0,0) scale(1); opacity: 1; }
            100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
        }
        .particle {
            position: fixed;
            width: 10px; height: 10px;
            border-radius: 50%;
            z-index: 199;
            animation: particleFly 1.2s ease-out forwards;
        }
    </style>
</head>
<body>

<!-- ========== STARTUP OVERLAY ========== -->
<div id="startupOverlay" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.95); z-index:9999; display:flex; flex-direction:column; align-items:center; justify-content:center; color:white;">
    <i data-lucide="monitor" style="width: 80px; height: 80px; color: #38bdf8; margin-bottom: 20px;"></i>
    <h2 style="font-size: 2.5rem; margin: 0; font-weight: 900; letter-spacing: 2px;">MONITOR TPS VIRTUAL</h2>
    <p style="color: #94a3b8; font-size: 1.2rem; margin-top: 10px;">Klik tombol di bawah untuk memulai sesi dan mengaktifkan audio otomatis.</p>
    <button onclick="startMonitorSesi()" style="margin-top:40px; padding:20px 50px; font-size:1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); border:none; border-radius:15px; color:white; cursor:pointer; font-weight:bold; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5);">
        Mulai Monitor
    </button>
</div>

<!-- ========== HALAMAN UTAMA (KOTAK SUARA) ========== -->
<div id="mainPage">
    <div class="header">
        <h1 id="monitorTitle" style="cursor: pointer; user-select: none;">KOTAK SUARA VIRTUAL</h1>
        <p>Pemilihan Ketua & Wakil Ketua OSIM</p>
    </div>

    <div class="monitor-content">
        <div class="ballot" id="ballotEl">
            <div class="ballot-inner">
                <div class="ballot-line"></div>
                <div class="ballot-line" style="width: 70%;"></div>
                <div class="ballot-line" style="width: 90%;"></div>
                <div style="text-align: center; margin-top: 25px;">
                    <i data-lucide="check-circle" style="color: #10b981; width: 36px; height: 36px;"></i>
                </div>
            </div>
        </div>

        <div class="kotak-suara" id="kotakSuara">
            <div class="kotak-lubang"></div>
            
            <i data-lucide="box" style="width: 45px; height: 45px; color: #64748b; margin-top: 25px;"></i>
            <div class="box-label">KOTAK SUARA</div>
            
            <div class="box-counter-display">
                <div class="stats-number" id="counterTotal"><?= number_format($total_suara) ?></div>
                <div class="stats-label">SUARA MASUK</div>
            </div>
        </div>

        <button class="btn-buka-kotak" id="btnBukaKotak" onclick="bukaKotakSuara()">
            <i data-lucide="lock-open" style="width: 22px; height: 22px;"></i>
            BUKA KOTAK SUARA
        </button>
        
        <div class="latest-voter" id="voterNameLabel">
            Terima Kasih, <span id="voterName">Siswa</span>
        </div>
    </div>
</div>

<!-- ========== HALAMAN HASIL (AFTER BUKA KOTAK) ========== -->
<div class="hasil-overlay" id="hasilOverlay">
    <button class="btn-kembali" onclick="kembaliKeMonitor()">
        <i data-lucide="arrow-left" style="width:16px; height:16px; vertical-align: -3px; margin-right: 5px;"></i> Kembali
    </button>
    <div class="hasil-header">
        <h1>🗳️ HASIL PEROLEHAN SUARA</h1>
        <p>Pemilihan Ketua & Wakil Ketua OSIM</p>
    </div>
    <div class="hasil-container" id="hasilContainer">
        <!-- Cards will be injected here via JS -->
    </div>
</div>

<script>
    lucide.createIcons();

    let audioInitialized = false;
    let synth = window.speechSynthesis;
    let voices = [];

    function populateVoiceList() { voices = synth.getVoices(); }
    populateVoiceList();
    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = populateVoiceList;
    }

    // Fungsi untuk membersihkan teks agar AI tidak mengeja huruf kapital
    function cleanTextForSpeech(text) {
        let cleaned = String(text).toLowerCase();
        cleaned = cleaned.replace(/\bosim\b/g, 'osim');
        cleaned = cleaned.replace(/\bm\./g, 'muhammad ');
        cleaned = cleaned.replace(/\bma\b/g, 'em a');
        return cleaned;
    }

    function speakText(text) {
        if (!audioInitialized) return;
        // synth.cancel(); // Dihapus agar suara AI mengantri (tidak terpotong)
        let textToSpeak = cleanTextForSpeech(text);
        let utterance = new SpeechSynthesisUtterance(textToSpeak);
        let idVoice = voices.find(v => v.lang === 'id-ID' || v.name.includes('Indonesia'));
        if (idVoice) utterance.voice = idVoice;
        utterance.rate = 0.9;
        synth.speak(utterance);
    }

    function startMonitorSesi() {
        if (!audioInitialized) {
            let utterance = new SpeechSynthesisUtterance("Sistem Suara Siap");
            let idVoice = voices.find(v => v.lang === 'id-ID' || v.name.includes('Indonesia'));
            if (idVoice) utterance.voice = idVoice;
            utterance.volume = 0; // Play silently just to unlock audio context
            synth.speak(utterance);
            audioInitialized = true;
        }
        document.getElementById('startupOverlay').style.display = 'none';
        lucide.createIcons();
    }

    function playAnimationAndSound(namaSiswa, newTotal) {
        confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });

        let ballot = document.getElementById('ballotEl');
        ballot.style.display = 'block';
        ballot.style.animation = 'none';
        void ballot.offsetWidth;
        ballot.style.animation = 'insertBallot 2s ease-in forwards';

        let label = document.getElementById('voterNameLabel');
        document.getElementById('voterName').innerText = namaSiswa;
        label.classList.add('show');
        setTimeout(() => { label.classList.remove('show'); }, 4000);

        setTimeout(() => {
            ballot.style.display = 'none';
            document.getElementById('counterTotal').innerText = newTotal;
        }, 2000);

        speakText("Terima kasih, " + namaSiswa + " telah menggunakan hak pilih.");
    }

    // Polling
    let isKotakLocked = true;
    setInterval(() => {
        fetch('api_tps.php?action=monitor')
        .then(r => r.json())
        .then(data => {
            // Update UI tombol Buka Kotak
            if (data.is_locked !== undefined) {
                const btn = document.getElementById('btnBukaKotak');
                if (data.is_locked) {
                    isKotakLocked = true;
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.innerHTML = '<i data-lucide="lock" style="width: 22px; height: 22px;"></i> KOTAK TERKUNCI';
                } else {
                    isKotakLocked = false;
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.innerHTML = '<i data-lucide="lock-open" style="width: 22px; height: 22px;"></i> BUKA KOTAK SUARA';
                }
                lucide.createIcons();
            }

            if (data.success && data.new_vote) {
                playAnimationAndSound(data.nama, data.total_suara);
            }
        })
        .catch(err => console.error("Monitor error:", err));
    }, 3000);

    // Secret Admin Toggle (Klik judul 3 kali cepat)
    let clickCount = 0;
    let clickTimer = null;
    document.getElementById('monitorTitle').addEventListener('click', () => {
        clickCount++;
        clearTimeout(clickTimer);
        if (clickCount >= 3) {
            clickCount = 0;
            toggleKotakLock();
        } else {
            clickTimer = setTimeout(() => { clickCount = 0; }, 500);
        }
    });

    function toggleKotakLock() {
        const pin = prompt("Masukkan PIN Admin untuk mengatur Kunci Kotak Suara:", "");
        if (pin === "1234") { // PIN dummy, bisa diganti
            fetch('api_tps.php?action=toggle_lock')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert(data.is_locked ? "Kotak Suara Berhasil DIKUNCI" : "Kotak Suara Berhasil DIBUKA");
                }
            });
        } else if (pin !== null) {
            alert("PIN Salah!");
        }
    }

    // ========== BUKA KOTAK SUARA ==========
    function createExplosionParticles() {
        const colors = ['#38bdf8', '#818cf8', '#f59e0b', '#ef4444', '#10b981', '#fbbf24', '#e879f9'];
        const box = document.getElementById('kotakSuara');
        const rect = box.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;

        for (let i = 0; i < 60; i++) {
            const p = document.createElement('div');
            p.classList.add('particle');
            p.style.left = cx + 'px';
            p.style.top = cy + 'px';
            p.style.background = colors[Math.floor(Math.random() * colors.length)];
            p.style.width = (Math.random() * 12 + 5) + 'px';
            p.style.height = p.style.width;
            p.style.setProperty('--tx', (Math.random() - 0.5) * 600 + 'px');
            p.style.setProperty('--ty', (Math.random() - 0.5) * 600 + 'px');
            document.body.appendChild(p);
            setTimeout(() => p.remove(), 1500);
        }
    }

    function bukaKotakSuara() {
        if (isKotakLocked) {
            alert("Kotak masih dikunci oleh Admin!");
            return;
        }

        const btn = document.getElementById('btnBukaKotak');
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader" style="width:22px;height:22px;"></i> Membuka...';
        lucide.createIcons();

        speakText("Perhatian. Kotak suara akan segera dibuka.");

        const kotak = document.getElementById('kotakSuara');
        
        // Phase 1: Shake (1.5s)
        setTimeout(() => {
            kotak.classList.add('opening');
        }, 1500);

        // Phase 2: Explosion + Confetti (2.5s)
        setTimeout(() => {
            kotak.classList.remove('opening');
            kotak.classList.add('exploded');
            createExplosionParticles();

            // Big multi-burst confetti
            const dur = 2000;
            const end = Date.now() + dur;
            (function frame() {
                confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#38bdf8','#818cf8','#fbbf24'] });
                confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#ef4444','#10b981','#f59e0b'] });
                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        }, 2800);

        // Phase 3: Load results and show overlay (4s)
        setTimeout(() => {
            fetch('api_tps.php?action=buka_kotak')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showHasilSuara(data.kandidat, data.total_suara);
                }
            });
        }, 4000);
    }

    function showHasilSuara(kandidat, totalSuara) {
        const overlay = document.getElementById('hasilOverlay');
        const container = document.getElementById('hasilContainer');
        container.innerHTML = '';

        // Find winner
        let maxVote = 0;
        kandidat.forEach(k => { if (parseInt(k.perolehan) > maxVote) maxVote = parseInt(k.perolehan); });

        kandidat.forEach((k, index) => {
            const isWinner = parseInt(k.perolehan) === maxVote && maxVote > 0;
            const digits = String(k.perolehan).padStart(3, '0').split('');

            let fotoHtml = '';
            if (k.foto) {
                fotoHtml = `<img src="../public/uploads/osim/${k.foto}" alt="Paslon ${k.nomor_urut}">`;
            } else {
                fotoHtml = `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1e293b;"><i data-lucide="users" style="width:60px;height:60px;color:#475569;"></i></div>`;
            }

            const cardHtml = `
                <div class="hasil-card" id="hasilCard${index}" data-winner="${isWinner}">
                    <div style="position:relative; display:inline-block;">
                        <div class="winner-crown">👑</div>
                        <div class="hasil-foto-wrapper">${fotoHtml}</div>
                    </div>
                    <div class="hasil-nomor">Paslon ${k.nomor_urut}</div>
                    <div class="hasil-nama">${k.nama_ketua}</div>
                    <div class="hasil-wakil">& ${k.nama_wakil}</div>
                    <div class="slot-counter-wrapper" id="slotWrapper${index}">
                        ${digits.map((d, i) => `
                            <div class="slot-digit-box">
                                <div class="slot-digit-strip" id="slot_${index}_${i}">
                                    ${[0,1,2,3,4,5,6,7,8,9].map(n => `<div class="slot-digit-num">${n}</div>`).join('')}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="hasil-suara-label">SUARA</div>
                </div>
            `;
            container.innerHTML += cardHtml;
        });

        overlay.classList.add('active');
        lucide.createIcons();

        // Animate cards appearing one by one
        let totalCandidates = kandidat.length;
        
        kandidat.forEach((k, index) => {
            setTimeout(() => {
                const card = document.getElementById('hasilCard' + index);
                card.classList.add('revealed');
                
                // Announce via AI
                speakText(`Paslon nomor ${k.nomor_urut}, ${k.nama_ketua}`);

                // Trigger slot animation for this card (digits spin from last to first)
                const digits = String(k.perolehan).padStart(3, '0').split('');
                const totalDigits = digits.length;

                digits.forEach((d, dIndex) => {
                    const digitEl = document.getElementById(`slot_${index}_${dIndex}`);
                    const targetNum = parseInt(d);
                    
                    // Animate from last digit first: delay is reversed
                    const reverseDelay = (totalDigits - 1 - dIndex) * 400;
                    const spinDelay = 800 + reverseDelay;

                    // First: rapid spin
                    let spinCount = 0;
                    const spinInterval = setInterval(() => {
                        const randNum = Math.floor(Math.random() * 10);
                        digitEl.style.transition = 'none';
                        digitEl.style.transform = `translateY(-${randNum * 65}px)`;
                        spinCount++;
                    }, 50);

                    // Then: land on final number
                    setTimeout(() => {
                        clearInterval(spinInterval);
                        digitEl.style.transition = 'transform 0.6s cubic-bezier(0.23, 1, 0.32, 1)';
                        digitEl.style.transform = `translateY(-${targetNum * 65}px)`;
                    }, spinDelay);
                });
            }, index * 4000 + 500);
        });

        // Highlight Winner AFTER all candidates are revealed
        setTimeout(() => {
            kandidat.forEach((k, index) => {
                const card = document.getElementById('hasilCard' + index);
                if (card.dataset.winner === 'true') {
                    card.classList.add('winner');
                    // Massive confetti for winner
                    confetti({ particleCount: 250, spread: 120, origin: { y: 0.5 }, colors: ['#fbbf24', '#f59e0b', '#ef4444', '#10b981', '#3b82f6'] });
                    speakText(`Selamat! Paslon nomor ${k.nomor_urut}, ${k.nama_ketua} meraih suara terbanyak dengan ${k.perolehan} suara!`);
                }
            });
        }, totalCandidates * 4000 + 1000);
    }

    function kembaliKeMonitor() {
        document.getElementById('hasilOverlay').classList.remove('active');
        const kotak = document.getElementById('kotakSuara');
        kotak.classList.remove('exploded');
        const btn = document.getElementById('btnBukaKotak');
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="lock-open" style="width:22px;height:22px;"></i> BUKA KOTAK SUARA';
        lucide.createIcons();
    }
</script>
</body>
</html>
