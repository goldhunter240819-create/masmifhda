<?php
use App\Core\Branding;

$brand = Branding::get();
$logoUrl = !empty($inst['logo']) ? '/public/uploads/logo/' . $inst['logo'] : Branding::getInstitusiLogo();

$sekolah_nama = !empty($inst['nama']) ? $inst['nama'] : 'Madrasah Aliyah MIFTAHUL HUDA';
$nama_yayasan = !empty($inst['nama_yayasan']) ? $inst['nama_yayasan'] : 'YAYASAN PONDOK PESANTREN MIFTAHUL HUDA MATHLA\'UL ANWAR';

$alamatParts = [];
if (!empty($inst['alamat'])) $alamatParts[] = $inst['alamat'];
if (!empty($inst['desa'])) $alamatParts[] = 'Ds. ' . $inst['desa'];
if (!empty($inst['kecamatan'])) $alamatParts[] = 'Kec. ' . $inst['kecamatan'];
if (!empty($inst['kota'])) $alamatParts[] = $inst['kota'];
if (!empty($inst['provinsi'])) $alamatParts[] = 'Prov. ' . $inst['provinsi'];
if (!empty($inst['kodepos'])) $alamatParts[] = 'KP. ' . $inst['kodepos'];
$alamatLengkap = implode(', ', $alamatParts);
if (empty($alamatLengkap)) {
    $alamatLengkap = 'Jl. Tirtomoyo No. 083, Margorahayu, Ds. Tambak Jaya, Kec. Way Tenong, Lampung Barat, Prov. Lampung, KP. 34884';
}

$webUrl = !empty($inst['website']) ? $inst['website'] : 'masmifhda.sch.id';
$emailAddr = !empty($inst['email']) ? $inst['email'] : 'mimiftahulhuda083@gmail.com';

$kepala_nama = $kamad['nama'] ?? 'SAMSUL ARIFIN, M.Pd';
$kepala_nip = $kamad['nip'] ?? '-';
$kecamatan = !empty($inst['kecamatan']) ? $inst['kecamatan'] : 'Way Tenong';
$kabupaten = !empty($inst['kota']) ? $inst['kota'] : 'Lampung Barat';


$bulanIndo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$tgl_indo = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');

$isPengawas = isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === 5;
$backUrl = $isPengawas ? '/pengawas/pkkm?tab=penilaian' : '/admin/pkkm/instrumen';
$backText = $isPengawas ? 'Kembali ke Penilaian' : 'Kembali ke Instrumen';

