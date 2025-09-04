<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SkillQuizController extends Controller
{
    /**
     * Display the skill quizzes page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('student.skill-quizzes');
    }

    /**
     * Get list of available quizzes for students.
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
            Log::error('Error fetching quizzes for student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quizzes. Please try again.'
            ], 500);
        }
    }

    /**
     * Get a specific quiz with questions (without answers).
     *
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuiz($uuid)
    {
        try {
            $quiz = Quiz::with(['questions' => function ($query) {
                $query->select('uuid', 'quiz_id', 'question');
            }])->where('uuid', $uuid)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $quiz
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching quiz for student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quiz. Please try again.'
            ], 500);
        }
    }

    /**
     * Check a single question answer for instant feedback.
     *
     * @param Request $request
     * @param string $quizUuid
     * @param string $questionUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAnswer(Request $request, $quizUuid, $questionUuid)
    {
        try {
            $validated = $request->validate([
                'answer' => 'required|string|max:255',
            ]);

            $question = QuizQuestion::where('uuid', $questionUuid)
                ->where('quiz_id', $quizUuid)
                ->firstOrFail();

            $isCorrect = strtolower(trim($validated['answer'])) === strtolower(trim($question->answer));

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error checking quiz answer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check answer. Please try again.'
            ], 500);
        }
    }

    /**
     * Submit quiz answers and get final feedback.
     *
     * @param Request $request
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitQuiz(Request $request, $uuid)
    {
        try {
            $validated = $request->validate([
                'answers' => 'required|array',
            ]);

            $quiz = Quiz::with('questions')->where('uuid', $uuid)->firstOrFail();

            $userAnswers = $validated['answers'];
            $correctCount = 0;
            $feedback = [];
            $totalQuestions = $quiz->questions->count();

            foreach ($quiz->questions as $question) {
                $userAnswer = $userAnswers[$question->uuid] ?? '';
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($question->answer));

                if ($isCorrect) {
                    $correctCount++;
                }

                $feedback[$question->uuid] = [
                    'question' => $question->question,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->answer,
                    'is_correct' => $isCorrect,
                ];
            }

            $score = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;

            QuizResult::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => auth()->user()->uuid,
                'quiz_id' => $quiz->uuid,
                'score' => $score,
            ]);

            return response()->json([
                'success' => true,
                'score' => $score,
                'feedback' => $feedback,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error submitting quiz: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quiz. Please try again.'
            ], 500);
        }
    }

    /**
     * Get the student's quiz history.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory()
    {
        try {
            $results = QuizResult::with('quiz')
                ->where('user_id', auth()->user()->uuid)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $results->map(function ($result) {
                    return [
                        'quiz_title' => $result->quiz->title ?? 'Unknown Quiz',
                        'score' => $result->score,
                        'taken_at' => $result->created_at->format('Y-m-d H:i:s'),
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching quiz history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quiz history. Please try again.'
            ], 500);
        }
    }
}