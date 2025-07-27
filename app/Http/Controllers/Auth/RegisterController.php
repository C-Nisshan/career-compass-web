<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CompleteProfileNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function registerStudent(Request $request)
    {
        Log::debug('Register student request', $request->all());
        return $this->register($request, 'student');
    }

    public function registerMentor(Request $request)
    {
        Log::debug('Register mentor request', $request->all());
        return $this->register($request, 'mentor');
    }

    protected function register(Request $request, $role)
    {
        Log::debug('Processing registration', ['role' => $role, 'input' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:student,mentor',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $user = User::create([
                'uuid' => Uuid::uuid4()->toString(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'status' => $role === 'mentor' ? 'pending' : 'approved',
                'is_active' => true,
                'token_version' => 1, // Initialize token version
            ]);

            Log::info('User registered, queuing notification', ['email' => $user->email]);
            try {
                $user->notify(new CompleteProfileNotification());
            } catch (\Exception $e) {
                Log::error('Failed to queue notification', ['error' => $e->getMessage()]);
            }

            // Log in the user and generate JWT token
            Auth::login($user);
            $token = JWTAuth::customClaims(['token_version' => $user->token_version])->fromUser($user);

            Log::info('Registration successful', ['email' => $user->email, 'token' => substr($token, 0, 10) . '...']);
            return response()->json([
                'message' => 'Registration successful! Please complete your profile.',
                'token' => $token,
                'redirect' => route('profile.edit'),
            ], 201)->withCookie(cookie('token', $token, 60, null, null, false, true)); // Set token cookie
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}