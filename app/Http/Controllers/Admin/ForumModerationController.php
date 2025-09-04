<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ForumModerationController extends Controller
{
    /**
     * Display the forum moderation dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.forum-moderation');
    }

    /**
     * Get list of forum posts with their details, optionally filtered by status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(Request $request)
    {
        try {
            $query = ForumPost::with(['user', 'tags', 'reports.reportedBy'])
                ->select(['uuid', 'user_id', 'title', 'body', 'status', 'pinned']);

            if ($request->has('status')) {
                if ($request->input('status') === 'reported') {
                    $query->whereHas('reports', function ($q) {
                        $q->whereIn('status', ['pending']);
                    });
                } else if (in_array($request->input('status'), ['active', 'hidden'])) {
                    $query->where('status', $request->input('status'));
                }
            }

            $posts = $query->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts. Please try again.'
            ], 500);
        }
    }

    /**
     * Pin or unpin a forum post.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinPost($uuid)
    {
        try {
            $post = ForumPost::where('uuid', $uuid)->firstOrFail();
            $newPinnedStatus = !$post->pinned;
            $post->update(['pinned' => $newPinnedStatus]);

            Log::info('Forum post pinned/unpinned', ['uuid' => $uuid, 'pinned' => $newPinnedStatus]);

            return response()->json([
                'success' => true,
                'message' => $newPinnedStatus ? 'Post pinned successfully.' : 'Post unpinned successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error pinning/unpinning post: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to pin/unpin post.'
            ], 404);
        }
    }

    /**
     * Delete (hide) a forum post.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost($uuid)
    {
        try {
            $post = ForumPost::where('uuid', $uuid)->firstOrFail();
            $post->update(['status' => 'hidden']);

            Log::info('Forum post hidden', ['uuid' => $uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Post hidden successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error hiding post: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to hide post.'
            ], 404);
        }
    }

    /**
     * Resolve a forum report.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolveReport($uuid)
    {
        try {
            $report = ForumReport::where('uuid', $uuid)->firstOrFail();
            $report->update(['status' => 'resolved']);

            Log::info('Forum report resolved', ['uuid' => $uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Report resolved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error resolving report: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve report.'
            ], 404);
        }
    }

    /**
     * Dismiss a forum report.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function dismissReport($uuid)
    {
        try {
            $report = ForumReport::where('uuid', $uuid)->firstOrFail();
            $report->update(['status' => 'dismissed']);

            Log::info('Forum report dismissed', ['uuid' => $uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Report dismissed successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error dismissing report: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss report.'
            ], 404);
        }
    }
}