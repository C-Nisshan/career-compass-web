<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumTag;
use App\Models\ForumVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Enums\RoleEnum;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class MentorForumController extends Controller
{
    /**
     * Display the browse posts page for mentors.
     *
     * @return \Illuminate\View\View
     */
    public function browsePosts()
    {
        return view('mentor.community-forum.browse-posts');
    }

    /**
     * Display the create post page for mentors.
     *
     * @return \Illuminate\View\View
     */
    public function createPost()
    {
        return view('mentor.community-forum.create-post');
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
                ->select(['uuid', 'user_id', 'title', 'body', 'pinned', 'mentor_guidance'])
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
            Log::error('Mentor: Error fetching forum posts: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts. Please try again.'
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
                ->select(['uuid', 'user_id', 'title', 'body', 'pinned', 'created_at', 'mentor_guidance'])
                ->where('uuid', $uuid)
                ->where('status', 'active')
                ->withCount('votes')
                ->firstOrFail();

            // Attempt to get authenticated user without enforcing middleware
            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                // No user authenticated, proceed without user_id
                Log::info('No authenticated user for contact form submission', [
                    'error' => $e->getMessage()
                ]);
            }

            $isMentor = $user;
            $hasVoted = $user ? ForumVote::where('forum_post_id', $post->uuid)
                ->where('user_id', $user->uuid)
                ->exists() : false;

            return response()->json([
                'success' => true,
                'data' => [
                    'post' => $post,
                    'can_pin' => $isMentor,
                    'has_voted' => $hasVoted
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Mentor: Error fetching forum post: ' . $e->getMessage(), ['uuid' => $uuid, 'exception' => $e]);
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

            // Attempt to get authenticated user without enforcing middleware
            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                // No user authenticated, proceed without user_id
                Log::info('No authenticated user for contact form submission', [
                    'error' => $e->getMessage()
                ]);
            }

            if (!$user) {
                Log::warning('Mentor: Comment creation attempted without authenticated mentor', ['uuid' => $uuid]);
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to comment as a mentor.'
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

            Log::info('Mentor: Comment created', [
                'uuid' => $comment->uuid,
                'post_uuid' => $post->uuid,
                'user_id' => $user->uuid
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.',
                'data' => $comment->load('user')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Mentor: Error creating comment: ' . $e->getMessage(), ['uuid' => $uuid, 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment. Please try again.'
            ], 500);
        }
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
            Log::error('Mentor: Error fetching forum tags: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tags. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a new mentor-specific guidance post.
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
                'tags.*' => 'exists:forum_tags,uuid',
                'is_guidance' => 'boolean'
            ]);

            // Attempt to get authenticated user without enforcing middleware
            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                // No user authenticated, proceed without user_id
                Log::info('No authenticated user for contact form submission', [
                    'error' => $e->getMessage()
                ]);
            }

            if (!$user) {
                Log::warning('Mentor: Forum post creation attempted without authenticated mentor', [
                    'request' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to create a post as a mentor.'
                ], 401);
            }

            $post = ForumPost::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->uuid,
                'title' => $validated['title'],
                'body' => $validated['body'],
                'status' => 'active',
                'pinned' => false,
                'mentor_guidance' => $validated['is_guidance'] ?? true
            ]);

            if (!empty($validated['tags'])) {
                $syncData = [];
                foreach ($validated['tags'] as $tagUuid) {
                    $syncData[$tagUuid] = ['uuid' => Str::uuid()];
                }
                $post->tags()->sync($syncData);
            }

            Log::info('Mentor: Forum post created', [
                'uuid' => $post->uuid,
                'user_id' => $user->uuid,
                'mentor_guidance' => $post->mentor_guidance
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully.',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            Log::error('Mentor: Error creating forum post: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post. Please try again.'
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
            // Attempt to get authenticated user without enforcing middleware
            $user = null;
            try {
                $token = $request->cookie('token');
                if ($token) {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::authenticate();
                }
            } catch (\Exception $e) {
                // No user authenticated, proceed without user_id
                Log::info('No authenticated user for contact form submission', [
                    'error' => $e->getMessage()
                ]);
            }

            if (!$user) {
                Log::warning('Mentor: Vote attempted without authenticated mentor', ['uuid' => $uuid]);
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to vote as a mentor.'
                ], 401);
            }

            $post = ForumPost::where('uuid', $uuid)->where('status', 'active')->firstOrFail();

            $vote = ForumVote::where('forum_post_id', $post->uuid)
                ->where('user_id', $user->uuid)
                ->first();

            if ($vote) {
                $vote->delete();
                $action = 'removed';
                Log::info('Mentor: Vote removed', ['post_uuid' => $post->uuid, 'user_id' => $user->uuid]);
            } else {
                ForumVote::create([
                    'uuid' => Str::uuid(),
                    'forum_post_id' => $post->uuid,
                    'user_id' => $user->uuid
                ]);
                $action = 'added';
                Log::info('Mentor: Vote added', ['post_uuid' => $post->uuid, 'user_id' => $user->uuid]);
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
            Log::error('Mentor: Error toggling vote: ' . $e->getMessage(), ['uuid' => $uuid, 'exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle vote. Please try again.'
            ], 500);
        }
    }

    /**
     * Helper method to get authenticated user from token.
     *
     * @param Request $request
     * @return \App\Models\User|null
     */
    protected function getAuthenticatedUser(Request $request)
    {
        try {
            $token = $request->cookie('token');
            if (!$token) {
                Log::info('Mentor: No token found in request cookies');
                return null;
            }

            JWTAuth::setToken($token);
            return JWTAuth::authenticate();
        } catch (\Exception $e) {
            Log::info('Mentor: Failed to authenticate user', [
                'error' => $e->getMessage(),
                'token' => substr($token ?? '', 0, 20) . '...' // Log partial token for debugging
            ]);
            return null;
        }
    }
}