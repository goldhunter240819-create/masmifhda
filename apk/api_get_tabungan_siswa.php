<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    echo "Unauthorized";
    exit;
}

$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;
if ($siswa_id <= 0) exit;

try {
    $stmt = $core_db->prepare("SELECT id, saldo FROM keuangan_tabungan WHERE siswa_id = ?");
    $stmt->execute([$siswa_id]);
    $tabungan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tabungan) {
        echo '
        <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 25px; text-align: center; margin-bottom: 20px;">
            <i class="fa-solid fa-lock" style="font-size: 2.8rem; color: #f43f5e; margin-bottom: 12px;"></i>
            <h4 style="color: #881337; margin-bottom: 6px; font-size: 1.1rem;">Belum Memiliki Rekening</h4>
            <p style="color: #9f1239; font-size: 0.85rem; line-height: 1.5; margin: 0;">
                Siswa ini belum mendaftar rekening tabungan. Silakan aktifkan rekening terlebih dahulu di menu <b>Rekening Siswa</b> sebelum melakukan transaksi setor atau tarik tunai.
            </p>
        </div>
        ';
        exit;
    }

    $saldo = $tabungan['saldo'];
    
    echo '<h3 class="section-title">Informasi Tabungan</h3>';
    echo '
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; margin-bottom: 20px;">
        <i class="fa-solid fa-piggy-bank" style="font-size: 3rem; color: #f59e0b; margin-bottom: 15px;"></i>
        <div style="font-size: 0.9rem; color: #64748b; margin-bottom: 5px;">Total Saldo Aktif</div>
        <div style="font-size: 2rem; font-weight: 800; color: #1e293b;">Rp '.number_format($saldo,0,',','.').'</div>
    </div>
    ';
    
    echo '<h3 class="section-title">Aksi Cepat</h3>';
    echo '<div style="display:flex; gap:10px;">';
    
    echo '
        <button onclick="bukaMutasi(\'setor\', '.$siswa_id.', '.$saldo.')" style="flex: 1; background:#10b981; color:white; border:none; padding:15px; border-radius:12px; font-weight:bold; font-size:1rem; cursor:pointer; box-shadow: 0 4px 6px rgba(16,185,129,0.3);">
            <i class="fa-solid fa-arrow-down" style="font-size: 1.2rem; margin-bottom: 5px; display: block;"></i> Setor Tunai
        </button>
    ';
    
    echo '
        <button onclick="bukaMutasi(\'tarik\', '.$siswa_id.', '.$saldo.')" style="flex: 1; background:#ef4444; color:white; border:none; padding:15px; border-radius:12px; font-weight:bold; font-size:1rem; cursor:pointer; box-shadow: 0 4px 6px rgba(239,68,68,0.3);">
            <i class="fa-solid fa-arrow-up" style="font-size: 1.2rem; margin-bottom: 5px; display: block;"></i> Tarik Tunai
        </button>
    ';
    
    echo '</div>';
    
    // Riwayat Terakhir
    if ($tabungan) {
        $stmt_riwayat = $core_db->prepare("SELECT * FROM keuangan_tabungan_mutasi WHERE siswa_id = ? ORDER BY tanggal DESC, id DESC LIMIT 5");
        $stmt_riwayat->execute([$siswa_id]);
        $riwayat = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($riwayat) > 0) {
            echo '<h3 class="section-title" style="margin-top:25px;">Riwayat Mutasi Terakhir</h3>';
            echo '<div style="display:flex; flex-direction:column; gap:10px;">';
            foreach ($riwayat as $r) {
                $is_masuk = ($r['jenis_mutasi'] === 'Masuk' || $r['jenis_mutasi'] === 'Setor');
                $icon = $is_masuk ? 'fa-arrow-down' : 'fa-arrow-up';
                $color = $is_masuk ? '#10b981' : '#ef4444';
                $bg = $is_masuk ? '#ecfdf5' : '#fee2e2';
                $sign = $is_masuk ? '+' : '-';
                
                echo '
                <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: '.$bg.'; color: '.$color.'; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid '.$icon.'"></i>
                        </div>
                        <div>
                            <div style="font-weight: bold; font-size: 0.9rem; color: #1e293b;">'.$r['jenis_mutasi'].'</div>
                            <div style="font-size: 0.75rem; color: #64748b;">'.date('d M Y, H:i', strtotime($r['tanggal'])).' &bull; '.$r['keterangan'].'</div>
                        </div>
                    </div>
                    <div style="font-weight: bold; color: '.$color.'; font-size: 0.95rem;">
                        '.$sign.' Rp '.number_format($r['jumlah'],0,',','.').'
                    </div>
                </div>
                ';
            }
            echo '</div>';
        }
    }
    
} catch (Exception $e) {
    echo "Error memuat tabungan: " . $e->getMessage();
}
