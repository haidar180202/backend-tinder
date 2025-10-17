<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/recommended', [UserController::class, 'getRecommendedUsers']);
    Route::post('/users/{id}/like', [UserController::class, 'likeUser']);
    Route::post('/users/{id}/dislike', [UserController::class, 'dislikeUser']);
    Route::get('/users/liked', [UserController::class, 'getLikedUsers']);
});