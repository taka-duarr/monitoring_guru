<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// AUTH - Semua Role Login dari Sini
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect /siswa/login ke /login (tidak ada lagi halaman login terpisah)
Route::get('/siswa/login', function () {
    return redirect()->route('login');
})->name('siswa.login');

// ==========================================
// ADMIN ROUTES (Hanya untuk Admin)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // CRUD Routes
    Route::resource('guru', App\Http\Controllers\GuruController::class);
    Route::resource('kelas', App\Http\Controllers\KelasController::class);
    Route::resource('jurusan', App\Http\Controllers\JurusanController::class);
    Route::resource('jadwalajar', App\Http\Controllers\JadwalAjarController::class);
    Route::resource('absenmasuk', App\Http\Controllers\AbsenMasukController::class);
    Route::resource('ketuakelas', App\Http\Controllers\KetuaKelasController::class);
    Route::resource('mapel', App\Http\Controllers\MapelController::class);
    Route::resource('ruangan', App\Http\Controllers\RuanganController::class);
    Route::resource('izin', App\Http\Controllers\IzinController::class);
    Route::resource('absenkeluar', App\Http\Controllers\AbsenKeluarController::class);
    Route::resource('statuskelas', App\Http\Controllers\StatusKelasController::class);
});

// ==========================================
// GURU ROUTES (Hanya untuk Guru)
// ==========================================
Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\GuruPortalController::class, 'dashboard'])->name('guru.dashboard');
    Route::get('/scan', [App\Http\Controllers\GuruPortalController::class, 'scan'])->name('guru.scan');
    Route::post('/scan', [App\Http\Controllers\AbsensiController::class, 'processQr'])->name('guru.processQr');
    Route::get('/izin', [App\Http\Controllers\GuruPortalController::class, 'izin'])->name('guru.izin');
    Route::get('/riwayat-mapel/{mapel_id}', [App\Http\Controllers\GuruPortalController::class, 'riwayatMapel'])->name('guru.riwayat_mapel');
});

// ==========================================
// SISWA / KETUA KELAS ROUTES
// ==========================================
Route::get('/siswa', function () {
    return redirect()->route('siswa.dashboard');
});

Route::middleware(['siswa'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SiswaPortalController::class, 'dashboard'])->name('siswa.dashboard');
});
