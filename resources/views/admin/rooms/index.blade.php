@extends('layouts.admin.master')

@section('title', 'Rooms')

@section('content')
    <div class="container mt-4">
        <h1>Rooms</h1>
        <p>List of rooms will be displayed here.</p>
        @foreach ($rooms as $room)
            <div>{{ $room->name }} - {{ $room->status }}</div>
        @endforeach
        {{ $rooms->links() }}
    </div>
@endsection