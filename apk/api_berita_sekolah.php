<?php
header('Content-Type: application/json');

// ============================================================================
// SISTEM CACHE: Simpan hasil scraping ke file JSON, refresh tiap 30 menit
// ============================================================================
$cache_file = __DIR__ . '/cache_postingan.json';
$cache_duration = 1800; // 30 menit

// Cek apakah cache masih valid
if (file_exists($cache_file)) {
    $cache_age = time() - filemtime($cache_file);
    if ($cache_age < $cache_duration) {
        echo file_get_contents($cache_file);
        exit;
    }
}

// ============================================================================
// SCRAPE SEMUA KATEGORI SECARA PARALEL (curl_multi)
// Ambil halaman per-kategori karena di situ tanggalnya lengkap
// ============================================================================
$category_urls = [
    'news'       => 'https://masmifhda.sch.id/posts/news/',
    'prestasi'   => 'https://masmifhda.sch.id/posts/prestasi/',
    'pengumuman' => 'https://masmifhda.sch.id/posts/pengumuman/',
    'akademik'   => 'https://masmifhda.sch.id/posts/akademik/',
];

// Inisialisasi curl multi
$mh = curl_multi_init();
$handles = [];

foreach ($category_urls as $cat => $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_multi_add_handle($mh, $ch);
    $handles[$cat] = $ch;
}

// Eksekusi semua request secara paralel
$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

// Ambil hasil dari setiap kategori
$all_posts = [];
libxml_use_internal_errors(true);

foreach ($handles as $cat => $ch) {
    $html = curl_multi_getcontent($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);

    if (!$html || $http_code !== 200) continue;

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $articles = $xpath->query('//article');

    foreach ($articles as $index => $article) {
        if ($index >= 2) break; // Ambil 2 terbaru per kategori

        // --- Link ---
        $linkNode = $xpath->query('.//a[contains(@href, "/posts/")]', $article)->item(0);
        $link = $linkNode ? "https://masmifhda.sch.id" . $linkNode->getAttribute('href') : "#";

        // --- Image ---
        $imgNode = $xpath->query('.//img', $article)->item(0);
        $imgSrc = '';
        if ($imgNode) {
            $src = $imgNode->getAttribute('src');
            $imgSrc = (strpos($src, 'http') === 0) ? $src : "https://masmifhda.sch.id" . $src;
        }

        // --- Title ---
        $titleNode = $xpath->query('.//h3', $article)->item(0);
        $title = $titleNode ? trim($titleNode->nodeValue) : "Tanpa Judul";

        // --- Tanggal (dari tag <time> - di halaman kategori ini PASTI ada) ---
        $timeNode = $xpath->query('.//time', $article)->item(0);
        $date_text = '';
        $date_sortable = '0000-00-00';
        if ($timeNode) {
            $date_text = trim($timeNode->nodeValue);
            // Gunakan datetime attribute jika ada (format ISO: 2026-08-25T00:00:00.000Z)
            $datetime_attr = $timeNode->getAttribute('datetime');
            if ($datetime_attr) {
                $ts = strtotime($datetime_attr);
                if ($ts !== false) {
                    $date_sortable = date('Y-m-d', $ts);
                }
            }
            // Fallback: parse dari teks
            if ($date_sortable === '0000-00-00') {
                $date_sortable = parse_tanggal_indo($date_text);
            }
        }

        // --- Deskripsi ---
        $descNode = $xpath->query('.//div[contains(@class, "text-muted")]//p', $article)->item(0);
        $desc = $descNode ? trim($descNode->nodeValue) : "";
        if (strlen($desc) > 100) {
            $desc = substr($desc, 0, 100) . "...";
        }

        // --- Kategori (dari badge/span) ---
        $catNode = $xpath->query('.//span[contains(@class, "capitalize")]', $article)->item(0);
        $category = $catNode ? trim($catNode->nodeValue) : ucfirst($cat);

        $all_posts[] = [
            'title'     => $title,
            'link'      => $link,
            'image'     => $imgSrc,
            'date'      => $date_text,
            'date_sort' => $date_sortable,
            'desc'      => $desc,
            'category'  => $category
        ];
    }
}

curl_multi_close($mh);

// Urutkan berdasarkan tanggal terbaru (descending)
usort($all_posts, function($a, $b) {
    return strcmp($b['date_sort'], $a['date_sort']);
});

// Ambil 3 postingan terbaru dari semua kategori
$top_posts = array_slice($all_posts, 0, 3);

// Hapus field date_sort dari output
foreach ($top_posts as &$p) {
    unset($p['date_sort']);
}
unset($p);

if (count($top_posts) > 0) {
    $result = json_encode(['status' => 'success', 'data' => $top_posts]);
} else {
    $result = json_encode(['status' => 'error', 'message' => 'Belum ada postingan terbaru.']);
}

// Simpan ke cache file
@file_put_contents($cache_file, $result);

// Gagal total? Coba pakai cache lama
if (count($top_posts) === 0 && file_exists($cache_file)) {
    echo file_get_contents($cache_file);
    exit;
}

echo $result;

// ============================================================================
// FUNGSI: Parse tanggal Indonesia ke format sortable (YYYY-MM-DD)
// ============================================================================
function parse_tanggal_indo($tgl) {
    $bulan_map = [
        'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
        'Mei' => '05', 'May' => '05', 'Jun' => '06', 'Jul' => '07',
        'Agu' => '08', 'Aug' => '08', 'Sep' => '09', 'Okt' => '10',
        'Oct' => '10', 'Nov' => '11', 'Des' => '12', 'Dec' => '12',
        'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
        'Juni' => '06', 'Juli' => '07', 'Agustus' => '08', 'September' => '09',
        'Oktober' => '10', 'November' => '11', 'Desember' => '12'
    ];

    $parts = preg_split('/\s+/', trim($tgl));
    if (count($parts) >= 3) {
        $day   = str_pad(intval($parts[0]), 2, '0', STR_PAD_LEFT);
        $month = $bulan_map[$parts[1]] ?? '01';
        $year  = intval($parts[2]);
        return sprintf('%04d-%s-%s', $year, $month, $day);
    }

    $ts = strtotime($tgl);
    if ($ts !== false) {
        return date('Y-m-d', $ts);
    }

    return '0000-00-00';
}
