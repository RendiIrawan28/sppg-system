<?php

use App\Http\Controllers\Api\DashboardReportController;
use App\Http\Controllers\Api\MobileExecController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModeController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\Api\DashboardMonitoringController;

Route::get('/dashboard', [DashboardReportController::class, 'dashboard']);

// Adapter untuk aplikasi lama yang masih memakai action Apps Script.
Route::post('/exec', [MobileExecController::class, 'handle']);

// Endpoint utama: bisa menerima format baru berbasis category dan format lama berbasis action.
Route::post('/reports', [MobileExecController::class, 'handle']);
Route::post('/reports/bulk', [MobileExecController::class, 'bulk']);
Route::delete('/reports/{category}/{id}', [MobileExecController::class, 'destroyByCategory']);

Route::get('/mode', [ModeController::class, 'status']);
Route::post('/mode', [ModeController::class, 'setMode']);

Route::post('/registerCard', [PresensiController::class, 'registerCard']);
Route::post('/presensi', [PresensiController::class, 'store']);
Route::get('/dashboard', [DashboardMonitoringController::class, 'index']);
