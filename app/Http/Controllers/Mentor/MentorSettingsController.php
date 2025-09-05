<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MentorSettingsController extends Controller
{
    /**
     * Display the mentor settings view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('mentor.settings');
    }

    /**
     * Change the authenticated mentor's password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== \App\Enums\RoleEnum::MENTOR) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Only mentors can change their password.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 422);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            Log::info('Mentor password changed', ['user_id' => $user->uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error changing mentor password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password. Please try again.'
            ], 500);
        }
    }
}
