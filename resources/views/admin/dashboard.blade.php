@extends('layouts.app')

@section('content')
<div class="container mx-auto p-5">
    <h1 class="section-title">Admin Dashboard</h1>
    <div id="logout-message" class="text-danger mt-2"></div>

    <!-- Stats Grid -->
    <div class="admin-dashboard-grid admin-dashboard-grid-cols-1 admin-dashboard-sm-grid-cols-2 admin-dashboard-lg-grid-cols-3 admin-dashboard-gap-6 admin-dashboard-mb-12">
        <!-- Total Users -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-blue-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Total Users</h3>
                    <p class="admin-dashboard-text-3xl admin-dashboard-font-bold admin-dashboard-text-gray-900" id="total-users">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Total Mentors -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-purple-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7v-2a3 3 0 005.356-1.857M4 17H2v2a3 3 0 005.356 1.857M4 17a3 3 0 015.356-1.857m6.288 0a3 3 0 015.356 1.857M12 12a4 4 0 100-8 4 4 0 000 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Total Mentors</h3>
                    <p class="admin-dashboard-text-3xl admin-dashboard-font-bold admin-dashboard-text-gray-900" id="total-mentors">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Active Forum Posts -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-green-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5v-4a2 2 0 012-2h10a2 2 0 012 2v4h-4M7 6h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Active Forum Posts</h3>
                    <p class="admin-dashboard-text-3xl admin-dashboard-font-bold admin-dashboard-text-gray-900" id="active-forum-posts">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Total Predictions -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-indigo-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V9a2 2 0 00-2-2h-2a2 2 0 00-2 2v10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Career Predictions</h3>
                    <p class="admin-dashboard-text-3xl admin-dashboard-font-bold admin-dashboard-text-gray-900" id="total-predictions">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Total Success Stories -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-yellow-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Success Stories</h3>
                    <p class="text-3xl font-bold text-gray-900" id="total-success-stories">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Total Quiz Results -->
        <div class="admin-dashboard-stat-card">
            <div class="flex items-center gap-4">
                <div class="admin-dashboard-icon-wrapper admin-dashboard-bg-red-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="admin-dashboard-text-lg admin-dashboard-font-semibold admin-dashboard-text-gray-700">Quiz Results</h3>
                    <p class="admin-dashboard-text-3xl admin-dashboard-font-bold admin-dashboard-text-gray-900" id="total-quiz-results">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="admin-dashboard-grid admin-dashboard-grid-cols-1 admin-dashboard-lg-grid-cols-2 admin-dashboard-gap-6">
        <!-- Recent Predictions -->
        <div class="admin-dashboard-activity-card">
            <h2 class="admin-dashboard-text-xl admin-dashboard-font-semibold admin-dashboard-text-gray-800 mb-4">Recent Career Predictions</h2>
            <div class="admin-dashboard-space-y-4" id="recent-predictions">
                <p class="admin-dashboard-text-gray-500">Loading...</p>
            </div>
        </div>

        <!-- Recent Forum Posts -->
        <div class="admin-dashboard-activity-card">
            <h2 class="admin-dashboard-text-xl admin-dashboard-font-semibold admin-dashboard-text-gray-800 mb-4">Recent Forum Posts</h2>
            <div class="admin-dashboard-space-y-4" id="recent-posts">
                <p class="admin-dashboard-text-gray-500">Loading...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">
@endpush

@push('scripts')
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.logoutUrl = '{{ route('api.logout') }}';
    </script>
    <script src="{{ asset('assets/js/auth/logout.js') }}"></script>
    <script src="{{ asset('assets/js/admin/admin_dashboard.js') }}"></script>
@endpush