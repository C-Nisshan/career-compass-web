<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CommunityController;

// Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MentorApprovalController;
use App\Http\Controllers\Admin\SuccessStoryController as AdminSuccessStoryController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\ForumModerationController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\CommentModerationController;


// Student
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController as StudentProfileController;
use App\Http\Controllers\Student\CareerRecommendationController;
use App\Http\Controllers\Student\SkillQuizController;
use App\Http\Controllers\Student\StudentSuccessStoryController as StudentSuccessStoryController;
use App\Http\Controllers\Student\CommunityForumController as StudentCommunityForumController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\StudentSettingsController as StudentSettingsController;
use App\Http\Controllers\Student\ForumController;

// Mentor
use App\Http\Controllers\Mentor\MentorDashboardController;
use App\Http\Controllers\Mentor\MentorProfileController as MentorProfileController;
use App\Http\Controllers\Mentor\CommunityForumController as MentorCommunityForumController;
use App\Http\Controllers\Mentor\AnalyticsController as MentorAnalyticsController;
use App\Http\Controllers\Mentor\MentorSettingsController as MentorSettingsController;
use App\Http\Controllers\Mentor\MentorPendingController;
use App\Http\Controllers\Mentor\MentorForumController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

Route::get('/auth/google', [RegisterController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [RegisterController::class, 'handleGoogleCallback']);

// Debug route
Route::get('/test-auth', function () {
    return response()->json([
        'user' => auth('api')->user(),
        'cookies' => request()->cookies->all(),
        'cookie_token' => request()->cookie('token'),
        'raw_cookie_header' => request()->header('Cookie', 'none'),
    ]);
})->middleware('jwt.cookie');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.cookie', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::get('/admin/mentor/approvals', [MentorApprovalController::class, 'index'])->name('admin.mentor.approvals');
    Route::get('/admin/success-stories', [AdminSuccessStoryController::class, 'index'])->name('admin.success.stories');
    Route::get('/admin/quiz-questions', [QuizManagementController::class, 'index'])->name('admin.quiz.questions');
    Route::get('/admin/forum-moderation', [ForumModerationController::class, 'index'])->name('admin.forum.moderation');
    Route::get('/admin/comment-moderation', [CommentModerationController::class, 'index'])->name('admin.comment-moderation');
    Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.cookie', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/profile', [StudentProfileController::class, 'index'])->name('student.profile');
    Route::get('/student/career-recommendations', [CareerRecommendationController::class, 'index'])->name('student.career.recommendations');
    Route::get('/student/skill-quizzes', [SkillQuizController::class, 'index'])->name('student.skill.quizzes');
    Route::get('/student/success-stories', [StudentSuccessStoryController::class, 'index'])->name('student.success.stories');
    Route::get('/student/community-forum', [StudentCommunityForumController::class, 'index'])->name('student.community.forum');
    Route::get('/student/reports', [ReportController::class, 'index'])->name('student.reports');
    Route::get('/student/settings', [StudentSettingsController::class, 'index'])->name('student.settings');

    Route::get('/student/community-forum/browse-posts', [ForumController::class, 'browsePosts'])->name('student.forum.browse-posts');
    Route::get('/student/community-forum/create-post', [ForumController::class, 'createPost'])->name('student.forum.create-post');
    Route::get('/student/community-forum/posts/{uuid}', [ForumController::class, 'showPost'])->name('student.forum.show-post');
});

/*
|--------------------------------------------------------------------------
| Mentor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.cookie', 'role:mentor'])->group(function () {
    Route::get('/mentor/dashboard', [MentorDashboardController::class, 'index'])->name('mentor.dashboard');
    Route::get('/mentor/profile', [MentorProfileController::class, 'index'])->name('mentor.profile');
    Route::get('/mentor/community-forum', [MentorCommunityForumController::class, 'index'])->name('mentor.community.forum');
    Route::get('/mentor/analytics', [MentorAnalyticsController::class, 'index'])->name('mentor.analytics');
    Route::get('/mentor/settings', [MentorSettingsController::class, 'index'])->name('mentor.settings');
    Route::get('/mentor/pending', [MentorPendingController::class, 'index'])->name('mentor.settings');

    Route::get('/mentor/community-forum/browse-posts', [MentorForumController::class, 'browsePosts'])->name('mentor.forum.browse-posts');
    Route::get('/mentor/community-forum/create-post', [App\Http\Controllers\Mentor\MentorForumController::class, 'createPost'])->name('mentor.forum.create-post');
});

/*
|--------------------------------------------------------------------------
| Common Routes for Authenticated Users
|--------------------------------------------------------------------------
*/
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::get('/community/forum', [CommunityController::class, 'forum'])->name('community.forum');
Route::get('/community/forum/create', [CommunityController::class, 'create'])->name('forum.create');
Route::post('/community/forum/store', [CommunityController::class, 'store'])->name('forum.store');
Route::get('/community/forum/{uuid}', [CommunityController::class, 'show'])->name('forum.show');