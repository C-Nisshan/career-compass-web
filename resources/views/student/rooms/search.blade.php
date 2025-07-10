@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Search Rooms</h1>
        <form method="GET" action="{{ route('rooms.search') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="Search by title or description" value="{{ request('keyword') }}">
                </div>
                <div class="col-md-3">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}">
                </div>
                <div class="col-md-3">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>

        @if($rooms->isEmpty())
            <p>No rooms found.</p>
        @else
            <div class="row">
                @foreach($rooms as $room)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $room->title }}</h5>
                                <p class="card-text">{{ Str::limit($room->description, 100) }}</p>
                                <p class="card-text"><strong>Price:</strong> ${{ $room->price }}</p>
                                <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $rooms->links() }}
        @endif
    </div>
@endsection