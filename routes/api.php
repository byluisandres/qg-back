<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticatedController;
use App\Http\Controllers\PasswordResetController;
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


Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthenticatedController::class, 'login'])->name('login');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    })->name('me');
    Route::post('/logout', [AuthenticatedController::class, 'logout'])->name('logout');
});
