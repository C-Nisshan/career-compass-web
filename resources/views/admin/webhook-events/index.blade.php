@extends('layouts.admin.master')

@section('title', 'Webhook Events')

@section('content')
    <div class="container mt-4">
        <h1>Webhook Events</h1>
        <p>List of webhook events will be displayed here.</p>
        @foreach ($webhookEvents as $event)
            <div>{{ $event->event_type }} - {{ $event->status }}</div>
        @endforeach
        {{ $webhookEvents->links() }}
    </div>
@endsection