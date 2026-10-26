<?php
$page_title = "Scanner TPS OSIM";
require_once 'koneksi.php';

// Cek apakah voting sedang dibuka
$stmtStatus = $core_db->query("SELECT setting_value FROM osim_settings WHERE setting_key = 'evoting_status'");
$evoting_status = $stmtStatus->fetchColumn() ?: 'closed';

if ($evoting_status != 'open') {
    echo "<div style='text-align:center; padding: 50px 20px; color: #ef4444; font-family:sans-serif;'>
            <i data-lucide='lock' style='width:64px; height:64px; margin-bottom: 20px;'></i>
            <h2>TPS Sedang Ditutup</h2>
            <p>Silakan hubungi panitia pemilihan OSIM.</p>
          </div>";
    echo "<script src='../public/assets/js/lucide.min.js'></script><script>lucide.createIcons();</script>";
    exit;
}

// Hapus session voting jika sebelumnya ada (mengembalikan ke awal)
unset($_SESSION['evoting_siswa_id']);
unset($_SESSION['evoting_siswa_nama']);

// Ambil tahun ajaran aktif
$stmtTa = $core_db->query("SELECT name FROM tahun_ajaran WHERE is_active = 1 LIMIT 1");
$tahun_ajaran_aktif = $stmtTa->fetchColumn() ?: date('Y');

