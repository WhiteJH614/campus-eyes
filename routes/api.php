<?php

use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// API v1 Routes - IFA Compliant Web Services
Route::prefix('v1')->group(function () {
    // Report endpoints (exposed for other modules to consume)
    Route::get('/reports', [ReportApiController::class, 'index'])->name('api.reports.index');
    Route::get('/reports/stats', [ReportApiController::class, 'stats'])->name('api.reports.stats');
    Route::get('/reports/status/{status}', [ReportApiController::class, 'byStatus'])->name('api.reports.byStatus');
    Route::get('/reports/urgency/{urgency}', [ReportApiController::class, 'byUrgency'])->name('api.reports.byUrgency');
    Route::get('/reports/{id}', [ReportApiController::class, 'show'])->name('api.reports.show');
});
