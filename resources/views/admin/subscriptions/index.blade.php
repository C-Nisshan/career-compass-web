@extends('layouts.admin.master')

@section('title', 'Subscriptions')

@section('content')
    <div class="container mt-4">
        <h1>Subscriptions</h1>
        <p>List of subscriptions will be displayed here.</p>
        @foreach ($subscriptions as $subscription)
            <div>Subscription #{{ $subscription->id }} - {{ $subscription->status }}</div>
        @endforeach
        {{ $subscriptions->links() }}
    </div>
@endsection