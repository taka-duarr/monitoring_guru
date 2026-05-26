<?php
$files = [
    'database/migrations/2026_05_26_003620_create_ketua_kelas_table.php',
    'database/seeders/DatabaseSeeder.php',
    'resources/views/admin/ketuakelas.blade.php',
    'resources/views/admin/ketuakelas_form.blade.php',
    'app/Http\Controllers\KetuaKelasController.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_ireplace('nisn', 'nik', $content);
        $content = str_replace('NISN', 'NIK', $content); // handle capitalization
        file_put_contents($file, $content);
        echo "Updated $file\n";
    } else {
        echo "File not found: $file\n";
    }
}
