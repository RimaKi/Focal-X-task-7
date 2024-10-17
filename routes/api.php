<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Rate limiting middleware by laravel
Route::middleware(['throttle:30,1'])->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('add-user', 'addUser');
            Route::post('logout', 'logout');
            Route::post('refresh', 'refresh');
            Route::post('/change-password', 'changePassword');
        });
        Route::apiResource('tasks', TaskController::class)->only(['index', 'store', 'show', 'destroy']);
        Route::put('tasks/{task}/progress', [TaskController::class, 'progressTask'])->middleware('can:updateStatus,task');
        Route::put('tasks/{task}/completed', [TaskController::class, 'completedTask'])->middleware('can:updateStatus,task');
        Route::put('/tasks/{task}/reassign/{user}', [TaskController::class, 'reassign'])->middleware('can:update,task');

        Route::post('/comments/{task}', [CommentController::class, 'store']);
        Route::post('/attachments/{task}', [AttachmentController::class, 'store']);
    });
});
