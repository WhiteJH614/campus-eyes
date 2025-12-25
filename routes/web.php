<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TechnicianController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AttachmentController;

use App\Http\Controllers\Technician\TechnicianController as TechController;
use App\Http\Controllers\ReportController as ReporterReportController;
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    return view('index');
});

Route::redirect('/reset-password', '/forgot-password');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login.perform');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $role = strtolower(auth()->user()->role ?? '');

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'technician' => redirect()->route('technician.dashboard'),
            'reporter' => redirect()->route('reporter.dashboard'),
            default => redirect('/'),
        };
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->to('/');
    })->name('logout.get');
});

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            '/technicians',
            [App\Http\Controllers\Admin\TechnicianController::class, 'index']
        )->name('admin.technicians.index');

        Route::delete(
            '/technicians/{id}',
            [App\Http\Controllers\Admin\TechnicianController::class, 'destroy']
        )->name('admin.technicians.destroy');
    });

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            '/locations',
            [App\Http\Controllers\Admin\LocationController::class, 'index']
        )->name('admin.locations.index');

        Route::post(
            '/blocks',
            [App\Http\Controllers\Admin\LocationController::class, 'storeBlock']
        )->name('admin.blocks.store');

        Route::post(
            '/rooms',
            [App\Http\Controllers\Admin\LocationController::class, 'storeRoom']
        )->name('admin.rooms.store');
    });

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get(
            '/rooms/{id}/edit',
            [App\Http\Controllers\Admin\LocationController::class, 'editRoom']
        )->name('admin.rooms.edit');

        Route::put(
            '/rooms/{id}',
            [App\Http\Controllers\Admin\LocationController::class, 'updateRoom']
        )->name('admin.rooms.update');

        Route::delete(
            '/rooms/{id}',
            [App\Http\Controllers\Admin\LocationController::class, 'deleteRoom']
        )->name('admin.rooms.delete');
    });

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::delete(
            '/students/{id}',
            [App\Http\Controllers\Admin\UserController::class, 'destroy']
        )->name('admin.students.delete');
    });

Route::prefix('admin')->middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/technicians', [TechnicianController::class, 'index'])
        ->name('admin.technicians.index');

    Route::post('/technicians', [TechnicianController::class, 'store'])
        ->name('admin.technicians.store');

    Route::get('/locations', [LocationController::class, 'index'])
        ->name('admin.locations.index');

    Route::post('/locations/block', [LocationController::class, 'storeBlock'])
        ->name('admin.locations.storeBlock');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('admin.reports.index');

    Route::get('/reports/{report}', [ReportController::class, 'show'])
        ->name('admin.reports.show');

    Route::put('/reports/{report}/assign', [ReportController::class, 'assignTechnician'])
        ->name('admin.reports.assign');

    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])
        ->name('admin.attachments.show');
});

Route::prefix('technician')->middleware(['auth', 'technician'])->group(function () {
    // Use TechController (the alias) instead of TechnicianController
    Route::get('/dashboard', [TechController::class, 'dashboard'])->name('technician.dashboard');
    Route::get('/tasks', [TechController::class, 'tasks'])->name('technician.tasks');
    Route::get('/tasks/{id}', [TechController::class, 'taskDetail'])->name('technician.task_detail');
    Route::get('/tasks/{id}/complete', function ($id) {
        return redirect()->route('technician.task_detail', $id);
    });
    Route::get('/profile', [TechController::class, 'profile'])->name('technician.profile');
    Route::post('/profile', [TechController::class, 'profileUpdateApi'])->name('technician.profile.update');
    Route::post('/profile/password', [TechController::class, 'profilePasswordApi'])->name('technician.profile.password');
    Route::get('/completed', [TechController::class, 'completed'])->name('technician.completed');

    Route::delete('/tasks/{id}/attachments/{attachment}', [TechController::class, 'deleteAfterPhoto'])
        ->name('technician.delete_after');

    Route::post('/tasks/{id}/proofs', [TechController::class, 'addProofImages'])
        ->name('technician.add_proof_images');

    Route::post('/tasks/{id}/update-status', [TechController::class, 'updateStatus'])->name('technician.update_status');
    Route::post('/tasks/{id}/status', [TechController::class, 'updateStatus']);
    Route::post('/tasks/{id}/complete', [TechController::class, 'completeJob'])->name('technician.complete_job');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/reporter/dashboard', function () {
        return view('reports.dashboard');
    })->name('reporter.dashboard');

    Route::get('/reports', [ReporterReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/create', [ReporterReportController::class, 'create'])
        ->name('reports.create');
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
// Author: Tan Jun Yan
Route::middleware(['auth', 'role:Reporter'])->group(function () {
    Route::get('/reporter/dashboard', function () {
        return view('reports.dashboard');
    })->name('reporter.dashboard')->middleware('role:Reporter');
    Route::get('/reports', [ReporterReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReporterReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReporterReportController::class, 'store'])->name('reports.store');
    // IMPORTANT: Specific routes must come BEFORE wildcard routes
    Route::get('/reports/rooms/{blockId}', [ReporterReportController::class, 'getRoomsByBlock'])->name('reports.rooms');
    Route::get('/reports/{report}', [ReporterReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [ReporterReportController::class, 'edit'])->name('reports.edit');
    Route::put('/reports/{report}', [ReporterReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [ReporterReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';