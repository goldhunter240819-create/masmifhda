<?php
// Validasi session (karena dari controller public)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['guru_id'])) {
    header("Location: /apk/login.php");
    exit;
}

function tgl_indo_eval($tanggal){
    $bulan = array (
        1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return (int)$pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$page_title = 'Evaluasi Supervisi ' . $tipe;
$favicon_url = '/public/assets/images/favicon.png';
$actionUrl = ($tipe == 'Kelas') ? '/kurikulum/supervisi/kelas/evaluasi' : '/kurikulum/supervisi/administrasi/evaluasi';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="icon" href="<?= htmlspecialchars($favicon_url) ?>" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html { background: #e2e8f0; display: flex; justify-content: center; }
        body { width: 100%; max-width: 480px; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #1e293b; padding-bottom: 20px; box-shadow: 0 0 25px rgba(0,0,0,0.1); min-height: 100vh; position: relative; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 2rem 1.5rem 4rem; color: white; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; position: relative; overflow: hidden; }
        .header-top { display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 2; }
        .greeting p { margin: 0; font-size: 0.9rem; opacity: 0.9; }
        .greeting h1 { margin: 5px 0 0; font-size: 1.5rem; font-weight: 800; }
        
        .btn-back { color: white; text-decoration: none; font-size: 1.2rem; margin-right: 15px; }
        
        .container { padding: 1.5rem; margin-top: -45px; position: relative; z-index: 3; }
        
        .card { background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .detail-row { display: flex; margin-bottom: 8px; font-size: 0.9rem; }
        .detail-row i { width: 20px; color: #94a3b8; margin-top: 3px; }
        .detail-text { flex: 1; color: #475569; }
        .detail-text strong { color: #1e293b; }
        
        .kategori-title { font-weight: 800; font-size: 1.1rem; color: #0f172a; margin: 1.5rem 0 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0; }
        .instrumen-card { background: white; border-radius: 15px; padding: 1.25rem; margin-bottom: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; }
        .instrumen-kode { font-size: 0.8rem; font-weight: 700; color: #10b981; margin-bottom: 0.25rem; }
        .instrumen-aspek { font-size: 0.95rem; color: #1e293b; font-weight: 600; line-height: 1.4; margin-bottom: 1rem; }
        
        .skor-container { display: flex; gap: 8px; justify-content: space-between; }
        .skor-container label { flex: 1; }
        .skor-btn { width: 100%; height: 45px; border-radius: 12px; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b; background: white; transition: all 0.2s; cursor: pointer; box-sizing: border-box; font-size: 1.1rem; }
        
        /* Dynamic Colors for 1-5 */
        .skor-radio[value="1"]:checked + .skor-btn { background: #ef4444; border-color: #ef4444; color: white; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); transform: translateY(-2px); }
        .skor-radio[value="2"]:checked + .skor-btn { background: #f97316; border-color: #f97316; color: white; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3); transform: translateY(-2px); }
        .skor-radio[value="3"]:checked + .skor-btn { background: #f59e0b; border-color: #f59e0b; color: white; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); transform: translateY(-2px); }
        .skor-radio[value="4"]:checked + .skor-btn { background: #84cc16; border-color: #84cc16; color: white; box-shadow: 0 4px 10px rgba(132, 204, 22, 0.3); transform: translateY(-2px); }
        .skor-radio[value="5"]:checked + .skor-btn, .skor-radio:checked + .skor-btn { background: #10b981; border-color: #10b981; color: white; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3); transform: translateY(-2px); }
        
        .textarea-custom { width: 100%; border: 1px solid #cbd5e1; border-radius: 15px; padding: 1rem; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.95rem; line-height: 1.5; resize: vertical; box-sizing: border-box; }
        .textarea-custom:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
        
        .sticky-bottom { position: sticky; bottom: 0; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; z-index: 10; margin-top: 2rem; }
        .btn-submit { background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 8px; font-family: inherit; cursor: pointer; }
    </style>
</head>
<body>

<form action="<?= $actionUrl ?>" method="POST" id="evalForm">
    <input type="hidden" name="id" value="<?= $supervisi['id'] ?>">
    <input type="hidden" name="source" value="mobile">

    <div class="header">
        <div class="header-top">
            <div style="display:flex; align-items:center;">
                <a href="/kurikulum/supervisi/kelas/mobile" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="greeting">
                    <p>Form Penilaian</p>
                    <h1>Evaluasi <?= $tipe ?></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="detail-row">
                <i class="fa-regular fa-calendar"></i>
                <div class="detail-text">Tanggal: <strong><?= tgl_indo_eval($supervisi['tanggal']) ?></strong></div>
            </div>
            <div class="detail-row">
                <i class="fa-solid fa-chalkboard-user"></i>
                <div class="detail-text"><?= $tipe == 'Kelas' ? 'Guru' : 'Pegawai' ?>: <strong><?= htmlspecialchars($supervisi[$tipe == 'Kelas' ? 'nama_guru' : 'nama_pegawai']) ?></strong></div>
            </div>
            <?php if($tipe == 'Kelas'): ?>
            <div class="detail-row">
                <i class="fa-solid fa-book"></i>
                <div class="detail-text">Kelas & Mapel: <strong><?= htmlspecialchars($supervisi['nama_kelas']) ?> - <?= htmlspecialchars($supervisi['nama_mapel']) ?></strong></div>
            </div>
            <?php endif; ?>
        </div>

        <?php 
        $grouped = [];
        foreach($instrumen as $ins) {
            $kategori = $ins['kategori'] ?: 'LAIN-LAIN';
            $grouped[$kategori][] = $ins;
        }
        $letters = range('A', 'Z');
        $c_index = 0;
        ?>

        <?php if(empty($instrumen)): ?>
            <div style="text-align: center; padding: 2rem; color:#64748b;">Belum ada instrumen.</div>
        <?php else: ?>
            <?php foreach($grouped as $kategori => $items): ?>
                <div class="kategori-title"><?= $letters[$c_index++] ?>. <?= strtoupper(htmlspecialchars($kategori)) ?></div>
                
                <?php $no = 1; foreach($items as $ins): ?>
                <div class="instrumen-card">
                    <div class="instrumen-kode">Aspek <?= $no++ ?> (Skor Max: <?= $ins['bobot_maksimal'] ?>)</div>
                    <div class="instrumen-aspek"><?= htmlspecialchars($ins['aspek']) ?></div>
                    
                    <div class="skor-container">
                        <?php 
                        $current = isset($hasil[$ins['id']]) ? $hasil[$ins['id']] : null;
                        for($i=1; $i<=$ins['bobot_maksimal']; $i++): 
                            $checked = ($current !== null && $current == $i) ? 'checked' : '';
                        ?>
                        <label>
                            <input type="radio" name="skor[<?= $ins['id'] ?>]" value="<?= $i ?>" <?= $checked ?> data-max="<?= $ins['bobot_maksimal'] ?>" required style="display:none;" class="skor-radio">
                            <div class="skor-btn"><?= $i ?></div>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="kategori-title">Catatan Tambahan</div>
        <textarea name="catatan_evaluasi" class="textarea-custom" rows="4" placeholder="Tulis catatan, masukan, atau saran di sini..."><?= htmlspecialchars($supervisi['catatan_evaluasi'] ?? '') ?></textarea>
    </div>

    <div class="sticky-bottom">
        <div>
            <div style="font-size:0.8rem; color:#64748b; font-weight:700;">Nilai Akhir</div>
            <div style="font-size:1.5rem; font-weight:800; color:#1e293b;" id="liveNilai">0</div>
        </div>
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-check-double"></i> Simpan
        </button>
    </div>
</form>

<script>
    const allRadios = document.querySelectorAll('.skor-radio');
    function hitungNilai() {
        let totalSkor = 0;
        let maxSkor = 0;
        
        const allGroups = new Set();
        allRadios.forEach(r => allGroups.add(r.getAttribute('name')));
        
        allGroups.forEach(name => {
            const radiosInGroup = document.querySelectorAll(`input[name="${name}"]`);
            if(radiosInGroup.length > 0) {
                const max = parseInt(radiosInGroup[0].getAttribute('data-max')) || 0;
                maxSkor += max;
                
                const checked = document.querySelector(`input[name="${name}"]:checked`);
                if(checked) {
                    totalSkor += parseInt(checked.value) || 0;
                }
            }
        });
        
        let nilai = 0;
        if(maxSkor > 0) {
            nilai = Math.round((totalSkor / maxSkor) * 100);
        }
        
        document.getElementById('liveNilai').innerText = nilai;
    }
    
    allRadios.forEach(radio => {
        radio.addEventListener('change', hitungNilai);
    });
    
    // Initial calculation
    hitungNilai();

    // SweetAlert Confirmation before Submit
    document.getElementById('evalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Ensure all radios are filled
        const allGroups = new Set();
        document.querySelectorAll('.skor-radio').forEach(r => allGroups.add(r.getAttribute('name')));
        let isComplete = true;
        allGroups.forEach(name => {
            if(!document.querySelector(`input[name="${name}"]:checked`)) {
                isComplete = false;
            }
        });

        if(!isComplete) {
            Swal.fire({
                title: 'Data Belum Lengkap',
                text: 'Mohon isi semua aspek penilaian sebelum menyimpan.',
                icon: 'warning',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        Swal.fire({
            title: 'Selesai Evaluasi?',
            text: "Evaluasi yang sudah disimpan tidak dapat diedit lagi.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('evalForm').submit();
            }
        });
    });
</script>

</body>
</html>
