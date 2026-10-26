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

// Ambil data semua user yang SUDAH memiliki deskripsi wajah
$res_descriptors = mysqli_query($koneksi, "SELECT id, nama, face_descriptor FROM users WHERE face_descriptor IS NOT NULL");
$labeled_descriptors = [];
while($row = mysqli_fetch_assoc($res_descriptors)) {
    $labeled_descriptors[] = [
        'id' => $row['id'],
        'nama' => $row['nama'],
        'descriptor' => json_decode($row['face_descriptor'])
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Wajah AI Super - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <script src="/public/absen/assets/js/face-api.js"></script>
    <style>
        .scanner-container {
            position: relative;
            width: 100%;
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #000;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            aspect-ratio: 16/9;
        }
        #video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        #canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: scaleX(-1); }
        
        .ai-hud {
            position: absolute; top: 20px; left: 20px;
            background: rgba(0,0,0,0.7); color: #fff; padding: 10px 20px;
            border-radius: 12px; backdrop-filter: blur(15px);
            font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;
            border-left: 4px solid #3b82f6;
            z-index: 5; box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .fullscreen-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px);
            z-index: 9999; display: flex; flex-direction: column;
            justify-content: center; align-items: center; color: white;
            display: none;
        }
        .fs-name { font-size: 6rem; font-weight: 900; letter-spacing: -3px; margin-bottom: 10px; text-transform: uppercase; text-shadow: 0 10px 30px rgba(59, 130, 246, 0.5);}
        .fs-msg { font-size: 2.5rem; font-weight: 300; opacity: 0.9;}

        :fullscreen #scanner-wrapper { 
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; 
            z-index: 1000; border-radius: 0 !important; background: #000;
        }
        :fullscreen .scanner-container { border-radius: 0 !important; height: 100vh !important; }
        :fullscreen .fs-hide-inner { display: none !important; }

        .shutter-effect {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; opacity: 0; pointer-events: none; z-index: 100;
        }

        /* Overlay Frame untuk membantu memposisikan wajah */
        .overlay-guide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            pointer-events: none; z-index: 4;
        }
        .guide-box {
            width: 250px; height: 320px;
            border: 4px solid #ef4444; /* Merah saat mencari */
            border-radius: 40px;
            box-shadow: 0 0 0 4000px rgba(15, 23, 42, 0.4);
            transition: all 0.3s ease;
        }
        .guide-box.active { 
            border-color: #0ea5e9; /* Hijau saat terdeteksi */
            box-shadow: 0 0 50px rgba(14, 165, 233, 0.5), 0 0 0 4000px rgba(15, 23, 42, 0.2); 
        }
    </style>
</head>
<body class="bg-light">

<div class="shutter-effect" id="shutter"></div>

<div class="fullscreen-overlay" id="fs-overlay">
    <div class="fs-name" id="fs-name">VERIFIED</div>
    <div class="fs-msg" id="fs-msg">ACCESS GRANTED</div>
</div>

