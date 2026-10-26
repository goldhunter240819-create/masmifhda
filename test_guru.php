<?php
require 'app/Core/Database.php';
$db_core = \App\Core\Database::connect('core');
$stmtGuru = $db_core->query('SELECT id, nama FROM guru ORDER BY nama ASC');
$guru_list = $stmtGuru->fetchAll(\PDO::FETCH_ASSOC);
echo count($guru_list);

