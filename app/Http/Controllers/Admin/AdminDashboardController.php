<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\CareerPrediction;
use App\Models\SuccessStory;
use App\Models\QuizResult;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_mentors' => User::where('role', 'mentor')->count(),
            'active_forum_posts' => ForumPost::where('status', 'active')->count(),
            'total_predictions' => CareerPrediction::count(),
            'total_success_stories' => SuccessStory::count(),
            'total_quiz_results' => QuizResult::count(),
            'recent_predictions' => CareerPrediction::with('user')
                ->orderBy('predicted_at', 'desc')
                ->take(5)
                ->get()
                ->map(fn($prediction) => [
                    'user' => $prediction->user->first_name . ' ' . ($prediction->user->last_name ?? ''),
                    'predicted_at' => $prediction->predicted_at->format('d M Y H:i'),
                ]),
            'recent_posts' => ForumPost::with('user')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(fn($post) => [
                    'title' => $post->title,
                    'user' => $post->user->first_name . ' ' . ($post->user->last_name ?? ''),
                ]),
        ]);
    }
}