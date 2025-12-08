<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::view('/login', 'Authentication.login');
Route::view('/register', 'Authentication.register');
Route::view('/forgot-password', 'Authentication.forgot-password');
Route::view('/reset-password', 'Authentication.reset-password');
Route::view('/dashboard', 'Authentication.dashboard-redirect');

Route::prefix('tech')->group(function () {
    Route::view('/dashboard', 'Technician.dashboard');
    Route::view('/tasks', 'Technician.tasks');
    Route::view('/tasks/completed', 'Technician.completed');
    Route::view('/tasks/{id}', 'Technician.task-detail');
    Route::view('/profile', 'Technician.profile');
});