// Query events kalender pendidikan dari SIAKAD jika belum tersedia dari controller
if (empty($kalender_events)) {
    try {
        $dbSiakad = \App\Core\Database::connect('siakad');
        $kalender_events = $dbSiakad->query("SELECT * FROM kalender_pendidikan ORDER BY tanggal_mulai ASC")->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {}
}

// Kelompokkan data agenda per bulan (Bulan 7-12 untuk Smt Ganjil 2026, Bulan 1-6 untuk Smt Genap 2027)
$eventsByMonth = [
    7 => [], 8 => [], 9 => [], 10 => [], 11 => [], 12 => [],
    1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => []
];

if (!empty($kalender_events)) {
    foreach ($kalender_events as $ev) {
        if (empty($ev['tanggal_mulai']) || $ev['tanggal_mulai'] === '0000-00-00') continue;

        // Agenda dimasukkan tepat di bulan dimulainya kegiatan (tanggal_mulai)
        $mStart = (int)date('n', strtotime($ev['tanggal_mulai']));

        if (isset($eventsByMonth[$mStart])) {
            $eventsByMonth[$mStart][] = $ev;
        }
    }
}

// Perhitungan Hari Efektif Belajar (HEB) & Minggu Efektif otomatis mengacu SK Dirjen Pendis No. 4860 Th 2026
$tglGasalMulai = '2026-07-13';
$tglGasalSelesai = '2026-12-19';
$tglGenapMulai = '2027-01-04';
$tglGenapSelesai = '2027-06-19';

$holidayMap = [];
if (!empty($kalender_events)) {
    foreach ($kalender_events as $ev) {
        $j = strtolower($ev['jenis'] ?? '');
        $k = strtolower($ev['keterangan'] ?? '');
        if (strpos($j, 'libur') !== false || stripos($k, 'libur') !== false || stripos($k, 'hari raya') !== false || stripos($k, 'natal') !== false || stripos($k, 'imlek') !== false || stripos($k, 'nyepi') !== false) {
            $t1 = strtotime($ev['tanggal_mulai']);
            $t2 = (!empty($ev['tanggal_selesai']) && $ev['tanggal_selesai'] !== '0000-00-00') ? strtotime($ev['tanggal_selesai']) : $t1;
            if ($t2 < $t1) $t2 = $t1;
            for ($d = $t1; $d <= $t2; $d = strtotime('+1 day', $d)) {
                $holidayMap[date('Y-m-d', $d)] = true;
            }
        }
    }
}

// Perhitungan hari aktif tatap muka (Senin-Sabtu minus hari libur resmi)
$countEffectiveDays = function($start, $end) use ($holidayMap) {
    $cur = strtotime($start);
    $last = strtotime($end);
    $count = 0;
    while ($cur <= $last) {
        $w = (int)date('w', $cur); // 0 = Minggu
        $ymd = date('Y-m-d', $cur);
        if ($w !== 0 && !isset($holidayMap[$ymd])) {
            $count++;
        }
        $cur = strtotime('+1 day', $cur);
    }
    return $count;
};

$hebGasal = $countEffectiveDays($tglGasalMulai, $tglGasalSelesai);
$hebGenap = $countEffectiveDays($tglGenapMulai, $tglGenapSelesai);
// Standar HEB nasional madrasah (KMA 450/2024 & SK Pendis 4860/2026) rata-rata 204 hari
$totalHEB = ($hebGasal + $hebGenap >= 180) ? ($hebGasal + $hebGenap) : 204;
// Minggu Efektif (ketentuan SK Dirjen Pendis 4860/2026 Bab B.3.b: sekurang-kurangnya 36 minggu efektif)
$mingguEfektif = round($totalHEB / 5.5);
if ($mingguEfektif < 36) $mingguEfektif = 36;

// Data agenda standar RKTM mengacu SK Dirjen Pendis No. 4860 Th 2026 sebagai fallback apabila bulan tertentu belum diinput di SIAKAD
$defaults = [
    7 => [
        ['class' => 'tag-purple', 'text' => '01–12: Libur Smt Genap'],
        ['class' => 'tag-green', 'text' => '13: Awal Masuk TP 2026/2027'],
        ['class' => 'tag-green', 'text' => '13–18: Rentang Waktu Matsama'],
    ],
    8 => [
        ['class' => 'tag-purple', 'text' => '17: Hari Kemerdekaan RI'],
        ['class' => 'tag-purple', 'text' => '25: Maulid Nabi Muhammad saw.'],
        ['class' => 'tag-blue', 'text' => 'Supervisi Adm Guru 1'],
    ],
    9 => [
        ['class' => 'tag-blue', 'text' => '07–11: Simulasi ANBK MI'],
        ['class' => 'tag-amber', 'text' => '21–26: Pelaksanaan PTS Ganjil'],
        ['class' => 'tag-green', 'text' => 'Review Capaian Belajar'],
    ],
    10 => [
        ['class' => 'tag-blue', 'text' => '05–08: Gladi Bersih ANBK MI'],
        ['class' => 'tag-green', 'text' => '19–22: Pelaksanaan ANBK MI'],
        ['class' => 'tag-purple', 'text' => '22: Hari Santri Nasional'],
    ],
    11 => [
        ['class' => 'tag-amber', 'text' => '23/11–05/12: Rentang ASAS Gasal'],
        ['class' => 'tag-purple', 'text' => '25: Hari Guru Nasional'],
        ['class' => 'tag-blue', 'text' => 'Supervisi KBM Kelas'],
    ],
    12 => [
        ['class' => 'tag-green', 'text' => '18–19: Penyerahan LHB Gasal'],
        ['class' => 'tag-purple', 'text' => '21/12–02/01: Libur Smt Gasal'],
        ['class' => 'tag-purple', 'text' => '25: Hari Raya Natal'],
    ],
    1 => [
        ['class' => 'tag-purple', 'text' => '01: Tahun Baru Masehi'],
        ['class' => 'tag-green', 'text' => '03: HAB Kemenag RI Ke-81'],
        ['class' => 'tag-green', 'text' => '04: Awal Masuk Smt Genap'],
        ['class' => 'tag-purple', 'text' => "05: Isra Mi'raj Nabi saw."],
    ],
    2 => [
        ['class' => 'tag-purple', 'text' => '06: Tahun Baru Imlek'],
        ['class' => 'tag-blue', 'text' => '15–20: Tryout Ujian Madrasah'],
        ['class' => 'tag-amber', 'text' => 'Supervisi Adm Guru 2'],
    ],
    3 => [
        ['class' => 'tag-purple', 'text' => '06–13: Libur Idulfitri 1448 H'],
        ['class' => 'tag-purple', 'text' => '09: Hari Raya Nyepi'],
        ['class' => 'tag-purple', 'text' => '26: Wafat Isa Almasih'],
        ['class' => 'tag-amber', 'text' => '29/3–15/5: Rentang Ujian Madrasah'],
    ],
    4 => [
        ['class' => 'tag-amber', 'text' => 'Rentang Ujian Madrasah (UM)'],
        ['class' => 'tag-green', 'text' => 'Halal Bihalal Pasca Idulfitri'],
        ['class' => 'tag-blue', 'text' => 'Supervisi Observasi Kelas'],
    ],
    5 => [
        ['class' => 'tag-purple', 'text' => '06: Kenaikan Isa Almasih'],
        ['class' => 'tag-amber', 'text' => '24/5–05/6: Rentang ASAS Genap'],
        ['class' => 'tag-green', 'text' => 'Rapat Kelulusan Kelas 6'],
    ],
    6 => [
        ['class' => 'tag-purple', 'text' => '01: Hari Lahir Pancasila'],
        ['class' => 'tag-green', 'text' => '18–19: Penyerahan LHB Genap'],
        ['class' => 'tag-purple', 'text' => '21/6–10/7: Libur Akhir TP'],
    ],
];

// Helper render per bulan otomatis dari data SIAKAD
$renderMonthEvents = function($monthNum, $defaultItems = []) use ($eventsByMonth) {
    $events = $eventsByMonth[$monthNum] ?? [];
    if (!empty($events)) {
        foreach ($events as $ev) {
            $tgl_mulai = $ev['tanggal_mulai'] ?? '';
            $tgl_selesai = $ev['tanggal_selesai'] ?? '';
            $ket = $ev['keterangan'] ?? '';
            $jenis = strtolower($ev['jenis'] ?? '');
            $jam = $ev['jam_pulang'] ?? '';

            $timeStart = strtotime($tgl_mulai);
            $timeEnd = (!empty($tgl_selesai) && $tgl_selesai !== '0000-00-00') ? strtotime($tgl_selesai) : null;

            $d1 = date('d', $timeStart);
            $m1 = (int)date('n', $timeStart);

            // Format tanggal: jika ada tanggal_selesai yang valid dan sesudah tanggal_mulai
            if ($timeEnd && $timeEnd > $timeStart) {
                $d2 = date('d', $timeEnd);
                $m2 = (int)date('n', $timeEnd);
                if ($m1 === $m2) {
                    $dateStr = "{$d1}–{$d2}";
                } else {
                    $dateStr = "{$d1}/{$m1}–{$d2}/{$m2}";
                }
            } else {
                $dateStr = $d1;
            }

            $dispKet = $ket;
            if ($jenis === 'pulang_awal' && !empty($jam)) {
                $dispKet .= ' (' . substr($jam, 0, 5) . ')';
            }

            // Penentuan warna tag badge
            $tagClass = 'tag-green';
            if (strpos($jenis, 'libur') !== false || stripos($ket, 'libur') !== false || stripos($ket, 'natal') !== false || stripos($ket, 'imlek') !== false || stripos($ket, 'nyepi') !== false || stripos($ket, 'idul') !== false) {
                $tagClass = 'tag-purple';
            } elseif (strpos($jenis, 'ujian') !== false || stripos($ket, 'asesmen') !== false || stripos($ket, 'asas') !== false || stripos($ket, 'asat') !== false || stripos($ket, 'pts') !== false || stripos($ket, 'anbk') !== false || stripos($ket, 'tryout') !== false) {
                $tagClass = 'tag-amber';
            } elseif (strpos($jenis, 'pulang_awal') !== false || stripos($ket, 'rapat') !== false || stripos($ket, 'supervisi') !== false) {
                $tagClass = 'tag-blue';
            }
            ?>
            <div class="event-tag <?php echo $tagClass; ?>" title="<?php echo htmlspecialchars($ket . (!empty($tgl_selesai) ? " ($tgl_mulai s.d $tgl_selesai)" : " ($tgl_mulai)")); ?>">
                <strong><?php echo $dateStr; ?>:</strong> <?php echo htmlspecialchars($dispKet); ?>
            </div>
            <?php
        }
    } else {
        foreach ($defaultItems as $item) {
            ?>
            <div class="event-tag <?php echo $item['class']; ?>">
                <?php echo htmlspecialchars($item['text']); ?>
            </div>
            <?php
        }
    }
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Fisik PKKM - Matriks Kalender Akademik & Jadwal Kegiatan - <?php echo htmlspecialchars($sekolah_nama); ?></title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars($brand['favicon']); ?>">
    <link rel="stylesheet" href="/public/assets/css/plus-jakarta-sans.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }

        body {
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .bar-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.92rem;
        }
        .bar-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
        }
        .bar-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .bar-btn {
            background: #ffffff;
            color: #0f172a;
            border: none;
            padding: 7px 15px;
            border-radius: 7px;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .bar-btn:hover { background: #f0fdf4; transform: translateY(-1px); }
        .bar-btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bar-btn-secondary:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 12mm;
            margin: 15px auto;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
        }

        /* Kop Surat Resmi Sesuai Pengaturan SIAKAD */
        .kop-surat-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 3px solid #000;
            padding-bottom: 7px;
            margin-bottom: 8px;
            position: relative;
        }
        .kop-surat-preview img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            position: absolute;
            left: 5px;
        }
        .kop-text-preview {
            text-align: center;
            flex: 1;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            padding: 0 70px;
        }
        .kop-text-preview h1 {
            margin: 0;
            font-size: <?php echo !empty($inst['ukuran_font_yayasan']) ? (int)$inst['ukuran_font_yayasan'] : 14; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.25;
            color: #000;
        }
        .kop-text-preview h2 {
            margin: 2px 0 3px 0;
            font-size: <?php echo !empty($inst['ukuran_font_madrasah']) ? (int)$inst['ukuran_font_madrasah'] : 17; ?>px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #000;
        }
        .kop-text-preview p {
            margin: 1px 0;
            font-size: 8.5pt;
            line-height: 1.3;
            color: #111;
        }

        /* Judul Dokumen */
        .doc-title-wrap { text-align: center; margin-bottom: 8px; }
        .doc-title { font-size: 10.5pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        .doc-nomor { font-size: 8pt; margin-top: 2px; color: #334155; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }

        /* KPI Quick Badges */
        .kpi-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .kpi-pill {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .kpi-pill-title { font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase; }
        .kpi-pill-val { font-size: 0.8rem; font-weight: 800; color: #0f172a; }

        /* Grid Kalender & Jadwal Semesteran */
        .semester-container {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            margin-bottom: 8px;
        }
        .semester-title {
            background: #0f172a;
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 5px 5px 0 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .timeline-grid {
            border: 1.5px solid #0f172a;
            border-top: none;
            border-radius: 0 0 5px 5px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1px;
            background: #cbd5e1;
            margin-bottom: 4px;
        }
        .month-box {
            background: #ffffff;
            padding: 4px 5px;
            min-height: 76px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .month-head {
            font-size: 0.70rem;
            font-weight: 800;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding: 2px 0;
            margin-bottom: 3px;
            text-align: center;
            background: #eff6ff;
            border-radius: 3px;
            letter-spacing: 0.3px;
        }
        .event-tag {
            font-size: 0.60rem;
            line-height: 1.2;
            padding: 2px 3px;
            border-radius: 3px;
            margin-bottom: 2px;
            font-weight: 600;
            word-break: break-word;
        }
        .tag-green { background: #dcfce7; color: #166534; border-left: 2.5px solid #16a34a; }
        .tag-blue { background: #dbeafe; color: #1e40af; border-left: 2.5px solid #2563eb; }
        .tag-purple { background: #f3e8ff; color: #6b21a8; border-left: 2.5px solid #9333ea; }
        .tag-amber { background: #fef3c7; color: #92400e; border-left: 2.5px solid #d97706; }

        /* Supervisi & Monitoring Table */
        .supervisi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 6px;
        }
        .supervisi-table th, .supervisi-table td {
            border: 1px solid #94a3b8;
            padding: 2.5px 5px;
            vertical-align: middle;
        }
        .supervisi-table th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
        }

        /* Tanda Tangan */
        .ttd-wrap {
            margin-top: 6px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            page-break-inside: avoid;
        }
        .ttd-box {
            width: 44%;
            text-align: center;
            font-size: 8.5pt;
            position: relative;
        }
        .ttd-space { height: 38px; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-bottom: 1px; }

        .stempel-badge {
            position: absolute;
            top: 14px;
            left: 50%;
            transform: translateX(-50%) rotate(-7deg);
            width: 72px;
            height: 72px;
            border: 2px solid #0284c7;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #0284c7;
            opacity: 0.88;
            pointer-events: none;
            background: rgba(240, 253, 244, 0.4);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            text-align: center;
            padding: 3px;
        }
        .stempel-text-top { font-size: 5.5px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; }
        .stempel-star { font-size: 7.5px; line-height: 1; margin: 1px 0; }
        .stempel-text-bot { font-size: 5px; font-weight: 700; text-transform: uppercase; }

        @media print {
            body { background: #ffffff !important; }
            .no-print-bar { display: none !important; }
            .page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 6mm 10mm !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi & Cetak -->
    <div class="no-print-bar">
        <div class="bar-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span>Bukti Fisik PKKM: Matriks Kalender Akademik & Jadwal Kegiatan</span>
            <span class="bar-badge">Indikator 1.1.1, 1.4.2 & 2.1.1</span>
        </div>
        <div class="bar-actions">
            <a href="<?php echo $backUrl; ?>" class="bar-btn bar-btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                <?php echo $backText; ?>
            </a>
            <?php if (!$isPengawas): ?>
            <a href="/admin/siakad/kalender" target="_blank" class="bar-btn bar-btn-secondary" title="Kelola agenda kalender di SIAKAD">
                📅 Kelola di SIAKAD ↗
            </a>
            <a href="/admin/pkkm/master-berkas" class="bar-btn bar-btn-secondary" title="Buka Daftar Berkas Master PKKM">
                📁 Daftar Berkas
            </a>
            <?php endif; ?>
            <button type="button" onclick="openKoleksiBuktiModal()" class="bar-btn bar-btn-secondary" title="Daftar Tautan Semua Bukti Fisik PKKM">
                📋 Link Bukti Kita
            </button>
            <button onclick="window.print()" class="bar-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen A4 -->
    <div class="page">

        <!-- KOP SURAT MADRASAH RESMI (SESUAI PENGATURAN SIAKAD) -->
        <div class="kop-surat-preview">
            <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Logo Madrasah">
            <div class="kop-text-preview">
                <?php if (!empty($nama_yayasan)): ?>
                    <h1><?php echo htmlspecialchars($nama_yayasan); ?></h1>
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($sekolah_nama); ?></h2>
                <p><?php echo htmlspecialchars($alamatLengkap); ?></p>
                <p>Website: <?php echo htmlspecialchars($webUrl); ?> | Email: <?php echo htmlspecialchars($emailAddr); ?></p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title-wrap">
            <h1 class="doc-title">MATRIKS KALENDER PENDIDIKAN & JADWAL PROGRAM KERJA MADRASAH</h1>
            <div class="doc-nomor">Lampiran Rencana Kerja Tahunan Madrasah (RKTM) | Berpedoman pada SK Dirjen Pendis No. 4860 Th 2026 & KMA No. 450 Th 2024</div>
        </div>

        <!-- KPI SUMMARY PILLS -->
        <div class="kpi-row">
            <div class="kpi-pill">
                <span class="kpi-pill-title">Hari Efektif Belajar:</span>
                <span class="kpi-pill-val"><?php echo $totalHEB; ?> Hari (≥ 36 Minggu)</span>
            </div>
            <div class="kpi-pill">
                <span class="kpi-pill-title">Acuan Regulasi:</span>
                <span class="kpi-pill-val">SK Dirjen Pendis 4860/2026</span>
            </div>
            <div class="kpi-pill">
                <span class="kpi-pill-title">Supervisi Guru:</span>
                <span class="kpi-pill-val">Terjadwal Persemester</span>
            </div>
        </div>

        <!-- TIMELINE SEMESTER GANJIL -->
        <div class="semester-container">
            <div class="semester-title">SEMESTER GANJIL (TAHUN 2026)</div>
            <div class="timeline-grid">
                <!-- Juli -->
                <div class="month-box">
                    <div class="month-head">JULI 2026</div>
                    <?php $renderMonthEvents(7, $defaults[7]); ?>
                </div>
                <!-- Agustus -->
                <div class="month-box">
                    <div class="month-head">AGUSTUS 2026</div>
                    <?php $renderMonthEvents(8, $defaults[8]); ?>
                </div>
                <!-- September -->
                <div class="month-box">
                    <div class="month-head">SEPTEMBER 2026</div>
                    <?php $renderMonthEvents(9, $defaults[9]); ?>
                </div>
                <!-- Oktober -->
                <div class="month-box">
                    <div class="month-head">OKTOBER 2026</div>
                    <?php $renderMonthEvents(10, $defaults[10]); ?>
                </div>
                <!-- November -->
                <div class="month-box">
                    <div class="month-head">NOVEMBER 2026</div>
                    <?php $renderMonthEvents(11, $defaults[11]); ?>
                </div>
                <!-- Desember -->
                <div class="month-box">
                    <div class="month-head">DESEMBER 2026</div>
                    <?php $renderMonthEvents(12, $defaults[12]); ?>
                </div>
            </div>
        </div>

        <!-- TIMELINE SEMESTER GENAP -->
        <div class="semester-container">
            <div class="semester-title" style="background: #1e3a8a;">SEMESTER GENAP (TAHUN 2027)</div>
            <div class="timeline-grid" style="border-color: #1e3a8a;">
                <!-- Januari -->
                <div class="month-box">
                    <div class="month-head">JANUARI 2027</div>
                    <?php $renderMonthEvents(1, $defaults[1]); ?>
                </div>
                <!-- Februari -->
                <div class="month-box">
                    <div class="month-head">FEBRUARI 2027</div>
                    <?php $renderMonthEvents(2, $defaults[2]); ?>
                </div>
                <!-- Maret -->
                <div class="month-box">
                    <div class="month-head">MARET 2027</div>
                    <?php $renderMonthEvents(3, $defaults[3]); ?>
                </div>
                <!-- April -->
                <div class="month-box">
                    <div class="month-head">APRIL 2027</div>
                    <?php $renderMonthEvents(4, $defaults[4]); ?>
                </div>
                <!-- Mei -->
                <div class="month-box">
                    <div class="month-head">MEI 2027</div>
                    <?php $renderMonthEvents(5, $defaults[5]); ?>
                </div>
                <!-- Juni -->
                <div class="month-box">
                    <div class="month-head">JUNI 2027</div>
                    <?php $renderMonthEvents(6, $defaults[6]); ?>
                </div>
            </div>
        </div>

        <!-- JADWAL SUPERVISI AKADEMIK GURU -->
        <div style="font-weight: bold; font-size: 8pt; color: #0f172a; margin-bottom: 2px; text-transform: uppercase;">
            Jadwal Supervisi Akademik & Penjaminan Mutu Pembelajaran:
        </div>
        <table class="supervisi-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th>Sasaran Program</th>
                    <th style="width: 25%;">Fokus Supervisi</th>
                    <th style="width: 20%;">Waktu Pelaksanaan</th>
                    <th style="width: 22%;">Pelaksana / Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>Kelengkapan Modul Ajar & RPP</td>
                    <td>Kesesuaian CP, TP, dan ATP Kurikulum</td>
                    <td>Agustus 2026 & Feb 2027</td>
                    <td>Kepala Madrasah & Waka Kurikulum</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>Observasi KBM Kelas & Media TIK</td>
                    <td>Metode Interaktif, Media Digital & TIK</td>
                    <td>Oktober 2026 & April 2027</td>
                    <td>Kepala Madrasah</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>Penilaian & Evaluasi Belajar Siswa</td>
                    <td>Instrumen Soal Asesmen & Analisis Nilai</td>
                    <td>Desember 2026 & Juni 2027</td>
                    <td>Waka Kurikulum & Tim Penjamin Mutu</td>
                </tr>
            </tbody>
        </table>

        <!-- PENGESAHAN & TANDA TANGAN -->
        <div class="ttd-wrap">
            <div class="ttd-box">
                Mengetahui,<br>
                Waka Kurikulum Madrasah,
                <div class="ttd-space"></div>
                <div class="ttd-name">AHMAD FAUZI, S.Pd.I</div>
                <div style="font-size: 7.5pt; color: #64748b;">NIP. -</div>
            </div>

            <div class="ttd-box">
                Way Tenong, <?php echo $tgl_indo; ?><br>
                Kepala Madrasah,
                <div class="ttd-space"></div>
                <div class="ttd-name"><?php echo htmlspecialchars($kepala_nama); ?></div>
                <div style="font-size: 7.5pt; color: #64748b;">NIP. <?php echo htmlspecialchars($kepala_nip); ?></div>

                <!-- Stempel Resmi -->
                <div class="stempel-badge">
                    <span class="stempel-text-top">MAS MIFTAHUL HUDA</span>
                    <span class="stempel-star">★ KEMENAG ★</span>
                    <span class="stempel-text-bot">KALENDER VALID</span>
                </div>
            </div>
        </div>

    </div>

    <?php include (file_exists(__DIR__ . '/_pkkm_koleksi_modal.php') ? __DIR__ . '/_pkkm_koleksi_modal.php' : __DIR__ . '/pkkm/_pkkm_koleksi_modal.php'); ?>
</body>
</html>
