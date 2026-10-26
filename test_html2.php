<?php
require 'app/Core/Database.php';
require 'app/Core/AcademicYear.php';
$db = \App\Core\Database::connect('core');
$selected_kelas = 4;
$active_year_name = '2026/2027';
$where = "WHERE s.status IN ('Aktif', 'Alumni') AND (rks.id IS NOT NULL OR s.tahun_ajaran = '$active_year_name')";
$where .= " AND COALESCE(rks.kelas_id, s.kelas_id) = 4";
$siswas = $db->query("SELECT s.*, u.username, u.plain_password, 
                     COALESCE(k_hist.nama_kelas, k.nama_kelas) as nama_kelas,
                     COALESCE(rks.kelas_id, s.kelas_id) as actual_kelas_id
                     FROM siswa s 
                     LEFT JOIN riwayat_kelas_siswa rks ON s.id = rks.siswa_id 
                         AND rks.tahun_ajaran_id IN (
                             SELECT id FROM tahun_ajaran WHERE name = '$active_year_name'
                         )
                     LEFT JOIN users u ON s.user_id = u.id 
                     LEFT JOIN kelas k ON s.kelas_id = k.id 
                     LEFT JOIN kelas k_hist ON rks.kelas_id = k_hist.id
                     $where
                     ORDER BY s.nama ASC")->fetchAll();
foreach ($siswas as &$s) {
    $s['qr_token'] = "TOKEN:" . $s['user_id'] . ":" . substr(md5($s['user_id'] . 'MASMIFHDA_SECURE_QR_2026'), 0, 16);
}
$kelas = $db->query("SELECT * FROM kelas ORDER BY nama_kelas ASC")->fetchAll();

function base_url($path = '') { return '/' . $path; }

ob_start();
?>
<table id="table-data-siswa">
    <tbody>
        <?php foreach($siswas as $s): ?>
        <tr>
            <td><?php echo $s['nis']; ?></td>
            <td><?php echo $s['nama']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
file_put_contents('html_output2.txt', $content);
echo "DONE";
