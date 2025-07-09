<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Booker\RoomController as BookerRoomController;
use App\Http\Controllers\Provider\RoomController as ProviderRoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

// Public API routes
Route::post('/auth/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/auth/verify-registration-otp', [RegisterController::class, 'verifyRegistrationOtp'])->middleware('throttle:100,1');
Route::post('/auth/register/student', [RegisterController::class, 'registerStudent']);
Route::post('/auth/register/mentor', [RegisterController::class, 'registerMentor']);
Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('api.forgot-password');
Route::post('/auth/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('api.verify-otp');
Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('api.reset-password');

// Protected API routes
Route::middleware('jwt.cookie')->group(function () {
    Route::get('/me', [LoginController::class, 'me'])->name('api.me');
    Route::post('/auth/logout', [LogoutController::class, 'logout'])->name('api.logout');
    Route::post('/refresh', [LoginController::class, 'refresh'])->name('api.refresh');
    Route::get('/admin/pending-registrations', [AdminController::class, 'pendingRegistrations']);
    Route::post('/admin/review-registration/{uuid}', [AdminController::class, 'reviewRegistration']);
});