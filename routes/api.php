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
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CareerPredictionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SuccessStoryController;
use App\Http\Controllers\Admin\QuizManagementController;

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
    Route::post('/forum', [ForumController::class, 'store'])->name('api.forum.store');

    // Forum APIs for Students
    Route::middleware('role:student')->group(function () {
        Route::put('/forum/{uuid}', [ForumController::class, 'update'])->name('api.forum.update');
        Route::delete('/forum/{uuid}', [ForumController::class, 'destroy'])->name('api.forum.destroy');
        Route::post('/forum/{postUuid}/comments', [ForumController::class, 'storeComment'])->name('api.forum.comments.store');
    });

    // Forum APIs for Mentors
    Route::middleware('role:mentor')->group(function () {
        Route::put('/forum/{uuid}', [ForumController::class, 'update'])->name('api.forum.update');
        Route::delete('/forum/{uuid}', [ForumController::class, 'destroy'])->name('api.forum.destroy');
        Route::post('/forum/{uuid}/pin', [ForumController::class, 'pin'])->name('api.forum.pin');
        Route::post('/forum/{uuid}/unpin', [ForumController::class, 'unpin'])->name('api.forum.unpin');
        Route::post('/forum/{postUuid}/comments', [ForumController::class, 'storeComment'])->name('api.forum.comments.store');
    });

    // Forum APIs for Admins
    Route::middleware('role:admin')->group(function () {
        Route::get('/forum/comments', [ForumController::class, 'comments'])->name('api.forum.comments');
        Route::put('/forum/{uuid}', [ForumController::class, 'update'])->name('api.forum.update');
        Route::delete('/forum/{uuid}', [ForumController::class, 'destroy'])->name('api.forum.destroy');
        Route::post('/forum/{uuid}/pin', [ForumController::class, 'pin'])->name('api.forum.pin');
        Route::post('/forum/{uuid}/unpin', [ForumController::class, 'unpin'])->name('api.forum.unpin');
        Route::post('/forum/{uuid}/moderate', [ForumController::class, 'moderatePost'])->name('api.forum.moderate');
        Route::post('/forum/comments/{uuid}/moderate', [ForumController::class, 'moderateComment'])->name('api.forum.comments.moderate');
        Route::post('/forum/{postUuid}/comments', [ForumController::class, 'storeComment'])->name('api.forum.comments.store');

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
    });
});

// Public Forum routes
Route::get('/forum', [ForumController::class, 'index'])->name('api.forum.index');
Route::get('/forum/{uuid}', [ForumController::class, 'show'])->name('api.forum.show');

Route::post('/predict-career', [CareerPredictionController::class, 'predict'])->name('api.predict-career');