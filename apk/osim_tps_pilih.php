<?php
require_once 'koneksi.php';

// Validasi Akses
if (!isset($_SESSION['evoting_siswa_id'])) {
    header("Location: osim_tps_scanner.php");
    exit;
}

$siswa_nama = $_SESSION['evoting_siswa_nama'];

$kandidat = $core_db->query("SELECT * FROM osim_kandidat WHERE status = 'aktif' ORDER BY nomor_urut ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bilik Suara OSIM</title>
    <link rel="stylesheet" href="../public/assets/css/plus-jakarta-sans.css">
    <script src="../public/assets/js/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0; padding: 0;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header h1 { margin: 0; font-size: 1.5rem; font-weight: 900; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; color: #bfdbfe; font-size: 0.9rem; }
        
        .container {
            flex: 1;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .welcome-badge {
            background: #fff;
            padding: 15px 20px;
            border-radius: 16px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid #3b82f6;
        }
        .welcome-badge h2 { margin: 0; color: #1e293b; font-size: 1.2rem; }
        .welcome-badge p { margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem; }

        .kandidat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .kandidat-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
        }
        .kandidat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #bfdbfe;
        }

        .nomor-urut {
            position: absolute;
            top: -15px; left: 50%;
            transform: translateX(-50%);
            background: #3b82f6;
            color: white;
            font-size: 1.5rem;
            font-weight: 900;
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            border: 4px solid #fff;
            z-index: 10;
        }

        .kandidat-foto {
            width: 100%;
            height: 250px;
            background: #e2e8f0;
            object-fit: cover;
            border-bottom: 1px solid #f1f5f9;
        }

        .kandidat-info {
            padding: 30px 20px 20px 20px;
            text-align: center;
            flex: 1;
        }
        .kandidat-nama {
            font-size: 1.2rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .kandidat-wakil {
            font-size: 0.95rem;
            color: #64748b;
            font-weight: 700;
        }
        
        .kandidat-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kandidat-btn:hover { background: #047857; }
        .kandidat-btn:active { transform: scale(0.98); }

        .btn-detail {
            background: none;
            border: none;
            color: #3b82f6;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: underline;
        }
        
        /* Modal Visi Misi */
        .modal-overlay {
            position: fixed; top:0; left:0; right:0; bottom:0;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            z-index: 999;
            display: none;
            align-items: center; justify-content: center;
            padding: 20px;
        }
        .modal-content {
            background: #fff;
            border-radius: 24px;
            width: 100%; max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        .modal-close {
            position: absolute; top: 15px; right: 15px;
            background: #f1f5f9; border: none;
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #64748b;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>E-VOTING OSIM</h1>
    <p>Gunakan hak pilih Anda dengan bijak</p>
</div>

<div class="container">
    <div class="welcome-badge">
        <h2>Selamat Datang, <?= htmlspecialchars($siswa_nama) ?></h2>
        <p>Silakan pilih salah satu kandidat di bawah ini.</p>
    </div>

    <div class="kandidat-grid">
        <?php foreach($kandidat as $k): ?>
        <div class="kandidat-card">
            <div class="nomor-urut"><?= $k['nomor_urut'] ?></div>
            
            <?php if($k['foto']): ?>
                <img src="../public/uploads/osim/<?= $k['foto'] ?>" class="kandidat-foto" alt="Paslon <?= $k['nomor_urut'] ?>">
            <?php else: ?>
                <div class="kandidat-foto" style="display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="users" style="width: 80px; height: 80px; color: #cbd5e1;"></i>
                </div>
            <?php endif; ?>
            
            <div class="kandidat-info">
                <div class="kandidat-nama"><?= htmlspecialchars($k['nama_ketua']) ?></div>
                <div class="kandidat-wakil">& <?= htmlspecialchars($k['nama_wakil']) ?></div>
                <button type="button" class="btn-detail" onclick='lihatVisiMisi(<?= json_encode($k['visi']) ?>, <?= json_encode($k['misi']) ?>, <?= $k['nomor_urut'] ?>)'>
                    Lihat Visi & Misi
                </button>
            </div>
            
            <button type="button" class="kandidat-btn" onclick="pilihKandidat(<?= $k['id'] ?>, <?= $k['nomor_urut'] ?>)">
                <i data-lucide="check-circle" style="vertical-align: -5px; margin-right: 5px;"></i> COBLOS
            </button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Visi Misi -->
<div class="modal-overlay" id="visimisiModal">
    <div class="modal-content">
        <button class="modal-close" onclick="document.getElementById('visimisiModal').style.display='none'"><i data-lucide="x"></i></button>
        <div style="padding: 25px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-top-left-radius: 24px; border-top-right-radius: 24px;">
            <h3 style="margin:0; color:#1e293b;" id="vmTitle">Visi & Misi</h3>
        </div>
        <div style="padding: 25px;">
            <div style="font-weight: 900; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 10px;">Visi</div>
            <div style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;" id="vmVisi"></div>
            
            <div style="font-weight: 900; color: #3b82f6; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 10px;">Misi</div>
            <div style="color: #475569; font-size: 0.95rem; line-height: 1.6;" id="vmMisi"></div>
        </div>
    </div>
</div>

<script>
lucide.createIcons();

function lihatVisiMisi(visi, misi, nomor) {
    document.getElementById('vmTitle').innerText = 'Visi & Misi Paslon ' + nomor;
    document.getElementById('vmVisi').innerHTML = visi.replace(/\n/g, '<br>');
    document.getElementById('vmMisi').innerHTML = misi.replace(/\n/g, '<br>');
    document.getElementById('visimisiModal').style.display = 'flex';
}

function pilihKandidat(id, nomor) {
    Swal.fire({
        title: 'Konfirmasi Pilihan',
        html: `Apakah Anda yakin ingin memilih <b>Paslon Nomor ${nomor}</b>?<br><br><small style="color:#ef4444;">Pilihan yang sudah disimpan tidak dapat diubah!</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Ya, Coblos!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menyimpan Suara...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Prepare form data
            const formData = new FormData();
            formData.append('kandidat_id', id);

            fetch('api_tps.php?action=vote', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terima Kasih!',
                        text: 'Suara Anda telah berhasil disimpan di Kotak Suara.',
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'osim_tps_scanner.php';
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error').then(() => {
                        if (data.message.includes('sudah memilih')) {
                            window.location.href = 'osim_tps_scanner.php';
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            });
        }
    });
}
</script>
</body>
</html>
