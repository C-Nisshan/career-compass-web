<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, Profile, StudentProfile, MentorProfile};
use App\Enums\{RoleEnum, VerifiedStatusEnum};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Mail};
use Illuminate\Support\Str;
use App\Mail\RegistrationStatus;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function registerStudent(Request $request)
    {
        $request->validate(rules: [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15|regex:/^[\+]?[0-9]{10,12}$/',
            'password' => 'required|string|min:8|confirmed',
            'nic_number' => ['nullable', 'regex:/^(\d{9}[VvXx]|\d{12})$/'],
            'date_of_birth' => 'nullable|date',
            'school' => 'nullable|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'learning_style' => 'nullable|string',
            'subjects_interested' => 'nullable|array',
            'career_goals' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        return $this->processRegistration($request, RoleEnum::STUDENT);
    }

    public function registerMentor(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15|regex:/^[\+]?[0-9]{10,12}$/',
            'password' => 'required|string|min:8|confirmed',
            'nic_number' => ['nullable', 'regex:/^(\d{9}[VvXx]|\d{12})$/'],
            'profession_title' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:100',
            'bio' => 'nullable|string',
            'areas_of_expertise' => 'nullable|array',
            'linkedin_url' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'availability' => 'nullable|string',
        ]);

        return $this->processRegistration($request, RoleEnum::MENTOR);
    }

    protected function processRegistration(Request $request, RoleEnum|string $role)
    {
        return DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $role,
                'status' => 'pending',
                'is_active' => true,
            ]);

            Profile::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->uuid,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'nic_number' => $request->nic_number,
                'verified_status' => VerifiedStatusEnum::Pending,
                'completion_step' => 'basic',
            ]);

            if ($role === RoleEnum::STUDENT) {
                StudentProfile::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->uuid,
                    'date_of_birth' => $request->date_of_birth,
                    'school' => $request->school,
                    'grade_level' => $request->grade_level,
                    'learning_style' => $request->learning_style,
                    'subjects_interested' => json_encode($request->subjects_interested),
                    'career_goals' => $request->career_goals,
                    'location' => $request->location,
                ]);
            }

            if ($role === RoleEnum::MENTOR) {
                MentorProfile::create([
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $user->uuid,
                    'profession_title' => $request->profession_title,
                    'industry' => $request->industry,
                    'experience_years' => $request->experience_years,
                    'bio' => $request->bio,
                    'areas_of_expertise' => json_encode($request->areas_of_expertise),
                    'linkedin_url' => $request->linkedin_url,
                    'portfolio_url' => $request->portfolio_url,
                    'availability' => $request->availability,
                ]);
            }

            Mail::to($user->email)->queue(new RegistrationStatus($user, 'pending'));

            return response()->json([
                'message' => __('Registration submitted. Awaiting admin approval.')
            ], 201);
        });
    }
}