<div class="container-fluid p-0">
    <div class="row g-0">
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="content-header fs-hide-inner mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="spinner-grow text-primary spinner-grow-sm" role="status"></div>
                        <h4 class="h-title mb-0">Pos Keamanan AI (Ultra Responsive)</h4>
                    </div>
                    <p class="text-muted-pro mb-0">Deteksi Instan 100ms | Tanpa Sentuhan | Tanpa QR</p>
                </div>
                <button onclick="toggleFullScreen()" class="btn-pro btn-pro-primary shadow-lg">
                    <i class="bi bi-lightning-charge-fill me-2"></i> Aktifkan Mode Kios
                </button>
            </header>

            <div class="row g-4 mb-4">
                <div class="col-lg-8" id="scanner-wrapper">
                    <div class="scanner-container">
                        <video id="video" autoplay muted playsinline></video>
                        <canvas id="canvas"></canvas>
                        
                        <div class="overlay-guide">
                            <div class="guide-box" id="guideBox"></div>
                        </div>

                        <div class="ai-hud" id="hudInfo">
                            WAKING UP AI CORES...
                        </div>

                        <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center fs-hide-inner position-absolute bottom-0 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent) !important; border:none;">
                            <span class="small fw-bold"><i class="bi bi-cpu-fill me-2 text-info"></i>TinyFace Engine v2</span>
                            <span class="badge bg-soft-info text-info border border-info border-opacity-25" id="latencyInfo">Initializing...</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 fs-hide-inner">
                    <div class="card-pro h-100 border-0 shadow-sm bg-white overflow-hidden p-0">
                        <div class="p-3 border-bottom bg-light d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold mb-0 small"><i class="bi bi-activity me-2"></i>Log Aktivitas Cepat</h6>
                            <span class="badge bg-primary rounded-circle" id="logCount">0</span>
                        </div>
                        <ul id="scanLog" class="list-unstyled mb-0 small p-3" style="max-height: 420px; overflow-y: auto;">
                            <!-- Log UI -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-pro fs-hide-inner p-0 overflow-hidden shadow-sm border-0">
                <div class="p-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-journal-text me-2"></i>REKAPAN KEHADIRAN BIOMETRIK</h6>
                        <p class="text-mini text-muted mb-0">HARI INI: <?php echo date('d F Y'); ?></p>
                    </div>
                    <button onclick="window.print()" class="btn btn-light border btn-sm px-3 fw-bold">
                        <i class="bi bi-printer me-2"></i>CETAK
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">ID</th>
                                <th class="border-0">Nama Lengkap</th>
                                <th class="border-0">Bidang</th>
                                <th class="border-0">Masuk</th>
                                <th class="border-0">Pulang</th>
                                <th class="text-center border-0">Akurasi AI</th>
                            </tr>
                        </thead>
                        <tbody id="tableLogBody">
                            <?php 
                            $no = 1; 
                            if(mysqli_num_rows($result_riwayat) > 0) {
                                while($row = mysqli_fetch_assoc($result_riwayat)): 
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo $row['nama']; ?></td>
                                <td class="small text-secondary"><?php echo $row['kelas_mapel']; ?></td>
                                <td class="text-success fw-bold"><?php echo $row['jam_masuk'] ? date('H:i', strtotime($row['jam_masuk'])) : '-'; ?></td>
                                <td class="text-danger fw-bold"><?php echo ($row['jam_keluar'] && $row['jam_keluar'] != '00:00:00') ? date('H:i', strtotime($row['jam_keluar'])) : '-'; ?></td>
                                <td class="text-center"><span class="badge bg-soft-success text-success px-3">VERIFIED</span></td>
                            </tr>
                            <?php endwhile; } else { ?>
                                <tr id="emptyRow"><td colspan="6" class="py-5 text-center text-muted small">Mencari data wajah...</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<audio id="beep" src="/public/absen/assets/audio/beep.mp3" preload="auto"></audio>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const hudInfo = document.getElementById('hudInfo');
const shutter = document.getElementById('shutter');
const fsOverlay = document.getElementById('fs-overlay');
const fsName = document.getElementById('fs-name');
const fsMsg = document.getElementById('fs-msg');
const guideBox = document.getElementById('guideBox');

const dbData = <?php echo json_encode($labeled_descriptors); ?>;
let faceMatcher = null;
let isProcessing = false;
let modelsLoaded = false;
let lastMatchedUserId = null;
let logCounter = 0;

const MODEL_URL = '/public/absen/assets/models/';

function speak(text) {
    if ('speechSynthesis' in window) {
        const u = new SpeechSynthesisUtterance(text);
        u.lang = 'id-ID';
        u.rate = 1.1;
        window.speechSynthesis.speak(u);
    }
}

async function initAI() {
    try {
        hudInfo.innerHTML = "LOADING LIGHTWEIGHT CORES...";
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        
        hudInfo.innerHTML = "MAPPING BIOMETRICS...";
        const descriptors = dbData.map(d => new faceapi.LabeledFaceDescriptors(d.id.toString(), [new Float32Array(d.descriptor)]));
        if(descriptors.length > 0) faceMatcher = new faceapi.FaceMatcher(descriptors, 0.5); // Lebih toleran untuk speed
        
        modelsLoaded = true;
        hudInfo.innerHTML = "ULTRA-RESPONSIVE ENGINE ONLINE";
        startScanner();
    } catch (e) {
        hudInfo.innerHTML = "NETWORK ERROR";
    }
}

function startScanner() {
    navigator.mediaDevices.getUserMedia({ video: { width: 1280, height: 720, frameRate: { ideal: 30, max: 60 } } })
        .then(s => { video.srcObject = s; run(); })
        .catch(() => Swal.fire('Error', 'Camera Access Required', 'error'));
}

async function run() {
    if (!modelsLoaded) {
        requestAnimationFrame(run);
        return;
    }

    const t0 = performance.now();
    const detect = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.35 }))
        .withFaceLandmarks().withFaceDescriptor();

    if (detect) {
        const score = Math.round(detect.detection.score * 100);
        guideBox.classList.add('active');
        if (faceMatcher) {
            const match = faceMatcher.findBestMatch(detect.descriptor);
            const t1 = performance.now();
            document.getElementById('latencyInfo').innerText = `Sensitivitas: ${score}% | Delay: ${Math.round(t1-t0)}ms`;

            if (match.label !== 'unknown' && !isProcessing && match.label !== lastMatchedUserId) {
                processAbsen(match.label);
            }
        }
    } else {
        guideBox.classList.remove('active');
        document.getElementById('latencyInfo').innerText = `Mendeteksi Wajah...`;
    }

    if (detect && faceMatcher) {
        const match = faceMatcher.findBestMatch(detect.descriptor);
        const t1 = performance.now();
        document.getElementById('latencyInfo').innerText = `${Math.round(t1-t0)}ms / frame`;

        if (match.label !== 'unknown' && !isProcessing && match.label !== lastMatchedUserId) {
            processAbsen(match.label);
        }

        // Draw HUD
        const size = { width: video.clientWidth, height: video.clientHeight };
        faceapi.matchDimensions(canvas, size);
        const resized = faceapi.resizeResults(detect, size);
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0,0, canvas.width, canvas.height);
        
        const box = resized.detection.box;
        ctx.strokeStyle = match.label !== 'unknown' ? '#0ea5e9' : '#3b82f6';
        ctx.lineWidth = 4;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
        
        if(match.label !== 'unknown') {
            ctx.fillStyle = '#0ea5e9';
            ctx.font = '700 16px sans-serif';
            ctx.fillText("VERIFIED", box.x, box.y - 10);
        }
    } else {
        canvas.getContext('2d').clearRect(0,0, canvas.width, canvas.height);
    }

    requestAnimationFrame(run);
}

