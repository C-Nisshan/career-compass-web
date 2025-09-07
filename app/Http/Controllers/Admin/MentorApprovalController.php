<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MentorApprovalController extends Controller
{
    /**
     * Display the mentor approvals dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.mentor-approvals');
    }

    /**
     * Get list of mentors with their profiles, optionally filtered by status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMentors(Request $request)
    {
        try {
            $query = User::with('mentorProfile')
                ->where('role', 'mentor')
                ->select(['uuid', 'first_name', 'last_name', 'email', 'status']);

            if ($request->has('status') && in_array($request->input('status'), ['pending', 'approved', 'rejected'])) {
                $query->where('status', $request->input('status'));
            }

            $mentors = $query->get();

            // Log if any mentor is missing a profile
            $mentors->each(function ($mentor) {
                if (!$mentor->mentorProfile) {
                    Log::warning('Mentor missing profile', ['uuid' => $mentor->uuid, 'email' => $mentor->email]);
                }
            });

            return response()->json([
                'success' => true,
                'data' => $mentors
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mentors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch mentors. Please try again.'
            ], 500);
        }
    }

    /**
     * Set mentor status to approved.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveMentor($uuid)
    {
        try {
            $mentor = User::where('role', 'mentor')
                ->where('uuid', $uuid)
                ->firstOrFail();

            $mentor->update(['status' => 'approved']);

            Log::info('Mentor approved', ['uuid' => $uuid, 'email' => $mentor->email]);

            return response()->json([
                'success' => true,
                'message' => 'Mentor approved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error approving mentor: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve mentor.'
            ], 404);
        }
    }

    /**
     * Set mentor status to rejected.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectMentor($uuid)
    {
        try {
            $mentor = User::where('role', 'mentor')
                ->where('uuid', $uuid)
                ->firstOrFail();

            $mentor->update(['status' => 'rejected']);

            Log::info('Mentor rejected', ['uuid' => $uuid, 'email' => $mentor->email]);

            return response()->json([
                'success' => true,
                'message' => 'Mentor rejected successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error rejecting mentor: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject mentor.'
            ], 404);
        }
    }

    /**
     * Set mentor status to pending.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPending($uuid)
    {
        try {
            $mentor = User::where('role', 'mentor')
                ->where('uuid', $uuid)
                ->firstOrFail();

            $mentor->update(['status' => 'pending']);

            Log::info('Mentor set to pending', ['uuid' => $uuid, 'email' => $mentor->email]);

            return response()->json([
                'success' => true,
                'message' => 'Mentor set to pending successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error setting mentor to pending: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to set mentor to pending.'
            ], 404);
        }
    }
}