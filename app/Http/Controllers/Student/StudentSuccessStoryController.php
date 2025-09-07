<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentSuccessStoryController extends Controller
{
    /**
     * Display the success stories view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('student.success-stories');
    }

    /**
     * Get list of success stories for students.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStories()
    {
        try {
            $stories = SuccessStory::select(['uuid', 'name', 'career_path', 'story', 'image', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform the image path to a full URL for the frontend
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
            Log::error('Error fetching success stories for students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch success stories. Please try again.'
            ], 500);
        }
    }
}