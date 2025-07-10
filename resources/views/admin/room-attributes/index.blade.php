@extends('layouts.admin.master')

@section('title', 'Room Attributes')

@section('content')
    <div class="container mt-4">
        <h1>Room Attributes</h1>
        <p>List of room attributes will be displayed here.</p>
        @foreach ($roomAttributes as $attribute)
            <div>{{ $attribute->attribute_type }}: {{ $attribute->attribute_value }}</div>
        @endforeach
        {{ $roomAttributes->links() }}
    </div>
@endsection