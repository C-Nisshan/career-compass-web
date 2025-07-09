<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home', ['lang' => app()->getLocale()]) }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login', ['lang' => app()->getLocale()]) }}">{{ __('messages.login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register', ['lang' => app()->getLocale()]) }}">{{ __('messages.register') }}</a>
                    </li>
                @endguest
                @auth
                    @if(auth()->user()->role->value === \App\Enums\RoleEnum::ADMIN->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('messages.admin_dashboard') }}</a>
                        </li>
                    @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::STUDENT->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('messages.booker_dashboard') }}</a>
                        </li>
                    @elseif(auth()->user()->role->value === \App\Enums\RoleEnum::MENTOR->value)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('mentor.dashboard', ['lang' => app()->getLocale()]) }}">{{ __('messages.provider_dashboard') }}</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form id="logout-form" action="{{ route('api.logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" id="logoutButton">{{ __('messages.logout') }}</button>
                        </form>
                    </li>
                @endauth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        {{ strtoupper(app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ url()->current() . '?lang=en' }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ url()->current() . '?lang=si' }}">Sinhala</a></li>
                        <li><a class="dropdown-item" href="{{ url()->current() . '?lang=ta' }}">Tamil</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>