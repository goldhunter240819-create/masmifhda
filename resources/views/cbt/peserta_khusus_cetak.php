<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Peserta CBT Kelas Jauh - MAS Miftahul Huda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }
        .page {
            max-width: 900px;
            margin: 0 auto;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .card-peserta {
            background: #ffffff;
            border: 2px solid #0284c7;
            border-radius: 12px;
            padding: 14px 16px;
            box-sizing: border-box;
            page-break-inside: avoid;
            position: relative;
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .card-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }
        .card-title h3 {
            margin: 0;
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 800;
        }
        .card-title p {
            margin: 2px 0 0 0;
            font-size: 0.68rem;
            color: #0284c7;
            font-weight: 700;
            text-transform: uppercase;
        }
        .card-body table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
        }
        .card-body td {
            padding: 3px 0;
            vertical-align: top;
        }
        .cred-box {
            background: #f0fdf4;
            border: 1px dashed #16a34a;
            border-radius: 8px;
            padding: 6px 10px;
            margin-top: 8px;
            font-size: 0.75rem;
            display: flex;
            justify-content: space-between;
        }
        .card-footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 0.68rem;
            color: #64748b;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .card-peserta {
                border-color: #333;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 900px; margin: 0 auto 15px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; font-size: 1.2rem;">Kartu Peserta CBT Kelas Jauh</h2>
            <p style="margin: 2px 0 0 0; font-size: 0.8rem; color: #64748b;">Total: <?php echo count($pesertaList); ?> Siswa</p>
        </div>
        <button onclick="window.print()" style="padding: 8px 18px; border-radius: 8px; background: #0284c7; color: white; border: none; font-weight: 700; cursor: pointer;">
            🖨️ Cetak Kartu (Print)
        </button>
    </div>

    <div class="page">
        <div class="cards-grid">
            <?php foreach($pesertaList as $p): ?>
            <div class="card-peserta">
                <div class="card-header">
                    <img src="<?php echo \App\Core\Branding::getInstitusiLogo(); ?>" alt="Logo" class="card-logo">
                    <div class="card-title">
                        <h3>MAS MIFTAHUL HUDA</h3>
                        <p>KARTU PESERTA CBT ONLINE (KELAS JAUH)</p>
                    </div>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td style="width: 85px; color: #64748b;">No. Peserta</td>
                            <td style="width: 10px;">:</td>
                            <td><strong style="color: #0284c7; font-family: monospace; font-size: 0.88rem;"><?php echo htmlspecialchars($p['no_peserta']); ?></strong></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">Nama Siswa</td>
                            <td>:</td>
                            <td><strong><?php echo htmlspecialchars($p['nama_siswa']); ?></strong></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">Tingkat / Rombel</td>
                            <td>:</td>
                            <td><?php echo htmlspecialchars($p['tingkat']); ?> (<?php echo htmlspecialchars($p['kelas_nama']); ?>)</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">Lokasi Ujian</td>
                            <td>:</td>
                            <td><?php echo htmlspecialchars($p['lokasi_cabang'] ?: 'Kelas Jauh'); ?></td>
                        </tr>
                    </table>

                    <div class="cred-box">
                        <div>
                            User: <strong style="font-family: monospace; color: #0f172a;"><?php echo htmlspecialchars($p['username']); ?></strong>
                        </div>
                        <div>
                            Pass: <strong style="font-family: monospace; color: #15803d;"><?php echo htmlspecialchars($p['password_plain'] ?: '123456'); ?></strong>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div>
                        Portal: <b>apk.masmifhda.sch.id</b>
                    </div>
                    <div style="text-align: right;">
                        Proktor Ujian,<br><br>
                        ( .................................... )
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
