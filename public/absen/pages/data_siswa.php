<?php
include '../includes/koneksi.php';
include '../includes/sync_handler.php';

// Auto Sync SIAKAD (Silent & Comprehensive)
syncSiakad($koneksi, 'siswa');

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$type = 'siswa';
$title_page = "Data Siswa Aktif";
$message = "";

// Hapus Data Satuan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id' AND tipe = '$type'");
    header("Location: data_siswa.php?msg=deleted");
    exit;
}

// Hapus Data Massal
if (isset($_POST['bulk_delete'])) {
    if (isset($_POST['ids']) && !empty($_POST['ids'])) {
        $ids = $_POST['ids'];
        $id_list = implode(',', array_map('intval', $ids));
        $query = "DELETE FROM users WHERE id IN ($id_list) AND tipe = '$type'";
        if (mysqli_query($koneksi, $query)) {
            $message = "success|" . count($ids) . " data siswa berhasil dihapus.";
        } else {
            $message = "error|Gagal menghapus data terpilih.";
        }
    }
}

// Filter Kelas
$selected_kelas = isset($_GET['kelas']) ? mysqli_real_escape_string($koneksi, $_GET['kelas']) : '';
$where_clause = "WHERE tipe = '$type'";
if ($selected_kelas) {
    $where_clause .= " AND kelas_mapel = '$selected_kelas'";
}

// Pagination & Limit
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Total Data (Filtered)
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users $where_clause");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Total Data (Global)
$global_total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE tipe = '$type'");
$global_total = mysqli_fetch_assoc($global_total_query)['total'];

$query = "SELECT * FROM users $where_clause ORDER BY nama ASC LIMIT $start, $limit";
$result = mysqli_query($koneksi, $query);

