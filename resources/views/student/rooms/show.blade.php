@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $room->title }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Description:</strong> {{ $room->description }}</p>
                <p><strong>Price:</strong> ${{ $room->price }}</p>
                <!-- Add booking button or form here -->
                <a href="{{ route('rooms.search') }}" class="btn btn-secondary">Back to Search</a>
            </div>
        </div>
    </div>
@endsection