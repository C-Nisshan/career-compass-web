<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ContactController;

// Public API routes
Route::post('/auth/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/auth/verify-registration-otp', [RegisterController::class, 'verifyRegistrationOtp'])->middleware('throttle:100,1');
Route::post('/register/student', [RegisterController::class, 'registerStudent'])->name('register.student');
Route::post('/register/mentor', [RegisterController::class, 'registerMentor'])->name('register.mentor');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('api.forgot-password');
Route::post('/auth/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('api.verify-otp');
Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('api.reset-password');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('api.contact.submit');

// Protected API routes
Route::middleware('jwt.cookie')->group(function () {
    Route::get('/me', [LoginController::class, 'me'])->name('api.me');
    Route::post('/auth/logout', [LogoutController::class, 'logout'])->name('api.logout');
    Route::post('/refresh', [LoginController::class, 'refresh'])->name('api.refresh');
    Route::get('/admin/pending-registrations', [AdminController::class, 'pendingRegistrations']);
    Route::post('/admin/review-registration/{uuid}', [AdminController::class, 'reviewRegistration']);
});