@extends('layouts.admin.master')

@section('title', 'Profiles')

@section('content')
    <div class="container mt-4">
        <h1>Profiles</h1>
        <p>List of profiles will be displayed here.</p>
        @foreach ($profiles as $profile)
            <div>{{ $profile->first_name }} {{ $profile->last_name }}</div>
        @endforeach
        {{ $profiles->links() }}
    </div>
@endsection