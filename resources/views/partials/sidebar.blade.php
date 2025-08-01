<!-- Navigation sidebar for CareerCompass dashboards -->
<div class="sidebar bg-gray-800 text-white w-64 h-screen fixed top-0 left-0 overflow-y-auto">
    <div class="p-4">
        <!-- Role-based sidebar navigation -->
        <nav class="space-y-2">
            @auth
                @if(auth()->user()->role->value === \App\Enums\RoleEnum::ADMIN->value)
                    <!-- Admin Sidebar -->
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('admin.dashboard') ? 'bg-blue-600' : '' }}" aria-label="Admin Dashboard">Admin Dashboard</a>
                    
                    <div class="py-2 px-4 font-semibold text-gray-400">Manage Users</div>
                    <a href="{{ route('admin.users') }}" class="block py-2 px-6 rounded hover:bg-blue-600 {{ Route::is('admin.users') ? 'bg-blue-600' : '' }}" aria-label="View Users">View Users</a>
                    <a href="{{ route('admin.mentor.approvals') }}" class="block py-2 px-6 rounded hover:bg-blue-600 {{ Route::is('admin.mentor.approvals') ? 'bg-blue-600' : '' }}" aria-label="Mentor Approvals">Mentor Approvals</a>
                    
                    <div class="py-2 px-4 font-semibold text-gray-400">Manage Content</div>
                    <a href="{{ route('admin.success.stories') }}" class="block py-2 px-6 rounded hover:bg-blue-600 {{ Route::is('admin.success.stories') ? 'bg-blue-600' : '' }}" aria-label="Success Stories">Success Stories</a>
                    <a href="{{ route('admin.quiz.questions') }}" class="block py-2 px-6 rounded hover:bg-blue-600 {{ Route::is('admin.quiz.questions') ? 'bg-blue-600' : '' }}" aria-label="Quiz Questions">Quiz Questions</a>
                    <a href="{{ route('admin.forum.moderation') }}" class="block py-2 px-6 rounded hover:bg-blue-600 {{ Route::is('admin.forum.moderation') ? 'bg-blue-600' : '' }}" aria-label="Forum Moderation">Forum Moderation</a>
                    
                    <a href="{{ route('admin.analytics') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('admin.analytics') ? 'bg-blue-600' : '' }}" aria-label="Analytics">Analytics</a>
                    <a href="{{ route('admin.settings') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('admin.settings') ? 'bg-blue-600' : '' }}" aria-label="Settings">Settings</a>

                @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::STUDENT->value)
                    <!-- Student Sidebar -->
                    <a href="{{ route('student.dashboard') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.dashboard') ? 'bg-blue-600' : '' }}" aria-label="Student Dashboard">Dashboard</a>
                    
                    <a href="{{ route('student.profile') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.profile') ? 'bg-blue-600' : '' }}" aria-label="Profile">Profile</a>
                    <a href="{{ route('student.career.recommendations') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.career.recommendations') ? 'bg-blue-600' : '' }}" aria-label="Career Recommendations">Career Recommendations</a>
                    <a href="{{ route('student.skill.quizzes') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.skill.quizzes') ? 'bg-blue-600' : '' }}" aria-label="Skill Quizzes">Skill Quizzes</a>
                    <a href="{{ route('student.success.stories') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.success.stories') ? 'bg-blue-600' : '' }}" aria-label="Success Stories">Success Stories</a>
                    <a href="{{ route('student.community.forum') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.community.forum') ? 'bg-blue-600' : '' }}" aria-label="Community Forum">Community Forum</a>
                    <a href="{{ route('student.reports') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.reports') ? 'bg-blue-600' : '' }}" aria-label="Reports">Reports</a>
                    <a href="{{ route('student.settings') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('student.settings') ? 'bg-blue-600' : '' }}" aria-label="Settings">Settings</a>

                @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::MENTOR->value)
                    @if(auth()->user()->status === 'pending')
                        <!-- Pending Mentor Sidebar -->
                        <div class="py-2 px-4 text-yellow-300">Your mentor application is pending approval.</div>
                        <a href="{{ route('mentor.profile') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.profile') ? 'bg-blue-600' : '' }}" aria-label="Profile">Profile</a>
                        <a href="{{ route('mentor.settings') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.settings') ? 'bg-blue-600' : '' }}" aria-label="Settings">Settings</a>
                    @else
                        <!-- Active Mentor Sidebar -->
                        <a href="{{ route('mentor.dashboard') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.dashboard') ? 'bg-blue-600' : '' }}" aria-label="Mentor Dashboard">Dashboard</a>
                        <a href="{{ route('mentor.profile') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.profile') ? 'bg-blue-600' : '' }}" aria-label="Profile">Profile</a>
                        <a href="{{ route('mentor.community.forum') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.community.forum') ? 'bg-blue-600' : '' }}" aria-label="Community Forum">Community Forum</a>
                        <a href="{{ route('mentor.analytics') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.analytics') ? 'bg-blue-600' : '' }}" aria-label="Mentorship Analytics">Mentorship Analytics</a>
                        <a href="{{ route('mentor.settings') }}" class="block py-2 px-4 rounded hover:bg-blue-600 {{ Route::is('mentor.settings') ? 'bg-blue-600' : '' }}" aria-label="Settings">Settings</a>
                    @endif
                @endif

                <!-- Logout for all authenticated users -->
                <form id="logout-form" action="{{ route('api.logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="block w-full text-left py-2 px-4 rounded hover:bg-red-600" aria-label="Logout">Logout</button>
                </form>
            @endauth
        </nav>
    </div>
</div>
