@extends('layouts.admin.master')

@section('title', 'Plans')

@section('content')
    <div class="container mt-4">
        <h1>Plans</h1>
        <p>List of plans will be displayed here.</p>
        @foreach ($plans as $plan)
            <div>{{ $plan->name }} - {{ $plan->price }} {{ $plan->currency }}</div>
        @endforeach
        {{ $plans->links() }}
    </div>
@endsection