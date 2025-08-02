<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Login request payload', $request->all());
        $key = 'login|' . $request->ip();

        // Check rate limiting
        try {
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(['error' => 'Too many login attempts. Try again in 60 seconds.'], 429);
            }
        } catch (\Exception $e) {
            Log::error('RateLimiter failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Rate limiting error. Please try again later.'], 500);
        }

        $credentials = $request->only('email', 'password');
        Log::info('Credentials for JWTAuth', $credentials);

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::error('JWTAuth authentication failed', $credentials);
            try {
                RateLimiter::hit($key, 60);
            } catch (\Exception $e) {
                Log::error('Failed to increment RateLimiter', ['error' => $e->getMessage()]);
            }
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        try {
            RateLimiter::clear($key);
        } catch (\Exception $e) {
            Log::error('Failed to clear RateLimiter', ['error' => $e->getMessage()]);
        }

        $user = JWTAuth::user();

        if (!$user) {
            return response()->json(['error' => 'Account is deactivated'], 403);
        }

        return $this->respondWithToken($token, $user);
    }

    protected function respondWithToken($token, $user)
    {
        // Set Secure to false for local development, domain to 127.0.0.1
        $cookie = cookie('token', $token, config('jwt.ttl'), '/', '127.0.0.1', false, true, false, 'Lax');

        // Check if user is a mentor and status is not approved
        if ($user->role === RoleEnum::MENTOR && $user->status !== 'approved') {
            $redirect = '/mentor/pending';
        } else {
            $redirect = match ($user->role) {
                RoleEnum::ADMIN => '/admin/dashboard',
                RoleEnum::STUDENT => '/student/dashboard',
                RoleEnum::MENTOR => '/mentor/dashboard',
                default => '/',
            };
        }

        Log::info('Cookie created', [
            'cookie_name' => 'token',
            'token_length' => strlen($token),
            'redirect' => $redirect,
            'cookie_attributes' => [
                'path' => '/',
                'secure' => false,
                'httpOnly' => true,
                'sameSite' => 'Lax',
                'domain' => '127.0.0.1',
            ],
        ]);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('profile'),
            'redirect' => $redirect,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ])->withCookie($cookie);
    }

    public function me()
    {
        $user = auth('api')->user()->load('profile');
        return response()->json($user);
    }

    public function refresh()
    {
        $newToken = JWTAuth::refresh();
        $user = auth('api')->user();

        return $this->respondWithToken($newToken, $user);
    }
}