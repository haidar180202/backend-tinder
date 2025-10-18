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
    // TODO: This route should ideally be a POST request as it modifies data.
    // Changed to GET to simplify testing directly from the browser.
    Route::get('/users/{id}/action', [UserController::class, 'userAction']);
    Route::get('/users/mycategories', [UserController::class, 'getMyDataByCategory']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::post('/profile/picture', [UserController::class, 'uploadProfilePicture']);
    Route::post('/pictures', [UserController::class, 'uploadPicture']);
});