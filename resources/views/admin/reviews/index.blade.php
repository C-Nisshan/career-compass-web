@extends('layouts.admin.master')

@section('title', 'Reviews')

@section('content')
    <div class="container mt-4">
        <h1>Reviews</h1>
        <p>List of reviews will be displayed here.</p>
        @foreach ($reviews as $review)
            <div>Rating: {{ $review->rating }} - {{ $review->comment }}</div>
        @endforeach
        {{ $reviews->links() }}
    </div>
@endsection