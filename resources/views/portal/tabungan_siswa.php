<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tabungan Digital - <?= htmlspecialchars($siswa['nama'] ?? 'Siswa') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            padding: 30px 20px;
            position: relative;
            overflow-x: hidden;
        }
        .bg-glow-1 {
            position: absolute; top: -100px; left: 10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%);
            z-index: 0; pointer-events: none;
        }
        .bg-glow-2 {
            position: absolute; bottom: -100px; right: 10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(14,165,233,0.18) 0%, transparent 70%);
            z-index: 0; pointer-events: none;
        }
        .container {
            max-width: 860px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 28px;
        }
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            color: #cbd5e1; text-decoration: none; font-weight: 600; font-size: 0.9rem;
            background: rgba(255,255,255,0.06); padding: 10px 18px; border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.1); transition: 0.25s;
        }
        .back-btn:hover {
            color: #fff; background: rgba(255,255,255,0.12);
        }
        /* ATM VIP Card */
        .atm-card {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #0f766e 100%);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 28px;
            padding: 36px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.25);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .atm-card::after {
            content: '';
            position: absolute; right: -50px; bottom: -50px; width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .atm-top {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 35px;
        }
        .bank-badge {
            font-family: 'Outfit', sans-serif;
            font-weight: 800; font-size: 1.1rem; letter-spacing: 1px;
            color: #fff;
            display: flex; align-items: center; gap: 10px;
        }
        .status-badge {
            background: rgba(16,185,129,0.25);
            border: 1px solid rgba(52,211,153,0.4);
            color: #6ee7b7;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .account-num {
            font-family: monospace;
            font-size: 1.35rem;
            letter-spacing: 3px;
            color: #99f6e4;
            margin-bottom: 24px;
            font-weight: 700;
        }
        .atm-bottom {
            display: flex; justify-content: space-between; align-items: flex-end;
            flex-wrap: wrap; gap: 20px;
        }
        .holder-label {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: #99f6e4; margin-bottom: 4px; font-weight: 600;
        }
        .holder-name {
            font-size: 1.3rem; font-weight: 800; color: #fff;
        }
        .holder-class {
            font-size: 0.85rem; color: #ccfbf1; font-weight: 500;
        }
        .balance-label {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: #99f6e4; margin-bottom: 4px; font-weight: 600; text-align: right;
        }
        .balance-value {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem; font-weight: 800; color: #fff; text-align: right;
        }
        /* Mutasi Panel */
        .panel {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 28px;
            backdrop-filter: blur(20px);
        }
        .panel-title {
            font-size: 1.15rem; font-weight: 800; color: #fff;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%; border-collapse: collapse;
        }
        th {
            text-align: left; padding: 12px 14px;
            color: #94a3b8; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        td {
            padding: 14px; border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 0.9rem; color: #e2e8f0;
        }
        tr:last-child td { border-bottom: none; }
        .pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700;
        }
        .pill-green { background: rgba(16,185,129,0.18); color: #34d399; }
        .pill-red { background: rgba(239,68,68,0.18); color: #f87171; }
        .empty-state {
            text-align: center; padding: 50px 20px; color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="container">
        <div class="top-bar">
            <a href="<?= base_url('portal') ?>" class="back-btn">
                <i data-lucide="arrow-left" style="width:18px;height:18px;"></i> Ke Portal Utama
            </a>
            <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">
                Tahun Ajaran <?= htmlspecialchars($_SESSION['active_year_name'] ?? '2026/2027') ?>
            </span>
        </div>

        <?php if ($tabungan): 
            $no_rekening = 'TBG-' . str_pad($siswa['id'], 5, '0', STR_PAD_LEFT);
        ?>
            <!-- KARTU TABUNGAN VIP -->
            <div class="atm-card">
                <div class="atm-top">
                    <div class="bank-badge">
                        <i data-lucide="shield-check" style="color: #34d399;"></i>
                        MASMIFHDA STUDENT BANK
                    </div>
                    <div class="status-badge">
                        <i data-lucide="check-circle-2" style="width:14px;height:14px;"></i> REKENING AKTIF
                    </div>
                </div>

                <div class="account-num"><?= $no_rekening ?></div>

                <div class="atm-bottom">
                    <div>
                        <div class="holder-label">Nama Nasabah</div>
                        <div class="holder-name"><?= htmlspecialchars($siswa['nama'] ?? 'Siswa') ?></div>
                        <div class="holder-class">NIS: <?= htmlspecialchars($siswa['nis'] ?? '-') ?> &bull; Kelas <?= htmlspecialchars($siswa['nama_kelas'] ?? '-') ?></div>
                    </div>
                    <div>
                        <div class="balance-label">Saldo Simpanan</div>
                        <div class="balance-value">Rp <?= number_format($tabungan['saldo'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <!-- TABEL RIWAYAT MUTASI -->
            <div class="panel">
                <div class="panel-title">
                    <i data-lucide="history" style="color:#38bdf8;"></i>
                    Riwayat Transaksi & Mutasi Tabungan
                </div>

                <?php if (empty($mutasi_list)): ?>
                    <div class="empty-state">
                        <i data-lucide="inbox" style="width:48px;height:48px;opacity:0.3;margin-bottom:12px;"></i>
                        <h4 style="color:#fff; margin-bottom:6px;">Belum Ada Transaksi</h4>
                        <p style="font-size: 0.85rem;">Semua riwayat setoran atau penarikan Anda akan tercatat secara otomatis di sini.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Jenis</th>
                                <th style="text-align: right;">Nominal</th>
                                <th>Keterangan</th>
                                <th style="text-align: right;">Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mutasi_list as $m): ?>
                            <tr>
                                <td style="color: #cbd5e1;">
                                    <?= date('d/m/Y', strtotime($m['tanggal'])) ?>
                                    <span style="display:block; font-size:0.75rem; color:#64748b;"><?= date('H:i', strtotime($m['tanggal'])) ?> WIB</span>
                                </td>
                                <td>
                                    <?php if ($m['jenis_mutasi'] == 'Setor'): ?>
                                        <span class="pill pill-green"><i data-lucide="arrow-down-left" style="width:12px;height:12px;"></i> Setor</span>
                                    <?php else: ?>
                                        <span class="pill pill-red"><i data-lucide="arrow-up-right" style="width:12px;height:12px;"></i> Tarik</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: <?= $m['jenis_mutasi']=='Setor' ? '#34d399' : '#f87171' ?>;">
                                    <?= $m['jenis_mutasi']=='Setor' ? '+' : '-' ?> Rp <?= number_format($m['jumlah'], 0, ',', '.') ?>
                                </td>
                                <td style="color:#cbd5e1;"><?= htmlspecialchars($m['keterangan'] ?: '-') ?></td>
                                <td style="text-align: right; font-weight: 700; color: #fff;">
                                    Rp <?= number_format($m['saldo_sesudah'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- STATUS BELUM BUKA REKENING -->
            <div class="panel" style="text-align: center; padding: 60px 30px;">
                <div style="width: 76px; height: 76px; border-radius: 24px; background: rgba(56,189,248,0.12); color: #38bdf8; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                    <i data-lucide="wallet-cards" style="width: 38px; height: 38px;"></i>
                </div>
                <h2 style="font-size: 1.6rem; font-weight: 800; color: #fff; margin-bottom: 12px;">
                    Belum Memiliki Rekening Tabungan
                </h2>
                <p style="color: #94a3b8; max-width: 480px; margin: 0 auto 30px auto; line-height: 1.6;">
                    Hai <?= htmlspecialchars($siswa['nama'] ?? 'Siswa') ?>, saat ini Anda belum mendaftar sebagai nasabah tabungan madrasah. Hubungi Wali Kelas atau Admin Tabungan untuk mengaktifkan rekening Anda.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 10px; background: rgba(255,255,255,0.06); padding: 12px 24px; border-radius: 100px; border: 1px solid rgba(255,255,255,0.1); color: #e2e8f0; font-size: 0.9rem; font-weight: 600;">
                    <i data-lucide="info" style="color: #38bdf8;"></i> Layanan tanpa biaya administrasi bulanan
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
