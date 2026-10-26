<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kwitansi Resmi - MI Miftahul Huda</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: radial-gradient(circle at 50% 0%, #f0fdf4 0%, #e8f5e9 50%, #d1e7dd 100%);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 24px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 25px 50px -12px rgba(6, 95, 70, 0.12);
            padding: 35px 30px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .verify-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 50%, #34d399 100%);
        }

        /* Badge Status */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f0f9ff;
            border: 1.5px solid #bae6fd;
            color: #075985;
            padding: 8px 20px;
            border-radius: 100px;
            font-size: 0.88rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
            animation: pulse-badge 2s infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 0 0 0px rgba(14, 165, 233, 0.2); }
            50% { box-shadow: 0 0 0 8px rgba(14, 165, 233, 0.05); }
        }

        .status-badge i {
            color: #0ea5e9;
        }

        .school-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #064e3b;
            margin: 0 0 5px 0;
        }

        .school-subtitle {
            font-size: 0.8rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
        }

        /* Detail List */
        .detail-list {
            text-align: left;
            margin-bottom: 30px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dashed #cbd5e1;
            gap: 15px;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            flex: 0 0 35%;
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
            margin-top: 3px;
        }

        .detail-val {
            flex: 1;
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }

        /* Highlight box for nominal */
        .nominal-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 25px 20px;
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
        }

        .nominal-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: #075985;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nominal-val {
            font-size: 1.8rem;
            font-weight: 800;
            color: #064e3b;
            word-break: break-word;
        }

        /* Seal section */
        .seal-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 25px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .seal-box i {
            color: #0284c7;
        }

        .close-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
            filter: brightness(0.95);
        }

        /* Footer copyright */
        .footer-credit {
            margin-top: 25px;
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <!-- Status Badge -->
    <div class="status-badge">
        <i data-lucide="check-circle-2"></i>
        Kwitansi Terverifikasi & Sah
    </div>

    <!-- Header info -->
    <h1 class="school-title">MI Miftahul Huda</h1>
    <div class="school-subtitle">Mathla'ul Anwar</div>
    
    <div class="divider"></div>

    <!-- Nominal -->
    <div class="nominal-box">
        <span class="nominal-label">Jumlah Pembayaran</span>
        <span class="nominal-val">Rp <?php echo number_format($p['jumlah'], 0, ',', '.'); ?>,-</span>
    </div>

    <!-- Details -->
    <div class="detail-list">
        <div class="detail-item">
            <span class="detail-label">No. Kwitansi</span>
            <span class="detail-val" style="font-family: monospace; font-size:1.05rem;">KW-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nama Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($p['nama']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">NIS Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($p['nis']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Kelas</span>
            <span class="detail-val"><?php echo htmlspecialchars($p['kelas']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Untuk Pembayaran</span>
            <span class="detail-val"><?php echo htmlspecialchars($p['jenis_pembayaran']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Tanggal Bayar</span>
            <span class="detail-val"><?php echo date('d-m-Y', strtotime($p['tanggal_bayar'])); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Penerima (Bendahara)</span>
            <span class="detail-val">
                <?php echo htmlspecialchars($adminNama); ?>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status Tagihan</span>
            <span class="detail-val" style="color: #0284c7;"><?php echo !empty($p['status_tagihan']) ? htmlspecialchars(strtoupper($p['status_tagihan'])) : 'LUNAS'; ?></span>
        </div>
    </div>

    <!-- Official Seal Box -->
    <div class="seal-box">
        <i data-lucide="shield-check"></i>
        <span>Sistem Verifikasi Pembayaran Digital MI Miftahul Huda Mathla'ul Anwar</span>
    </div>

    <button onclick="window.close()" class="close-btn">Tutup Halaman</button>

    <div class="footer-credit">
        Powered by Aula System Platform &copy; <?php echo date('Y'); ?>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


