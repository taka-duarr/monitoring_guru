<?php
$m = glob(__DIR__ . '/database/migrations/*_create_gurus_table.php')[0];
file_put_contents($m, str_replace('email', 'nik', file_get_contents($m)));
file_put_contents(__DIR__ . '/app/Models/Guru.php', str_replace('email', 'nik', file_get_contents(__DIR__ . '/app/Models/Guru.php')));
file_put_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php', str_replace('email', 'nik', file_get_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php')));
echo "Done replacing email to nik.";
