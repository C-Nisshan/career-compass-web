<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumTag;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ForumController extends Controller
{
    /**
     * List all active posts with optional tag filter.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = $this->authenticateUser($request); // Returns null for guests
            Log::info('Fetching forum posts', [
                'user_id' => $user?->uuid ?? 'guest',
                'user_role' => $user?->role->value ?? 'guest',
                'tag_filter' => $request->query('tag'),
                'status_filter' => $request->query('status'),
            ]);

            $tag = $request->query('tag');
            $status = $request->query('status');
            $posts = ForumPost::when($status, function ($query, $status) {
                    $query->where('status', $status);
                }, function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['user', 'tags', 'comments' => function ($query) {
                    $query->where('status', 'active')->with('user');
                }])
                ->when($tag, function ($query, $tag) {
                    $query->whereHas('tags', function ($q) use ($tag) {
                        $q->where('name', $tag);
                    });
                })
                ->orderBy('pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json(['posts' => $posts], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum posts: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to fetch posts'], 500);
        }
    }

    /**
     * List all comments with optional status filter.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments(Request $request)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user || $user->role !== RoleEnum::ADMIN) {
                Log::warning('Unauthorized attempt to fetch comments', [
                    'user_id' => $user?->uuid ?? 'none',
                    'user_role' => $user?->role->value ?? 'none',
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            Log::info('Fetching forum comments', [
                'user_id' => $user->uuid,
                'status_filter' => $request->query('status'),
            ]);

            $status = $request->query('status');
            $comments = ForumComment::when($status, function ($query, $status) {
                    $query->where('status', $status);
                })
                ->with(['user', 'post'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['comments' => $comments], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching comments: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to fetch comments'], 500);
        }
    }

    /**
     * Show a single post with comments.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request); // May return null for guests
            Log::info('Fetching single forum post', [
                'user_id' => $user?->uuid ?? 'guest',
                'user_role' => $user?->role->value ?? 'guest',
                'post_uuid' => $uuid,
            ]);

            $post = ForumPost::where('uuid', $uuid)
                ->where('status', 'active')
                ->with(['user', 'tags', 'comments' => function ($query) {
                    $query->where('status', 'active')->with('user');
                }])
                ->firstOrFail();

            return response()->json(['post' => $post], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    /**
     * Create a new forum post.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Log::info('Creating new forum post', [
                'user_id' => $user->uuid,
                'user_role' => $user->role->value,
            ]);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|exists:forum_tags,name',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for post creation', [
                    'errors' => $validator->errors(),
                    'user_id' => $user->uuid,
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = ForumPost::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->uuid,
                'title' => $request->title,
                'body' => $request->body,
                'status' => 'active',
            ]);

            if ($request->has('tags')) {
                $tagIds = ForumTag::whereIn('name', $request->tags)->pluck('uuid');
                $post->tags()->attach($tagIds);
            }

            Log::info('Post created successfully', [
                'post_uuid' => $post->uuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post->load(['user', 'tags']),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }

    /**
     * Update an existing post.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = ForumPost::where('uuid', $uuid)->firstOrFail();

            if ($post->user_id !== $user->uuid && $user->role !== RoleEnum::ADMIN) {
                Log::warning('Unauthorized attempt to update post', [
                    'user_id' => $user->uuid,
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'tags' => 'nullable|array',
                'tags.*' => 'string|exists:forum_tags,name',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for post update', [
                    'errors' => $validator->errors(),
                    'user_id' => $user->uuid,
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            if ($request->has('tags')) {
                $tagIds = ForumTag::whereIn('name', $request->tags)->pluck('uuid');
                $post->tags()->sync($tagIds);
            }

            Log::info('Post updated successfully', [
                'post_uuid' => $post->uuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json([
                'message' => 'Post updated successfully',
                'post' => $post->load(['user', 'tags']),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to update post'], 500);
        }
    }

    /**
     * Delete a post.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $post = ForumPost::where('uuid', $uuid)->firstOrFail();

            if ($post->user_id !== $user->uuid && $user->role !== RoleEnum::ADMIN) {
                Log::warning('Unauthorized attempt to delete post', [
                    'user_id' => $user->uuid,
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->delete();
            Log::info('Post deleted successfully', [
                'post_uuid' => $uuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to delete post'], 500);
        }
    }

    /**
     * Create a comment on a post.
     *
     * @param Request $request
     * @param string $postUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment(Request $request, $postUuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'comment' => 'required|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for comment creation', [
                    'errors' => $validator->errors(),
                    'user_id' => $user->uuid,
                    'post_uuid' => $postUuid,
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = ForumPost::where('uuid', $postUuid)->where('status', 'active')->firstOrFail();

            $comment = ForumComment::create([
                'uuid' => Str::uuid(),
                'forum_post_id' => $postUuid,
                'user_id' => $user->uuid,
                'comment' => $request->comment,
                'status' => 'active',
            ]);

            Log::info('Comment created successfully', [
                'comment_uuid' => $comment->uuid,
                'post_uuid' => $postUuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json([
                'message' => 'Comment created successfully',
                'comment' => $comment->load('user'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage(), [
                'post_uuid' => $postUuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to create comment'], 500);
        }
    }

    /**
     * Pin a post (mentors and admins only).
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function pin(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user || !in_array($user->role, [RoleEnum::MENTOR, RoleEnum::ADMIN])) {
                Log::warning('Unauthorized attempt to pin post', [
                    'user_id' => $user?->uuid ?? 'none',
                    'user_role' => $user?->role->value ?? 'none',
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post = ForumPost::where('uuid', $uuid)->firstOrFail();
            $post->update(['pinned' => true]);

            Log::info('Post pinned successfully', [
                'post_uuid' => $uuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json([
                'message' => 'Post pinned successfully',
                'post' => $post,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error pinning post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to pin post'], 500);
        }
    }

    /**
     * Unpin a post (mentors and admins only).
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpin(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user || !in_array($user->role, [RoleEnum::MENTOR, RoleEnum::ADMIN])) {
                Log::warning('Unauthorized attempt to unpin post', [
                    'user_id' => $user?->uuid ?? 'none',
                    'user_role' => $user?->role->value ?? 'none',
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post = ForumPost::where('uuid', $uuid)->firstOrFail();
            $post->update(['pinned' => false]);

            Log::info('Post unpinned successfully', [
                'post_uuid' => $uuid,
                'user_id' => $user->uuid,
            ]);

            return response()->json([
                'message' => 'Post unpinned successfully',
                'post' => $post,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error unpinning post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to unpin post'], 500);
        }
    }

    /**
     * Moderate a post (admins only).
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function moderatePost(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user || $user->role !== RoleEnum::ADMIN) {
                Log::warning('Unauthorized attempt to moderate post', [
                    'user_id' => $user?->uuid ?? 'none',
                    'user_role' => $user?->role->value ?? 'none',
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,hidden',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for post moderation', [
                    'errors' => $validator->errors(),
                    'user_id' => $user->uuid,
                    'post_uuid' => $uuid,
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = ForumPost::where('uuid', $uuid)->firstOrFail();
            $post->update(['status' => $request->status]);

            Log::info('Post moderated successfully', [
                'post_uuid' => $uuid,
                'user_id' => $user->uuid,
                'new_status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Post moderated successfully',
                'post' => $post,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error moderating post: ' . $e->getMessage(), [
                'post_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to moderate post'], 500);
        }
    }

    /**
     * Moderate a comment (admins only).
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function moderateComment(Request $request, $uuid)
    {
        try {
            $user = $this->authenticateUser($request);
            if (!$user || $user->role !== RoleEnum::ADMIN) {
                Log::warning('Unauthorized attempt to moderate comment', [
                    'user_id' => $user?->uuid ?? 'none',
                    'user_role' => $user?->role->value ?? 'none',
                    'comment_uuid' => $uuid,
                ]);
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,hidden',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed for comment moderation', [
                    'errors' => $validator->errors(),
                    'user_id' => $user->uuid,
                    'comment_uuid' => $uuid,
                ]);
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $comment = ForumComment::where('uuid', $uuid)->firstOrFail();
            $comment->update(['status' => $request->status]);

            Log::info('Comment moderated successfully', [
                'comment_uuid' => $uuid,
                'user_id' => $user->uuid,
                'new_status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Comment moderated successfully',
                'comment' => $comment,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error moderating comment: ' . $e->getMessage(), [
                'comment_uuid' => $uuid,
                'request_url' => $request->fullUrl(),
            ]);
            return response()->json(['error' => 'Failed to moderate comment'], 500);
        }
    }

    /**
     * Authenticate user using JWT token from cookie.
     *
     * @param Request $request
     * @return \App\Models\User|null
     */
    protected function authenticateUser(Request $request)
    {
        try {
            $token = $request->cookie('token');
            if (!$token) {
                Log::warning('No token found in cookie for forum action', [
                    'request_url' => $request->fullUrl(),
                    'cookies' => $request->cookies->all(),
                ]);
                return null;
            }

            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if (!$user) {
                Log::warning('User not found for token', [
                    'token_prefix' => substr($token, 0, 10) . '...',
                    'request_url' => $request->fullUrl(),
                ]);
                return null;
            }

            $payload = JWTAuth::getPayload();
            if ($payload->get('token_version') !== $user->token_version) {
                Log::warning('Token version mismatch', [
                    'payload_version' => $payload->get('token_version'),
                    'user_version' => $user->token_version,
                    'request_url' => $request->fullUrl(),
                ]);
                return null;
            }

            auth('api')->setUser($user);
            return $user;
        } catch (\Exception $e) {
            Log::error('Authentication error in ForumController: ' . $e->getMessage(), [
                'request_url' => $request->fullUrl(),
                'token_prefix' => isset($token) ? substr($token, 0, 10) . '...' : 'none',
            ]);
            return null;
        }
    }
}