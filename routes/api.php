<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::prefix('auth')->group(function () {
});

// JWT-protected routes
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // 🟢 Student-only routes
    Route::middleware('role:student')->group(function () {
        Route::get('student/dashboard', fn() => response()->json(['message' => 'Welcome Student']));
    });

    // 🟡 Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/panel', fn() => response()->json(['message' => 'Welcome Admin']));
    });

    // 🟣 Mentor-only routes
    Route::middleware('role:mentor')->group(function () {
        Route::get('mentor/insights', fn() => response()->json(['message' => 'Welcome Mentor']));
    });

    // 🔁 Multi-role (e.g., admin OR mentor)
    Route::middleware('role:admin,mentor')->group(function () {
        Route::get('staff/area', fn() => response()->json(['message' => 'Admins & Mentors only']));
    });
});

Route::get('/test', fn() => response()->json(['message' => 'API works']));
