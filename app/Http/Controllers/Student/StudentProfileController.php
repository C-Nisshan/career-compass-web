<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentProfileController extends Controller
{
    /**
     * Display the student profile view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('student.profile');
    }

    /**
     * Get the authenticated student's profile data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if ($user->role !== \App\Enums\RoleEnum::STUDENT) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only students can view this profile.'
                ], 403);
            }

            $profile = StudentProfile::where('user_id', $user->uuid)->first();
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'nic_number' => $user->nic_number,
                'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                'school' => $profile->school ?? null,
                'grade_level' => $profile->grade_level ?? null,
                'learning_style' => $profile->learning_style ?? null,
                'subjects_interested' => $profile->subjects_interested ?? [],
                'career_goals' => $profile->career_goals ?? null,
                'location' => $profile->location ?? null,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching student profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile data. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the authenticated student's profile data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== \App\Enums\RoleEnum::STUDENT) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only students can update this profile.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'nic_number' => 'nullable|string|max:50',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'school' => 'nullable|string|max:255',
                'grade_level' => 'nullable|string|max:50',
                'learning_style' => 'nullable|in:visual,auditory,kinesthetic',
                'subjects_interested' => 'nullable|array',
                'subjects_interested.*' => 'string',
                'career_goals' => 'nullable|string',
                'location' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update User model
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'nic_number' => $request->nic_number,
            ];

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $userData['profile_picture'] = $path;
            }

            $user->update($userData);

            // Update or create StudentProfile
            $profile = StudentProfile::where('user_id', $user->uuid)->first();
            $profileData = [
                'school' => $request->school,
                'grade_level' => $request->grade_level,
                'learning_style' => $request->learning_style,
                'subjects_interested' => $request->subjects_interested,
                'career_goals' => $request->career_goals,
                'location' => $request->location,
            ];

            if ($profile) {
                $profile->update($profileData);
            } else {
                $profileData['uuid'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $profileData['user_id'] = $user->uuid;
                StudentProfile::create($profileData);
            }

            // Prepare response data with full profile picture URL
            $user->refresh();
            $data = array_merge($userData, $profileData);
            if ($user->profile_picture) {
                $data['profile_picture'] = Storage::url($user->profile_picture);
            }

            Log::info('Student profile updated', ['user_id' => $user->uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating student profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile. Please try again.'
            ], 500);
        }
    }
}
