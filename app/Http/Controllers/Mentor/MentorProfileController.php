<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MentorProfileController extends Controller
{
    /**
     * Display the mentor profile view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('mentor.profile');
    }

    /**
     * Get the authenticated mentor's profile data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if ($user->role !== \App\Enums\RoleEnum::MENTOR) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only mentors can view this profile.'
                ], 403);
            }

            $profile = MentorProfile::where('user_id', $user->uuid)->first();
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'nic_number' => $user->nic_number,
                'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                'profession_title' => $profile->profession_title ?? null,
                'industry' => $profile->industry ?? null,
                'experience_years' => $profile->experience_years ?? null,
                'bio' => $profile->bio ?? null,
                'areas_of_expertise' => $profile->areas_of_expertise ?? [],
                'linkedin_url' => $profile->linkedin_url ?? null,
                'portfolio_url' => $profile->portfolio_url ?? null,
                'availability' => $profile->availability ?? null,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mentor profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch profile data. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the authenticated mentor's profile data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== \App\Enums\RoleEnum::MENTOR) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only mentors can update this profile.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'nic_number' => 'nullable|string|max:50',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

            // Update or create MentorProfile
            $profile = MentorProfile::where('user_id', $user->uuid)->first();
            $profileData = [
                'profession_title' => $request->profession_title,
                'industry' => $request->industry,
                'experience_years' => $request->experience_years,
                'bio' => $request->bio,
                'areas_of_expertise' => $request->areas_of_expertise,
                'linkedin_url' => $request->linkedin_url,
                'portfolio_url' => $request->portfolio_url,
                'availability' => $request->availability,
            ];

            if ($profile) {
                $profile->update($profileData);
            } else {
                $profileData['uuid'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $profileData['user_id'] = $user->uuid;
                MentorProfile::create($profileData);
            }

            // Prepare response data with full profile picture URL
            $user->refresh();
            $data = array_merge($userData, $profileData);
            if ($user->profile_picture) {
                $data['profile_picture'] = Storage::url($user->profile_picture);
            }

            Log::info('Mentor profile updated', ['user_id' => $user->uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating mentor profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile. Please try again.'
            ], 500);
        }
    }
}
