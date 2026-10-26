<?php
include '../includes/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Ambil user yang belum terdaftar wajahnya
$query_users = "SELECT * FROM users ORDER BY nama ASC";
$res_users = mysqli_query($koneksi, $query_users);
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Wajah Auto - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <script src="/public/absen/assets/js/face-api.js"></script>
    <style>
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            aspect-ratio: 4/3;
        }
        #video { width: 100%; height: 100%; object-fit: cover; display: block; transform: scaleX(-1); }
        #canvas { position: absolute; top:0; left:0; width: 100%; height: 100%; transform: scaleX(-1); }
        
        .overlay-guide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .guide-box {
            width: 280px; height: 350px;
            border: 4px solid #ef4444; /* Default Merah */
            border-radius: 40px;
            box-shadow: 0 0 0 4000px rgba(15, 23, 42, 0.7);
            transition: all 0.3s ease;
            position: relative;
        }
        .guide-box::before {
            content: 'POSISIKAN WAJAH DI SINI';
            position: absolute; top: -40px; left: 50%; transform: translateX(-50%);
            color: #ef4444; font-size: 0.75rem; font-weight: 800; white-space: nowrap;
            letter-spacing: 1px;
        }
        .guide-box.active { 
            border-color: #0ea5e9; /* Hijau saat pas */
            box-shadow: 0 0 40px rgba(14, 165, 233, 0.4), 0 0 0 4000px rgba(15, 23, 42, 0.4); 
        }
        .guide-box.active::before { content: 'WAJAH TERDETEKSI - DIAM...'; color: #0ea5e9; }
        .guide-box.success { border-color: #3b82f6; } /* Biru saat simpan */

        .ai-status {
            position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
            background: rgba(0,0,0,0.8); color: #fff; padding: 10px 25px;
            border-radius: 30px; font-size: 0.85rem; font-weight: 600;
            white-space: nowrap; backdrop-filter: blur(10px); display: flex; align-items: center; gap: 10px;
            z-index: 10;
        }

        .capture-progress {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 100px; height: 100px; display: none;
            z-index: 20;
        }

        .shutter-effect {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; opacity: 0; pointer-events: none; z-index: 100;
        }
    </style>
</head>
<body>

<div class="shutter-effect" id="shutter"></div>

<div class="container-fluid p-0">
    <div class="row g-0">
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="content-header mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white p-3 rounded-4 shadow-sm">
                        <i class="bi bi-robot fs-4"></i>
                    </div>
                    <div>
                        <h4 class="h-title mb-1">Registrasi Wajah Tanpa Klik (Auto)</h4>
                        <p class="text-muted-pro mb-0">AI akan otomatis menyimpan data saat wajah terdeteksi stabil.</p>
                    </div>
                </div>
            </header>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card-pro h-100 shadow-sm border-0">
                        <h6 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-person-badge me-2"></i>Pilih Personel</h6>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Identitas Anggota</label>
                            <select id="userSelector" class="form-select py-3 border-2 shadow-none" style="border-radius: 12px; font-weight: 600;">
                                <option value="">-- Pilih Nama Anggota --</option>
                                <?php while($u = mysqli_fetch_assoc($res_users)): ?>
                                    <option value="<?php echo $u['id']; ?>" data-status="<?php echo $u['face_descriptor'] ? 'Terdaftar' : 'Belum Terdaftar'; ?>">
                                        <?php echo $u['nama']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div id="selectionInfo" class="p-4 bg-light rounded-4 mb-4 d-none animate__animated animate__fadeIn">
                            <div class="text-center">
                                <div class="avatar-bg mx-auto mb-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <h5 class="fw-bold mb-1" id="disp-name">Nama</h5>
                                <span class="badge rounded-pill px-3 py-2" id="statusBadge">Status</span>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <div class="p-3 bg-soft-blue rounded-3 border-start border-4 border-primary">
                                <div class="fw-bold mb-1 small text-primary"><i class="bi bi-magic me-1"></i> Cara Kerja Auto-Scan</div>
                                <div class="small text-secondary">Setelah memilih nama, cukup hadapkan wajah ke kamera selama 2 detik. AI akan melakukan **Auto-Capture** saat posisi wajah sudah pas.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="camera-container shadow-lg">
                        <video id="video" autoplay muted playsinline></video>
                        <canvas id="canvas"></canvas>
                        <div class="capture-progress" id="progressRing">
                            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%;">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"></path>
                                <path id="progressPath" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#3b82f6" stroke-width="3" stroke-dasharray="0, 100"></path>
                            </svg>
                        </div>
                        <div class="overlay-guide">
                            <div class="guide-box" id="guideBox"></div>
                        </div>
                        <div class="ai-status" id="aiStatus">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span>Menghubungkan ke Neural Network...</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const aiStatus = document.getElementById('aiStatus');
const guideBox = document.getElementById('guideBox');
const progressRing = document.getElementById('progressRing');
const progressPath = document.getElementById('progressPath');
const userSelector = document.getElementById('userSelector');
const shutter = document.getElementById('shutter');

let modelsLoaded = false;
let isSaving = false;
let stableFrames = 0;
const REQUIRED_STABLE_FRAMES = 15; // Sekitar 1.5 - 2 detik pada device rata-rata

const MODEL_URL = '/public/absen/assets/models/';

async function loadAI() {
    try {
        aiStatus.innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Menyiapkan Akselerator Kecepatan...';
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        
        modelsLoaded = true;
        aiStatus.innerHTML = '<i class="bi bi-shield-check-fill text-success"></i> INTELLIGENCE READY';
        startVideo();
    } catch (e) {
        aiStatus.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Gagal Memuat AI';
    }
}

function startVideo() {
    navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
        .then(stream => { video.srcObject = stream; loop(); })
        .catch(() => Swal.fire('Error', 'Izin Kamera Diperlukan', 'error'));
}

async function loop() {
    if (!modelsLoaded || isSaving) {
        requestAnimationFrame(loop);
        return;
    }

    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.35 }))
        .withFaceLandmarks().withFaceDescriptor();

    if (detections && userSelector.value) {
        const score = Math.round(detections.detection.score * 100);
        guideBox.classList.add('active');
        stableFrames++;
        
        const progress = Math.min((stableFrames / REQUIRED_STABLE_FRAMES) * 100, 100);
        progressRing.style.display = 'block';
        progressPath.setAttribute('stroke-dasharray', `${progress}, 100`);
        aiStatus.innerHTML = `<i class="bi bi-cpu-fill text-info"></i> KUALITAS SINYAL: ${score}% | SCANNING... ${Math.round(progress)}%`;

        if (stableFrames >= REQUIRED_STABLE_FRAMES) {
            autoCapture(detections.descriptor);
        }
    } else {
        stableFrames = 0;
        guideBox.classList.remove('active');
        progressRing.style.display = 'none';
        if(userSelector.value) {
            aiStatus.innerHTML = '<i class="bi bi-camera-fill text-primary"></i> HADAPKAN WAJAH KE ARAH BOX';
        } else {
            aiStatus.innerHTML = '<i class="bi bi-person-lines-fill text-warning"></i> PILIH NAMA ANGGOTA DAHULU';
        }
    }

    // Drawing
    const displaySize = { width: video.clientWidth, height: video.clientHeight };
    faceapi.matchDimensions(canvas, displaySize);
    if(detections) {
        const resized = faceapi.resizeResults(detections, displaySize);
        canvas.getContext('2d').clearRect(0,0, canvas.width, canvas.height);
        faceapi.draw.drawFaceLandmarks(canvas, resized);
    } else {
        canvas.getContext('2d').clearRect(0,0, canvas.width, canvas.height);
    }

    requestAnimationFrame(loop);
}

