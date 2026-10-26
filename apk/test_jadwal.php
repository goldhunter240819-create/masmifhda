<?php
require 'koneksi.php';
print_r($siakad_db->query('DESCRIBE jadwal_pelajaran')->fetchAll(PDO::FETCH_ASSOC));
