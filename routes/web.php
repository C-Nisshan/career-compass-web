<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Mentor\MentorDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Public routes (Blade views)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/auth/google', [RegisterController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [RegisterController::class, 'handleGoogleCallback']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// Debug route for testing authentication
Route::get('/test-auth', function () {
    return response()->json([
        'user' => auth('api')->user(),
        'cookies' => request()->cookies->all(),
        'cookie_token' => request()->cookie('token'),
        'raw_cookie_header' => request()->header('Cookie', 'none'),
    ]);
})->middleware('jwt.cookie');

// Apply localization middleware to all routes
Route::middleware(['jwt.cookie'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])
        ->name('student.dashboard');
    
    Route::get('/mentor/dashboard', [MentorDashboardController::class, 'index'])
        ->name('mentor.dashboard');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});



