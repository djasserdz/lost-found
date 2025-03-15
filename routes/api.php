<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth.token']);

Route::apiResource('User', UserController::class);
Route::apiResource('Post', PostController::class)->middleware('auth.token');

Route::prefix('Post/{post}')->group(function () {
    Route::apiResource('Comment', CommentController::class)->middleware('auth.token');
});
