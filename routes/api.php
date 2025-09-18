<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
	Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');


Route::middleware('auth:api')->group(function () {
	
	Route::get('/tasks', [TaskController::class, 'index']);
	Route::post('/tasks', [TaskController::class, 'store']);
	Route::get('/tasks/{task}', [TaskController::class, 'show']);
	Route::put('/tasks/{task}', [TaskController::class, 'update']);
	Route::patch('/tasks/{task}', [TaskController::class, 'update']);
	Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
	Route::post('/tasks/{task}/dependencies', [TaskController::class, 'addDependency']);
	Route::delete('/tasks/{task}/dependencies', [TaskController::class, 'removeDependency']);
});


