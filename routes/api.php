<?php

use App\Http\Controllers\Technician\TechnicianController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->get(
    '/technician/dashboard',
    [TechnicianController::class, 'dashboardApi']
);

Route::middleware('web')->get(
    '/tech/tasks',
    [TechnicianController::class, 'tasksApi']
);

Route::middleware('web')->get('/tech/tasks/{id}', [TechnicianController::class, 'taskDetailApi']);

Route::middleware('web')->get(
    '/tech/completed',
    [TechnicianController::class, 'completedApi']
);

Route::middleware('web')->get(
    '/tech/profile',
    [TechnicianController::class, 'profileApi']
);

Route::middleware('web')->post(
    '/tech/profile',
    [TechnicianController::class, 'profileUpdateApi']
);

Route::middleware('web')->post(
    '/tech/profile/password',
    [TechnicianController::class, 'profilePasswordApi']
);
