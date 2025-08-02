<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CareerCompass') }}</title>
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/forum.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mentor-approvals.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mentor-pending.css') }}">

    @stack('styles')
</head>
<body>
    @include('partials.navbar')

    <div class="main-wrapper">
        @if(auth()->check() && (Route::is('admin.*') || Route::is('student.*') || (Route::is('mentor.*') && auth()->user()->status === 'approved')))
            @include('partials.sidebar')
        @endif

        <div class="content-container">
            @yield('content')
        </div>
    </div>

    @include('partials.footer')

    @stack('modals')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/auth/logout.js') }}"></script>
    <script src="{{ asset('assets/js/global.js') }}"></script>
    <script src="{{ asset('assets/js/header.js') }}"></script>
    <script src="{{ asset('assets/js/home.js') }}"></script>
    <script src="{{ asset('assets/js/footer.js') }}"></script>
    @stack('scripts')
</body>
</html>