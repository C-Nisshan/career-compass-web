<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumTag;
use App\Models\ForumComment;
use App\Models\ForumVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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
                ->withCount('comments')
                ->withCount('votes');

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

    /**
     * Display the create post page.
     *
     * @return \Illuminate\View\View
     */
    public function createPost()
    {
        return view('student.community-forum.create-post');
    }

    /**
     * Get list of available forum tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTags()
    {
        try {
            $tags = ForumTag::select(['uuid', 'name'])->get();

            return response()->json([
                'success' => true,
                'data' => $tags
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum tags: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tags. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a new forum post.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePost(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'tags' => 'array',
                'tags.*' => 'exists:forum_tags,uuid'
            ]);

            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                Log::info('No authenticated user for forum post creation', [
                    'error' => $e->getMessage()
                ]);
            }

            if (!$user) {
                Log::warning('Forum post creation attempted without authenticated user');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to create a post.'
                ], 401);
            }

            $post = ForumPost::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->uuid,
                'title' => $validated['title'],
                'body' => $validated['body'],
                'status' => 'active',
                'pinned' => false
            ]);

            if (!empty($validated['tags'])) {
                $syncData = [];
                foreach ($validated['tags'] as $tagUuid) {
                    $syncData[$tagUuid] = ['uuid' => Str::uuid()];
                }
                $post->tags()->sync($syncData);
            }

            Log::info('Forum post created', ['uuid' => $post->uuid, 'user_id' => $user->uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully.',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating forum post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post. Please try again.'
            ], 500);
        }
    }

    /**
     * Get details of a specific forum post with comments and vote count.
     *
     * @param string $uuid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPost($uuid, Request $request)
    {
        try {
            $post = ForumPost::with(['user', 'tags', 'comments.user'])
                ->select(['uuid', 'user_id', 'title', 'body', 'pinned', 'created_at'])
                ->where('uuid', $uuid)
                ->where('status', 'active')
                ->withCount('votes')
                ->firstOrFail();

            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                Log::info('No authenticated user for fetching post', ['error' => $e->getMessage()]);
            }

            $hasVoted = $user ? ForumVote::where('forum_post_id', $post->uuid)
                ->where('user_id', $user->uuid)
                ->exists() : false;

            return response()->json([
                'success' => true,
                'data' => [
                    'post' => $post,
                    'has_voted' => $hasVoted
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post. Please try again.'
            ], 404);
        }
    }

    /**
     * Create a new comment for a forum post.
     *
     * @param string $uuid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment($uuid, Request $request)
    {
        try {
            $validated = $request->validate([
                'comment' => 'required|string|max:5000'
            ]);

            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                Log::info('No authenticated user for comment creation', ['error' => $e->getMessage()]);
            }

            if (!$user) {
                Log::warning('Comment creation attempted without authenticated user');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to comment.'
                ], 401);
            }

            $post = ForumPost::where('uuid', $uuid)->where('status', 'active')->firstOrFail();

            $comment = ForumComment::create([
                'uuid' => Str::uuid(),
                'forum_post_id' => $post->uuid,
                'user_id' => $user->uuid,
                'comment' => $validated['comment'],
                'status' => 'active'
            ]);

            Log::info('Comment created', ['uuid' => $comment->uuid, 'post_uuid' => $post->uuid, 'user_id' => $user->uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
                'data' => $comment->load('user')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment. Please try again.'
            ], 500);
        }
    }

    /**
     * Upvote or remove upvote for a forum post.
     *
     * @param string $uuid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleVote($uuid, Request $request)
    {
        try {
            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                Log::info('No authenticated user for voting', ['error' => $e->getMessage()]);
            }

            if (!$user) {
                Log::warning('Vote attempted without authenticated user');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to vote.'
                ], 401);
            }

            $post = ForumPost::where('uuid', $uuid)->where('status', 'active')->firstOrFail();

            $vote = ForumVote::where('forum_post_id', $post->uuid)
                ->where('user_id', $user->uuid)
                ->first();

            if ($vote) {
                $vote->delete();
                $action = 'removed';
                Log::info('Vote removed', ['post_uuid' => $post->uuid, 'user_id' => $user->uuid]);
            } else {
                ForumVote::create([
                    'uuid' => Str::uuid(),
                    'forum_post_id' => $post->uuid,
                    'user_id' => $user->uuid
                ]);
                $action = 'added';
                Log::info('Vote added', ['post_uuid' => $post->uuid, 'user_id' => $user->uuid]);
            }

            $voteCount = ForumVote::where('forum_post_id', $post->uuid)->count();

            return response()->json([
                'success' => true,
                'message' => "Vote $action successfully.",
                'data' => [
                    'vote_count' => $voteCount,
                    'has_voted' => $action === 'added'
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error toggling vote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle vote. Please try again.'
            ], 500);
        }
    }
}