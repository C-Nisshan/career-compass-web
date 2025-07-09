@extends('layouts.admin.master')

@section('title', 'Audit Logs')

@section('content')
    <div class="container mt-4">
        <h1>Audit Logs</h1>
        <p>List of audit logs will be displayed here.</p>
        @foreach ($auditLogs as $log)
            <div>{{ $log->action }} - {{ $log->created_at }}</div>
        @endforeach
        {{ $auditLogs->links() }}
    </div>
@endsection