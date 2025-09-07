<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Log;

class CommunityController extends Controller
{
    public function forum()
    {
        return view('community.forum');
    }

    public function create()
    {
        return view('community.create_posts');
    }

    public function store(Request $request)
    {
        // This method may be redundant with the API; consider redirecting to API or keeping for non-API form submissions
        return redirect()->route('community.forum');
    }

    public function show($uuid)
    {
        return view('community.show_post', ['uuid' => $uuid]);
    }

    /**
     * Get list of active forum posts with their details for public view.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(Request $request)
    {
        try {
            $query = ForumPost::with([
                'user',
                'user.studentProfile',
                'user.mentorProfile',
                'tags'
            ])
                ->select(['uuid', 'user_id', 'title', 'body', 'status', 'pinned', 'created_at'])
                ->where('status', 'active')
                ->orderBy('pinned', 'desc')
                ->orderBy('created_at', 'desc');

            $posts = $query->get()->map(function ($post) {
                if ($post->user) {
                    // Always use first_name + last_name from users table
                    $name = trim("{$post->user->first_name} {$post->user->last_name}") ?: 'Anonymous';

                    // Detect role label
                    if ($post->user->studentProfile) {
                        $roleLabel = 'Student';
                    } elseif ($post->user->mentorProfile) {
                        $roleLabel = 'Mentor';
                    } else {
                        $roleLabel = ucfirst(strtolower($post->user->role->name ?? 'User'));
                    }

                    $post->user->display_name = $name;
                    $post->user->role_label = $roleLabel;
                } else {
                    $post->user = (object) [
                        'display_name' => 'Anonymous',
                        'role_label' => 'Guest'
                    ];
                }

                return $post;
            });

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching public forum posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts. Please try again.'
            ], 500);
        }
    }
}
