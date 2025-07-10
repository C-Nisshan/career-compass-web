@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-4 shadow-lg">
        <h3 class="text-center mb-4">My Profile</h3>
        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ $profile->profile_picture_path ? asset('storage/' . $profile->profile_picture_path) : asset('assets/images/default-avatar.png') }}" alt="Profile Picture" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5>{{ $profile->first_name }} {{ $profile->last_name }}</h5>
                <p class="text-muted">{{ $user->role->value }}</p>
            </div>
            <div class="col-md-8">
                <dl class="row">
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4">First Name</dt>
                    <dd class="col-sm-8">{{ $profile->first_name ?? 'Not provided' }}</dd>
                    <dt class="col-sm-4">Last Name</dt>
                    <dd class="col-sm-8">{{ $profile->last_name ?? 'Not provided' }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $profile->phone ?? 'Not provided' }}</dd>
                    <dt class="col-sm-4">Address</dt>
                    <dd class="col-sm-8">{{ $profile->address ?? 'Not provided' }}</dd>
                    <dt class="col-sm-4">NIC Number</dt>
                    <dd class="col-sm-8">{{ $profile->nic_number ?? 'Not provided' }}</dd>
                    @if($user->role->value === 'provider')
                        <dt class="col-sm-4">Verified Status</dt>
                        <dd class="col-sm-8">{{ $profile->verified_status }}</dd>
                        <dt class="col-sm-4">Documents</dt>
                        <dd class="col-sm-8">
                            @if($documents->isEmpty())
                                No documents uploaded.
                            @else
                                <ul>
                                    @foreach($documents as $document)
                                        <li>{{ $document->document_type }}: {{ $document->verified_status }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </dd>
                    @endif
                </dl>
                <div class="text-end">
                    <a href="{{ route('profile.edit', ['lang' => app()->getLocale()]) }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection