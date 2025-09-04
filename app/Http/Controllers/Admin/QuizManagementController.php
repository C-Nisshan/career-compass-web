<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuizManagementController extends Controller
{
    /**
     * Display the quiz management dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.quiz-questions');
    }

    /**
     * Get list of quizzes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuizzes()
    {
        try {
            $quizzes = Quiz::select(['uuid', 'title', 'description', 'created_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $quizzes
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching quizzes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quizzes. Please try again.'
            ], 500);
        }
    }

    /**
     * Store a new quiz.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeQuiz(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            $quiz = Quiz::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
            ]);

            Log::info('Quiz created', ['uuid' => $quiz->uuid, 'title' => $quiz->title]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz created successfully.',
                'data' => $quiz
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating quiz: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quiz. Please try again.'
            ], 500);
        }
    }

    /**
     * Get list of quiz questions.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestions(Request $request)
    {
        try {
            $query = QuizQuestion::with('quiz')->select(['uuid', 'quiz_id', 'question', 'answer', 'created_at'])
                ->orderBy('created_at', 'desc');

            if ($request->has('quiz_id') && $request->quiz_id) {
                $query->where('quiz_id', $request->quiz_id);
            }

            $questions = $query->get();

            return response()->json([
                'success' => true,
                'data' => $questions
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching quiz questions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quiz questions. Please try again.'
            ], 500);
        }
    }

    /**
     * Store a new quiz question.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeQuestion(Request $request)
    {
        try {
            $validated = $request->validate([
                'quiz_id' => 'required|uuid|exists:quizzes,uuid',
                'question' => 'required|string|max:1000',
                'answer' => 'required|string|max:255',
            ]);

            $question = QuizQuestion::create([
                'quiz_id' => $validated['quiz_id'],
                'question' => $validated['question'],
                'answer' => $validated['answer'],
            ]);

            Log::info('Quiz question created', ['uuid' => $question->uuid, 'question' => $question->question]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz question created successfully.',
                'data' => $question->load('quiz')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating quiz question: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quiz question. Please try again.'
            ], 500);
        }
    }

    /**
     * Update an existing quiz question.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuestion(Request $request, $uuid)
    {
        try {
            $question = QuizQuestion::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'quiz_id' => 'required|uuid|exists:quizzes,uuid',
                'question' => 'required|string|max:1000',
                'answer' => 'required|string|max:255',
            ]);

            $question->update([
                'quiz_id' => $validated['quiz_id'],
                'question' => $validated['question'],
                'answer' => $validated['answer'],
            ]);

            Log::info('Quiz question updated', ['uuid' => $question->uuid, 'question' => $question->question]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz question updated successfully.',
                'data' => $question->load('quiz')
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error updating quiz question: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating quiz question: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quiz question. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a quiz question.
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyQuestion($uuid)
    {
        try {
            $question = QuizQuestion::where('uuid', $uuid)->firstOrFail();
            $question->delete();

            Log::info('Quiz question deleted', ['uuid' => $uuid, 'question' => $question->question]);

            return response()->json([
                'success' => true,
                'message' => 'Quiz question deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting quiz question: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quiz question. Please try again.'
            ], 500);
        }
    }
}
