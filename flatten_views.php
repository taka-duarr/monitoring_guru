<?php
$models = ['Guru', 'Kelas', 'Jurusan', 'JadwalAjar', 'AbsenMasuk'];

foreach ($models as $model) {
    $lower = strtolower($model);
    $oldViewPath = __DIR__ . "/resources/views/admin/{$lower}/index.blade.php";
    $newViewPath = __DIR__ . "/resources/views/admin/{$lower}.blade.php";
    
    // Move view file
    if (file_exists($oldViewPath)) {
        rename($oldViewPath, $newViewPath);
        rmdir(dirname($oldViewPath)); // delete the empty folder
    }

    // Update Controller
    $controllerPath = __DIR__ . "/app/Http/Controllers/{$model}Controller.php";
    if (file_exists($controllerPath)) {
        $content = file_get_contents($controllerPath);
        $content = str_replace("view('admin.{$lower}.index',", "view('admin.{$lower}',", $content);
        file_put_contents($controllerPath, $content);
    }
}
echo "Flattned view structure successfully!";
