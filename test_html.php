<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role_id'] = 1;
$_SERVER['HTTP_HOST'] = 'apk.masmifhda.sch.id';
$_SERVER['REQUEST_URI'] = '/siakad/siswa?kelas_id=4&debug_siswas=1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['kelas_id'] = 4;
$_GET['debug_siswas'] = 1;
require 'public/index.php';
