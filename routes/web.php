<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

//Technician routes ================================================================
Route::prefix('tech')->group(function () {
    Route::view('/dashboard', 'Technician.dashboard');
    Route::view('/tasks', 'Technician.tasks');
    Route::view('/tasks/detail', 'Technician.task-detail');
    Route::view('/completed', 'Technician.completed');
    Route::view('/profile', 'Technician.profile');
});
