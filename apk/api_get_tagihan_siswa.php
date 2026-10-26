<?php
require_once 'koneksi.php';

if (!isset($_SESSION['guru_id'])) {
    echo "Unauthorized";
    exit;
}

$siswa_id = isset($_GET['siswa_id']) ? intval($_GET['siswa_id']) : 0;
if ($siswa_id <= 0) exit;

try {
    // Ambil tagihan
    $stmt_t = $core_db->prepare("SELECT t.*, (SELECT COALESCE(SUM(jumlah), 0) FROM keuangan_komite_pembayaran p WHERE p.siswa_id = t.siswa_id AND p.jenis_pembayaran = t.nama_tagihan) as jumlah_terbayar FROM keuangan_tagihan t WHERE t.siswa_id = :siswa_id ORDER BY t.jatuh_tempo ASC");
    $stmt_t->execute(['siswa_id' => $siswa_id]);
    $tagihans = $stmt_t->fetchAll(PDO::FETCH_ASSOC);
    
    // Cek Akses Pembayaran spesifik untuk Guru
    $allowed_tagihan = [];
    $is_restricted = false;
    
    if (isset($_SESSION['guru_id'])) {
        $gid = $_SESSION['guru_id'];
        $akses = $core_db->query("SELECT akses_pembayaran FROM keuangan_akses WHERE guru_id = $gid")->fetch();
        if ($akses && !empty($akses['akses_pembayaran']) && $akses['akses_pembayaran'] !== '[]') {
            $is_restricted = true;
            $allowed_tagihan = json_decode($akses['akses_pembayaran'], true) ?: [];
        } else {
            // Check if they are full admin (Super Admin)
            if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])) {
                $is_restricted = false;
            } else {
                // If they are not super admin, and have no specific access, they shouldn't see anything?
                // Wait, if they are just an App Admin but have no spesifik access, maybe they should see all?
                // Let's check app_admins.
                $uid = $_SESSION['user_id'] ?? 0;
                $is_app_admin = $core_db->query("SELECT id FROM app_admins WHERE user_id = $uid AND app_code = 'keuangan'")->fetch();
                if (!$is_app_admin) {
                    $is_restricted = true; // Not an app admin and no specific access
                }
            }
        }
    }
    
    // Cek can_delete
    $can_delete = false;
    if (isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [1, 99])) {
        $can_delete = true;
    } else {
        if (isset($_SESSION['user_id'])) {
            $is_app_admin = $core_db->prepare("SELECT id FROM app_admins WHERE user_id = ? AND app_code = 'keuangan'");
            $is_app_admin->execute([$_SESSION['user_id']]);
            if ($is_app_admin->fetch()) $can_delete = true;
        }
        if (!$can_delete && isset($_SESSION['guru_id'])) {
            $cekDel = $core_db->prepare("SELECT can_delete FROM keuangan_akses WHERE guru_id = ?");
            $cekDel->execute([$_SESSION['guru_id']]);
            $adm = $cekDel->fetch();
            if ($adm && $adm['can_delete'] == 1) $can_delete = true;
        }
    }
    
    // Lakukan filter
    $filtered_tagihan = [];
    foreach ($tagihans as $t) {
        if ($is_restricted) {
            if (!in_array($t['nama_tagihan'], $allowed_tagihan)) {
                continue;
            }
        }
        $filtered_tagihan[] = $t;
    }
    $tagihans = $filtered_tagihan;
    
    if (count($tagihans) === 0) {
        echo '<div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; color: #64748b;"><i class="fa-solid fa-check-circle" style="font-size:2rem; color:#10b981; margin-bottom:10px;"></i><br>Tidak ada tagihan (yang Anda kelola) untuk siswa ini.</div>';
        exit;
    }
    
    echo '<h3 class="section-title">Daftar Tagihan Siswa</h3>';
    echo '<div style="display:flex; flex-direction:column; gap:12px;">';
    
    foreach ($tagihans as $t) {
        $sisa = $t['jumlah_tagihan'] - $t['jumlah_terbayar'];
        $lunas = ($sisa <= 0);
        $status_color = $lunas ? '#10b981' : '#ef4444';
        $status_bg = $lunas ? '#ecfdf5' : '#fee2e2';
        $status_text = $lunas ? 'Lunas' : 'Belum Lunas';
        if (!$lunas && $t['jumlah_terbayar'] > 0) {
            $status_color = '#f59e0b';
            $status_bg = '#fffbeb';
            $status_text = 'Menyicil';
        }
        
        echo '
        <div style="background: white; border: 1px solid #e2e8f0; border-left: 4px solid '.$status_color.'; border-radius: 8px; padding: 15px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                <div>
                    <div style="font-weight:bold; color:#1e293b; font-size:1rem; margin-bottom:3px;">'.htmlspecialchars($t['nama_tagihan']).'</div>
                    <div style="font-size:0.8rem; color:#64748b;"><i class="fa-regular fa-calendar"></i> Jatuh Tempo: '.date('d M Y', strtotime($t['jatuh_tempo'])).'</div>
                </div>
                <div style="background:'.$status_bg.'; color:'.$status_color.'; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:bold;">
                    '.$status_text.'
                </div>
            </div>
            
            <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:0.85rem;">
                <span style="color:#64748b;">Total Tagihan:</span>
                <span style="font-weight:600;">Rp '.number_format($t['jumlah_tagihan'],0,',','.').'</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:0.85rem;">
                <span style="color:#64748b;">Sudah Dibayar:</span>
                <span style="font-weight:600; color:#10b981;">Rp '.number_format($t['jumlah_terbayar'],0,',','.').'</span>
            </div>
            ';
            
            if (!$lunas) {
                echo '
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #cbd5e1; padding-top:10px;">
                    <div style="font-weight:bold; color:#ef4444; font-size:1rem;">
                        Sisa: Rp '.number_format($sisa,0,',','.').'
                    </div>
                    <button onclick="bukaBayar('.$siswa_id.', '.$t['id'].', \''.addslashes($t['nama_tagihan']).'\', '.$sisa.')" style="background:#10b981; color:white; border:none; padding:8px 15px; border-radius:6px; font-size:0.85rem; font-weight:bold; cursor:pointer;">
                        <i class="fa-solid fa-money-bill-wave"></i> Bayar
                    </button>
                </div>
                ';
            }
            
        echo '</div>';
    }
    
    echo '</div>';

    // AMBIL RIWAYAT PEMBAYARAN SISWA INI (Sesuai dengan filter akses)
    $stmt_r = $core_db->prepare("SELECT * FROM keuangan_komite_pembayaran WHERE siswa_id = :siswa_id ORDER BY id DESC");
    $stmt_r->execute(['siswa_id' => $siswa_id]);
    $riwayats = $stmt_r->fetchAll(PDO::FETCH_ASSOC);

    $filtered_riwayat = [];
    foreach ($riwayats as $r) {
        if ($is_restricted) {
            if (!in_array($r['jenis_pembayaran'], $allowed_tagihan)) {
                continue;
            }
        }
        $filtered_riwayat[] = $r;
    }

    echo '<h3 class="section-title" style="margin-top:25px;">Riwayat Pembayaran Terbaru</h3>';
    if (count($filtered_riwayat) === 0) {
        echo '<div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: center; color: #64748b;">Belum ada riwayat pembayaran.</div>';
    } else {
        echo '<div style="display:flex; flex-direction:column; gap:10px;">';
        foreach ($filtered_riwayat as $r) {
            echo '
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div>
                        <div style="font-weight:bold; color:#1e293b; font-size:0.95rem;">'.htmlspecialchars($r['jenis_pembayaran']).'</div>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:3px;"><i class="fa-regular fa-calendar"></i> '.date('d M Y', strtotime($r['tanggal_bayar'])).'</div>
                    </div>
                    <div style="font-weight:bold; color:#10b981; font-size:1.05rem; text-align:right;">
                        + Rp '.number_format($r['jumlah'],0,',','.').'
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:8px; padding-top:12px; border-top:1px dashed #e2e8f0;">
                    <button onclick="bukaKwitansi('.$r['id'].')" style="background:#f0f9ff; color:#0284c7; border:none; padding:6px 12px; border-radius:6px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:0.2s;"><i class="fa-solid fa-print"></i> Kwitansi</button>';
                    
                if ($can_delete) {
                    echo '<button onclick="hapusPembayaran('.$r['id'].')" style="background:#fef2f2; color:#ef4444; border:none; padding:6px 12px; border-radius:6px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:0.2s;"><i class="fa-solid fa-trash"></i> Hapus</button>';
                }
                
            echo '
                </div>
            </div>';
        }
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo "Error memuat tagihan: " . $e->getMessage();
}
