<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskSummaryController;
use Illuminate\Support\Facades\Route;

Route::get('tasks/summary', TaskSummaryController::class);
Route::apiResource('tasks', TaskController::class);
