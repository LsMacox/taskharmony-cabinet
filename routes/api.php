<?php

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
use App\Http\Controllers\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('register', [AuthController::class, 'register'])->name('register');

        Route::group(
            ['middleware' => 'auth:sanctum'],
            function () {
                Route::get('user-abilities', [AuthController::class, 'userAbilities'])->name('userAbilities');
                Route::get('logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('user', [AuthController::class, 'user'])->name('user');
            }
        );
});
