@extends('layouts.admin.master')

@section('title', 'Room Media')

@section('content')
    <div class="container mt-4">
        <h1>Room Media</h1>
        <p>List of room media will be displayed here.</p>
        @foreach ($roomMedia as $media)
            <div>{{ $media->media_type }} - {{ $media->file_path }}</div>
        @endforeach
        {{ $roomMedia->links() }}
    </div>
@endsection