async function autoCapture(descriptor) {
    isSaving = true;
    guideBox.classList.add('success');
    aiStatus.innerHTML = '<i class="bi bi-cloud-upload-fill text-success"></i> MENGUNCI IDENTITAS...';
    
    // Shutter effect
    shutter.style.opacity = '0.6';
    setTimeout(() => shutter.style.opacity = '0', 150);

    const formData = new FormData();
    formData.append('user_id', userSelector.value);
    formData.append('descriptor', JSON.stringify(Array.from(descriptor)));

    try {
        const res = await fetch('../actions/simpan_wajah.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if(data.status === 'success') {
            Swal.fire({ 
                icon: 'success', 
                title: 'Data Tersimpan!', 
                text: 'Biometrik wajah berhasil diaktifkan.',
                timer: 2000, showConfirmButton: false 
            });
            const opt = userSelector.options[userSelector.selectedIndex];
            opt.dataset.status = 'Terdaftar';
            updateUI(opt);
        }
    } catch (e) {
        Swal.fire('Gagal', 'Terjadi gangguan jaringan', 'error');
    } finally {
        setTimeout(() => {
            isSaving = false;
            stableFrames = 0;
            guideBox.classList.remove('success');
        }, 3000);
    }
}

function updateUI(opt) {
    const info = document.getElementById('selectionInfo');
    const badge = document.getElementById('statusBadge');
    if(opt.value) {
        info.classList.remove('d-none');
        document.getElementById('disp-name').innerText = opt.text;
        badge.innerText = opt.dataset.status;
        badge.className = opt.dataset.status === 'Terdaftar' ? 'badge bg-success px-3 py-2' : 'badge bg-secondary px-3 py-2';
    } else {
        info.classList.add('d-none');
    }
}

userSelector.addEventListener('change', (e) => updateUI(e.target.options[e.target.selectedIndex]));

loadAI();
</script>
<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>


