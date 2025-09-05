<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CareerPrediction;
use App\Models\QuizResult;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        // Pass authenticated user's UUID to the view
        $userId = Auth::user()->uuid;
        return view('student.dashboard', compact('userId'));
    }

    public function getRecentRecommendations(Request $request)
    {
        $userId = Auth::user()->uuid;
        $recommendations = CareerPrediction::where('user_id', $userId)
            ->orderBy('predicted_at', 'desc')
            ->take(1)
            ->get(['uuid', 'recommendations', 'predicted_at']);

        return response()->json([
            'success' => true,
            'data' => $recommendations
        ]);
    }

    public function getQuizScores(Request $request)
    {
        $userId = Auth::user()->uuid;
        $quizResults = QuizResult::where('user_id', $userId)
            ->with('quiz:title,uuid') // Assuming quiz has a title
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['uuid', 'quiz_id', 'score', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $quizResults
        ]);
    }

    public function getForumActivity(Request $request)
    {
        $userId = Auth::user()->uuid;
        $posts = ForumPost::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['uuid', 'title', 'created_at']);

        $comments = ForumComment::where('user_id', $userId)
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
        ]);
    }
}