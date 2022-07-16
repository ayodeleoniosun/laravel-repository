<?php

use App\Http\Controllers\AccountController;
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
    Route::prefix('accounts')->group(function () {
        Route::controller(AccountController::class)->group(function () {
            Route::post('/register', 'register')->name('accounts.register');
            Route::post('/login', 'login')->name('accounts.login');
            Route::post('/forgot-password', 'forgotPassword')->name('accounts.forgot_password');
            Route::post('/reset-password', 'resetPassword')->name('accounts.reset_password');
        });
    });

    Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/{slug}', 'profile')->name('user.profile');
            Route::post('/profile/update/picture', 'updateProfilePicture')->name('user.update.profile.picture');
            Route::put('/profile/update/personal-information', 'updateProfile')->name('user.update.profile');
            Route::put('/profile/update/password', 'updatePassword')->name('user.update.password');
        });
    });
});
