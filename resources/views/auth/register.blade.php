@extends('layouts.app')

@section('content')
<div class="register-container min-vh-100 d-flex align-items-center justify-content-center" style="background-color: #f0f2f5;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 500px;">
        <h3 class="text-center mb-4">{{ __('auth.sign_up') }}</h3>

        <!-- Progress Bar -->
        <div class="progress mb-3">
            <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                25% Complete
            </div>
        </div>

        <!-- Error Message Box -->
        <div id="sign-up-messages" class="alert alert-danger d-none text-center fw-bold mb-3"></div>

        <form id="sign-up-form" action="javascript:void(0)">
            @csrf

            <!-- Role Selector -->
            <div class="mb-3">
                <label for="role" class="form-label">{{ __('auth.role') }}</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">{{ __('auth.select_role') }}</option>
                    <option value="student">{{ __('auth.student') }}</option>
                    <option value="mentor">{{ __('auth.mentor') }}</option>
                </select>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('auth.email') }}</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.password') }}</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>

            <!-- Note (dynamic role hint) -->
            <div class="mb-3 role-note d-none text-muted small"></div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100" id="sign-up-button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                {{ __('auth.sign_up') }}
            </button>

            <!-- Or divider -->
            <div class="text-center my-3 text-muted">— {{ __('auth.or') }} —</div>

            <!-- Google Sign-up -->
            <div class="text-center">
                <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100">
                    {{ __('auth.sign_up_google') }}
                </a>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-3">
                <a href="{{ route('login') }}">{{ __('auth.already_have_account') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Role-based note display
        document.addEventListener('DOMContentLoaded', () => {
            const roleSelect = document.getElementById('role');
            const noteBox = document.querySelector('.role-note');

            const notes = {
                student: `{{ __('auth.student_note') }}`,
                mentor: `{{ __('auth.mentor_note') }}`
            };

            roleSelect.addEventListener('change', function () {
                const selected = this.value;
                if (notes[selected]) {
                    noteBox.classList.remove('d-none');
                    noteBox.textContent = notes[selected];
                } else {
                    noteBox.classList.add('d-none');
                    noteBox.textContent = '';
                }
            });
        });
    </script>

    <script src="{{ asset('config.js') }}"></script>
    <script src="{{ asset('assets/js/auth/register.js') }}"></script>
@endpush
