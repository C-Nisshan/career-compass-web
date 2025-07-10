@extends('layouts.admin.master')

@section('title', 'Payment Methods')

@section('content')
    <div class="container mt-4">
        <h1>Payment Methods</h1>
        <p>List of payment methods will be displayed here.</p>
        @foreach ($paymentMethods as $method)
            <div>{{ $method->method_type }} - {{ $method->verified_status }}</div>
        @endforeach
        {{ $paymentMethods->links() }}
    </div>
@endsection