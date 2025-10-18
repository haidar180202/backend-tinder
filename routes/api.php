<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users/recommended', [UserController::class, 'getRecommendedUsers']);
    Route::post('/users/{id}/action', [UserController::class, 'userAction']);
    Route::get('/users/mycategories', [UserController::class, 'getMyDataByCategory']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/picture', [UserController::class, 'uploadProfilePicture']);
});