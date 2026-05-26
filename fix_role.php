<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Guru::where('jabatan', 'kepala_sekolah')->update(['jabatan' => 'admin']);
echo "Updated to admin";
