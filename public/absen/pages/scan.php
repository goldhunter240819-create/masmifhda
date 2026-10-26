<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Ambil Riwayat Kehadiran Hari Ini Secara Real-time
$tgl_hari_ini = date('Y-m-d');
$query_riwayat = "SELECT a.*, u.nama, u.kelas_mapel FROM absensi a 
                  JOIN users u ON a.user_id = u.id 
                  WHERE DATE(a.tanggal) = '$tgl_hari_ini'
                  ORDER BY a.id DESC LIMIT 50";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pemindai - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/html5-qrcode.min.js"></script>
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <style>
        #reader { width: 100%; max-height: 480px; border: none !important; border-radius: var(--radius-lg); overflow: hidden; background: #000; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        #reader video { max-height: 480px !important; object-fit: cover !important; border-radius: var(--radius-lg); -webkit-transform: scaleX(-1); transform: scaleX(-1); }
        .fullscreen-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(6, 78, 59, 0.95); backdrop-filter: blur(10px);
            z-index: 9999; display: flex; flex-direction: column;
            justify-content: center; align-items: center; color: white;
            display: none;
        }
        .fs-name { font-size: 5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 20px; text-transform: uppercase; text-align: center;}
        .fs-msg { font-size: 2rem; font-weight: 400; opacity: 0.8; text-align: center;}
        
        /* Modifikasi Layout Fullscreen Kiosk */
        :fullscreen #scanner-wrapper { 
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; 
            z-index: 1000; border-radius: 0 !important; background: #000; padding: 0 !important; margin: 0;
            display: flex; flex-direction: column; justify-content: center;
        }
        :fullscreen #reader { border-radius: 0 !important; height: 100vh !important; }
        :fullscreen #reader video { height: 100% !important; object-fit: cover !important; border-radius: 0 !important; }
        :fullscreen .fs-hide-inner { display: none !important; }
    </style>
</head>
<body>

<div class="fullscreen-overlay" id="fs-overlay">
    <div class="fs-name" id="fs-name">NAMA</div>
    <div class="fs-msg" id="fs-msg">Status Pesan</div>
