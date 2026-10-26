<?php
$path = 'c:/xampp/htdocs/masmifhda/public/index.php';
$content = file_get_contents($path);

// 1. Fix array definitions for siakadRoutes and others
$content = preg_replace("/\['([^']+)',\s*base_url\('([^']+)'\)/", "['$1', '/$2'", $content);

// 2. Fix the foreach logic for siakadRoutes
$content = str_replace("if (\$r[1] === base_url('siakad')) {", "if (\$r[1] === '/siakad') {", $content);
$content = str_replace("\$router->add(\$r[0], base_url('admin/siakad/dashboard'), \$r[2]);", "\$router->add(\$r[0], '/admin/siakad/dashboard', \$r[2]);", $content);
$content = str_replace("\$router->add(\$r[0], str_replace(base_url('siakad'), base_url('admin/siakad'), \$r[1]), \$r[2]);", "\$router->add(\$r[0], str_replace('/siakad', '/admin/siakad', \$r[1]), \$r[2]);", $content);

// 3. Fix double base_url() inside redirect()
// e.g. redirect(base_url('login')) -> redirect('/login')
$content = preg_replace("/redirect\(base_url\('([^']+)'\)\)/", "redirect('/$1')", $content);

file_put_contents($path, $content);
echo "Fixed arrays and double redirects!\n";
