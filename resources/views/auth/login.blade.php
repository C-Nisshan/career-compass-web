@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="card p-4 shadow-lg mx-auto" style="width: 400px;">
        <h3 class="text-center">Login</h3>
        <form id="login-form" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="login-button">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                Login
            </button>
            <div class="text-center mt-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a> | 
                <a href="{{ route('register') }}">Sign Up</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .toast-container {
        z-index: 1055;
    }
    .toast {
        min-width: 300px;
        max-width: 90vw;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .toast-progress-bar {
        height: 4px;
        background-color: rgba(255, 255, 255, 0.7);
        width: 100%;
        animation: toast-progress 5s linear forwards;
    }
    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0; }
    }
</style>
@endpush

@push('modals')
<!-- Toast Container -->
<div id="toast-container" class="toast-container position-fixed top-50 start-50 translate-middle"></div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgot-password-form">
                    @csrf
                    <div class="mb-3">
                        <label for="forgot-email" class="form-label">Email</label>
                        <input type="email" name="email" id="forgot-email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="forgot-password-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Send OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade" id="otpVerificationModal" tabindex="-1" aria-labelledby="otpVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpVerificationModalLabel">Verify OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="otp-form">
                    @csrf
                    <input type="hidden" name="email" id="otp-email">
                    <div class="mb-3">
                        <label for="otp" class="form-label">OTP</label>
                        <input type="text" name="otp" id="otp" class="form-control" required maxlength="6">
                    </div>
                    <div class="mb-3 text-center">
                        <p id="otp-timer" class="text-muted">Time remaining: <span id="timer-display">2:00</span></p>
                        <button type="button" class="btn btn-link d-none" id="resend-otp-button">Resend OTP</button>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="otp-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Verify OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" media="screen" class="btn-close">Close</button>
            </div>
            <div class="media-body">
                <form id="reset-password-form">
                    @csrf
                    <input type="hidden" name="email" id="reset-email">
                    <input type="hidden" name="otp" id="otp">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="reset-password-button">
                        <span class="spinner-border spinner-border d-none" role="status"></span>
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/auth/login.js') }}"></script>
@endpush('')