</div>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
            <header class="content-header fs-hide-inner">
                <div class="h-title">Pos Keamanan Pemindai</div>
                <button onclick="toggleFullScreen()" class="btn-pro btn-pro-primary py-1 px-3">
                    <i class="bi bi-arrows-fullscreen"></i> Mode Kios
                </button>
            </header>

            <div class="content-padding">

            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card-pro h-100 p-0 overflow-hidden" id="scanner-wrapper" style="background:#064e3b;">
                        <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center fs-hide-inner">
                            <span class="small fw-bold"><i class="bi bi-camera-video me-2"></i>Kamera Live</span>
                            <span class="badge bg-success">Online Server</span>
                        </div>
                        <div id="reader"></div>
                        <div class="p-3 text-center fs-hide-inner" style="background:#064e3b; color:#f0f9ff; border-top: 1px dashed rgba(255,255,255,0.1);">
                            <div class="spinner-grow spinner-grow-sm text-success me-2 align-middle" role="status"></div>
                            <span class="small fw-bold align-middle" style="letter-spacing: 1px;">ARAHKAN KARTU QR KE LENSA KAMERA</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 fs-hide-inner">
                    <div class="card-pro h-100 border-start-0 border-end-0 border-bottom-0 border-top border-4 border-primary bg-white">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Log Aktivitas Sesi Ini</h6>
                        <div id="debug-info" class="mb-2 p-2 bg-light small font-monospace" style="display:none; max-height: 100px; overflow: auto;"></div>
                        <ul id="scanLog" class="list-unstyled mb-0 small" style="max-height: 250px; overflow-y: auto;">
                            <!-- JS logs go here -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TABEL RIWAYAT TERINTEGRASI -->
            <div class="card-pro fs-hide-inner p-0 overflow-hidden shadow-sm">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-primary mb-1"><i class="bi bi-clock-history me-2"></i>LOG RIWAYAT HARI INI</h6>
                        <p class="text-muted-pro small mb-0">Terakhir Dimuat: <?php echo date('d M Y - H:i'); ?> | Menampilkan rekaman real-time ke bawah.</p>
                    </div>
                    <div>
                        <!-- Optional: Print button if they want to print today's log straight from scanner -->
                        <button onclick="window.print()" class="btn-pro btn-pro-outline text-dark border p-2 bg-light">
                            <i class="bi bi-printer"></i>
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive-pro">
                    <table class="table-pro mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Bidang / Kelas</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th class="text-center pe-4" style="width: 120px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="tableLogBody">
                            <?php 
                            $no = 1; 
                            if(mysqli_num_rows($result_riwayat) > 0) {
                                while($row = mysqli_fetch_assoc($result_riwayat)): 
                            ?>
                            <tr>
                                <td class="ps-4 text-secondary num-col"><?php echo $no++; ?></td>
                                <td class="fw-bold text-dark"><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['kelas_mapel'] ? $row['kelas_mapel'] : '-'; ?></td>
                                <td>
                                    <span class="text-primary font-monospace fw-bold">
                                        <i class="bi bi-box-arrow-in-right me-1 text-success"></i> <?php echo $row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '--:--'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-primary font-monospace fw-bold">
                                        <i class="bi bi-box-arrow-right me-1 text-danger"></i> <?php echo ($row['jam_keluar'] && $row['jam_keluar'] != '00:00:00') ? date('H:i', strtotime($row['jam_keluar'])) : '--:--'; ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if ($row['status'] == 'Tidak Ada Jam'): ?>
                                        <span class="badge-pro bg-soft-secondary text-secondary py-1 px-3">TIDAK ADA JAM</span>
                                    <?php else: ?>
                                        <span class="badge-pro bg-soft-green py-1 px-3">HADIR</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            } else { 
                            ?>
                                <tr id="emptyRow">
                                    <td colspan="6" class="py-5 text-center text-muted-pro">Belum ada aktivitas terekam.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <footer class="footer-pro fs-hide-inner mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Attendance Pro &copy; <?php echo date('Y'); ?></span>
                    <span>Modul Kios Scanner AI</span>
                </div>
            </footer>
        </div>

        <footer class="footer-pro">
            Attendance Engine &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<audio id="beep" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<script>
    let isProcessing = false;
    let lastScanCode = "";

    // Deteksi Keamanan Konteks (Cek apakah diakses via IP Tanpa HTTPS)
    if (!window.isSecureContext && window.location.hostname !== 'localhost') {
        Swal.fire({
            title: 'Kamera Terkunci (Mode IP)',
            html: `Browser memblokir kamera karena Anda mengakses via IP tanpa HTTPS.<br><br>
                   <b>Cara Mengaktifkan:</b><br>
                   1. Buka <code>chrome://flags/#unsafely-treat-insecure-origin-as-secure</code><br>
                   2. Masukkan <code>${window.location.origin}</code> ke kotak teks.<br>
                   3. Ubah status ke <b>Enabled</b> lalu restart Chrome.`,
            icon: 'warning',
            confirmButtonText: 'Saya Mengerti',
            confirmButtonColor: '#0284c7'
        });
    }
    
    const fsOverlay = document.getElementById('fs-overlay');
    const fsName = document.getElementById('fs-name');
    const fsMsg = document.getElementById('fs-msg');

    function speak(text) {
        if ('speechSynthesis' in window) {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 1.0; 
            utterance.pitch = 1.0; 
            
            let voices = window.speechSynthesis.getVoices();
            let chosenVoice = null;
            
            // Prioritas 1: Suara Cewek Asli (Edge punya Mba Gadis, atau voice bernama female)
            for(let i=0; i<voices.length; i++) {
                let n = voices[i].name.toLowerCase();
                let l = voices[i].lang.toLowerCase();
                if(l.includes('id') && (n.includes('gadis') || n.includes('female'))) {
                    chosenVoice = voices[i]; break;
                }
            }
            
            // Prioritas 2 (Jika tidak ada Gadis/Female): Google Bahasa Indonesia (Di Chrome pasti default cewek)
            if(!chosenVoice) {
                for(let i=0; i<voices.length; i++) {
                    if(voices[i].name === 'Google Bahasa Indonesia') {
                        chosenVoice = voices[i]; break;
                    }
                }
            }
            
            // Fallback: Suara ID apapun
            if(!chosenVoice) chosenVoice = voices.find(v => v.lang.includes('id'));
            
            if(chosenVoice) utterance.voice = chosenVoice;
            window.speechSynthesis.speak(utterance);
        }
    }

    function addLog(code, name, msg, type = 'success') {
        const logList = document.getElementById('scanLog');
        const d = new Date();
        const timeStr = d.getHours().toString().padStart(2, '0') + ":" + d.getMinutes().toString().padStart(2, '0');
        
        let icon = type === 'success' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
        
        const newLog = `
            <li class="mb-2 pb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">${name}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">${msg}</div>
                    </div>
                    <div class="text-end">
                        <div class="font-monospace text-primary fw-bold" style="font-size: 0.8rem;">${timeStr}</div>
                        ${icon}
                    </div>
                </div>
            </li>
        `;
        logList.insertAdjacentHTML('afterbegin', newLog);
    }

    function addLogToTable(code, name, statusStr, infoTime, statusAbsen = 'Hadir') {
        const tbody = document.getElementById('tableLogBody');
        const emptyRow = document.getElementById('emptyRow');
        if(emptyRow) emptyRow.remove();
        
        let inTime = '--:--';
        let outTime = '--:--';
        let kls = '-';
        
        // Coba parser waktu saat ini utk UI palsu real time (karena database sdh update, refresh browser dpt data utuh)
        const d = new Date();
        const timeStr = d.getHours().toString().padStart(2, '0') + ":" + d.getMinutes().toString().padStart(2, '0');
        
        if(statusStr.includes('Masuk') && !statusStr.includes('DITOLAK')) inTime = timeStr;
        if(statusStr.includes('Pulang') && !statusStr.includes('DITOLAK')) outTime = timeStr;

        let badgeClass = statusAbsen === 'Tidak Ada Jam' ? 'bg-soft-secondary text-secondary' : 'bg-soft-green';
        let badgeText = statusAbsen === 'Tidak Ada Jam' ? 'TIDAK ADA JAM' : 'HADIR';
        
        if (statusStr.includes('DITOLAK')) {
            badgeClass = 'bg-soft-amber text-danger';
            badgeText = 'DITOLAK';
        }

        const newRow = `
            <tr style="background:#fefce8; transition: background 2s;">
                <td class="ps-4 text-secondary num-col">Baru</td>
                <td class="fw-bold text-dark">${name}</td>
                <td>${kls}</td>
                <td>
                    <span class="text-primary font-monospace fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1 text-success"></i> ${inTime}
                    </span>
                </td>
                <td>
                    <span class="text-primary font-monospace fw-bold">
                        <i class="bi bi-box-arrow-right me-1 text-danger"></i> ${outTime}
                    </span>
                </td>
                <td class="text-center pe-4">
                    <span class="badge-pro ${badgeClass} py-1 px-3">
                        ${badgeText}
                    </span>
                </td>
            </tr>
        `;
        
        tbody.insertAdjacentHTML('afterbegin', newRow);
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing || decodedText === lastScanCode) return;
        
        const debug = document.getElementById('debug-info');
        debug.style.display = 'block';
        debug.innerHTML = "Scanned: " + decodedText.substring(0,20) + "...<br>" + debug.innerHTML;

        lastScanCode = decodedText;
        isProcessing = true;
        
        document.getElementById('beep').play().catch(e => console.log('Auto-play prevented'));

        const formData = new FormData();
        formData.append('qr_code', decodedText);

        fetch('../actions/absensi_proses.php', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            debug.innerHTML = "Server Responded<br>" + debug.innerHTML;
            return res.json();
        })
        .then(data => {
            debug.innerHTML = "Status: " + data.status + "<br>" + debug.innerHTML;
            const nama = data.nama || 'Sistem Terkunci';
            
                const pick = arr => arr[Math.floor(Math.random() * arr.length)];
                
                // 1. Penanganan Error Waktu
                if (data.status === 'error_time') {
                    let msg = "";
                    if(data.message.includes("ditutup")) {
                        msg = pick([
                            `Wah sayang banget, sistem presensinya udah kututup kak.`,
                            `Yah, udah kepalang sore, absennya udah kumatikan. Terlambat euy!`
                        ]);
                    } else {
                        msg = pick([
                            `Dih, aneh banget, ini kan belum jamnya absen loh kak.`,
                            `Waduh, jam segini kok nempelin kartu. Cek jadwal lagi deh kak.`,
                            `Eitss, belum waktunya untuk absen. Sabar yaa, tunggu jam mulainya.`
                        ]);
                    }
                    if (document.fullscreenElement) {
                        fsName.innerText = "AKSES DITOLAK";
                        fsMsg.innerText = msg;
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'warning', title: 'Akses Ditolak', text: msg, timer: 3000, showConfirmButton: false });
                    speak(msg); 
                    addLogToTable(decodedText, 'DITOLAK', 'DITOLAK (DILUAR JAM)', '');
                    addLog(decodedText, 'DITOLAK', msg, 'error');
                }
                // 2. Sukses Masuk
                else if (data.status === 'success_masuk') {
                    const formalMsg = pick([
                        `Haloo ${nama}, selamat datang. Semangat ya ngebantai kerjaannya hari ini!`,
                        `Pagi ${nama}! Jangan lupa ngopi dulu biar fokus kerjanya.`,
                        `Presensi masuk mantap. Ciee ${nama} rajin banget dateng pagi ini!`,
                        `Selamat datang ${nama}. Senyum dulu dong biar harinya cerah!`,
                        `Data masuk aman. Halo ${nama}, sukses terus ya untuk aktivitas hari ini!`
                    ]);
                    if (document.fullscreenElement) {
                        fsName.innerText = nama;
                        fsMsg.innerText = "Presensi Masuk Berhasil.";
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'success', title: 'Berhasil Masuk', text: formalMsg, timer: 3000, showConfirmButton: false });
                    speak(formalMsg);
                    addLogToTable(decodedText, nama, 'Masuk Sukses', '', data.status_absen);
                    addLog(decodedText, nama, 'Masuk Sukses');
                }
                // 3. Info Sudah Masuk
                else if (data.status === 'info_masuk') {
                    const infoMsg = pick([
                        `Loh loh, ${nama} kan udah absen tadi, masa mau absen masuk dua kali?`,
                        `Eh ${nama}, daritadi datamu tuh udah keabsen tau. Enggak usah nge-scan lagi.`,
                        `Santai kawan, absensi masuk kamu udah aman terkendali kok ${nama}.`
                    ]);
                    if (document.fullscreenElement) {
                        fsName.innerText = nama;
                        fsMsg.innerText = "Sudah Melakukan Presensi Masuk.";
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'info', title: 'Informasi', text: infoMsg, timer: 2500, showConfirmButton: false });
                    speak(infoMsg);
                    addLog(decodedText, nama, 'Sudah Absen Masuk');
                }
                // 4. Sukses Pulang
                else if (data.status === 'success_keluar') {
                    const formalMsg = pick([
                        `Asik akhirnya pulang juga. Hati-hati bawa kendaraannya ya ${nama}.`,
                        `Alhamdulillah beres juga kerja hari ini. Selamat beristirahat ${nama}!`,
                        `Kerjaan beres, mantap! Langsung balik ke rumah ya ${nama}, jangan nongkrong dulu.`,
                        `Waktunya pulang yeeey! Sampai jumpa besok lagi ya manis, eh, ${nama}.`
                    ]);
                    if (document.fullscreenElement) {
                        fsName.innerText = nama;
                        fsMsg.innerText = "Presensi Pulang Berhasil.";
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'success', title: 'Berhasil Pulang', text: formalMsg, timer: 3000, showConfirmButton: false });
                    speak(formalMsg);
                    addLogToTable(decodedText, nama, 'Pulang Sukses', '', data.status_absen);
                    addLog(decodedText, nama, 'Pulang Sukses');
                }
                // 5. Info Sudah Pulang
                else if (data.status === 'info_keluar') {
                    const infoMsg = pick([
                        `Hei ${nama}, kamu tuh kan udah absen pulang barusan. Yakali mau dobel.`,
                        `Data kepulanganmu udah aku simpen kok ${nama}. Buruan gih berangkat pulang.`
                    ]);
                    if (document.fullscreenElement) {
                        fsName.innerText = nama;
                        fsMsg.innerText = "Sudah Melakukan Presensi Pulang.";
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'info', title: 'Informasi', text: infoMsg, timer: 2500, showConfirmButton: false });
                    speak(infoMsg);
                    addLog(decodedText, nama, 'Sudah Absen Pulang');
                }
                else {
                    const msgServer = data.message || "Identitas tidak dikenali oleh sistem.";
                    if (document.fullscreenElement) {
                        fsName.innerText = "GAGAL";
                        fsMsg.innerText = msgServer;
                        fsOverlay.style.display = 'flex';
                        setTimeout(() => fsOverlay.style.display = 'none', 3000);
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal', text: msgServer });
                    speak(msgServer);
                    addLog(decodedText, 'DITOLAK', msgServer, 'error');
                }
                
                setTimeout(() => { isProcessing = false; lastScanCode = ""; }, 4000);
            }).catch(e => {
                debug.innerHTML = "Error: " + e.message + "<br>" + debug.innerHTML;
                setTimeout(() => { isProcessing = false; lastScanCode = ""; }, 4000);
            });
        }

        // Low Level Implementation for clean UI
        const html5QrCode = new Html5Qrcode("reader");
        
        function startScanner() {
            html5QrCode.start(
                { facingMode: "environment" }, 
                { fps: 15, qrbox: { width: 250, height: 250 }, aspectRatio: 1.777778 },
                onScanSuccess,
                (errorMessage) => { /* Background scan loops */ }
            ).catch((err) => {
                console.error("Gagal memulai scanner:", err);
                if(err.includes("Permission") || err.includes("NotAllowedError")) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Izin Kamera Ditolak',
                        text: 'Pastikan Anda memberikan izin kamera di browser untuk melakukan scan.'
                    });
                }
            });
        }

        // Jalankan scanner jika konteks aman
        if (window.isSecureContext || window.location.hostname === 'localhost') {
            startScanner();
        } else {
            // Jika tidak aman, beri tombol manual sebagai alternatif agar UI tidak kosong
            document.getElementById('reader').innerHTML = `
                <div class="p-5 text-center">
                    <i class="bi bi-camera-video-off display-4 text-secondary mb-3"></i>
                    <p class="small text-muted">Akses Kamera diblokir karena protokol tidak aman (HTTP). Silakan gunakan HTTPS atau localhost.</p>
                    <button onclick="startScanner()" class="btn-pro btn-pro-outline shadow-sm">Coba Paksa Nyalakan Kamera</button>
                </div>
            `;
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch((err) => {
                    alert(`Error: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
    </script>
    
<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

