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