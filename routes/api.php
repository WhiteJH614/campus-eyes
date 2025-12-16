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

// API v1 Routes
Route::prefix('v1')->group(function () {
    // Report endpoints (public for other modules to access)
    Route::get('/reports', [ReportApiController::class, 'index']);
    Route::get('/reports/stats', [ReportApiController::class, 'stats']);
    Route::get('/reports/{id}', [ReportApiController::class, 'show']);
});
