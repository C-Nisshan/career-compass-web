<!-- Navigation sidebar for CareerCompass dashboards -->
<div class="sidebar bg-gray-900 text-white w-64 h-screen fixed top-0 left-0 overflow-y-auto">
    <div class="p-6">
        <!-- Role-based sidebar navigation -->
        <nav class="space-y-1">
            @auth
                @if(auth()->user()->role->value === \App\Enums\RoleEnum::ADMIN->value)
                    <!-- Admin Sidebar -->
                    <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('admin.dashboard') ? 'bg-gray-700' : '' }}" aria-label="Admin Dashboard">Dashboard</a>
                    
                    <div class="py-3 px-4 font-medium text-gray-300 uppercase tracking-wide">Manage Users</div>
                    <a href="{{ route('admin.users') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('admin.users') ? 'bg-gray-700' : '' }}" aria-label="View Users">View Users</a>
                    <a href="{{ route('admin.mentor.approvals') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('admin.mentor.approvals') ? 'bg-gray-700' : '' }}" aria-label="Mentor Approvals">Mentor Approvals</a>
                    
                    <div class="py-3 px-4 font-medium text-gray-300 uppercase tracking-wide">Manage Content</div>
                    <a href="{{ route('admin.success.stories') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('admin.success.stories') ? 'bg-gray-700' : '' }}" aria-label="Success Stories">Success Stories</a>
                    <a href="{{ route('admin.quiz.questions') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('admin.quiz.questions') ? 'bg-gray-700' : '' }}" aria-label="Quiz Questions">Quiz Questions</a>
                    
                    <div class="py-3 px-4 font-medium text-gray-300 uppercase tracking-wide">Community</div>
                    <a href="{{ route('admin.forum.moderation') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('admin.forum.moderation') ? 'bg-gray-700' : '' }}" aria-label="Forum Moderation">Forum Moderation</a>
                    <a href="{{ route('community.forum') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('community.forum', 'forum.show') ? 'bg-gray-700' : '' }}" aria-label="Community Forum">Community Forum</a>
                    
                    <a href="{{ route('admin.analytics') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('admin.analytics') ? 'bg-gray-700' : '' }}" aria-label="Analytics">Analytics</a>
                    <a href="{{ route('admin.settings') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('admin.settings') ? 'bg-gray-700' : '' }}" aria-label="Settings">Settings</a>

                @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::STUDENT->value)
                    <!-- Student Sidebar -->
                    <a href="{{ route('student.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.dashboard') ? 'bg-gray-700' : '' }}" aria-label="Student Dashboard">Dashboard</a>
                    
                    <a href="{{ route('student.profile') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.profile') ? 'bg-gray-700' : '' }}" aria-label="Profile">Profile</a>
                    <a href="{{ route('student.career.recommendations') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.career.recommendations') ? 'bg-gray-700' : '' }}" aria-label="Career Recommendations">Career Recommendations</a>
                    <a href="{{ route('student.skill.quizzes') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.skill.quizzes') ? 'bg-gray-700' : '' }}" aria-label="Skill Quizzes">Skill Quizzes</a>
                    <a href="{{ route('student.success.stories') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.success.stories') ? 'bg-gray-700' : '' }}" aria-label="Success Stories">Success Stories</a>
                    
                    <div class="py-3 px-4 font-medium text-gray-300 uppercase tracking-wide">Community</div>
                    <a href="{{ route('student.community.forum') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('student.community.forum', 'community.forum', 'forum.show') ? 'bg-gray-700' : '' }}" aria-label="Community Forum">View Forum</a>
                    <a href="{{ route('forum.create') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('forum.create') ? 'bg-gray-700' : '' }}" aria-label="Create Forum Post">Create Post</a>
                    
                    <a href="{{ route('student.reports') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.reports') ? 'bg-gray-700' : '' }}" aria-label="Reports">Reports</a>
                    <a href="{{ route('student.settings') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('student.settings') ? 'bg-gray-700' : '' }}" aria-label="Settings">Settings</a>

                @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::MENTOR->value)
                    @if(auth()->user()->status === 'pending')
                        <!-- Pending Mentor Sidebar -->
                        <div class="py-3 px-4 text-yellow-300">Your mentor application is pending approval.</div>
                        <a href="{{ route('mentor.profile') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.profile') ? 'bg-gray-700' : '' }}" aria-label="Profile">Profile</a>
                        <a href="{{ route('mentor.settings') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.settings') ? 'bg-gray-700' : '' }}" aria-label="Settings">Settings</a>
                    @else
                        <!-- Active Mentor Sidebar -->
                        <a href="{{ route('mentor.dashboard') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.dashboard') ? 'bg-gray-700' : '' }}" aria-label="Mentor Dashboard">Dashboard</a>
                        <a href="{{ route('mentor.profile') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.profile') ? 'bg-gray-700' : '' }}" aria-label="Profile">Profile</a>
                        
                        <div class="py-3 px-4 font-medium text-gray-300 uppercase tracking-wide">Community</div>
                        <a href="{{ route('mentor.community.forum') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('mentor.community.forum', 'community.forum', 'forum.show') ? 'bg-gray-700' : '' }}" aria-label="Community Forum">View Forum</a>
                        <a href="{{ route('forum.create') }}" class="block py-2.5 px-6 rounded hover:bg-gray-700 {{ Route::is('forum.create') ? 'bg-gray-700' : '' }}" aria-label="Create Forum Post">Create Post</a>
                        
                        <a href="{{ route('mentor.analytics') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.analytics') ? 'bg-gray-700' : '' }}" aria-label="Mentorship Analytics">Mentorship Analytics</a>
                        <a href="{{ route('mentor.settings') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('mentor.settings') ? 'bg-gray-700' : '' }}" aria-label="Settings">Settings</a>
                    @endif
                @endif

                <!-- Logout for all authenticated users -->
                <form id="logout-form" action="{{ route('api.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="block py-2.5 px-4 rounded hover:bg-red-700 w-full text-left" aria-label="Logout">Logout</button>
                </form>
            @endauth
            @guest
                <!-- Guest Sidebar -->
                <a href="{{ route('home') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('home') ? 'bg-gray-700' : '' }}" aria-label="Home">Home</a>
                <a href="{{ route('login') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('login') ? 'bg-gray-700' : '' }}" aria-label="Login">Login</a>
                <a href="{{ route('register') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('register') ? 'bg-gray-700' : '' }}" aria-label="Register">Register</a>
                <a href="{{ route('about') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('about') ? 'bg-gray-700' : '' }}" aria-label="About">About</a>
                <a href="{{ route('contact') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('contact') ? 'bg-gray-700' : '' }}" aria-label="Contact">Contact</a>
                <a href="{{ route('community.forum') }}" class="block py-2.5 px-4 rounded hover:bg-gray-700 {{ Route::is('community.forum', 'forum.show') ? 'bg-gray-700' : '' }}" aria-label="Community Forum">Community Forum</a>
            @endguest
        </nav>
    </div>
</div>