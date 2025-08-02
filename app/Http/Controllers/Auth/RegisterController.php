<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\MentorProfile;
use App\Notifications\CompleteProfileNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Enums\RoleEnum;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function registerStudent(Request $request)
    {
        Log::debug('Register student request', $request->all());
        return $this->register($request, RoleEnum::STUDENT);
    }

    public function registerMentor(Request $request)
    {
        Log::debug('Register mentor request', $request->all());
        return $this->register($request, RoleEnum::MENTOR);
    }

    protected function register(Request $request, RoleEnum $role)
    {
        Log::debug('Processing registration', ['role' => $role->value, 'input' => $request->all()]);

        // Define common validation rules
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nic_number' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ];

        // Add role-specific validation rules
        if ($role === RoleEnum::STUDENT) {
            $rules = array_merge($rules, [
                'date_of_birth' => 'nullable|date',
                'school' => 'nullable|string|max:255',
                'grade_level' => 'nullable|string|max:50',
                'learning_style' => 'nullable|in:visual,auditory,kinesthetic',
                'subjects_interested' => 'nullable|array',
                'subjects_interested.*' => 'string',
                'career_goals' => 'nullable|string',
                'location' => 'nullable|string|max:255',
            ]);
        } elseif ($role === RoleEnum::MENTOR) {
            $rules = array_merge($rules, [
                'profession_title' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer|min:0',
                'bio' => 'nullable|string',
                'areas_of_expertise' => 'nullable|array',
                'areas_of_expertise.*' => 'string',
                'linkedin_url' => 'nullable|url',
                'portfolio_url' => 'nullable|url',
                'availability' => 'nullable|string|max:255',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            // Begin transaction
            DB::beginTransaction();

            // Create user
            $userData = [
                'uuid' => Uuid::uuid4()->toString(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role, // Store enum instance
                'status' => $role === RoleEnum::MENTOR ? 'pending' : 'approved',
                'is_active' => true,
                'token_version' => 1,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'nic_number' => $request->nic_number,
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $userData['profile_picture'] = $path;
            }

            $user = User::create($userData);

            // Create role-specific profile
            if ($role === RoleEnum::STUDENT) {
                StudentProfile::create([
                    'uuid' => Uuid::uuid4()->toString(),
                    'user_id' => $user->uuid,
                    'date_of_birth' => $request->date_of_birth,
                    'school' => $request->school,
                    'grade_level' => $request->grade_level,
                    'learning_style' => $request->learning_style,
                    'subjects_interested' => $request->subjects_interested,
                    'career_goals' => $request->career_goals,
                    'location' => $request->location,
                ]);
            } elseif ($role === RoleEnum::MENTOR) {
                MentorProfile::create([
                    'uuid' => Uuid::uuid4()->toString(),
                    'user_id' => $user->uuid,
                    'profession_title' => $request->profession_title,
                    'industry' => $request->industry,
                    'experience_years' => $request->experience_years,
                    'bio' => $request->bio,
                    'areas_of_expertise' => $request->areas_of_expertise,
                    'linkedin_url' => $request->linkedin_url,
                    'portfolio_url' => $request->portfolio_url,
                    'availability' => $request->availability,
                ]);
            }

            // Commit transaction
            DB::commit();

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
                'message' => 'Registration successful!',
                'token' => $token,
                'redirect' => route('login'),
            ], 201)->withCookie(cookie('token', $token, 60, null, null, false, true));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}