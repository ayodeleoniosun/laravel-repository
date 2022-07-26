<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/register', 'register')->name('auth.register');
            Route::post('/login', 'login')->name('auth.login');
            Route::post('/forgot-password', 'forgotPassword')->name('auth.forgot_password');
            Route::post('/reset-password', 'resetPassword')->name('auth.reset_password');
        });
    });

    Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/{slug}', 'profile')->name('user.profile');
            Route::post('/profile/update/picture', 'updateProfilePicture')->name('user.update.profile.picture');
            Route::put('/profile/update/personal-information', 'updateProfile')->name('user.update.profile');
            Route::put('/profile/update/password', 'updatePassword')->name('user.update.password');
            Route::get('/profile/logout', 'logout')->name('user.logout');
        });
    });
});
