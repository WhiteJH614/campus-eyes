<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Models\Block;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Technician\TechnicianController;

Route::get('/', function () {
    return view('index');
});


Route::redirect('/reset-password', '/forgot-password');

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = strtolower($user->role ?? '');

        return match ($role) {
            'technician' => redirect()->route('technician.dashboard'),
            'reporter' => redirect()->route('reporter.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    // Generic logged-in dashboard (used by Breeze auth scaffolding)
    Route::get('/reporter/dashboard', function () {
        return view('reports.dashboard');
    })->name('reports.dashboard')->middleware('role:Reporter');

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

    Route::post('/tasks/{id}/proofs', [TechnicianController::class, 'addProofImages'])
        ->name('technician.add_proof_images');

    Route::post('/tasks/{id}/update-status', [TechnicianController::class, 'updateStatus'])->name('technician.update_status');
    // Alternate status endpoint to match UI action
    Route::post('/tasks/{id}/status', [TechnicianController::class, 'updateStatus']);
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

// Reporter Routes (protected by auth and role middleware)
Route::middleware(['auth', 'role:Reporter'])->group(function () {
    Route::get('/reporter/dashboard', function () {
        return view('reports.dashboard');
    })->name('reporter.dashboard')->middleware('role:Reporter');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    // IMPORTANT: Specific routes must come BEFORE wildcard routes
    Route::get('/reports/rooms/{blockId}', [ReportController::class, 'getRoomsByBlock'])->name('reports.rooms');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
