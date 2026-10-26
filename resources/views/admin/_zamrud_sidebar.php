<?php
/* Universal Admin Sidebar Ã¢â‚¬â€ Zamrud Design System
 * Variables expected from controller:
 *   $active_module : 'finance' | 'persuratan' | 'inventaris' | 'perpustakaan' | 'elearning'
 *   $active_page   : 'overview' | 'tagihan' | ... (sub-page of module)
 *   $adminNama     : string
 *   $adminInitial  : string
 */
$am = isset($active_module) ? $active_module : '';
if (empty($am) && strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/perpus') !== false) {
    $am = 'perpus';
}
$ap = isset($active_page)   ? $active_page   : '';

$modules = array(
    'finance'      => array('icon'=>'credit-card',  'label'=>'Keuangan',       'url'=>base_url('admin/finance')),
    'absen_v2'     => array('icon'=>'calendar-check', 'label'=>'Absen',       'url'=>base_url('admin/absen-v2')),
    'siakad'       => array('icon'=>'graduation-cap', 'label'=>'SIAKAD',         'url'=>base_url('admin/siakad')),
    'osim'         => array('icon'=>'users', 'label'=>'OSIM',         'url'=>base_url('osim')),
    'pkkm'         => array('icon'=>'check-square', 'label'=>'PKKM',   'url'=>base_url('admin/pkkm')),
    'absen'        => array('icon'=>'calendar-check', 'label'=>'Presensi / Jurnal', 'url'=>base_url('admin/absen')),
    'pengaturan'   => array('icon'=>'settings',      'label'=>'Pengaturan',     'url'=>base_url('admin/pengaturan')),
);

