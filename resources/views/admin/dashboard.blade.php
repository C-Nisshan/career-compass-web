@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div id="logout-message" class="text-danger mt-2"></div>
    </div>
@endsection

@push('scripts')
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.logoutUrl = '{{ route('api.logout') }}';
    </script>
    <script src="{{ asset('assets/js/auth/logout.js') }}"></script>
@endpush