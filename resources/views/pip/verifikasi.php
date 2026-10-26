<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen PIP - MAS Miftahul Huda</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background Orbs */
        .orb {
            position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.5;
            animation: float 10s infinite alternate ease-in-out;
        }
        .orb-1 { width: 300px; height: 300px; background: #0ea5e9; top: -100px; left: -100px; }
        .orb-2 { width: 400px; height: 400px; background: #38bdf8; bottom: -150px; right: -150px; animation-delay: -5s; }
        
        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(50px) scale(1.1); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative;
            z-index: 10;
            text-align: center;
        }

        .icon-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
        }

        .valid .icon-circle { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: #fff; }
        .invalid .icon-circle { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; }

        .status-title { font-size: 2rem; font-weight: 800; margin-bottom: 5px; letter-spacing: 1px; }
        .valid .status-title { color: #4ade80; text-shadow: 0 0 20px rgba(74,222,128,0.4); }
        .invalid .status-title { color: #f87171; text-shadow: 0 0 20px rgba(248,113,113,0.4); }

        .status-subtitle { font-size: 1rem; color: #94a3b8; margin-bottom: 30px; }

        .detail-box {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            text-align: left;
            margin-bottom: 30px;
        }

        .detail-item { margin-bottom: 15px; }
        .detail-item:last-child { margin-bottom: 0; }
        .detail-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
        .detail-value { font-size: 1.1rem; font-weight: 600; color: #f1f5f9; }

        .btn-home {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; padding: 16px; border-radius: 16px;
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);
            color: #fff; font-size: 1rem; font-weight: 700; text-decoration: none;
            transition: all 0.3s;
        }
        .btn-home:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }

        .footer { margin-top: 25px; font-size: 0.8rem; color: #64748b; }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="glass-card <?php echo $isValid ? 'valid' : 'invalid'; ?>">
        
        <?php if($isValid): ?>
            <div class="icon-circle">
                <i data-lucide="check-circle" style="width: 48px; height: 48px;"></i>
            </div>
            <h1 class="status-title">DOKUMEN VALID</h1>
            <p class="status-subtitle">Surat Pernyataan Persetujuan Orang Tua PIP tercatat resmi di database SIAKAD MAS Miftahul Huda.</p>

            <div class="detail-box">
                <div class="detail-item">
                    <div class="detail-label">Nama Siswa</div>
                    <div class="detail-value"><?php echo htmlspecialchars($student['NAMA_SISWA'] ?? $student['NAMA']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">NISN / Kelas</div>
                    <div class="detail-value"><?php echo htmlspecialchars($student['NISN']); ?> - Kelas <?php echo htmlspecialchars($student['KELAS'] ?? '-'); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Waktu Persetujuan</div>
                    <div class="detail-value"><?php echo date('d M Y, H:i', strtotime($student['confirmed_at'])); ?> WIB</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">ID Registrasi</div>
                    <div class="detail-value" style="font-family: monospace; color: #38bdf8;"><?php echo htmlspecialchars($student['NIK']); ?></div>
                </div>
            </div>

        <?php else: ?>
            <div class="icon-circle">
                <i data-lucide="x-circle" style="width: 48px; height: 48px;"></i>
            </div>
            <h1 class="status-title">TIDAK DITEMUKAN</h1>
            <p class="status-subtitle">Maaf, dokumen dengan ID registrasi tersebut tidak ditemukan atau belum disetujui di dalam sistem SIAKAD kami.</p>
        <?php endif; ?>

        <a href="https://masmifhda.sch.id" class="btn-home">
            <i data-lucide="globe"></i> Website Resmi Madrasah
        </a>

        <div class="footer">
            Sistem Informasi Akademik &bull; MAS Miftahul Huda
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
