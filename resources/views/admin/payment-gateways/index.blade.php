@extends('layouts.admin.master')

@section('title', 'Payment Gateways')

@section('content')
    <div class="container mt-4">
        <h1>Payment Gateways</h1>
        <p>List of payment gateways will be displayed here.</p>
        @foreach ($paymentGateways as $gateway)
            <div>{{ $gateway->name }} - {{ $gateway->provider }}</div>
        @endforeach
        {{ $paymentGateways->links() }}
    </div>
@endsection