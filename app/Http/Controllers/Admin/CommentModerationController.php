<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumComment;
use App\Models\ForumReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentModerationController extends Controller
{
    /**
     * Display the comment moderation dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.comment-moderation');
    }

    /**
     * Get list of forum comments with their details, optionally filtered by status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request)
    {
        try {
            $query = ForumComment::with(['user', 'post', 'reports.reportedBy'])
                ->select(['uuid', 'forum_post_id', 'user_id', 'comment', 'status']);

            if ($request->has('status')) {
                if ($request->input('status') === 'reported') {
                    $query->whereHas('reports', function ($q) {
                        $q->whereIn('status', ['pending']);
                    });
                } else if (in_array($request->input('status'), ['active', 'hidden'])) {
                    $query->where('status', $request->input('status'));
                }
            }

            $comments = $query->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching forum comments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments. Please try again.'
            ], 500);
        }
    }

    /**
     * Hide a forum comment.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function hideComment($uuid)
    {
        try {
            $comment = ForumComment::where('uuid', $uuid)->firstOrFail();
            $comment->update(['status' => 'hidden']);

            Log::info('Forum comment hidden', ['uuid' => $uuid]);

            return response()->json([
                'success' => true,
                'message' => 'Comment hidden successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error hiding comment: ' . $e->getMessage(), ['uuid' => $uuid]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to hide comment.'
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