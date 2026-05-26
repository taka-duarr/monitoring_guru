<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Generated CRUD Routes
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
