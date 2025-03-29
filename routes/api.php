<?php

use App\Http\Controllers\authController;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Public Routes
Route::post('/register', [authController::class, 'register']);
Route::post('/login', [authController::class, 'login']);


//Private Routes

Route::middleware([IsUserAuth::class])->group(function () {
    Route::get('/user', [authController::class, 'getUser']);
    Route::post('/logout', [authController::class, 'logout']);
});
Route::get('/up', function () {
    return response()->json([
        'message' => 'Server is up and running'
    ], 200);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found'
    ], 404);
});
