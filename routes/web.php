<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Technician\TechnicianController;

Route::get('/', function () {
    return view('index');
});

Route::view('/login', 'Authentication.login')->name('login');
Route::view('/register', 'Authentication.register')->name('register');
Route::view('/forgot-password', 'Authentication.forgot-password')->name('password.request');
Route::view('/reset-password', 'Authentication.reset-password')->name('password.reset');

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    // Generic logged-in dashboard (used by Breeze auth scaffolding)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->to('/');
    })->name('logout.get');
});

Route::prefix('technician')->middleware('auth')->group(function () {
    Route::get('/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');
    Route::get('/tasks', [TechnicianController::class, 'tasks'])->name('technician.tasks');
    Route::get('/tasks/{id}', [TechnicianController::class, 'taskDetail'])->name('technician.task_detail');
    Route::get('/tasks/{id}/complete', function ($id) {
        return redirect()->route('technician.task_detail', $id);
    });
    Route::get('/profile', [TechnicianController::class, 'profile'])->name('technician.profile');
    Route::post('/profile', [TechnicianController::class, 'updateProfile'])->name('technician.profile.update');
    Route::post('/profile/password', [TechnicianController::class, 'updatePassword'])->name('technician.profile.password');
    Route::get('/completed', [TechnicianController::class, 'completed'])->name('technician.completed');

    Route::delete('/tasks/{id}/attachments/{attachment}', [TechnicianController::class, 'deleteAfterPhoto'])
        ->name('technician.delete_after');

    Route::post('/tasks/{id}/update-status', [TechnicianController::class, 'updateStatus'])->name('technician.update_status');
    Route::post('/tasks/{id}/complete', [TechnicianController::class, 'completeJob'])->name('technician.complete_job');
});

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return 'DB OK: ' . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return 'DB ERROR: ' . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
