@extends('layouts.admin.master')

@section('title', 'Refunds')

@section('content')
    <div class="container mt-4">
        <h1>Refunds</h1>
        <p>List of refunds will be displayed here.</p>
        @foreach ($refunds as $refund)
            <div>Refund #{{ $refund->id }} - {{ $refund->status }}</div>
        @endforeach
        {{ $refunds->links() }}
    </div>
@endsection