$sub_menus = array(
    'finance' => array(
        array('icon'=>'layout-dashboard','label'=>'Overview',    'url'=>base_url('admin/finance'),          'page'=>'overview'),
        array(
            'icon'=>'repeat',
            'label'=>'Transaksi',
            'page'=>'transaksi',
            'subpages'=>array('komite_pemasukan','komite_pengeluaran'),
            'dropdown'=>array(
                array('icon'=>'arrow-down-left','label'=>'Pemasukan', 'url'=>base_url('admin/finance/komite/pemasukan'), 'page'=>'komite_pemasukan'),
                array('icon'=>'arrow-up-right','label'=>'Pengeluaran', 'url'=>base_url('admin/finance/komite/pengeluaran'), 'page'=>'komite_pengeluaran'),
            )
        ),
        array(
            'icon'=>'database',
            'label'=>'Master Data',
            'page'=>'master',
            'subpages'=>array('komite_kategori','komite_jenis'),
            'dropdown'=>array(
                array('icon'=>'list','label'=>'Master Kategori', 'url'=>base_url('admin/finance/komite/kategori'), 'page'=>'komite_kategori'),
                array('icon'=>'tags','label'=>'Jenis Tagihan', 'url'=>base_url('admin/finance/komite/jenis-tagihan'), 'page'=>'komite_jenis'),
            )
        ),
        array(
            'icon'=>'users',
            'label'=>'Keuangan Siswa',
            'page'=>'keuangan_siswa',
            'subpages'=>array('komite_pembayaran_siswa','tagihan'),
            'dropdown'=>array(
                array('icon'=>'user-check','label'=>'Pemb. Siswa', 'url'=>base_url('admin/finance/komite/pembayaran-siswa'), 'page'=>'komite_pembayaran_siswa'),
                array('icon'=>'file-text','label'=>'Tagihan', 'url'=>base_url('admin/finance/tagihan'), 'page'=>'tagihan'),
            )
        ),
        array('icon'=>'file-spreadsheet','label'=>'Laporan', 'url'=>base_url('admin/finance/komite/laporan'), 'page'=>'komite_laporan'),
        array('icon'=>'shield-check','label'=>'Akses Bendahara', 'url'=>base_url('admin/finance/akses'), 'page'=>'akses_bendahara'),
    ),
    'bos' => array(
        array('icon'=>'landmark','label'=>'Dashboard BOS', 'url'=>base_url('admin/bos'), 'page'=>'bos_overview'),
        array('icon'=>'file-spreadsheet','label'=>'RKAS Tahunan', 'url'=>base_url('admin/bos/rkas'), 'page'=>'bos_rkas'),
        array('icon'=>'arrow-down-left','label'=>'Penerimaan Dana', 'url'=>base_url('admin/bos/pemasukan'), 'page'=>'bos_pemasukan'),
        array('icon'=>'arrow-up-right','label'=>'Belanja / Pengeluaran', 'url'=>base_url('admin/bos/pengeluaran'), 'page'=>'bos_pengeluaran'),
        array('icon'=>'book-open','label'=>'Buku Kas Umum (BKU)', 'url'=>base_url('admin/bos/bku'), 'page'=>'bos_bku'),
        array('icon'=>'wallet','label'=>'Buku Kas Tunai', 'url'=>base_url('admin/bos/tunai'), 'page'=>'bos_tunai'),
        array('icon'=>'building','label'=>'Buku Bank', 'url'=>base_url('admin/bos/bank'), 'page'=>'bos_bank'),
        array('icon'=>'percent','label'=>'Buku Pajak', 'url'=>base_url('admin/bos/pajak'), 'page'=>'bos_pajak'),
        array('icon'=>'printer','label'=>'Cetak Laporan', 'url'=>base_url('admin/bos/laporan'), 'page'=>'bos_laporan'),
        array('icon'=>'shield-check','label'=>'Akses Bendahara', 'url'=>base_url('admin/bos/akses'), 'page'=>'bos_akses'),
    ),
    'persuratan' => array(
        array('icon'=>'layout-dashboard','label'=>'Overview',    'url'=>base_url('admin/persuratan'),             'page'=>'overview'),
        array('icon'=>'plus-circle',     'label'=>'Buat Surat',  'url'=>base_url('admin/persuratan/buat'),        'page'=>'buat'),
        array(
            'icon'=>'mail',
            'label'=>'Surat Masuk & Keluar',
            'page'=>'surat_kelola',
            'subpages'=>array('masuk','keluar','disposisi','arsip'),
            'dropdown'=>array(
                array('icon'=>'inbox',           'label'=>'Surat Masuk', 'url'=>base_url('admin/persuratan/masuk'),       'page'=>'masuk'),
                array('icon'=>'send',            'label'=>'Surat Keluar','url'=>base_url('admin/persuratan/keluar'),      'page'=>'keluar'),
                array('icon'=>'git-branch',      'label'=>'Disposisi',   'url'=>base_url('admin/persuratan/disposisi'),   'page'=>'disposisi'),
                array('icon'=>'archive',         'label'=>'Arsip Surat', 'url'=>base_url('admin/persuratan/arsip'),       'page'=>'arsip'),
            )
        ),
        array(
            'icon'=>'file-badge',
            'label'=>'Surat Keputusan (SK)',
            'page'=>'sk_kelola',
            'subpages'=>array('sk','pemberkasan'),
            'dropdown'=>array(
                array('icon'=>'file-text',       'label'=>'SK Kamad',    'url'=>base_url('admin/persuratan/sk'),          'page'=>'sk'),
                array('icon'=>'file-code',       'label'=>'Template SK Guru', 'url'=>base_url('admin/persuratan/pemberkasan_template'), 'page'=>'pemberkasan'),
            )
        ),
        array(
            'icon'=>'file-signature',
            'label'=>'Surat Dinas Umum',
            'page'=>'surat_umum',
            'subpages'=>array('suratumum'),
            'dropdown'=>array(
                array('icon'=>'pen-tool',        'label'=>'Generator Surat',    'url'=>base_url('admin/persuratan/umum/buat'),   'page'=>'suratumum'),
                array('icon'=>'archive',         'label'=>'Arsip Surat',        'url'=>base_url('admin/persuratan/umum'),        'page'=>'suratumum_list'),
            )
        ),
    ),
    'inventaris' => array(
        array('icon'=>'layout-dashboard','label'=>'Overview',    'url'=>base_url('admin/inventaris'),             'page'=>'overview'),
        array('icon'=>'box',             'label'=>'Daftar Aset', 'url'=>base_url('admin/inventaris/aset'),        'page'=>'aset'),
        array('icon'=>'hand-helping',    'label'=>'Peminjaman',  'url'=>base_url('admin/inventaris/peminjaman'),  'page'=>'peminjaman'),
        array('icon'=>'bar-chart-2',     'label'=>'Laporan',     'url'=>base_url('admin/inventaris/laporan'),     'page'=>'laporan'),
    ),
    'perpustakaan' => array(
        array('icon'=>'layout-dashboard','label'=>'Dashboard',    'url'=>base_url('admin/perpustakaan'),             'page'=>'overview'),
        array(
            'icon'=>'book-open', 
            'label'=>'Master Buku', 
            'page'=>'master_buku',
            'subpages'=>array('buku','kategori','rak','penulis','penerbit'),
            'dropdown'=>array(
                array('icon'=>'file-text','label'=>'Data Buku',    'url'=>base_url('admin/perpustakaan/buku'),     'page'=>'buku'),
                array('icon'=>'tags',     'label'=>'Kategori Buku','url'=>base_url('admin/perpustakaan/kategori'), 'page'=>'kategori'),
                array('icon'=>'layers',   'label'=>'Rak Buku',     'url'=>base_url('admin/perpustakaan/rak'),      'page'=>'rak'),
                array('icon'=>'user',     'label'=>'Penulis',      'url'=>base_url('admin/perpustakaan/penulis'),  'page'=>'penulis'),
                array('icon'=>'building', 'label'=>'Penerbit',     'url'=>base_url('admin/perpustakaan/penerbit'), 'page'=>'penerbit'),
            )
        ),
        array('icon'=>'users', 'label'=>'Anggota', 'url'=>base_url('admin/perpustakaan/anggota'), 'page'=>'anggota'),
        array(
            'icon'=>'repeat', 
            'label'=>'Transaksi', 
            'page'=>'transaksi',
            'subpages'=>array('peminjaman','pengembalian','perpanjangan','riwayat'),
            'dropdown'=>array(
                array('icon'=>'arrow-up-right','label'=>'Peminjaman',   'url'=>base_url('admin/perpustakaan/peminjaman'), 'page'=>'peminjaman'),
                array('icon'=>'arrow-down-left','label'=>'Pengembalian', 'url'=>base_url('admin/perpustakaan/pengembalian'),'page'=>'pengembalian'),
                array('icon'=>'calendar-plus',  'label'=>'Perpanjangan', 'url'=>base_url('admin/perpustakaan/perpanjangan'),'page'=>'perpanjangan'),
                array('icon'=>'history',        'label'=>'Riwayat',      'url'=>base_url('admin/perpustakaan/riwayat'),     'page'=>'riwayat'),
            )
        ),
        array(
            'icon'=>'tablet', 
            'label'=>'Digital Library', 
            'page'=>'digital',
            'subpages'=>array('ebook','modul','jurnal','video'),
            'dropdown'=>array(
                array('icon'=>'file-text','label'=>'E-Book', 'url'=>base_url('admin/perpustakaan/ebook'), 'page'=>'ebook'),
                array('icon'=>'file-up',  'label'=>'Modul',  'url'=>base_url('admin/perpustakaan/modul'), 'page'=>'modul'),
                array('icon'=>'book',     'label'=>'Jurnal', 'url'=>base_url('admin/perpustakaan/jurnal'),'page'=>'jurnal'),
                array('icon'=>'video',    'label'=>'Video',  'url'=>base_url('admin/perpustakaan/video'), 'page'=>'video'),
            )
        ),
        array('icon'=>'user-check', 'label'=>'Pengunjung', 'url'=>base_url('admin/perpustakaan/pengunjung'), 'page'=>'pengunjung'),
        array('icon'=>'file-bar-chart', 'label'=>'Laporan', 'url'=>base_url('admin/perpustakaan/laporan'), 'page'=>'laporan'),
        array('icon'=>'settings', 'label'=>'Pengaturan', 'url'=>base_url('admin/perpustakaan/pengaturan'), 'page'=>'pengaturan'),
    ),
    'elearning' => array(
        array('icon'=>'layout-dashboard','label'=>'Overview',    'url'=>base_url('admin/elearning'),          'page'=>'overview'),
        array('icon'=>'film',            'label'=>'Materi',      'url'=>base_url('admin/elearning/materi'),   'page'=>'materi'),
        array('icon'=>'clipboard-check', 'label'=>'Tugas & Kuis','url'=>base_url('admin/elearning/tugas'),    'page'=>'tugas'),
        array('icon'=>'message-square',  'label'=>'Forum',       'url'=>base_url('admin/elearning/forum'),    'page'=>'forum'),
    ),
    'absen_v2' => array(
        array('icon'=>'layout-dashboard','label'=>'Rekap Harian',  'url'=>base_url('admin/absen-v2'),         'page'=>'overview'),
        array('icon'=>'calendar-range',  'label'=>'Rekap Bulanan', 'url'=>base_url('admin/absen-v2/rekap'),   'page'=>'rekap'),
        array('icon'=>'user-check',      'label'=>'Absen Manual',  'url'=>base_url('admin/absen-v2/manual'),  'page'=>'manual'),
        array('icon'=>'calendar-days',   'label'=>'Jadwal Guru',   'url'=>base_url('admin/absen-v2/jadwal'),  'page'=>'jadwal'),
    ),
    'cbt_v2' => array(
        array('icon'=>'layout-dashboard','label'=>'Dashboard',     'url'=>base_url('admin/cbt-v2'),            'page'=>'overview'),
        array('icon'=>'database',        'label'=>'Topik Soal',    'url'=>base_url('admin/cbt-v2/topik'),     'page'=>'topik'),
        array('icon'=>'book-open',       'label'=>'Bank Soal',     'url'=>base_url('admin/cbt-v2/soal'),      'page'=>'soal'),
        array('icon'=>'file-up',         'label'=>'Import Soal',   'url'=>base_url('admin/cbt-v2/import'),    'page'=>'import'),
        array('icon'=>'calendar-clock',  'label'=>'Jadwal Ujian',  'url'=>base_url('admin/cbt-v2/jadwal'),    'page'=>'jadwal'),
        array('icon'=>'key',             'label'=>'Token Ujian',   'url'=>base_url('admin/cbt-v2/token'),     'page'=>'token'),
        array('icon'=>'bar-chart-3',     'label'=>'Hasil Ujian',   'url'=>base_url('admin/cbt-v2/hasil'),     'page'=>'hasil'),
        array('icon'=>'check-square',    'label'=>'Evaluasi Essay','url'=>base_url('admin/cbt-v2/evaluasi'),  'page'=>'evaluasi'),
        array('icon'=>'users',           'label'=>'Daftar Peserta','url'=>base_url('admin/cbt-v2/peserta'),   'page'=>'peserta'),
        array('icon'=>'settings',        'label'=>'Pengaturan',    'url'=>base_url('admin/cbt-v2/config'),    'page'=>'config'),
    ),
    'siakad' => array(
        array('icon'=>'layout-dashboard','label'=>'Dashboard',    'url'=>base_url('admin/siakad'),             'page'=>'overview'),
        array(
            'icon'=>'database', 
            'label'=>'Master Data', 
            'page'=>'master',
            'subpages'=>array('tahun_ajaran','institusi','kelas','mapel'),
            'dropdown'=>array(
                array('icon'=>'calendar','label'=>'Tahun Ajaran', 'url'=>base_url('admin/siakad/tahun-ajaran'), 'page'=>'tahun_ajaran'),
                array('icon'=>'building','label'=>'Institusi',    'url'=>base_url('admin/siakad/institusi'),    'page'=>'institusi'),
                array('icon'=>'layers',  'label'=>'Data Kelas',   'url'=>base_url('admin/siakad/kelas'),        'page'=>'kelas'),
                array('icon'=>'book',    'label'=>'Mata Pelajaran','url'=>base_url('admin/siakad/mapel'),        'page'=>'mapel'),
            )
        ),
        array(
            'icon'=>'user-check', 
            'label'=>'Guru & Staff', 
            'page'=>'guru',
            'subpages'=>array('data_guru','mengajar','tugas_guru'),
            'dropdown'=>array(
                array('icon'=>'users',    'label'=>'Data Guru',     'url'=>base_url('admin/siakad/guru'),          'page'=>'data_guru'),
                array('icon'=>'book-open','label'=>'Jadwal Mengajar','url'=>base_url('admin/siakad/guru/mengajar'), 'page'=>'mengajar'),
                array('icon'=>'clipboard','label'=>'Tugas Tambahan','url'=>base_url('admin/siakad/guru/tugas'),    'page'=>'tugas_guru'),
            )
        ),
        array(
            'icon'=>'users', 
            'label'=>'Kesiswaan', 
            'page'=>'siswa',
            'subpages'=>array('data_siswa','naik_kelas','alumni'),
            'dropdown'=>array(
                array('icon'=>'user-plus',  'label'=>'Data Siswa', 'url'=>base_url('admin/siakad/siswa'),            'page'=>'data_siswa'),
                array('icon'=>'trending-up','label'=>'Naik Kelas', 'url'=>base_url('admin/siakad/siswa/naik-kelas'), 'page'=>'naik_kelas'),
                array('icon'=>'graduation-cap','label'=>'Alumni',  'url'=>base_url('admin/siakad/siswa/alumni'),     'page'=>'alumni'),
            )
        ),
        array(
            'icon'=>'file-spreadsheet', 
            'label'=>'Akademik', 
            'page'=>'akademik',
            'subpages'=>array('input_nilai','template_nilai','raport'),
            'dropdown'=>array(
                array('icon'=>'edit-3',  'label'=>'Input Nilai',    'url'=>base_url('admin/siakad/nilai'),          'page'=>'input_nilai'),
                array('icon'=>'file-text','label'=>'Template Nilai', 'url'=>base_url('admin/siakad/nilai/template'), 'page'=>'template_nilai'),
                array('icon'=>'book',     'label'=>'E-Raport',      'url'=>base_url('admin/siakad/raport'),         'page'=>'raport'),
            )
        ),
        array('icon'=> 'refresh-cw', 'label'=> 'Sinkron CBT', 'url'=> base_url('admin/siakad/sinkron-cbt'), 'page'=> 'sinkron'),
        array(
            'icon'=>'folder-open', 
            'label'=>'Lain-lain', 
            'page'=>'lain_lain',
            'subpages'=>array('rapat'),
            'dropdown'=>array(
                array('icon'=>'users', 'label'=>'Daftar Hadir Rapat', 'url'=>base_url('admin/siakad/lain-lain/rapat'), 'page'=>'rapat'),
                array('icon'=>'bell', 'label'=>'Broadcast Notifikasi', 'url'=>base_url('admin/siakad/lain-lain/broadcast'), 'page'=>'broadcast'),
            )
        ),
    ),
    'ujian_madrasah' => array(
        array('icon'=> 'layout-dashboard', 'label'=> 'Dashboard',    'url'=> base_url('admin/ujian-madrasah'),        'page'=> 'overview'),
        array('icon'=> 'folder-open',      'label'=> 'Berkas Ujian', 'url'=> base_url('admin/ujian-madrasah/berkas'), 'page'=> 'berkas'),
        array('icon'=> 'trending-up',      'label'=> 'Nilai Ujian',  'url'=> base_url('admin/ujian-madrasah/nilai'),  'page'=> 'nilai'),
    ),
    'piagam' => array(
        array('icon'=> 'layout-dashboard', 'label'=> 'Dashboard',    'url'=> base_url('admin/piagam'),            'page'=> 'overview'),
        array('icon'=> 'scroll',           'label'=> 'Template',     'url'=> base_url('admin/piagam/templates'),   'page'=> 'templates'),
        array('icon'=> 'palette',          'label'=> 'Designer',     'url'=> base_url('admin/piagam/designer'),    'page'=> 'designer'),
        array('icon'=> 'users',            'label'=> 'Data Peserta', 'url'=> base_url('admin/piagam/peserta'),     'page'=> 'peserta'),
        array('icon'=> 'wand2',            'label'=> 'Generate',     'url'=> base_url('admin/piagam/generate'),    'page'=> 'generate'),
        array('icon'=> 'folder-open',      'label'=> 'Asset Manager','url'=> base_url('admin/piagam/assets'),      'page'=> 'assets'),
        array('icon'=> 'history',          'label'=> 'Riwayat',      'url'=> base_url('admin/piagam/riwayat'),     'page'=> 'riwayat'),
        array('icon'=> 'settings',         'label'=> 'Pengaturan',   'url'=> base_url('admin/piagam/pengaturan'),  'page'=> 'pengaturan'),
    ),
    'ai_generator' => array(
        array('icon'=> 'layout-dashboard', 'label'=> 'Dashboard',          'url'=> base_url('admin/ai-generator'),             'page'=> 'dashboard'),
        array('icon'=> 'sparkles',         'label'=> 'AI Generator Soal',  'url'=> base_url('admin/ai-generator/create'),      'page'=> 'create'),
        array('icon'=> 'database',         'label'=> 'Bank Soal',          'url'=> base_url('admin/ai-generator/bank-soal'),   'page'=> 'bank_soal'),
        array('icon'=> 'package',          'label'=> 'Paket Ujian',        'url'=> base_url('admin/ai-generator/paket'),       'page'=> 'paket'),
        array('icon'=> 'network',          'label'=> 'Kisi-Kisi',          'url'=> base_url('admin/ai-generator/kisi-kisi'),   'page'=> 'kisi_kisi'),
        array('icon'=> 'printer',          'label'=> 'Export',             'url'=> base_url('admin/ai-generator/export'),      'page'=> 'export'),
        array('icon'=> 'history',          'label'=> 'Riwayat Generate',   'url'=> base_url('admin/ai-generator/riwayat'),     'page'=> 'riwayat'),
        array('icon'=> 'settings',         'label'=> 'Pengaturan AI',      'url'=> base_url('admin/ai-generator/settings'),    'page'=> 'settings'),
    ),
    'tabungan' => array(
        array('icon'=>'layout-dashboard',  'label'=>'Overview',            'url'=>base_url('admin/tabungan'),                 'page'=>'overview'),
        array('icon'=>'monitor-smartphone','label'=>'Kasir',               'url'=>base_url('admin/tabungan/kasir'),           'page'=>'kasir'),
        array('icon'=>'users',             'label'=>'Rekening Siswa',      'url'=>base_url('admin/tabungan/siswa'),           'page'=>'siswa'),
        array('icon'=>'history',           'label'=>'Riwayat Mutasi',      'url'=>base_url('admin/tabungan/mutasi'),          'page'=>'mutasi'),
        array('icon'=>'shield-check',      'label'=>'Hak Akses',           'url'=>base_url('admin/tabungan/akses'),           'page'=>'akses', 'admin_only'=>true),
    ),
    'pkkm' => array(
        array('icon'=>'layout-dashboard',  'label'=>'Dashboard', 'url'=>base_url('admin/pkkm'),           'page'=>'dashboard'),
        array('icon'=>'file-check-2',      'label'=>'Instrumen', 'url'=>base_url('admin/pkkm/instrumen'), 'page'=>'instrumen'),
        array('icon'=>'folder-open',       'label'=>'Dokumen',   'url'=>base_url('admin/pkkm/dokumen'),   'page'=>'dokumen'),
    ),
    'perpus' => array(
        array('icon'=>'layout-dashboard',  'label'=>'Dashboard Utama',     'url'=>base_url('admin/perpus'), 'page'=>'dashboard_utama'),
        array(
            'icon'=>'book-open', 
            'label'=>'E-Library', 
            'page'=>'elibrary',
            'subpages'=>array('ebook', 'tambah', 'perpus_kategori'),
            'dropdown'=>array(
                array('icon'=>'monitor-play', 'label'=>'Daftar E-Book', 'url'=>base_url('admin/perpus/ebook'), 'page'=>'ebook'),
                array('icon'=>'book-plus', 'label'=>'Upload E-Book', 'url'=>base_url('admin/perpus/tambah'), 'page'=>'tambah'),
                array('icon'=>'tags', 'label'=>'Master Kategori', 'url'=>base_url('admin/perpus/kategori'), 'page'=>'perpus_kategori'),
            )
        ),
        array(
            'icon'=>'book', 
            'label'=>'Buku Fisik', 
            'page'=>'buku_fisik_master',
            'subpages'=>array('buku_fisik', 'rak', 'penulis_penerbit'),
            'dropdown'=>array(
                array('icon'=>'book', 'label'=>'Katalog Buku', 'url'=>base_url('admin/perpus/fisik'), 'page'=>'buku_fisik'),
                array('icon'=>'layers', 'label'=>'Master Rak', 'url'=>base_url('admin/perpus/rak'), 'page'=>'rak'),
                array('icon'=>'users-2', 'label'=>'Penulis & Penerbit', 'url'=>base_url('admin/perpus/penulis-penerbit'), 'page'=>'penulis_penerbit'),
            )
        ),
        array(
            'icon'=>'repeat', 
            'label'=>'Sirkulasi', 
            'page'=>'sirkulasi_master',
            'subpages'=>array('sirkulasi', 'kunjungan'),
            'dropdown'=>array(
                array('icon'=>'arrow-right-left', 'label'=>'Sirkulasi & Pinjam', 'url'=>base_url('admin/perpus/sirkulasi'), 'page'=>'sirkulasi'),
                array('icon'=>'users', 'label'=>'Buku Tamu & Scan', 'url'=>base_url('admin/perpus/kunjungan'), 'page'=>'kunjungan'),
            )
        ),
    ),
    'osim' => array(
        array('icon'=>'layout-dashboard',  'label'=>'Dashboard OSIM',      'url'=>base_url('osim'),                           'page'=>'overview'),
        array('icon'=>'users',             'label'=>'Pengurus',            'url'=>base_url('osim/pengurus'),                  'page'=>'pengurus'),
        array('icon'=>'briefcase',         'label'=>'Proker',              'url'=>base_url('osim/proker'),                    'page'=>'proker'),
        array('icon'=>'file-text',         'label'=>'Proposal',            'url'=>base_url('osim/proposal'),                  'page'=>'proposal'),
        array(
            'icon'=>'vote', 
            'label'=>'E-Voting', 
            'page'=>'evoting',
            'subpages'=>array('evoting_dashboard','evoting_kandidat'),
            'dropdown'=>array(
                array('icon'=>'layout-dashboard','label'=>'Dashboard E-Voting', 'url'=>base_url('osim/evoting'), 'page'=>'evoting_dashboard'),
                array('icon'=>'users',           'label'=>'Kandidat', 'url'=>base_url('osim/evoting/kandidat'), 'page'=>'evoting_kandidat'),
            )
        ),
        array('icon'=>'settings',          'label'=>'Pengaturan',          'url'=>base_url('osim/pengaturan'),                'page'=>'pengaturan'),
    ),
    'pkkm' => array(
        array('icon'=>'layout-dashboard',  'label'=>'Dashboard',           'url'=>base_url('admin/pkkm'),                     'page'=>'overview'),
        array('icon'=>'file-check-2',      'label'=>'Instrumen Penilaian', 'url'=>base_url('admin/pkkm/instrumen'),           'page'=>'instrumen'),
        array('icon'=>'folder-open',       'label'=>'Daftar Berkas',       'url'=>base_url('admin/pkkm/master-berkas'),       'page'=>'master_berkas'),
        array('icon'=>'users-2',           'label'=>'Tim Penanggung Jawab','url'=>base_url('admin/pkkm/tim'),                 'page'=>'tim_pkkm'),
        array('icon'=>'user-check',        'label'=>'Akun Pengawas',       'url'=>base_url('admin/pkkm/pengawas'),            'page'=>'pengawas'),
    ),
);

