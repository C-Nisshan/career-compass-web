<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumVote;
use App\Models\SuccessStory;
use App\Models\MentorFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MentorDashboardController extends Controller
{
    public function index()
    {
        $mentorId = Auth::user()->uuid;
        return view('mentor.dashboard', compact('mentorId'));
    }

    public function getForumContributions(Request $request)
    {
        try {
            $mentorId = Auth::user()->uuid;
            $posts = ForumPost::where('user_id', $mentorId)
                ->withCount('comments')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(['uuid', 'title', 'created_at']);

            $comments = ForumComment::where('user_id', $mentorId)
                ->with('post:title,uuid')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(['uuid', 'forum_post_id', 'comment', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => $posts,
                    'comments' => $comments
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum contributions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch forum contributions.'
            ], 500);
        }
    }

    public function getStudentEngagement(Request $request)
    {
        try {
            $mentorId = Auth::user()->uuid;
            $posts = ForumPost::where('user_id', $mentorId)
                ->withCount(['comments', 'votes'])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(['uuid', 'title', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching student engagement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student engagement.'
            ], 500);
        }
    }

    public function getSuccessStories(Request $request)
    {
        try {
            $stories = SuccessStory::select(['uuid', 'name', 'career_path', 'story', 'image', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();

            $stories->transform(function ($story) {
                if ($story->image) {
                    $story->image_url = Storage::url($story->image);
                }
                return $story;
            });

            return response()->json([
                'success' => true,
                'data' => $stories
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching success stories for mentor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch success stories.'
            ], 500);
        }
    }

    public function getFeedback(Request $request)
    {
        try {
            $mentorId = Auth::user()->uuid;
            $feedback = MentorFeedback::where('mentor_id', $mentorId)
                ->where('status', 'active')
                ->with('student:first_name,last_name,uuid')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get(['uuid', 'student_id', 'feedback', 'rating', 'created_at'])
                ->map(function ($feedback) {
                    $feedback->student_name = $feedback->student ? ($feedback->student->first_name . ' ' . $feedback->student->last_name) : 'Unknown';
                    return $feedback;
                });

            return response()->json([
                'success' => true,
                'data' => $feedback
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching mentor feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feedback.'
            ], 500);
        }
    }
}