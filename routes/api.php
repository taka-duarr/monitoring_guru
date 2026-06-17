<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    
    // Rute API untuk fitur absensi (akan dibuat controllernya)
    Route::get('/jadwal', [App\Http\Controllers\Api\JadwalController::class, 'index']);
    Route::post('/scan', [App\Http\Controllers\Api\AbsensiController::class, 'scan']);
    Route::get('/status-kelas', [App\Http\Controllers\Api\StatusKelasController::class, 'index']);
    Route::get('/riwayat', [App\Http\Controllers\Api\JadwalController::class, 'riwayatGlobal']);
    Route::get('/riwayat-mapel/{mapel_id}', [App\Http\Controllers\Api\JadwalController::class, 'riwayatMapel']);
    Route::get('/absen-murid/{absen_masuk_id}', [App\Http\Controllers\Api\AbsensiController::class, 'getAbsenMurid']);
    Route::post('/absen-murid/{absen_masuk_id}', [App\Http\Controllers\Api\AbsensiController::class, 'saveAbsenMurid']);
    
    // Rute API untuk Izin
    Route::get('/izin', [App\Http\Controllers\Api\IzinController::class, 'index']);
    Route::post('/izin', [App\Http\Controllers\Api\IzinController::class, 'store']);

    // Rute API untuk Profil
    Route::post('/profile/update', [App\Http\Controllers\Api\ProfileController::class, 'update']);
});
