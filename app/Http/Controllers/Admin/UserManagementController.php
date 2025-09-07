<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display the user management dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.users');
    }

    /**
     * Get list of users with their profiles, optionally filtered by role.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        try {
            $query = User::with(['studentProfile', 'mentorProfile'])
                ->select(['uuid', 'first_name', 'last_name', 'email', 'role', 'status', 'is_active'])
                ->whereNull('deleted_at'); // Exclude soft-deleted users

            if ($request->has('role') && in_array($request->input('role'), [RoleEnum::STUDENT->value, RoleEnum::MENTOR->value, RoleEnum::ADMIN->value])) {
                $query->where('role', $request->input('role'));
            }

            $users = $query->get();

            // Log if any user is missing a profile (for students and mentors)
            $users->each(function ($user) {
                if ($user->role === RoleEnum::STUDENT && !$user->studentProfile) {
                    Log::warning('Student missing profile', ['uuid' => $user->uuid, 'email' => $user->email]);
                } elseif ($user->role === RoleEnum::MENTOR && !$user->mentorProfile) {
                    Log::warning('Mentor missing profile', ['uuid' => $user->uuid, 'email' => $user->email]);
                }
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a user (soft delete).
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($uuid)
    {
        try {
            $user = User::where('uuid', $uuid)->whereNull('deleted_at')->firstOrFail();
            $user->delete(); // Soft delete

            Log::info('User deleted', ['uuid' => $uuid, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user. Please try again.'
            ], 500);
        }
    }
}