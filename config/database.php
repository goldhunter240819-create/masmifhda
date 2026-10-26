<?php

/**
 * Database Configuration - Aula System (/masmifhda)
 * 
 * ⚠️ PENTING: Database ini TERPISAH dari sistem /mismifhda
 * Semua database menggunakan prefix 'aula_' agar tidak tercampur
 * 
 * /mismifhda → core_db, siakad_db, absen_db (sistem lain)
 * /masmifhda → aula_db, aula_siakad_db, aula_absen_db (sistem ini)
 */

return [
    // Database inti Aula System - users, guru, siswa, keuangan, modul
    'core' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_db',
        'username' => 'root',
        'password' => '',
    ],

    // Database SIAKAD khusus Aula System
    'siakad' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_siakad_db',
        'username' => 'root',
        'password' => '',
    ],

    // Database Absensi QR V2 khusus Aula System
    'absen' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_absen_db',
        'username' => 'root',
        'password' => '',
    ],

    // Database Absensi Siswa khusus Aula System
    'absensi' => [
        'host'     => '127.0.0.1',
        'db_name'  => 'aula_absensi_db',
        'username' => 'root',
        'password' => '',
    ],
];
