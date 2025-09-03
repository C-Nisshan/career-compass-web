<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SuccessStoryController extends Controller
{
    /**
     * Display the success stories dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.success-stories');
    }

    /**
     * Get list of success stories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStories()
    {
        try {
            $stories = SuccessStory::select(['uuid', 'name', 'career_path', 'story', 'image', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Optionally, transform the image path to a full URL for the frontend
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
            Log::error('Error fetching success stories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch success stories. Please try again.'
            ], 500);
        }
    }

    /**
     * Store a new success story.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'career_path' => 'required|string|max:255',
                'story' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            ]);

            $storyData = [
                'name' => $validated['name'],
                'career_path' => $validated['career_path'],
                'story' => $validated['story'],
            ];

            if ($request->hasFile('image')) {
                // Store image in storage/app/public/success_stories
                $path = $request->file('image')->store('success_stories', 'public');
                $storyData['image'] = $path; // Store relative path (e.g., success_stories/filename.jpg)
            }

            $story = SuccessStory::create($storyData);

            Log::info('Success story created', ['uuid' => $story->uuid, 'name' => $story->name]);

            // Optionally, add the full image URL to the response
            if ($story->image) {
                $story->image_url = Storage::url($story->image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Success story created successfully.',
                'data' => $story
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating success story: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create success story. Please try again.'
            ], 500);
        }
    }

    /**
     * Update an existing success story.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uuid)
    {
        try {
            $story = SuccessStory::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'career_path' => 'required|string|max:255',
                'story' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $storyData = [
                'name' => $validated['name'],
                'career_path' => $validated['career_path'],
                'story' => $validated['story'],
            ];

            if ($request->hasFile('image')) {
                if ($story->image && Storage::disk('public')->exists($story->image)) {
                    Storage::disk('public')->delete($story->image);
                }
                $path = $request->file('image')->store('success_stories', 'public');
                $storyData['image'] = $path;
            }

            $story->update($storyData);

            Log::info('Success story updated', ['uuid' => $story->uuid, 'name' => $story->name]);

            if ($story->image) {
                $story->image_url = Storage::url($story->image);
            }

            return response()->json([
                'success' => true,
                'message' => 'Success story updated successfully.',
                'data' => $story
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating success story: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating success story: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update success story: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a success story.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($uuid)
    {
        try {
            $story = SuccessStory::where('uuid', $uuid)->firstOrFail();

            if ($story->image) {
                // Delete image from storage/app/public/success_stories
                Storage::disk('public')->delete($story->image);
            }

            $story->delete();

            Log::info('Success story deleted', ['uuid' => $uuid, 'name' => $story->name]);

            return response()->json([
                'success' => true,
                'message' => 'Success story deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting success story: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete success story. Please try again.'
            ], 500);
        }
    }
}