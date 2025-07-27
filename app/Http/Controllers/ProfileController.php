<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $roleProfile = $user->role === 'mentor' ? $user->mentorProfile : $user->studentProfile;
        
        return view('profile.edit', compact('user', 'profile', 'roleProfile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $generalRules = [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'nic_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $studentRules = [
            'date_of_birth' => 'nullable|date',
            'school' => 'nullable|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'learning_style' => 'nullable|string|in:visual,auditory,kinesthetic',
            'subjects_interested' => 'nullable|array',
            'subjects_interested.*' => 'string|max:100',
            'career_goals' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ];

        $mentorRules = [
            'profession_title' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'bio' => 'nullable|string|max:1000',
            'areas_of_expertise' => 'nullable|array',
            'areas_of_expertise.*' => 'string|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'availability' => 'nullable|string|max:255',
        ];

        $rules = $generalRules;
        if ($user->role === 'student') {
            $rules = array_merge($rules, $studentRules);
        } elseif ($user->role === 'mentor') {
            $rules = array_merge($rules, $mentorRules);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::warning('Profile update validation failed', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Update general profile
            $profileData = [
                'user_id' => $user->uuid,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'nic_number' => $request->input('nic_number'),
                'verified_status' => $user->role === 'mentor' ? 'pending' : 'approved',
                'completion_step' => 'completed',
            ];

            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $profileData['profile_picture_path'] = $path;
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->uuid],
                $profileData
            );

            // Update role-specific profile
            if ($user->role === 'student') {
                $user->studentProfile()->updateOrCreate(
                    ['user_id' => $user->uuid],
                    [
                        'date_of_birth' => $request->input('date_of_birth'),
                        'school' => $request->input('school'),
                        'grade_level' => $request->input('grade_level'),
                        'learning_style' => $request->input('learning_style'),
                        'subjects_interested' => json_encode($request->input('subjects_interested', [])),
                        'career_goals' => $request->input('career_goals'),
                        'location' => $request->input('location'),
                    ]
                );
            } elseif ($user->role === 'mentor') {
                $user->mentorProfile()->updateOrCreate(
                    ['user_id' => $user->uuid],
                    [
                        'profession_title' => $request->input('profession_title'),
                        'industry' => $request->input('industry'),
                        'experience_years' => $request->input('experience_years'),
                        'bio' => $request->input('bio'),
                        'areas_of_expertise' => json_encode($request->input('areas_of_expertise', [])),
                        'linkedin_url' => $request->input('linkedin_url'),
                        'portfolio_url' => $request->input('portfolio_url'),
                        'availability' => $request->input('availability'),
                    ]
                );
            }

            Log::info('Profile updated successfully', ['user_id' => $user->uuid]);
            return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
        }
    }
}