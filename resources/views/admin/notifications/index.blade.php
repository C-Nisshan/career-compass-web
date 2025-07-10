@extends('layouts.admin.master')

@section('title', 'Notifications')

@section('content')
    <div class="container mt-4">
        <h1>Notifications</h1>
        <p>List of notifications will be displayed here.</p>
        @foreach ($notifications as $notification)
            <div>{{ $notification->type }} - {{ $notification->message }}</div>
        @endforeach
        {{ $notifications->links() }}
    </div>
@endsection