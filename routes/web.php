<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Models\Block;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Reporter Routes (protected by auth and role middleware)
Route::middleware(['auth', 'role:Reporter'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    // IMPORTANT: Specific routes must come BEFORE wildcard routes
    Route::get('/reports/rooms/{blockId}', [ReportController::class, 'getRoomsByBlock'])->name('reports.rooms');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});

require __DIR__.'/auth.php';

