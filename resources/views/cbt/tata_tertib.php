<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tata Tertib Ujian CBT - <?= htmlspecialchars($ujianInfo['nama_ujian'] ?? 'Ujian') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .tt-card {
            background: #ffffff;
            width: 100%;
            max-width: 560px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }
        .tt-header {
            background: linear-gradient(135deg, #0284c7, #1d4ed8);
            color: #ffffff;
            padding: 20px 22px;
        }
        .tt-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            margin-bottom: 8px;
        }
        .tt-header h1 {
            font-size: 1.18rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 4px;
        }
        .tt-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
            font-size: 0.78rem;
            opacity: 0.95;
        }
        .tt-meta span {
            background: rgba(15, 23, 42, 0.22);
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .tt-body {
            padding: 20px 22px;
        }
        .tt-alert {
            background: #fffbeb;
            border: 1.5px solid #fcd34d;
            color: #92400e;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .tt-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 18px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .tt-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 0.83rem;
            line-height: 1.45;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 9px 12px;
            border-radius: 10px;
        }
        .tt-list li i {
            color: #dc2626;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .tt-agree {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            padding: 12px 14px;
            border-radius: 12px;
            cursor: pointer;
            margin-bottom: 16px;
            user-select: none;
        }
        .tt-agree input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 1px;
            accent-color: #1d4ed8;
            flex-shrink: 0;
            cursor: pointer;
        }
        .tt-agree span {
            font-size: 0.84rem;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1.4;
        }
        .tt-actions {
            display: flex;
            gap: 10px;
        }
        .btn-back {
            flex: 0 0 auto;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            font-weight: 700;
            font-size: 0.86rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-start {
            flex: 1;
            padding: 13px 18px;
            border-radius: 12px;
            border: none;
            background: #94a3b8;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: not-allowed;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-start.ready {
            background: linear-gradient(135deg, #16a34a, #15803d);
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
        }
    </style>
</head>
<body>
    <div class="tt-card">
        <div class="tt-header">
            <div class="tt-badge"><i class="fa-solid fa-shield-halved"></i> TATA TERTIB UJIAN CBT</div>
            <h1><?= htmlspecialchars($ujianInfo['nama_ujian'] ?? 'Ujian CBT') ?></h1>
            <div class="tt-meta">
                <span><i class="fa-solid fa-book"></i> <?= htmlspecialchars($ujianInfo['mapel_nama'] ?? '-') ?></span>
                <span><i class="fa-regular fa-clock"></i> <?= (int)($ujianInfo['durasi_menit'] ?? 60) ?> Menit</span>
                <span><i class="fa-solid fa-list-ol"></i> <?= (int)($jumlahSoal ?? 0) ?> Soal</span>
            </div>
        </div>

        <div class="tt-body">
            <div class="tt-alert">
                <i class="fa-solid fa-triangle-exclamation" style="font-size:1.1rem; margin-top:2px;"></i>
                <div>Setelah Anda menyetujui tata tertib dan masuk ke ruang ujian, sesi langsung aktif. Tombol kembali akan otomatis mengarahkan Anda kembali ke halaman pengerjaan hingga ujian selesai disubmit.</div>
            </div>

            <ul class="tt-list">
                <li><i class="fa-solid fa-ban"></i><span><strong>Wajib Layar Penuh (Fullscreen):</strong> Ujian harus dikerjakan dalam mode layar penuh. Keluar fullscreen dihitung sebagai pelanggaran.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Pindah Tab / Home / Floating Window:</strong> Membuka WhatsApp, Google, AI, layar belah (split screen), atau jendela mengambang otomatis terdeteksi &amp; memicu alarm.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Spam Tombol Kembali:</strong> Menekan tombol kembali paksa saat ujian berlangsung dapat menyebabkan akun terkunci otomatis.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>1 Akun 1 Perangkat:</strong> Login akun yang sama di perangkat lain saat ujian aktif otomatis ditolak.</span></li>
                <li><i class="fa-solid fa-circle-exclamation"></i><span><strong>Batas Maksimal <?= (int)($maxPelanggaran ?? 3) ?>x Pelanggaran:</strong> Jika melewati batas pelanggaran, sistem otomatis mengunci atau men-submit ujian Anda.</span></li>
            </ul>

            <label class="tt-agree" for="chkSetujuTatib">
                <input type="checkbox" id="chkSetujuTatib">
                <span>Saya telah membaca, memahami, dan menyetujui seluruh Tata Tertib Ujian CBT di atas serta siap mengerjakan dengan jujur.</span>
            </label>

            <div class="tt-actions">
                <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <button type="button" id="btnMulaiUjian" class="btn-start" disabled>
                    <i class="fa-solid fa-play"></i> Mulai Mengerjakan Ujian
                </button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const ujianId = <?= (int)$ujian_id ?>;
            const isAdminPreview = <?= !empty($isAdminPreview) ? 'true' : 'false' ?>;
            const kerjakanUrl = <?= json_encode($kerjakanUrl) ?>;
            const statusUrl = <?= json_encode($statusUrl) ?>;
            const dashboardUrl = <?= json_encode($dashboardUrl) ?>;
            const agreedKey = 'cbt_tatib_agreed_' + ujianId;

            const chk = document.getElementById('chkSetujuTatib');
            const btn = document.getElementById('btnMulaiUjian');

            chk.addEventListener('change', function() {
                if (chk.checked) {
                    btn.disabled = false;
                    btn.classList.add('ready');
                } else {
                    btn.disabled = true;
                    btn.classList.remove('ready');
                }
            });

            btn.addEventListener('click', function() {
                if (!chk.checked) return;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membuka Ruang Ujian...';
                if (!isAdminPreview) {
                    try { sessionStorage.setItem(agreedKey, '1'); } catch (e) {}
                }
                // Navigasi normal agar halaman Tata Tertib tetap berada di history sebagai bantalan sebelum dashboard
                window.location.href = kerjakanUrl;
            });

            function checkAndRouteLoop() {
                if (isAdminPreview) return;
                fetch(statusUrl, { cache: 'no-store', credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(res => {
                        if (!res || !res.success) return;
                        const st = String(res.status || '');
                        if (st === 'Selesai') {
                            try { sessionStorage.removeItem(agreedKey); } catch (e) {}
                            window.location.replace(dashboardUrl);
                        } else if (st === 'Mengerjakan' || st === 'Sedang Mengerjakan' || st === 'Terkunci') {
                            window.location.replace(kerjakanUrl);
                        } else {
                            let wasAgreed = false;
                            try { wasAgreed = sessionStorage.getItem(agreedKey) === '1'; } catch (e) {}
                            if (wasAgreed) {
                                window.location.replace(kerjakanUrl);
                            }
                        }
                    })
                    .catch(() => {});
            }

            // Anti bfcache Chrome: saat siswa menekan tombol Back dari kerjakan dan sempat mendarat di sini
            window.addEventListener('pageshow', function(event) {
                let isBackNav = event.persisted;
                try {
                    const navEntries = performance.getEntriesByType('navigation');
                    if (navEntries && navEntries.length > 0 && navEntries[0].type === 'back_forward') {
                        isBackNav = true;
                    }
                } catch (e) {}
                if (isBackNav) {
                    checkAndRouteLoop();
                }
            });
        })();
    </script>
</body>
</html>
