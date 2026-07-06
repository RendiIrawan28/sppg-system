<?php

use App\Http\Controllers\Api\DashboardReportController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardReportController::class, 'dashboard']);

Route::post('/reports', [DashboardReportController::class, 'store']);

Route::post('/reports/bulk', [DashboardReportController::class, 'bulkStore']);

Route::delete('/reports/{dashboardReport}', [DashboardReportController::class, 'destroy']);