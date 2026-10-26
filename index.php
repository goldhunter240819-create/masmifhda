<?php
$baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($baseUri === '/') $baseUri = '';
header('Location: ' . $baseUri . '/portal');
exit;

