<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.sppg');
})->name('home');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::prefix('presensi')->name('presensi.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/data/presensi', [DashboardController::class, 'getPresensiData'])
        ->name('data.presensi');

    Route::post('/reset-hari-ini', [PresensiController::class, 'resetHariIni'])
        ->name('reset.hari-ini');

    Route::get('/manual', [PresensiController::class, 'manualCreate'])
        ->name('manual.create');

    Route::post('/manual', [PresensiController::class, 'manualStore'])
        ->name('manual.store');
        
    Route::post('/riwayat/{presensi}/checkout-manual', [PresensiController::class, 'manualCheckout'])
        ->name('manual.checkout');

    Route::get('/riwayat', [RiwayatController::class, 'index'])
        ->name('riwayat.index');

    Route::get('/riwayat/export', [RiwayatController::class, 'export'])
        ->name('riwayat.export');

    Route::resource('pegawai', PegawaiController::class);
    Route::resource('divisi', DivisiController::class);

    Route::post('/mode/registrasi', [PegawaiController::class, 'setRegistrationMode'])
        ->name('mode.registrasi');

    Route::post('/mode/presensi', [PegawaiController::class, 'setPresensiMode'])
        ->name('mode.presensi');
});
