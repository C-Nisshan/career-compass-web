<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            $cookie = cookie()->forget('token', '/', '127.0.0.1');
            Log::info('User logged out successfully', [
                'user_id' => auth('api')->id() ?? 'unknown',
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json([
                'message' => 'Logged out successfully',
                'redirect' => route('home'), // Redirect to home page
            ])->withCookie($cookie);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json([
                'error' => 'Logout failed',
                'redirect' => route('home'), // Fallback to home page
            ], 500)->withCookie(cookie()->forget('token', '/', '127.0.0.1'));
        }
    }
}