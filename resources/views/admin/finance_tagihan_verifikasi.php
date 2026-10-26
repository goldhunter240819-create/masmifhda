<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Tagihan Resmi - <?php echo htmlspecialchars($siswa['nama']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: radial-gradient(circle at 50% 0%, #f0fdf4 0%, #f1f5f9 60%, #e2e8f0 100%);
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
            max-width: 600px;
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
            background: linear-gradient(90deg, #0ea5e9 0%, #0284c7 50%, #075985 100%);
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
            font-size: 1.6rem;
            font-weight: 700;
            color: #064e3b;
            margin: 0 0 5px 0;
            text-transform: uppercase;
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

        /* Highlight box for nominal */
        .nominal-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            border-radius: 16px;
            padding: 22px 20px;
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-align: center;
        }

        .nominal-box.lunas {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
        }

        .nominal-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nominal-label.lunas {
            color: #166534;
        }

        .nominal-val {
            font-size: 1.8rem;
            font-weight: 800;
            color: #991b1b;
        }

        .nominal-val.lunas {
            color: #166534;
        }

        /* Detail List */
        .detail-list {
            text-align: left;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
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
        }

        .detail-val {
            flex: 1;
            font-size: 0.92rem;
            color: #0f172a;
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }

        /* Table inside modal */
        .verif-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 0.8rem;
            text-align: left;
        }

        .verif-table th {
            background: #f1f5f9;
            color: #475569;
            padding: 8px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        .verif-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .verif-table tr.total-row {
            font-weight: bold;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            color: #0f172a;
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
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.4;
        }

        .seal-box i {
            color: #0284c7;
            flex-shrink: 0;
        }

        .close-btn {
            display: inline-block;
            margin-top: 25px;
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
            width: 100%;
            box-sizing: border-box;
        }

        .close-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.3);
            filter: brightness(0.95);
        }

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
        <i data-lucide="shield-check"></i>
        Dokumen Tagihan Resmi & Sah
    </div>

    <!-- Header info -->
    <h1 class="school-title"><?php echo htmlspecialchars($institusi['nama'] ?? 'MI MIFTAHUL HUDA'); ?></h1>
    <div class="school-subtitle">Status Terverifikasi Sistem</div>
    
    <div class="divider"></div>

    <!-- Details -->
    <div class="detail-list">
        <div class="detail-item">
            <span class="detail-label">Nama Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['nama']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">NIS Siswa</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['nis']); ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Kelas</span>
            <span class="detail-val"><?php echo htmlspecialchars($siswa['kelas']); ?></span>
        </div>
    </div>

    <div class="divider"></div>

    <!-- Table Details -->
    <h4 style="text-align: left; margin: 10px 0 5px 0; color: #334155; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Status Tagihan</h4>
    <table class="verif-table">
        <thead>
            <tr>
                <th>Jenis Tagihan</th>
                <th style="text-align: right;">Tagihan</th>
                <th style="text-align: right;">Terbayar</th>
                <th style="text-align: right;">Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tagihanList as $t): ?>
            <tr>
                <td style="font-weight: 600;"><?php echo htmlspecialchars($t['nama_tagihan']); ?></td>
                <td style="text-align: right;">Rp <?php echo number_format($t['jumlah_tagihan'], 0, ',', '.'); ?></td>
                <td style="text-align: right; color: #16a34a;">Rp <?php echo number_format($t['terbayar'], 0, ',', '.'); ?></td>
                <td style="text-align: right; font-weight: 700; color: <?php echo $t['sisa'] > 0 ? '#dc2626' : '#16a34a'; ?>;">
                    Rp <?php echo number_format($t['sisa'], 0, ',', '.'); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td>TOTAL</td>
                <td style="text-align: right;">Rp <?php echo number_format($totalTagihan, 0, ',', '.'); ?></td>
                <td style="text-align: right; color: #16a34a;">Rp <?php echo number_format($totalTerbayar, 0, ',', '.'); ?></td>
                <td style="text-align: right; color: <?php echo $totalSisa > 0 ? '#dc2626' : '#16a34a'; ?>;">Rp <?php echo number_format($totalSisa, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Nominal Box -->
    <div class="nominal-box <?php echo $totalSisa <= 0 ? 'lunas' : ''; ?>">
        <span class="nominal-label <?php echo $totalSisa <= 0 ? 'lunas' : ''; ?>">
            <?php echo $totalSisa <= 0 ? 'Status Tagihan' : 'Sisa Tagihan yang Harus Dibayar'; ?>
        </span>
        <span class="nominal-val <?php echo $totalSisa <= 0 ? 'lunas' : ''; ?>">
            <?php echo $totalSisa <= 0 ? 'LUNAS (Rp 0)' : 'Rp ' . number_format($totalSisa, 0, ',', '.') . ',-'; ?>
        </span>
    </div>

    <!-- Official Seal Box -->
    <div class="seal-box">
        <i data-lucide="shield-alert"></i>
        <span>Lembar verifikasi digital ini diterbitkan secara otomatis oleh sistem administrasi keuangan resmi MI Miftahul Huda Mathla'ul Anwar.</span>
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