$module_labels = array(
    'finance'        => 'Keuangan',
    'absen_v2'       => 'Absen',
    'siakad'         => 'SIAKAD',
    'bos'            => 'BOS',
    'osim'           => 'OSIM',
    'perpus'         => 'Perpustakaan',
    'pkkm'           => 'PKKM',
);
$current_label = isset($module_labels[$am]) ? $module_labels[$am] : 'Admin';
?>
<style>
    .z-sidebar a.z-nav-item { text-decoration: none !important; }
    .z-sidebar .z-nav-dropdown, 
    .z-sidebar .z-nav-dropdown-toggle, 
    .z-sidebar .z-nav-item {
        margin-top: 1px !important;
        margin-bottom: 1px !important;
    }
    .z-sidebar .z-nav-dropdown-toggle, 
    .z-sidebar .z-nav-item {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    .z-sidebar .z-nav-dropdown-menu .z-nav-item {
        padding-top: 0.4rem !important;
        padding-bottom: 0.4rem !important;
    }
</style>
<div class="z-sidebar" id="zSidebar">
    <div class="z-sidebar-logo">
        <div class="logo-icon" style="background: transparent; padding: 0;">
            <img src="<?php echo \App\Core\Branding::get()['logo']; ?>" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px;">
        </div>
        <div>
            <div class="logo-text"><?php echo \App\Core\Branding::get()['name']; ?></div>
            <div class="logo-sub"><?php echo $current_label; ?></div>
        </div>
    </div>

    <div class="z-sidebar-scroll">
        <!-- Academic Year Switcher -->
        <?php include __DIR__ . '/../partials/academic_year_widget.php'; ?>
        
        <!-- Module Sub-menu -->
        <?php if(isset($sub_menus[$am])): ?>
        <div class="z-nav-cat"><?php echo strtoupper($current_label); ?></div>
        <?php foreach($sub_menus[$am] as $item): ?>
            <?php 
            // Cek batasan akses menu (khusus finance)
            if ($am === 'finance' && isset($allowed_menus) && !in_array('all', $allowed_menus)) {
                if (isset($item['dropdown'])) {
                    $any_sub_allowed = false;
                    foreach ($item['dropdown'] as $sub) {
                        if (in_array($sub['page'], $allowed_menus)) {
                            $any_sub_allowed = true;
                            break;
                        }
                    }
                    if (!$any_sub_allowed) continue;
                } else {
                    if (!in_array($item['page'], $allowed_menus)) continue;
                }
            }
            // Cek admin only
            if (isset($item['admin_only']) && $item['admin_only'] === true && !in_array($_SESSION['role_id'] ?? 0, [1, 99])) {
                continue;
            }
            ?>
            <?php if(isset($item['dropdown'])): ?>
                <?php $isOpen = ($ap===$item['page'] || (isset($item['subpages']) && in_array($ap, $item['subpages']))); ?>
                <div class="z-nav-dropdown <?php echo $isOpen ? 'open' : ''; ?>">
                    <div class="z-nav-dropdown-toggle <?php echo $isOpen ? 'active' : ''; ?>" onclick="var p = this.parentElement; document.querySelectorAll('.z-nav-dropdown').forEach(function(d){ if(d !== p) d.classList.remove('open'); }); p.classList.toggle('open');">
                        <div class="dt-left">
                            <i data-lucide="<?php echo $item['icon']; ?>"></i>
                            <?php echo $item['label']; ?>
                        </div>
                        <i data-lucide="chevron-down" class="dt-icon"></i>
                    </div>
                    <div class="z-nav-dropdown-menu">
                        <?php foreach($item['dropdown'] as $sub): ?>
                        <?php 
                        if ($am === 'finance' && isset($allowed_menus) && !in_array('all', $allowed_menus)) {
                            if (!in_array($sub['page'], $allowed_menus)) continue;
                        }
                        ?>
                        <a href="<?php echo $sub['url']; ?>" class="z-nav-item <?php echo $ap===$sub['page']?'active':''; ?>">
                            <i data-lucide="<?php echo $sub['icon']; ?>"></i>
                            <?php echo $sub['label']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo $item['url']; ?>" class="z-nav-item <?php echo $ap===$item['page']?'active':''; ?>">
                    <i data-lucide="<?php echo $item['icon']; ?>"></i>
                    <?php echo $item['label']; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div class="z-sidebar-foot" style="display:none;"></div>
</div>
<div class="z-overlay" id="zOverlay" onclick="zToggleSidebar()"></div>

