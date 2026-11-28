<!DOCTYPE html>
<html lang="id" translate="no" class="notranslate">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google" content="notranslate">
    <title>Konfirmasi Token Ujian - <?= htmlspecialchars($ujianInfo['nama_ujian'] ?? 'Ujian') ?></title>
    <style>
        html, body { overscroll-behavior-y: contain; }
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
        .tk-card {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }
        .tk-header {
            background: linear-gradient(135deg, #1d4ed8, #4338ca);
            color: #ffffff;
            padding: 20px 22px;
        }
        .tk-badge {
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
        .tk-header h1 {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.3;
        }
        .tk-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 0.78rem;
        }
        .tk-meta span {
            background: rgba(15, 23, 42, 0.24);
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .tk-body { padding: 20px 22px; }
        .tk-token-box {
            background: #eff6ff;
            border: 2px dashed #3b82f6;
            border-radius: 14px;
            padding: 14px;
            text-align: center;
            margin-bottom: 16px;
        }
        .tk-token-label {
            font-size: 0.74rem;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }
        .tk-token-code {
            font-size: 1.75rem;
            font-weight: 900;
            letter-spacing: 5px;
            color: #0f172a;
            font-family: monospace;
            user-select: none;
        }
        .tk-hint {
            display: inline-block;
            margin-top: 8px;
            font-size: 0.74rem;
            font-weight: 700;
            color: #b45309;
            background: #fef3c7;
            padding: 3px 10px;
            border-radius: 999px;
        }
        .tk-input-wrap { margin-bottom: 14px; }
        .tk-input-wrap label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .tk-input {
            width: 100%;
            padding: 13px 14px;
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 4px;
            text-align: center;
            text-transform: uppercase;
            outline: none;
            transition: border-color 0.2s;
        }
        .tk-input:focus { border-color: #2563eb; }
        .tk-err {
            display: none;
            margin-top: 7px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #dc2626;
            text-align: center;
        }
        .tk-warn {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.77rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .tk-actions { display: flex; gap: 10px; }
        .btn-back {
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
        .btn-go {
            flex: 1;
            padding: 13px 18px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.92rem;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="tk-card">
        <div class="tk-header">
            <div class="tk-badge">🔑 TAHAP 2 • KONFIRMASI TOKEN UJIAN</div>
            <h1><?= htmlspecialchars($ujianInfo['nama_ujian'] ?? 'Ujian CBT') ?></h1>
            <div class="tk-meta">
                <span>📘 <?= htmlspecialchars($ujianInfo['mapel_nama'] ?? '-') ?></span>
                <span>⏱️ <?= (int)($ujianInfo['durasi_menit'] ?? 60) ?> Menit</span>
                <span>📝 <?= (int)($jumlahSoal ?? 0) ?> Soal</span>
            </div>
        </div>

        <div class="tk-body">
            <div class="tk-token-box">
                <div class="tk-token-label">Kode Token Ujian Resmi (Ketik Ulang di Bawah)</div>
                <div class="tk-token-code" id="txtTokenResmi"><?= htmlspecialchars($tokenResmi !== '' ? $tokenResmi : '------') ?></div>
                <?php if (strpos($tokenResmi, 'O') !== false): ?>
                    <div class="tk-hint">ℹ️ Memakai <b>HURUF O</b> (Bukan angka nol 0)</div>
                <?php elseif (strpos($tokenResmi, '0') !== false): ?>
                    <div class="tk-hint">ℹ️ Memakai <b>ANGKA 0 (NOL)</b></div>
                <?php endif; ?>
            </div>

            <div class="tk-input-wrap">
                <label for="inpTokenConfirm">Masukkan Kode Token di Atas:</label>
                <input type="text" id="inpTokenConfirm" class="tk-input" placeholder="KETIK TOKEN" autocomplete="off" autocorrect="off" autocapitalize="characters" spellcheck="false">
                <div id="tkErrMsg" class="tk-err"></div>
            </div>

            <div class="tk-warn">
                <span>🛡️</span>
                <div>Pengawasan aktif sejak tahap ini. Dilarang spam tombol kembali (kunci 10 jam) atau pindah tab/aplikasi.</div>
            </div>

            <div class="tk-actions">
                <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="btn-back">
                    🏠 Beranda
                </a>
                <button type="button" id="btnSubmitToken" class="btn-go">
                    ▶ Mulai Kerjakan
                </button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const expectedToken = <?= json_encode($tokenResmi) ?>;
            const kerjakanUrl = <?= json_encode($kerjakanUrl) ?>;
            const statusUrl = <?= json_encode($statusUrl) ?>;
            const dashboardUrl = <?= json_encode($dashboardUrl) ?>;
            const lockBackUrl = <?= json_encode($lockBackUrl) ?>;
            const loginUrl = <?= json_encode($loginUrl) ?>;
            const isAdminPreview = <?= !empty($isAdminPreview) ? 'true' : 'false' ?>;
            const lockHours = <?= (int)($lockHours ?? 10) ?>;

            const inp = document.getElementById('inpTokenConfirm');
            const err = document.getElementById('tkErrMsg');
            const btn = document.getElementById('btnSubmitToken');

            function submitToken() {
                const entered = (inp.value || '').trim().toUpperCase();
                if (!entered) {
                    inp.style.borderColor = '#dc2626';
                    err.textContent = 'Ketikkan kode token di atas terlebih dahulu!';
                    err.style.display = 'block';
                    inp.focus();
                    return;
                }
                const normE = entered.replace(/0/g, 'O');
                const normX = (expectedToken || '').replace(/0/g, 'O');
                if (expectedToken && normE !== normX) {
                    inp.style.borderColor = '#dc2626';
                    err.textContent = 'Token tidak cocok! Periksa kembali huruf/angka di kotak atas.';
                    err.style.display = 'block';
                    inp.focus();
                    return;
                }
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Membuka Soal...';
                window.location.href = kerjakanUrl;
            }

            btn.addEventListener('click', submitToken);
            inp.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') submitToken();
            });

            // Anti-spam tombol Kembali di halaman Token (3x dalam 4 detik -> lock 10 jam)
            if (!isAdminPreview) {
                let backTaps = [];
                try {
                    for (let i = 0; i < 4; i++) {
                        window.history.pushState({ cbt_tk: i }, document.title, window.location.href);
                    }
                } catch (e) {}

                window.addEventListener('popstate', function() {
                    const now = Date.now();
                    backTaps.push(now);
                    backTaps = backTaps.filter(t => now - t <= 4000);
                    try { window.history.pushState({ cbt_tk: 'trap' }, document.title, window.location.href); } catch (e) {}

                    if (backTaps.length >= 3 && lockHours > 0) {
                        fetch(lockBackUrl, {
                            method: 'POST',
                            keepalive: true,
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'source=' + encodeURIComponent('Halaman Token')
                        }).then(r => r.json()).then(res => {
                            const ts = (res && res.locked_until_ts) ? res.locked_until_ts : (Math.floor(Date.now() / 1000) + (lockHours * 3600));
                            const msg = `Akun Anda di-logout & dikunci ${lockHours} jam. Silahkan pulang saja, renungi di rumah, besok kesini lagi!`;
                            window.location.replace(loginUrl + '?locked_msg=' + encodeURIComponent(msg) + '&locked_until_ts=' + ts);
                        }).catch(() => {
                            window.location.replace(loginUrl);
                        });
                    }
                });

                function checkAndRouteLoop() {
                    fetch(statusUrl + '&record_escape=1&source=Halaman%20Token', { cache: 'no-store', credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(res => {
                            if (!res || !res.success) return;
                            const st = String(res.status || '');
                            if (st === 'Selesai') {
                                window.location.replace(dashboardUrl);
                            } else if (st === 'Mengerjakan' || st === 'Sedang Mengerjakan' || st === 'Terkunci') {
                                window.location.replace(kerjakanUrl);
                            }
                        }).catch(() => {});
                }

                window.addEventListener('pageshow', function(event) {
                    let isBackNav = event.persisted;
                    try {
                        const navEntries = performance.getEntriesByType('navigation');
                        if (navEntries && navEntries.length > 0 && navEntries[0].type === 'back_forward') isBackNav = true;
                    } catch (e) {}
                    if (isBackNav) checkAndRouteLoop();
                });
            }
        })();
    </script>
</body>
</html>
