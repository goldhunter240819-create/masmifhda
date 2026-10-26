<?php

function syncSiakad($koneksi, $type = 'all') {
    $siakad_db = "aula_db"; // Menggunakan aula_db sebagai sumber utama (MA)
    $absen_db = "aula_absen_db";
    
    // Sync Guru
    if ($type == 'guru' || $type == 'all') {
        $res_guru = mysqli_query($koneksi, "SELECT g.*, u.username FROM $siakad_db.guru g LEFT JOIN $siakad_db.users u ON g.user_id = u.id");
        if ($res_guru) {
            while ($g = mysqli_fetch_assoc($res_guru)) {
                $nip = mysqli_real_escape_string($koneksi, $g['nip']);
                $nama = mysqli_real_escape_string($koneksi, $g['nama']);
                $jabatan = mysqli_real_escape_string($koneksi, $g['jabatan']);
                $nik = mysqli_real_escape_string($koneksi, $g['nik']);
                $tempat_lahir = mysqli_real_escape_string($koneksi, $g['tempat_lahir']);
                $tanggal_lahir = $g['tanggal_lahir'] ? "'".$g['tanggal_lahir']."'" : "NULL";
                $jenis_kelamin = mysqli_real_escape_string($koneksi, $g['jenis_kelamin']);
                $alamat = mysqli_real_escape_string($koneksi, $g['alamat']);
                $no_hp = mysqli_real_escape_string($koneksi, $g['no_hp']);
                $email = mysqli_real_escape_string($koneksi, $g['email']);
                $foto = mysqli_real_escape_string($koneksi, $g['foto']);
                
                if (empty($nip)) continue;
                
                $cek = mysqli_query($koneksi, "SELECT id FROM $absen_db.users WHERE qr_code = '$nip' AND tipe = 'guru'");
                if (mysqli_num_rows($cek) > 0) {
                    $q = "UPDATE $absen_db.users SET 
                          nama = '$nama', 
                          kelas_mapel = '$jabatan',
                          nik = '$nik',
                          tempat_lahir = '$tempat_lahir',
                          tanggal_lahir = $tanggal_lahir,
                          jenis_kelamin = '$jenis_kelamin',
                          alamat = '$alamat',
                          no_hp = '$no_hp',
                          email = '$email',
                          foto = '$foto'
                          WHERE qr_code = '$nip' AND tipe = 'guru'";
                    mysqli_query($koneksi, $q);
                } else {
                    $q = "INSERT INTO $absen_db.users (nama, tipe, kelas_mapel, qr_code, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_hp, email, foto) 
                          VALUES ('$nama', 'guru', '$jabatan', '$nip', '$nik', '$tempat_lahir', $tanggal_lahir, '$jenis_kelamin', '$alamat', '$no_hp', '$email', '$foto')";
                    mysqli_query($koneksi, $q);
                }
            }
        }
    }
    
    // Sync Siswa
    if ($type == 'siswa' || $type == 'all') {
        $res_siswa = mysqli_query($koneksi, "SELECT s.*, k.nama_kelas 
                                           FROM $siakad_db.siswa s 
                                           LEFT JOIN $siakad_db.kelas k ON s.kelas_id = k.id 
                                           WHERE s.status = 'Aktif'");
        if ($res_siswa) {
            while ($s = mysqli_fetch_assoc($res_siswa)) {
                $nis = mysqli_real_escape_string($koneksi, $s['nis']);
                $nama = mysqli_real_escape_string($koneksi, $s['nama']);
                $kelas = mysqli_real_escape_string($koneksi, $s['nama_kelas']);
                $nisn = mysqli_real_escape_string($koneksi, $s['nisn']);
                $nik = mysqli_real_escape_string($koneksi, $s['nik']);
                $tempat_lahir = mysqli_real_escape_string($koneksi, $s['tempat_lahir']);
                $tanggal_lahir = $s['tanggal_lahir'] ? "'".$s['tanggal_lahir']."'" : "NULL";
                $jenis_kelamin = mysqli_real_escape_string($koneksi, $s['jenis_kelamin']);
                $alamat = mysqli_real_escape_string($koneksi, $s['alamat']);
                $nama_ayah = mysqli_real_escape_string($koneksi, $s['nama_ayah']);
                $nama_ibu = mysqli_real_escape_string($koneksi, $s['nama_ibu']);
                $no_hp_ortu = mysqli_real_escape_string($koneksi, $s['no_hp_ortu']);
                $foto = mysqli_real_escape_string($koneksi, $s['foto']);
                $agama = mysqli_real_escape_string($koneksi, $s['agama']);
                
                if (empty($nis)) continue;
                
                $cek = mysqli_query($koneksi, "SELECT id FROM $absen_db.users WHERE qr_code = '$nis' AND tipe = 'siswa'");
                if (mysqli_num_rows($cek) > 0) {
                    $q = "UPDATE $absen_db.users SET 
                          nama = '$nama', 
                          kelas_mapel = '$kelas',
                          nisn = '$nisn',
                          nik = '$nik',
                          tempat_lahir = '$tempat_lahir',
                          tanggal_lahir = $tanggal_lahir,
                          jenis_kelamin = '$jenis_kelamin',
                          alamat = '$alamat',
                          nama_ayah = '$nama_ayah',
                          nama_ibu = '$nama_ibu',
                          no_hp_ortu = '$no_hp_ortu',
                          foto = '$foto',
                          agama = '$agama'
                          WHERE qr_code = '$nis' AND tipe = 'siswa'";
                    mysqli_query($koneksi, $q);
                } else {
                    $q = "INSERT INTO $absen_db.users (nama, tipe, kelas_mapel, qr_code, nisn, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, nama_ayah, nama_ibu, no_hp_ortu, foto, agama) 
                          VALUES ('$nama', 'siswa', '$kelas', '$nis', '$nisn', '$nik', '$tempat_lahir', $tanggal_lahir, '$jenis_kelamin', '$alamat', '$nama_ayah', '$nama_ibu', '$no_hp_ortu', '$foto', '$agama')";
                    mysqli_query($koneksi, $q);
                }
            }
        }
    }
}
?>
