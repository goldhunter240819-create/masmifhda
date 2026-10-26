<?php
// Tampilan Verifikasi SK Publik
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Keputusan - MIS MIFHDA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #334155;
        }
        .verify-card {
            background: #ffffff;
            width: 100%;
            max-width: 480px;
            border-radius: 20px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px;
        }
        .verify-header {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }
        .verify-header .logo {
            width: 60px;
            height: 60px;
            background: #ffffff;
            border-radius: 12px;
            padding: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .verify-header h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }
        .verify-header p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            color: #94a3b8;
        }
        .status-box {
            text-align: center;
            padding: 30px 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }
        .status-icon.valid {
            background: rgba(14, 165, 233, 0.1);
            color: #0ea5e9;
        }
        .status-icon.invalid {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .status-icon.preview {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        .status-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0 0 5px;
        }
        .status-desc {
            font-size: 0.95rem;
            color: #64748b;
            margin: 0;
        }
        .status-valid-text { color: #0ea5e9; }
        .status-invalid-text { color: #ef4444; }
        .status-preview-text { color: #f59e0b; }

        .details-box {
            padding: 25px;
        }
        .detail-row {
            margin-bottom: 15px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-size: 0.8rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .detail-val {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
            word-wrap: break-word;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.85rem;
            color: #94a3b8;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>

<div class="verify-card">
    <div class="verify-header">
        <div class="logo">
            <i data-lucide="shield-check" style="width:40px;height:40px;color:#0ea5e9;"></i>
        </div>
        <h1>Portal Verifikasi Dokumen</h1>
        <p>Madrasah Ibtidaiyah Miftahul Huda</p>
    </div>

    <?php if ($is_valid): ?>
        <?php if ($is_preview): ?>
            <!-- Mode Preview -->
            <div class="status-box">
                <div class="status-icon preview">
                    <i data-lucide="eye" style="width:40px;height:40px;"></i>
                </div>
                <h2 class="status-title status-preview-text">Mode Pratinjau</h2>
                <p class="status-desc">QR Code ini masih dalam mode pratinjau (Draf belum disimpan di database).</p>
            </div>
            <div class="details-box">
                <div class="detail-row">
                    <div class="detail-label">Nomor Surat</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['nomor_sk'] ?? '---'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tentang</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['judul'] ?? '---'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal Ditetapkan</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['tanggal'] ?? '---'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Penandatangan</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['penandatangan'] ?? '---'); ?><br><span style="font-size:0.85em;color:#64748b;"><?php echo htmlspecialchars($sk['jabatan_ttd'] ?? '---'); ?></span></div>
                </div>
            </div>
        <?php else: ?>
            <!-- Valid SK -->
            <div class="status-box">
                <div class="status-icon valid">
                    <i data-lucide="check-circle-2" style="width:45px;height:45px;"></i>
                </div>
                <h2 class="status-title status-valid-text">Dokumen Resmi & Valid</h2>
                <p class="status-desc">Surat Keputusan ini diterbitkan secara sah oleh institusi kami dan tercatat di pangkalan data.</p>
            </div>
            <div class="details-box">
                <div class="detail-row">
                    <div class="detail-label">Nomor SK</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['nomor_sk'] ?? '-'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Perihal / Tentang</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['judul']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tahun Pelajaran</div>
                    <div class="detail-val"><?php echo htmlspecialchars($sk['tahun'] ?? '-'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal Ditetapkan</div>
                    <div class="detail-val">
                        <?php 
                            $bulanIndo = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
                            $dateStr = date('d F Y', strtotime($sk['tanggal']));
                            echo strtr($dateStr, $bulanIndo); 
                        ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Penandatangan</div>
                    <div class="detail-val">
                        <?php echo htmlspecialchars($sk['penandatangan']); ?><br>
                        <span style="font-size:0.85em;color:#64748b;"><?php echo htmlspecialchars($sk['jabatan_ttd']); ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Invalid SK -->
        <div class="status-box" style="border-bottom:none;">
            <div class="status-icon invalid">
                <i data-lucide="x-octagon" style="width:45px;height:45px;"></i>
            </div>
            <h2 class="status-title status-invalid-text">Dokumen Tidak Ditemukan</h2>
            <p class="status-desc" style="margin-bottom:20px;">QR Code tidak valid atau dokumen mungkin telah dipalsukan. Dokumen ini tidak tercatat dalam database resmi kami.</p>
            <a href="<?php echo base_url(''); ?>" style="display:inline-block;padding:10px 20px;background:#ef4444;color:#fff;text-decoration:none;border-radius:10px;font-weight:600;font-size:0.9rem;">Kembali ke Beranda</a>
        </div>
    <?php endif; ?>

    <div style="text-align:center; padding: 0 20px 20px;">
        <button onclick="window.close()" style="background:#0f172a; color:#fff; border:none; padding:12px 24px; border-radius:10px; font-weight:600; cursor:pointer; font-size:0.95rem; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 15px rgba(0,0,0,0.15)';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 10px rgba(0,0,0,0.1)';">
            <i data-lucide="x-circle" style="width:18px;height:18px;"></i> Tutup Halaman
        </button>
    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> MIS MIFHDA. Sistem Informasi Akademik.
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>