async function processAbsen(uid) {
    isProcessing = true;
    lastMatchedUserId = uid;
    
    document.getElementById('beep').play().catch(() => {});
    shutter.style.opacity = '0.5';
    setTimeout(() => shutter.style.opacity = '0', 150);

    const f = new FormData();
    f.append('user_id', uid);

    try {
        const r = await fetch('../actions/absensi_proses.php', { method: 'POST', body: f });
        const d = await r.json();
        
        const n = d.nama || 'Identify Success';
        const m = d.message || "AUTHORIZED";
        
        fsName.innerText = n;
        fsMsg.innerText = m;
        fsOverlay.style.display = 'flex';
        setTimeout(() => fsOverlay.style.display = 'none', 2500);

        let v = "";
        if(d.status === 'success_masuk') v = `Selamat datang ${n}, selamat bekerja.`;
        else if(d.status === 'success_keluar') v = `Hati-hati di jalan ${n}, selamat beristirahat.`;
        else v = d.message;

        speak(v);
        updateLogs(n, m);
    } catch(e) {}

    setTimeout(() => isProcessing = false, 2000);
    setTimeout(() => lastMatchedUserId = null, 15000); // 15s cooldown p orang yang sama
}

function updateLogs(n, m) {
    const list = document.getElementById('scanLog');
    const t = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    logCounter++;
    document.getElementById('logCount').innerText = logCounter;
    
    const html = `
        <li class="mb-2 pb-2 border-bottom animate__animated animate__fadeInLeft">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold text-dark">${n}</div>
                    <div class="text-mini text-muted">${m}</div>
                </div>
                <div class="text-primary font-monospace fw-bold">${t}</div>
            </div>
        </li>
    `;
    list.insertAdjacentHTML('afterbegin', html);
    
    const body = document.getElementById('tableLogBody');
    const er = document.getElementById('emptyRow');
    if(er) er.remove();
    const row = `
        <tr style="background:#f0f9ff; transition: background 2s;">
            <td class="ps-4 text-muted small">NEW</td>
            <td class="fw-bold">${n}</td>
            <td class="small text-secondary">-</td>
            <td class="text-success fw-bold">${m.includes('Masuk') ? t : '-'}</td>
            <td class="text-danger fw-bold">${m.includes('Pulang') ? t : '-'}</td>
            <td class="text-center"><span class="badge bg-success px-3">AI VERIFIED</span></td>
        </tr>
    `;
    body.insertAdjacentHTML('afterbegin', row);
}

function toggleFullScreen() {
    if (!document.fullscreenElement) document.documentElement.requestFullscreen();
    else document.exitFullscreen();
}

initAI();
</script>
<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>