// Ambil data kandidat
$kandidat = $core_db->query("SELECT * FROM osim_kandidat WHERE status = 'aktif' ORDER BY nomor_urut ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../public/assets/images/favicon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../public/assets/css/plus-jakarta-sans.css">
    <script src="../public/assets/js/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
    body { background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%); color: #0f172a; font-family: 'Inter', sans-serif; margin: 0; padding: 0; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }
    .tps-header { background: transparent; padding: 25px 20px 15px; text-align: center; position: relative; z-index: 20; }
    .header-content { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; margin-top: 10px; }
    .logo-large { height: 75px; width: auto; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); margin-bottom: 5px; }
    .gradient-text { margin: 0; font-size: clamp(1.5rem, 6vw, 2.2rem); font-weight: 900; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: 1px; white-space: nowrap; }
    .header-content h2 { margin: 0; font-size: clamp(0.9rem, 3.5vw, 1.1rem); font-weight: 800; color: #334155; letter-spacing: 2px; white-space: nowrap; }
    .academic-year { font-size: 0.85rem; font-weight: 700; color: #475569; background: #e2e8f0; padding: 4px 12px; border-radius: 20px; margin-top: 5px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05); }
    
    .scanner-container { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; padding: 10px 20px 20px 20px; position: relative; z-index: 1; overflow-y: auto; }
    
    #reader { width: 100%; max-width: 400px; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px -10px rgba(59, 130, 246, 0.25), 0 10px 20px -5px rgba(0, 0, 0, 0.1); border: 4px solid #ffffff; position: relative; z-index: 5; background: #ffffff; }
    #reader video { object-fit: cover; border-radius: 18px; width: 100%; }
    
    .camera-controls { margin-top: 25px; margin-bottom: 20px; display: flex; gap: 15px; justify-content: center; z-index: 20; }
    
    .btn-gradient { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); transition: all 0.3s; font-family: 'Inter', sans-serif; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4); }
    .btn-gradient:active { transform: translateY(0); }

    .tps-footer { text-align: center; padding: 15px; color: #64748b; font-size: 0.9rem; z-index: 20; background: transparent; }
    
    .action-btn {
        background: rgba(255, 255, 255, 0.9); color: #334155;
        border: 1px solid #e2e8f0;
        padding: 6px 12px; border-radius: 8px;
        cursor: pointer; font-weight: 600; font-size: 0.8rem;
        display: flex; align-items: center; gap: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .action-btn:hover { background: #f8fafc; transform: translateY(-1px); border-color: #cbd5e1; }
    
    /* UI Voting */
    .voting-container { flex: 1; padding: 20px; max-width: 1000px; margin: 0 auto; width: 100%; box-sizing: border-box; display: flex; flex-direction: column; overflow-y: auto; }
    .welcome-badge { background: rgba(255, 255, 255, 0.9); padding: 12px 20px; border-radius: 16px; text-align: center; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6; }
    .welcome-badge h2 { margin: 0; color: #1e293b; font-size: clamp(1rem, 4vw, 1.2rem); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .welcome-badge p { margin: 5px 0 0 0; color: #64748b; font-size: 0.85rem; }
    
    /* Horizontal Scroll Layout untuk Kandidat */
    .kandidat-grid { display: flex; gap: 20px; overflow-x: auto; scroll-snap-type: x mandatory; padding: 10px 5px 20px 5px; }
    .kandidat-grid::-webkit-scrollbar { height: 8px; }
    .kandidat-grid::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .kandidat-grid::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
    
    .kandidat-card { flex: 0 0 auto; width: clamp(260px, 80vw, 300px); scroll-snap-align: center; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); transition: transform 0.3s, box-shadow 0.3s; position: relative; border: 2px solid transparent; display: flex; flex-direction: column; }
    .kandidat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-color: #bfdbfe; }
    .nomor-urut { position: absolute; top: 10px; left: 10px; background: #3b82f6; color: white; font-size: 1.2rem; font-weight: 900; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); border: 3px solid #fff; z-index: 10; }
    .kandidat-foto { width: 100%; height: clamp(160px, 35vh, 280px); background: #f8fafc; object-fit: contain; border-bottom: 1px solid #f1f5f9; padding-top: 10px; }
    .kandidat-info { padding: 12px 15px; text-align: center; flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .kandidat-nama { font-size: 1.1rem; font-weight: 900; color: #0f172a; margin-bottom: 3px; }
    .kandidat-wakil { font-size: 0.9rem; color: #64748b; font-weight: 700; }
    .kandidat-btn {
        background: linear-gradient(135deg, #10b981 0%, #34d399 50%, #059669 100%);
        background-size: 200% 200%;
        animation: shimmer 3s ease infinite;
        color: white; border: none; width: calc(100% - 20px); margin: 0 auto 10px auto;
        padding: 14px; font-size: 1rem; font-weight: 800; cursor: pointer;
        transition: all 0.3s; text-transform: uppercase; letter-spacing: 1.5px;
        border-radius: 14px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .kandidat-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5); }
    .kandidat-btn:active { transform: translateY(0) scale(0.97); }
    @keyframes shimmer { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .btn-detail { background: none; border: none; color: #3b82f6; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin-top: 8px; text-decoration: underline; }
    
    /* Modal Visi Misi */
    .modal-overlay { position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); z-index: 999; display: none; align-items: center; justify-content: center; padding: 20px; }
    .modal-content { background: #fff; border-radius: 24px; width: 100%; max-width: 500px; max-height: 80vh; overflow-y: auto; position: relative; text-align: left; }
    .modal-close { position: absolute; top: 15px; right: 15px; background: #e2e8f0; border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #0f172a; z-index: 10; font-size: 1.5rem; font-weight: bold; line-height: 1; transition: background 0.2s; }
    .modal-close:hover { background: #cbd5e1; }
    </style>
</head>
<body>

<div class="tps-header" id="tps-header-section">
    <div style="position: absolute; top: 15px; right: 20px; display: flex; gap: 8px; z-index: 50;">
        <button class="action-btn" onclick="testVoice()" title="Tes Suara AI">
            <i data-lucide="volume-2" style="width: 18px; height: 18px; margin-bottom: -3px;"></i>
        </button>
        <button class="action-btn" onclick="toggleFullScreen()" title="Fullscreen">
            <i data-lucide="maximize" style="width: 18px; height: 18px; margin-bottom: -3px;"></i>
        </button>
    </div>
    
    <div class="header-content">
        <img src="../public/assets/images/logo.png" alt="Logo" class="logo-large">
        <h1 class="gradient-text">TPS E-VOTING OSIM</h1>
        <h2>MA MIFTAHUL HUDA</h2>
        <div class="academic-year">Tahun Pelajaran <?= htmlspecialchars($tahun_ajaran_aktif) ?></div>
    </div>
</div>

<div id="scanner-section" class="scanner-container">
    <div id="reader"></div>
    <div class="camera-controls">
        <button class="btn-gradient" onclick="flipCamera()">
            <i data-lucide="switch-camera" style="width: 18px; height: 18px;"></i> Balik Kamera
        </button>
    </div>
</div>

<div id="voting-section" style="display:none;" class="voting-container">
    <div class="welcome-badge">
        <h2 id="welcome-text" style="margin: 0; padding: 0;">
            <div style="font-size: 0.95rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Selamat Datang,</div>
            <div id="welcome-name" style="color: #1e293b; font-size: clamp(1.1rem, 5vw, 1.4rem); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 5px; font-weight: 900;">Nama Pemilih</div>
        </h2>
        <p>Silakan pilih salah satu kandidat di bawah ini.</p>
    </div>

    <div class="kandidat-grid" id="kandidatGridContainer">
        <?php foreach($kandidat as $k): ?>
        <div class="kandidat-card">
            <div class="nomor-urut"><?= $k['nomor_urut'] ?></div>
            <?php if($k['foto']): ?>
                <img src="../public/uploads/osim/<?= $k['foto'] ?>" class="kandidat-foto" alt="Paslon <?= $k['nomor_urut'] ?>">
            <?php else: ?>
                <div class="kandidat-foto" style="display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="users" style="width: 80px; height: 80px; color: #cbd5e1;"></i>
                </div>
            <?php endif; ?>
            <div class="kandidat-info">
                <div class="kandidat-nama"><?= htmlspecialchars($k['nama_ketua']) ?></div>
                <div class="kandidat-wakil">& <?= htmlspecialchars($k['nama_wakil']) ?></div>
                <button type="button" class="btn-detail" onclick='lihatVisiMisi(<?= json_encode($k['visi']) ?>, <?= json_encode($k['misi']) ?>, <?= $k['nomor_urut'] ?>)'>
                    Lihat Visi & Misi
                </button>
            </div>
            <button type="button" class="kandidat-btn" onclick="pilihKandidat(<?= $k['id'] ?>, <?= $k['nomor_urut'] ?>)">
                <i data-lucide="check-circle" style="vertical-align: -5px; margin-right: 5px; width:18px; height:18px;"></i> COBLOS
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="tps-footer">
    TPS E-Voting OSIM &copy; <?= htmlspecialchars($tahun_ajaran_aktif) ?> MAS MIFHDA
</div>

<!-- Modal Visi Misi -->
<div class="modal-overlay" id="visimisiModal">
    <div class="modal-content">
        <button class="modal-close" onclick="document.getElementById('visimisiModal').style.display='none'">&times;</button>
        <div style="padding: 25px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-top-left-radius: 24px; border-top-right-radius: 24px;">
            <h3 style="margin:0; color:#1e293b;" id="vmTitle">Visi & Misi</h3>
        </div>
        <div style="padding: 25px;">
            <div style="font-weight: 900; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 10px;">Visi</div>
            <div style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;" id="vmVisi"></div>
            
            <div style="font-weight: 900; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 10px;">Misi</div>
            <div style="color: #475569; font-size: 0.95rem; line-height: 1.6;" id="vmMisi"></div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    let isProcessing = false;
    const html5QrCode = new Html5Qrcode("reader");
    let currentFacingMode = "user"; // Gunakan kamera depan secara default

    function startScanner() {
        // Konfigurasi scanner
        const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
        
        html5QrCode.start(
            { facingMode: currentFacingMode },
            config,
            onScanSuccess
        ).then(() => {
            // Mirroring kamera depan jika diperlukan
            setTimeout(() => {
                const videoElement = document.querySelector("#reader video");
                if (videoElement) {
                    if (currentFacingMode === "user") {
                        videoElement.style.transform = "scaleX(-1)";
                    } else {
                        videoElement.style.transform = "scaleX(1)";
                    }
                }
            }, 100);
        }).catch(err => {
            console.error("Gagal memulai scanner", err);
            // Ignore error silent or just log, html5-qrcode might trigger catch if camera denied
            if(err && typeof err === 'string' && err.includes("NotAllowedError")) {
                Swal.fire('Error', 'Izin kamera ditolak. Silakan izinkan akses kamera di browser Anda.', 'error');
            }
        });
    }

    function flipCamera() {
        if (html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
                startScanner();
            }).catch(err => {
                console.error("Gagal stop scanner", err);
            });
        } else {
            currentFacingMode = currentFacingMode === "user" ? "environment" : "user";
            startScanner();
        }
    }

    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log(`Error attempting to enable fullscreen: ${err.message}`);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    // Fungsi untuk membersihkan teks agar AI tidak mengeja (spelling) huruf kapital
    function cleanTextForSpeech(text) {
        let cleaned = String(text).toLowerCase();
        cleaned = cleaned.replace(/\bosim\b/g, 'osim');
        cleaned = cleaned.replace(/\bm\./g, 'muhammad ');
        cleaned = cleaned.replace(/\bma\b/g, 'em a');
        return cleaned;
    }

    // Fungsi Suara AI
    function speak(text, onEndCallback = null) {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
            let textToSpeak = cleanTextForSpeech(text);
            const utterance = new SpeechSynthesisUtterance(textToSpeak);
            utterance.lang = 'id-ID'; // Bahasa Indonesia
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            
            if (onEndCallback) {
                let callbackFired = false;
                const fireCallback = () => {
                    if (!callbackFired) {
                        callbackFired = true;
                        onEndCallback();
                    }
                };
                utterance.onend = fireCallback;
                utterance.onerror = fireCallback;
                
                // Fallback timeout in case onend fails to fire
                setTimeout(fireCallback, 5000);
            }
            
            window.speechSynthesis.speak(utterance);
        } else {
            if (onEndCallback) {
                setTimeout(onEndCallback, 1500);
            }
        }
    }

    function testVoice() {
        speak("Ini adalah tes suara sistem validasi pemilih.");
        Swal.fire({
            icon: 'info',
            title: 'Tes Suara AI',
            text: 'Suara sistem sedang dimainkan...',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;
        
        // Pause scanner visual
        if (html5QrCode.pause) {
            html5QrCode.pause();
        }
        
        Swal.fire({
            title: 'Memproses...',
            text: 'Mencari data pemilih',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('api_tps.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nis: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let panggilan = data.panggilan ? data.panggilan : '';
                let fullName = panggilan + data.nama;
                
                // Ubah nama di halaman voting
                document.getElementById('welcome-name').innerText = fullName;
                
                // Switch view ke mode e-voting
                document.getElementById('scanner-section').style.display = 'none';
                document.getElementById('tps-header-section').style.display = 'none';
                document.getElementById('voting-section').style.display = 'flex';
                
                // Reset scroll posisi kandidat saat halaman voting dimunculkan
                // Harus dilakukan SAAT element sudah 'flex' agar browser meresponnya
                let grid = document.getElementById('kandidatGridContainer');
                if (grid) grid.scrollLeft = 0;
                
                // Hentikan kamera sementara (karena library bisa throw error jika distop saat tidak scan, kita catch)
                if (html5QrCode.isScanning) {
                    html5QrCode.stop().catch(e => console.log(e));
                }
                
                // Tutup swal loading
                Swal.close();
                
                // Mainkan suara tanpa callback redirect
                speak("Verifikasi berhasil. Selamat datang, " + fullName);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Verifikasi Berhasil',
                    text: 'Selamat datang, ' + fullName,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                let errorTitle = 'Akses Ditolak';
                let isSudahMemilih = data.message.toLowerCase().includes('sudah menggunakan hak suaranya');
                
                if (isSudahMemilih) {
                    errorTitle = 'Peringatan!';
                    speak(data.message);
                } else {
                    speak("Maaf, akses ditolak. " + data.message);
                }
                
                Swal.fire({
                    icon: isSudahMemilih ? 'warning' : 'error',
                    title: errorTitle,
                    text: data.message,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    isProcessing = false;
                    if (html5QrCode.resume) html5QrCode.resume();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan jaringan'
            }).then(() => {
                isProcessing = false;
                if (html5QrCode.resume) html5QrCode.resume();
            });
        });
    }

    // Mulai kamera secara otomatis
    document.addEventListener("DOMContentLoaded", () => {
        startScanner();
    });

    // Fungsi tambahan untuk UI E-Voting
    function lihatVisiMisi(visi, misi, nomor) {
        document.getElementById('vmTitle').innerText = 'Visi & Misi Paslon ' + nomor;
        
        // Render Visi
        document.getElementById('vmVisi').innerHTML = visi.replace(/\n/g, '<br>');
        
        // Render Misi (dengan Numbering)
        let misiLines = misi.split('\n').filter(line => line.trim() !== '');
        if (misiLines.length > 1) {
            let listHtml = '<ol style="padding-left: 20px; margin: 0;">';
            misiLines.forEach(line => {
                listHtml += '<li style="margin-bottom: 8px;">' + line.trim() + '</li>';
            });
            listHtml += '</ol>';
            document.getElementById('vmMisi').innerHTML = listHtml;
        } else {
            document.getElementById('vmMisi').innerHTML = misi.replace(/\n/g, '<br>');
        }
        
        document.getElementById('visimisiModal').style.display = 'flex';
    }

    function resetToScanner() {
        document.getElementById('voting-section').style.display = 'none';
        document.getElementById('scanner-section').style.display = 'block';
        document.getElementById('tps-header-section').style.display = 'block';
        
        isProcessing = false;
        startScanner();
    }

    function pilihKandidat(id, nomor) {
        Swal.fire({
            title: 'Konfirmasi Pilihan',
            html: `Apakah Anda yakin ingin memilih <b>Paslon Nomor ${nomor}</b>?<br><br><small style="color:#ef4444;">Pilihan yang sudah disimpan tidak dapat diubah!</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: 'Ya, Coblos!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan Suara...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const formData = new FormData();
                formData.append('kandidat_id', id);

                fetch('api_tps.php?action=vote', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        speak("Terima kasih, suara Anda telah berhasil disimpan di Kotak Suara.");
                        Swal.fire({
                            icon: 'success',
                            title: 'Terima Kasih!',
                            text: 'Suara Anda telah berhasil disimpan di Kotak Suara.',
                            timer: 2500,
                            showConfirmButton: false
                        }).then(() => {
                            resetToScanner();
                        });
                    } else {
                        speak("Gagal. " + data.message);
                        Swal.fire('Gagal', data.message, 'error').then(() => {
                            if (data.message.includes('sudah memilih') || data.message.includes('Sesi habis')) {
                                resetToScanner();
                            }
                        });
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
                });
            }
        });
    }
</script>

</body>
</html>
