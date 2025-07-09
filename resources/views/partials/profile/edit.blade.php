@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-4 shadow-lg">
        <h3 class="text-center mb-4">Edit Profile</h3>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('profile.update', ['lang' => app()->getLocale()]) }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $profile->first_name) }}">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $profile->last_name) }}">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $profile->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $profile->address) }}">
                    </div>
                    <div class="mb-3">
                        <label for="nic_number" class="form-label">NIC Number</label>
                        <input type="text" name="nic_number" id="nic_number" class="form-control" value="{{ old('nic_number', $profile->nic_number) }}">
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                    </div>
                </div>
                @if($user->role->value === 'provider')
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nic_file" class="form-label">NIC Document (PDF)</label>
                            <input type="file" name="nic_file" id="nic_file" class="form-control" accept="application/pdf">
                        </div>
                        <div class="mb-3">
                            <label for="land_deed_file" class="form-label">Land Deed Document (PDF)</label>
                            <input type="file" name="land_deed_file" id="land_deed_file" class="form-control" accept="application/pdf">
                        </div>
                        <p class="text-muted">Documents will be submitted for admin approval.</p>
                    </div>
                @endif
            </div>
            <div class="text-end">
                <a href="{{ route('profile.index', ['lang' => app()->getLocale()]) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection