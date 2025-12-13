<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('index');
});


//Authentication =================================================================================================
/*
|--------------------------------------------------------------------------
| AUTH VIEW ROUTES
|--------------------------------------------------------------------------
*/

// Login page
Route::view('/login', 'Authentication.login')->name('login');

// Register page
Route::view('/register', 'Authentication.register')->name('register');

// Forgot password page
Route::view('/forgot-password', 'Authentication.forgot-password')->name('password.request');

// Reset password page
Route::view('/reset-password', 'Authentication.reset-password')->name('password.reset');

/*
|--------------------------------------------------------------------------
| AUTH ACTION ROUTES (POST)
|--------------------------------------------------------------------------
*/

// Register form submit
Route::middleware('guest')->group(function () {
    // Show login page
    Route::view('/login', 'Authentication.login')->name('login');

    // Handle login POST
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');

    // Show register page
    Route::view('/register', 'Authentication.register')->name('register');

    // Handle register POST
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Optional GET fallback for logout (links that hit /logout via GET)
    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->to('/');
    })->name('logout.get');
});

// // Login form submit
// Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');

// // Logout (POST recommended)
// Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// // Forgot password email submit
// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
//     ->name('password.email');

// // Reset password submit
// Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
//     ->name('password.update');


// ===================================================================================================
use App\Http\Controllers\Technician\TechnicianController;

Route::prefix('technician')->middleware('auth')->group(function () {

    // Technician dashboard
    Route::get('/dashboard', [TechnicianController::class, 'dashboard'])
        ->name('technician.dashboard');

    Route::get('/jobs', [TechnicianController::class, 'myJobs'])
        ->name('technician.my_jobs');

    Route::get('/jobs/{id}', [TechnicianController::class, 'jobDetails'])
        ->name('technician.job_details');

    Route::post('/jobs/{id}/update-status', [TechnicianController::class, 'updateStatus'])
        ->name('technician.update_status');

    Route::post('/jobs/{id}/complete', [TechnicianController::class, 'completeJob'])
        ->name('technician.complete_job');
});




// Draft=================================================================================================
use Illuminate\Support\Facades\DB;

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return 'DB OK: ' . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return 'DB ERROR: ' . $e->getMessage();
    }
});

