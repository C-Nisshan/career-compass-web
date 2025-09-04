<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\MentorApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CareerPredictionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SuccessStoryController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\ForumModerationController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Student\ForumController;
use App\Http\Controllers\Mentor\MentorForumController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\Student\SkillQuizController;

// Public API routes
Route::post('/auth/login', [LoginController::class, 'login'])->name('api.login');
Route::post('/auth/verify-registration-otp', [RegisterController::class, 'verifyRegistrationOtp'])->middleware('throttle:100,1');
Route::post('/register/student', [RegisterController::class, 'registerStudent'])->name('register.student');
Route::post('/register/mentor', [RegisterController::class, 'registerMentor'])->name('register.mentor');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('api.forgot-password');
Route::post('/auth/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('api.verify-otp');
Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword'])->name('api.reset-password');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('api.contact.submit');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('api.newsletter.subscribe');

// Protected API routes
Route::middleware('jwt.cookie')->group(function () {
    Route::get('/me', [LoginController::class, 'me'])->name('api.me');
    Route::post('/auth/logout', [LogoutController::class, 'logout'])->name('api.logout');
    Route::post('/refresh', [LoginController::class, 'refresh'])->name('api.refresh');
    Route::get('/admin/pending-registrations', [AdminController::class, 'pendingRegistrations']);
    Route::post('/admin/review-registration/{uuid}', [AdminController::class, 'reviewRegistration']);

    Route::get('/admin/mentors', [MentorApprovalController::class, 'getMentors'])->name('api.mentors.list');
    Route::post('/admin/mentors/approve/{uuid}', [MentorApprovalController::class, 'approveMentor'])->name('api.mentors.approve');
    Route::post('/admin/mentors/reject/{uuid}', [MentorApprovalController::class, 'rejectMentor'])->name('api.mentors.reject');
    Route::post('/admin/mentors/pending/{uuid}', [MentorApprovalController::class, 'setPending'])->name('api.mentors.pending');

    // Forum APIs for Students
    Route::middleware('role:student')->group(function () {
        Route::get('/student/forum-posts', [ForumController::class, 'getPosts'])->name('api.student.forum-posts');
        Route::get('/student/forum-tags', [ForumController::class, 'getTags'])->name('api.student.forum-tags');
        Route::post('/student/forum-posts', [ForumController::class, 'storePost'])->name('api.student.forum-posts.store');
        Route::get('/student/forum-posts/{uuid}', [ForumController::class, 'getPost']);
        Route::post('/student/forum-posts/{uuid}/comment', [ForumController::class, 'storeComment']);
        Route::post('/student/forum-posts/{uuid}/vote', [ForumController::class, 'toggleVote']);

        // New routes to add to api.php under the student middleware group:
        Route::get('/student/quizzes', [SkillQuizController::class, 'getQuizzes'])->name('api.student.quizzes');
        Route::get('/student/quizzes/{uuid}', [SkillQuizController::class, 'getQuiz'])->name('api.student.quiz');
        Route::post('/student/quizzes/{uuid}/submit', [SkillQuizController::class, 'submitQuiz'])->name('api.student.quiz.submit');
        Route::post('/student/quizzes/{quizUuid}/questions/{questionUuid}/check', [SkillQuizController::class, 'checkAnswer'])->name('api.student.quiz.check-answer');
        Route::get('/student/quiz-history', [SkillQuizController::class, 'getHistory'])->name('api.student.quiz-history');
    });

    // Forum APIs for Mentors
    Route::middleware('role:mentor')->group(function () {
        Route::get('/mentor/forum-posts', [App\Http\Controllers\Mentor\MentorForumController::class, 'getPosts']);
        Route::get('/mentor/forum-posts/{uuid}', [App\Http\Controllers\Mentor\MentorForumController::class, 'getPost']);
        Route::post('/mentor/forum-posts/{uuid}/comment', [App\Http\Controllers\Mentor\MentorForumController::class, 'storeComment']);
        Route::get('/mentor/forum-tags', [App\Http\Controllers\Mentor\MentorForumController::class, 'getTags']);
        Route::post('/mentor/forum-posts', [App\Http\Controllers\Mentor\MentorForumController::class, 'storePost']);
    });

    // Forum APIs for Admins
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserManagementController::class, 'getUsers'])->name('api.admin.users');
        Route::delete('/admin/users/{uuid}', [UserManagementController::class, 'deleteUser'])->name('api.admin.users.delete');

        Route::get('/admin/success-stories', [SuccessStoryController::class, 'getStories'])->name('api.admin.success-stories');
        Route::post('/admin/success-stories', [SuccessStoryController::class, 'store'])->name('api.admin.success-stories.store');
        Route::put('/admin/success-stories/{uuid}', [SuccessStoryController::class, 'update'])->name('api.admin.success-stories.update');
        Route::delete('/admin/success-stories/{uuid}', [SuccessStoryController::class, 'destroy'])->name('api.admin.success-stories.destroy');

        Route::get('/admin/quizzes', [QuizManagementController::class, 'getQuizzes'])->name('api.admin.quizzes');
        Route::post('/admin/quizzes', [QuizManagementController::class, 'storeQuiz'])->name('api.admin.quizzes.store');
        Route::get('/admin/quiz-questions', [QuizManagementController::class, 'getQuestions'])->name('api.admin.quiz-questions');
        Route::post('/admin/quiz-questions', [QuizManagementController::class, 'storeQuestion'])->name('api.admin.quiz-questions.store');
        Route::put('/admin/quiz-questions/{uuid}', [QuizManagementController::class, 'updateQuestion'])->name('api.admin.quiz-questions.update');
        Route::delete('/admin/quiz-questions/{uuid}', [QuizManagementController::class, 'destroyQuestion'])->name('api.admin.quiz-questions.destroy');

        // Forum Moderation Routes
        Route::get('/admin/forum-posts', [ForumModerationController::class, 'getPosts'])->name('api.admin.forum-posts');
        Route::post('/admin/forum-posts/pin/{uuid}', [ForumModerationController::class, 'pinPost'])->name('api.admin.forum-posts.pin');
        Route::post('/admin/forum-posts/delete/{uuid}', [ForumModerationController::class, 'deletePost'])->name('api.admin.forum-posts.delete');
        Route::post('/admin/forum-reports/resolve/{uuid}', [ForumModerationController::class, 'resolveReport'])->name('api.admin.forum-reports.resolve');
        Route::post('/admin/forum-reports/dismiss/{uuid}', [ForumModerationController::class, 'dismissReport'])->name('api.admin.forum-reports.dismiss');

        // Comment Moderation Routes
        Route::get('/admin/forum-comments', [CommentModerationController::class, 'getComments'])->name('api.admin.forum-comments');
        Route::post('/admin/forum-comments/hide/{uuid}', [CommentModerationController::class, 'hideComment'])->name('api.admin.forum-comments.hide');
    });
});

Route::post('/predict-career', [CareerPredictionController::class, 'predict'])->name('api.predict-career');
Route::get('/community/forum-posts', [CommunityController::class, 'getPosts'])->name('api.community.forum-posts');