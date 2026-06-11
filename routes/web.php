<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MuridController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;

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
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // CRUD Routes
    Route::get('guru/export', [App\Http\Controllers\GuruController::class, 'export'])->name('guru.export');
    Route::get('guru/import/template', [App\Http\Controllers\GuruController::class, 'downloadTemplate'])->name('guru.import.template');
    Route::post('guru/import', [App\Http\Controllers\GuruController::class, 'import'])->name('guru.import');
    Route::resource('guru', App\Http\Controllers\GuruController::class);
    Route::resource('users', UserController::class);

    // Nested routes for Murid
    Route::get('kelas/{kelas}/murid', [MuridController::class, 'index'])->name('kelas.murid.index');
    Route::get('kelas/{kelas}/murid/import/template', [MuridController::class, 'downloadTemplate'])->name('kelas.murid.import.template');
    Route::post('kelas/{kelas}/murid/import', [MuridController::class, 'import'])->name('kelas.murid.import');
    Route::post('kelas/{kelas}/murid', [MuridController::class, 'store'])->name('kelas.murid.store');
    Route::put('kelas/{kelas}/murid/{murid}', [MuridController::class, 'update'])->name('kelas.murid.update');
    Route::delete('kelas/{kelas}/murid/{murid}', [MuridController::class, 'destroy'])->name('kelas.murid.destroy');

    Route::resource('angkatan', App\Http\Controllers\AngkatanController::class);
    Route::get('kelas/export', [App\Http\Controllers\KelasController::class, 'export'])->name('kelas.export');
    Route::get('kelas/import/template', [App\Http\Controllers\KelasController::class, 'downloadTemplate'])->name('kelas.import.template');
    Route::post('kelas/import', [App\Http\Controllers\KelasController::class, 'import'])->name('kelas.import');
    Route::resource('kelas', App\Http\Controllers\KelasController::class);
    Route::get('jurusan/export', [App\Http\Controllers\JurusanController::class, 'export'])->name('jurusan.export');
    Route::get('jurusan/import/template', [App\Http\Controllers\JurusanController::class, 'downloadTemplate'])->name('jurusan.import.template');
    Route::post('jurusan/import', [App\Http\Controllers\JurusanController::class, 'import'])->name('jurusan.import');
    Route::resource('jurusan', App\Http\Controllers\JurusanController::class);
    Route::get('jadwalajar/export', [App\Http\Controllers\JadwalAjarController::class, 'export'])->name('jadwalajar.export');
    Route::get('jadwalajar/import/template', [App\Http\Controllers\JadwalAjarController::class, 'downloadTemplate'])->name('jadwalajar.import.template');
    Route::post('jadwalajar/import', [App\Http\Controllers\JadwalAjarController::class, 'import'])->name('jadwalajar.import');
    Route::resource('jadwalajar', App\Http\Controllers\JadwalAjarController::class);
    
    // Absen Masuk & Murid Read-only
    Route::get('absenmasuk/{absenmasuk}/murid', [App\Http\Controllers\AbsenMasukController::class, 'murid'])->name('absenmasuk.murid');
    Route::resource('absenmasuk', App\Http\Controllers\AbsenMasukController::class);
    Route::get('ketuakelas/export', [App\Http\Controllers\KetuaKelasController::class, 'export'])->name('ketuakelas.export');
    Route::get('ketuakelas/import/template', [App\Http\Controllers\KetuaKelasController::class, 'downloadTemplate'])->name('ketuakelas.import.template');
    Route::post('ketuakelas/import', [App\Http\Controllers\KetuaKelasController::class, 'import'])->name('ketuakelas.import');
    Route::resource('ketuakelas', App\Http\Controllers\KetuaKelasController::class);
    Route::get('mapel/export', [App\Http\Controllers\MapelController::class, 'export'])->name('mapel.export');
    Route::get('mapel/import/template', [App\Http\Controllers\MapelController::class, 'downloadTemplate'])->name('mapel.import.template');
    Route::post('mapel/import', [App\Http\Controllers\MapelController::class, 'import'])->name('mapel.import');
    Route::resource('mapel', App\Http\Controllers\MapelController::class);
    Route::get('ruangan/export', [App\Http\Controllers\RuanganController::class, 'export'])->name('ruangan.export');
    Route::get('ruangan/import/template', [App\Http\Controllers\RuanganController::class, 'downloadTemplate'])->name('ruangan.import.template');
    Route::post('ruangan/import', [App\Http\Controllers\RuanganController::class, 'import'])->name('ruangan.import');
    Route::resource('ruangan', App\Http\Controllers\RuanganController::class);
    Route::post('izin/{id}/approve', [App\Http\Controllers\IzinController::class, 'approve'])->name('izin.approve');
    Route::resource('izin', App\Http\Controllers\IzinController::class);

    Route::resource('statuskelas', App\Http\Controllers\StatusKelasController::class);

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('laporan', [LaporanController::class, 'generate'])->name('laporan.generate');
    Route::get('laporan/riwayat', [LaporanController::class, 'riwayat'])->name('laporan.riwayat');
    Route::get('laporan/{riwayat}/download', [LaporanController::class, 'download'])->name('laporan.download');
    Route::delete('laporan/{riwayat}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

    // Pengaturan
    Route::get('pengaturan', [App\Http\Controllers\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('pengaturan', [App\Http\Controllers\PengaturanController::class, 'update'])->name('pengaturan.update');

    // Profil Saya
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'index'])->name('admin.profile.index');
    Route::put('/profil', [App\Http\Controllers\ProfileController::class, 'update'])->name('admin.profile.update');
});

// ==========================================
// GURU ROUTES (Hanya untuk Guru)
// ==========================================
Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\GuruPortalController::class, 'dashboard'])->name('guru.dashboard');
    Route::get('/scan', [App\Http\Controllers\GuruPortalController::class, 'scan'])->name('guru.scan');
    Route::post('/scan', [App\Http\Controllers\AbsensiController::class, 'processQr'])->name('guru.processQr');
    Route::get('/izin', [App\Http\Controllers\GuruPortalController::class, 'izin'])->name('guru.izin');
    Route::post('/izin', [App\Http\Controllers\GuruPortalController::class, 'storeIzin'])->name('guru.store_izin');
    Route::get('/riwayat-mapel/{mapel_id}', [App\Http\Controllers\GuruPortalController::class, 'riwayatMapel'])->name('guru.riwayat_mapel');
    Route::get('/absen-murid/{absen_masuk_id}', [App\Http\Controllers\GuruPortalController::class, 'absenMurid'])->name('guru.absen_murid');
    Route::post('/absen-murid/{absen_masuk_id}', [App\Http\Controllers\GuruPortalController::class, 'storeAbsenMurid'])->name('guru.store_absen_murid');

    // Profil Saya
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'index'])->name('guru.profile.index');
    Route::put('/profil', [App\Http\Controllers\ProfileController::class, 'update'])->name('guru.profile.update');
});

// ==========================================
// SISWA / KETUA KELAS ROUTES
// ==========================================
Route::get('/siswa', function () {
    return redirect()->route('siswa.dashboard');
});

Route::middleware(['siswa'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SiswaPortalController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/jadwal/{id}/status', [App\Http\Controllers\SiswaPortalController::class, 'checkStatus'])->name('siswa.check_status');

    // Profil Saya
    Route::get('/profil', [App\Http\Controllers\ProfileController::class, 'index'])->name('siswa.profile.index');
    Route::put('/profil', [App\Http\Controllers\ProfileController::class, 'update'])->name('siswa.profile.update');
});
