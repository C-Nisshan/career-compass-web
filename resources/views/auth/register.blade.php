@extends('layouts.app')

@section('content')
<div class="register-container" style="background-color: black;">
    <div class="card p-4 shadow-lg mx-auto" style="width: 400px;">
        <h3 class="text-center">{{ __('auth.sign_up') }}</h3>
        <div class="progress mb-3">
            <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25% Complete</div>
        </div>
        <div id="sign-up-messages" class="text-danger text-center fw-bold mb-3"></div>

        <form id="sign-up-form" method="POST">
            @csrf
            <div class="mb-3">
                <label for="role" class="form-label">{{ __('auth.role') }}</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="">{{ __('auth.select_role') }}</option>
                    <option value="booker">{{ __('auth.student_booker') }}</option>
                    <option value="provider">{{ __('auth.room_provider') }}</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('auth.email') }}</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.password') }}</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3 provider-fields d-none">
                <p class="text-muted">{{ __('auth.provider_note') }}</p>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="sign-up-button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                {{ __('auth.sign_up') }}
            </button>
            <div class="text-center mt-3">
                <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100">
                    {{ __('auth.sign_up_google') }}
                </a>
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('login') }}">{{ __('auth.already_have_account') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('config.js') }}"></script>
    <script src="{{ asset('assets/js/auth/register.js') }}"></script>
@endpush