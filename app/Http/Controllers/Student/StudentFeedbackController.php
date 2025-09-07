<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MentorFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentFeedbackController extends Controller
{
    public function index()
    {
        $studentId = Auth::user()->uuid;
        return view('student.feedback', compact('studentId'));
    }

    public function getFeedback(Request $request)
    {
        try {
            $studentId = Auth::user()->uuid;
            $feedback = MentorFeedback::where('student_id', $studentId)
                ->with('mentor:first_name,last_name,uuid')
                ->orderBy('created_at', 'desc')
                ->get(['uuid', 'mentor_id', 'feedback', 'rating', 'created_at'])
                ->map(function ($feedback) {
                    $feedback->mentor->name = $feedback->mentor ? ($feedback->mentor->first_name . ' ' . $feedback->mentor->last_name) : 'Unknown';
                    return $feedback;
                });

            return response()->json([
                'success' => true,
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching student feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feedback.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'mentor_id' => 'required|uuid|exists:users,uuid',
                'feedback' => 'required|string|max:1000',
                'rating' => 'nullable|integer|min:1|max:5',
            ]);

            $feedback = MentorFeedback::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'student_id' => Auth::user()->uuid,
                'mentor_id' => $request->mentor_id,
                'feedback' => $request->feedback,
                'rating' => $request->rating,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error storing feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback.'
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $request->validate([
                'feedback' => 'required|string|max:1000',
                'rating' => 'nullable|integer|min:1|max:5',
            ]);

            $feedback = MentorFeedback::where('uuid', $uuid)
                ->where('student_id', Auth::user()->uuid)
                ->firstOrFail();

            $feedback->update([
                'feedback' => trim($request->feedback),
                'rating' => $request->rating ? (string) $request->rating : null, // Adjust based on schema
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback updated successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found or you do not have permission to update it.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('Validation failed for feedback update: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update feedback.'
            ], 500);
        }
}

    public function destroy($uuid)
    {
        try {
            $feedback = MentorFeedback::where('uuid', $uuid)
                ->where('student_id', Auth::user()->uuid)
                ->firstOrFail();

            $feedback->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feedback deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete feedback.'
            ], 500);
        }
    }

    public function getMentors()
    {
        try {
            $mentors = User::where('role', \App\Enums\RoleEnum::MENTOR->value)
                ->where('status', 'approved')
                ->with('mentorProfile:uuid,user_id,profession_title,industry,areas_of_expertise,linkedin_url')
                ->get(['uuid', 'first_name', 'last_name', 'email']);

            return response()->json([
                'success' => true,
                'data' => $mentors->map(function ($mentor) {
                    return [
                        'uuid' => $mentor->uuid,
                        'name' => $mentor->first_name . ' ' . $mentor->last_name,
                        'email' => $mentor->email,
                        'profession_title' => $mentor->mentorProfile?->profession_title,
                        'industry' => $mentor->mentorProfile?->industry,
                        'areas_of_expertise' => $mentor->mentorProfile?->areas_of_expertise,
                        'linkedin_url' => $mentor->mentorProfile?->linkedin_url,
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mentors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch mentors.'
            ], 500);
        }
    }
}