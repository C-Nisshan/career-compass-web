@extends('layouts.admin.master')

@section('title', 'Failed Logins')

@section('content')
    <div class="container mt-4">
        <h1>Failed Logins</h1>
        <p>List of failed login attempts will be displayed here.</p>
        @foreach ($failedLogins as $login)
            <div>{{ $login->email }} - {{ $login->attempted_at }}</div>
        @endforeach
        {{ $failedLogins->links() }}
    </div>
@endsection