// Ambil List Kelas untuk Filter
$kelas_list = mysqli_query($koneksi, "SELECT DISTINCT kelas_mapel FROM users WHERE tipe = 'siswa' AND kelas_mapel != '' ORDER BY kelas_mapel ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_page; ?> - <?php echo $app_name; ?></title>
    <link href="/public/absen/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/absen/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="icon" href="<?php echo $app_favicon; ?>">
    <script src="/public/absen/assets/js/sweetalert2.all.min.js"></script>
    <script src="/public/assets/js/lucide.min.js"></script>
    <style>
        .check-column { width: 40px; text-align: center; }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        
        .siswa-photo-container {
            width: 54px; 
            height: 54px; 
            border-radius: 12px; 
            background: #f3f4f6; 
            overflow: hidden; 
            border: 2px solid white; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            flex-shrink: 0;
        }
        .siswa-photo-container img {
            width: 100%; 
            height: 100%; 
            object-fit: cover;
        }
        
        .badge-kelas {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(6, 95, 70, 0.05);
            color: #0ea5e9;
            font-weight: 600;
        }
        
        .parent-info {
            font-size: 0.8rem;
            color: #64748b;
        }
        .parent-info div {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include '../includes/sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <div>
                <h4 class="h-title mb-0"><?php echo $title_page; ?></h4>
                <p class="text-muted-pro small mb-0">Manajemen data peserta didik dan sinkronisasi otomatis SIAKAD.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btnBulkDelete" class="btn-pro btn-pro-outline text-danger border-danger border-opacity-25 py-1 px-3" style="display: none;" onclick="confirmBulkDelete()">
                    <i class="bi bi-trash3 me-1"></i> Hapus Terpilih (<span id="checkCount">0</span>)
                </button>
                <a href="../actions/sinkron_siakad.php?type=siswa" class="btn-pro btn-pro-outline text-success border-success border-opacity-25 py-1 px-3">
                    <i class="bi bi-arrow-repeat me-1"></i> Sinkron Sekarang
                </a>
                <a href="../actions/cetak.php?tipe=siswa" target="_blank" class="btn-pro btn-pro-outline py-1 px-3">
                    <i class="bi bi-printer"></i> Cetak Kartu Massal
                </a>
            </div>
        </header>

        <div class="content-padding">
            <!-- Filter Section -->
            <div class="card-pro mb-4 p-3 bg-light bg-opacity-50">
                <form action="" method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="small fw-bold text-muted">Filter Kelas:</label>
                    </div>
                    <div class="col-auto">
                        <select name="kelas" class="form-select form-select-sm border-0 shadow-sm" onchange="this.form.submit()" style="min-width: 150px; border-radius: 8px;">
                            <option value="">Semua Kelas</option>
                            <?php while($k = mysqli_fetch_assoc($kelas_list)): ?>
                                <option value="<?php echo $k['kelas_mapel']; ?>" <?php echo $selected_kelas == $k['kelas_mapel'] ? 'selected' : ''; ?>>
                                    <?php echo $k['kelas_mapel']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-auto ms-md-4">
                        <label class="small fw-bold text-muted">Tampilkan:</label>
                    </div>
                    <div class="col-auto">
                        <select name="limit" class="form-select form-select-sm border-0 shadow-sm" onchange="this.form.submit()" style="border-radius: 8px;">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 Data</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 Data</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 Data</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 Data</option>
                        </select>
                    </div>
                    <div class="col text-end">
                        <span class="small text-muted-pro">Menampilkan <b><?php echo $total_data; ?></b> dari <b><?php echo $global_total; ?></b> Siswa</span>
                    </div>
                </form>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 small">Data siswa berhasil dihapus dari sistem.</div>
            <?php endif; ?>

            <?php if ($message): 
                $m = explode('|', $message);
                echo "<script>Swal.fire({ icon: '".($m[0] == 'success' ? 'success' : 'error')."', title: 'Informasi', text: '".$m[1]."', confirmButtonColor: '#0ea5e9' });</script>";
            endif; ?>

            <form id="bulkForm" action="" method="POST">
                <input type="hidden" name="bulk_delete" value="1">
                <div class="card-pro p-0 overflow-hidden">
                    <div class="table-responsive-pro border-0">
                        <table class="table-pro" id="table-siswa">
                            <thead>
                                <tr>
                                    <th class="ps-4 check-column">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </th>
                                    <th style="width: 80px;">Foto</th>
                                    <th>NIS / Nama</th>
                                    <th>Kelas</th>
                                    <th>Orang Tua</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4 check-column">
                                        <input class="form-check-input row-check" type="checkbox" name="ids[]" value="<?php echo $row['id']; ?>">
                                    </td>
                                    <td>
                                        <div class="siswa-photo-container">
                                            <?php if($row['foto']): ?>
                                                <img src="/uploads/siswa/<?php echo $row['foto']; ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($row['nama']); ?>&background=10b981&color=fff&bold=true'">
                                            <?php else: ?>
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['nama']); ?>&background=10b981&color=fff&bold=true">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['nama']; ?></div>
                                        <div class="small font-monospace text-muted"><?php echo $row['qr_code']; ?> / <?php echo $row['nisn'] ?: '-'; ?></div>
                                    </td>
                                    <td><span class="badge-kelas"><?php echo $row['kelas_mapel'] ?: '-'; ?></span></td>
                                    <td>
                                        <div class="parent-info">
                                            <div><i data-lucide="user" style="width: 12px; height: 12px;"></i> <?php echo $row['nama_ayah'] ?: '-'; ?></div>
                                            <div><i data-lucide="phone" style="width: 12px; height: 12px;"></i> <?php echo $row['no_hp_ortu'] ?: '-'; ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group gap-1">
                                            <a href="../actions/cetak.php?id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary border-0" title="Cetak Kartu">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="confirmDelete(<?php echo $row['id']; ?>)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($result) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted italic">Belum ada data siswa. SIlakan klik Sinkron SIAKAD.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Controls -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Menampilkan <?php echo $start + 1; ?> - <?php echo min($start + $limit, $total_data); ?> dari <?php echo $total_data; ?> data
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>&kelas=<?php echo $selected_kelas; ?>">Sebelumnya</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&kelas=<?php echo $selected_kelas; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>&kelas=<?php echo $selected_kelas; ?>">Berikutnya</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <footer class="footer-pro">
            Management System &copy; <?php echo date('Y'); ?>
        </footer>
    </main>
</div>

<script src="/public/absen/assets/js/bootstrap.bundle.min.js"></script>
<script>
    lucide.createIcons();
    
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    const checkCount = document.getElementById('checkCount');

    checkAll.addEventListener('change', function() {
        rowChecks.forEach(check => {
            check.checked = checkAll.checked;
        });
        updateBulkButton();
    });

    rowChecks.forEach(check => {
        check.addEventListener('change', function() {
            const allChecked = Array.from(rowChecks).every(c => c.checked);
            checkAll.checked = allChecked;
            updateBulkButton();
        });
    });

    function updateBulkButton() {
        const checkedOnes = Array.from(rowChecks).filter(c => c.checked).length;
        if (checkedOnes > 0) {
            btnBulkDelete.style.display = 'inline-flex';
            checkCount.innerText = checkedOnes;
        } else {
            btnBulkDelete.style.display = 'none';
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data siswa akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?hapus=' + id;
            }
        });
    }

    function confirmBulkDelete() {
        Swal.fire({
            title: 'Hapus Terpilih?',
            text: "Semua data yang dicentang akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkForm').submit();
            }
        });
    }
</script>
</body>
</html>


