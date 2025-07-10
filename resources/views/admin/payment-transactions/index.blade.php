@extends('layouts.admin.master')

@section('title', 'Payment Transactions')

@section('content')
    <div class="container mt-4">
        <h1>Payment Transactions</h1>
        <p>List of payment transactions will be displayed here.</p>
        @foreach ($paymentTransactions as $transaction)
            <div>Transaction #{{ $transaction->id }} - {{ $transaction->status }}</div>
        @endforeach
        {{ $paymentTransactions->links() }}
    </div>
@endsection