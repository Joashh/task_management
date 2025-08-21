<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;

// Public routes
Route::middleware(['web'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Routes that require authentication
Route::middleware(['web', 'auth'])->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Task routes
    Route::post('/recordtask', [TaskController::class, 'store']);  
    Route::get('/my-tasks', [TaskController::class, 'myTasks']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-task-order', [TaskController::class, 'updateOrder']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);

});

// Public admin/user listing
Route::get('/users', [AdminUserController::class, 'index']);

