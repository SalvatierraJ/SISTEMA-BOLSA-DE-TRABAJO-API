<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\usersController;
use App\Http\Middleware\IsUserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Public Routes
Route::post('/register', [authController::class, 'register'])->name('register');
Route::post('/login', [authController::class, 'login'])->name('login');


//Private Routes

Route::middleware([IsUserAuth::class])->group(function () {
    Route::get('/user', [authController::class, 'getUser'])->name('user.profile');
    Route::post('/logout', [authController::class, 'logout'])->name('user.logOut');

    //endpoint for user
    Route::get('/users', [usersController::class, 'allUsers'])->name('user.all');
    Route::get('/user/{id}', [usersController::class, 'getUser'])->name('user.get');
    Route::put('/user/{id}', [usersController::class, 'updateUser'])->name('user.update');
    Route::delete('/user/{id}', [usersController::class, 'deleteUser'])->name('user.delete');
    Route::post('/user/register', [authController::class, 'register'])->name('user.register');
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
