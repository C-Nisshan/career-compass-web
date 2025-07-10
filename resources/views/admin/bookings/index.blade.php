@extends('layouts.admin.master')

@section('title', 'Bookings')

@section('content')
    <div class="container mt-4">
        <h1>Bookings</h1>
        <p>List of bookings will be displayed here.</p>
        @foreach ($bookings as $booking)
            <div>Booking #{{ $booking->id }} - {{ $booking->status }}</div>
        @endforeach
        {{ $bookings->links() }}
    </div>
@endsection