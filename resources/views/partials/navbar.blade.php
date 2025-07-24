<nav class="navbar navbar-expand-lg navbar-light bg-light enhanced-navbar">
    <div class="container">
        <a class="navbar-brand compass-logo" href="{{ route('home', ['lang' => app()->getLocale()]) }}">
            <!-- Decorative outer ring -->
            <div class="decorative-ring"></div>
            
            <!-- Floating particles -->
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            
            <!-- Main compass structure -->
            <div class="compass-rim">
                <div class="inner-compass">
                    <div class="compass-body">
                        <div class="compass-face">
                            <!-- Compass directions -->
                            <div class="direction north">{{ __('header.compass_north') }}</div>
                            <div class="direction south">{{ __('header.compass_south') }}</div>
                            <div class="direction east">{{ __('header.compass_east') }}</div>
                            <div class="direction west">{{ __('header.compass_west') }}</div>
                            
                            <!-- Mystical needle -->
                            <div class="compass-needle">
                                <div class="needle-north"></div>
                                <div class="needle-south"></div>
                            </div>
                            
                            <!-- Center gem -->
                            <div class="center-gem"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Integrated text -->
            <div class="compass-text career-text">{{ __('header.career_text') }}</div>
            <div class="compass-text compass-word">{{ __('header.compass_word') }}</div>
        </a>
        <button class="navbar-toggler hamburger" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav nav-links ms-auto">
                <li class="nav-item"><a class="nav-link" href="#features">{{ __('header.features') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#how-it-works">{{ __('header.how_it_works') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#testimonials">{{ __('header.testimonials') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#community">{{ __('header.community') }}</a></li>
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login', ['lang' => app()->getLocale()]) }}">{{ __('header.login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register', ['lang' => app()->getLocale()]) }}">{{ __('header.register') }}</a>
                    </li>
                @endguest
                @auth
                    @if(auth()->user()->role->value === \App\Enums\RoleEnum::ADMIN->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('header.admin_dashboard') }}</a>
                        </li>
                    @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::STUDENT->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('header.booker_dashboard') }}</a>
                        </li>
                    @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::MENTOR->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('mentor.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('header.provider_dashboard') }}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('api.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" id="logoutButton">{{ __('header.logout') }}</button>
                        </form>
                    </li>
                @endauth
            </ul>
            <a href="#" class="cta-btn ms-3">{{ __('header.start_free') }}</a>
        </div>
    </div>
</nav>
