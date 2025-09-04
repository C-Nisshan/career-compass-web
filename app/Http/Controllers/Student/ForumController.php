<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForumController extends Controller
{
    /**
     * Display the browse posts page.
     *
     * @return \Illuminate\View\View
     */
    public function browsePosts()
    {
        return view('student.community-forum.browse-posts');
    }

    /**
     * Get list of active forum posts with their details, optionally filtered by search term.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(Request $request)
    {
        try {
            $query = ForumPost::with(['user', 'tags', 'comments'])
                ->select(['uuid', 'user_id', 'title', 'body', 'pinned'])
                ->where('status', 'active')
                ->withCount('comments');

            if ($request->has('search') && $search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhereHas('tags', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
                });
            }

            $posts = $query->orderBy('pinned', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum posts for student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts. Please try again.'
            ], 500);
        }
    }
}