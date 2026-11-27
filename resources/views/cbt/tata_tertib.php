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
                <li><i class="fa-solid fa-sun" style="color:#d97706;"></i><span><strong>Layar Wajib Tetap Hidup (Tidak Boleh Terkunci/Mati):</strong> Pastikan layar HP/laptop selalu menyala selama ujian. Jika layar mati atau terkunci (sleep/power), sistem menganggap Anda meninggalkan ujian.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Wajib Layar Penuh (Fullscreen):</strong> Ujian wajib dikerjakan dalam mode layar penuh. Keluar dari mode fullscreen dihitung sebagai pelanggaran dan harus segera kembali sebelum waktu hitung mundur habis.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Pindah Tab / Tombol Home / Aplikasi Lain:</strong> Menekan tombol Home, berpindah tab, Alt+Tab / tombol Windows, atau membuka WhatsApp, Google, dan AI otomatis memicu alarm &amp; pelanggaran.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Layar Belah (Split Screen) &amp; Jendela Mengambang:</strong> Mengaktifkan split screen atau floating window di atas ujian otomatis terblokir dan tercatat sebagai pelanggaran.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Ganti Jaringan &amp; Screenshot / DevTools:</strong> Dilarang mengganti WiFi ke Data Seluler saat ujian, melakukan tangkapan layar (gestur 3 jari / PrintScreen), atau membuka Inspect Element / DevTools.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>Dilarang Spam Tombol Kembali:</strong> Menekan tombol Kembali secara paksa saat ujian berlangsung akan langsung me-logout &amp; mengunci akun selama 10 jam.</span></li>
                <li><i class="fa-solid fa-ban"></i><span><strong>1 Akun 1 Perangkat:</strong> Login akun yang sama di perangkat lain saat ujian berlangsung otomatis ditolak sistem.</span></li>
                <li><i class="fa-solid fa-circle-exclamation"></i><span><strong>Batas Maksimal <?= (int)($maxPelanggaran ?? 3) ?>x Pelanggaran:</strong> Jika melewati batas pelanggaran, sistem otomatis mengunci atau men-submit ujian Anda (pelanggaran bersifat kumulatif dan tidak hilang saat reload).</span></li>
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
            const nextTokenUrl = <?= json_encode($nextTokenUrl ?? $kerjakanUrl) ?>;
            const kerjakanUrl = <?= json_encode($kerjakanUrl) ?>;
            const statusUrl = <?= json_encode($statusUrl) ?>;
            const dashboardUrl = <?= json_encode($dashboardUrl) ?>;
            const lockBackUrl = <?= json_encode($lockBackUrl ?? '') ?>;
            const loginUrl = <?= json_encode($loginUrl ?? '') ?>;
            const lockHours = <?= (int)($lockHours ?? 10) ?>;
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
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menuju Konfirmasi Token...';
                if (!isAdminPreview) {
                    try { sessionStorage.setItem(agreedKey, '1'); } catch (e) {}
                }
                // Navigasi normal agar halaman Tata Tertib tetap berada di history sebagai bantalan sebelum dashboard
                window.location.href = nextTokenUrl;
            });

            if (!isAdminPreview && lockBackUrl) {
                let backTaps = [];
                try {
                    for (let i = 0; i < 4; i++) {
                        window.history.pushState({ cbt_tt: i }, document.title, window.location.href);
                    }
                } catch (e) {}
                window.addEventListener('popstate', function() {
                    const now = Date.now();
                    backTaps.push(now);
                    backTaps = backTaps.filter(t => now - t <= 4000);
                    try { window.history.pushState({ cbt_tt: 'trap' }, document.title, window.location.href); } catch (e) {}
                    if (backTaps.length >= 3 && lockHours > 0) {
                        fetch(lockBackUrl, {
                            method: 'POST',
                            keepalive: true,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'source=' + encodeURIComponent('Halaman Tata Tertib')
                        }).then(r => r.json()).then(res => {
                            const ts = (res && res.locked_until_ts) ? res.locked_until_ts : (Math.floor(Date.now() / 1000) + (lockHours * 3600));
                            const msg = `Akun Anda di-logout & dikunci ${lockHours} jam. Silahkan pulang saja, renungi di rumah, besok kesini lagi!`;
                            window.location.replace(loginUrl + '?locked_msg=' + encodeURIComponent(msg) + '&locked_until_ts=' + ts);
                        }).catch(() => {
                            window.location.replace(loginUrl);
                        });
                    }
                });
            }

            function checkAndRouteLoop() {
                if (isAdminPreview) return;
                fetch(statusUrl + '&record_escape=1&source=Halaman%20Tata%20Tertib', { cache: 'no-store', credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(res => {
                        if (!res || !res.success) return;
                        const st = String(res.status || '');
                        if (st === 'Selesai') {
                            try { sessionStorage.removeItem(agreedKey); } catch (e) {}
                            window.location.replace(dashboardUrl);
                        } else if (st === 'Mengerjakan' || st === 'Sedang Mengerjakan' || st === 'Terkunci') {
                            window.location.replace(kerjakanUrl);
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
