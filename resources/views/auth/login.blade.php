@extends('layouts.app')

@section('content')
<div class="login-main-wrapper">
    <div class="login-animated-bg"></div>
    <div class="login-particles" id="login-particles">
        <div class="login-particle"></div>
        <div class="login-particle"></div>
        <div class="login-particle"></div>
        <div class="login-particle"></div>
    </div>

    <div class="login-container mt-4 mb-4">
        <div class="login-card">
            <h3>Login</h3>
            <form id="login-form" method="POST" class="login-form">
                @csrf
                <div class="mb-3">
                    <label for="login-email" class="form-label">Email</label>
                    <input type="email" name="email" id="login-email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" name="password" id="login-password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" id="login-button">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Login
                </button>
                <div class="text-center mt-3">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#login-forgot-password-modal">Forgot Password?</a>
                    <a href="{{ route('register') }}">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="login-toast-container" class="login-toast-container"></div>
</div>
@endsection

@push('modals')
<!-- Forgot Password Modal -->
<div class="modal fade login-modal" id="login-forgot-password-modal" tabindex="-1" aria-labelledby="login-forgot-password-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="login-forgot-password-modal-label">Forgot Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="login-forgot-password-form" class="login-forgot-password-form">
                    @csrf
                    <div class="mb-3">
                        <label for="login-forgot-email" class="form-label">Email</label>
                        <input type="email" name="email" id="login-forgot-email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="login-forgot-password-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Send OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade login-modal" id="login-otp-verification-modal" tabindex="-1" aria-labelledby="login-otp-verification-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="login-otp-verification-modal-label">Verify OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="login-otp-form" class="login-otp-form">
                    @csrf
                    <input type="hidden" name="email" id="login-otp-email">
                    <div class="mb-3">
                        <label for="login-otp" class="form-label">OTP</label>
                        <input type="text" name="otp" id="login-otp" class="form-control" required maxlength="6">
                    </div>
                    <div class="mb-3 text-center">
                        <p id="login-otp-timer" class="text-muted">Time remaining: <span id="login-timer-display">2:00</span></p>
                        <button type="button" class="btn btn-link d-none" id="login-resend-otp-button">Resend OTP</button>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="login-otp-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Verify OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade login-modal" id="login-reset-password-modal" tabindex="-1" aria-labelledby="login-reset-password-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="login-reset-password-modal-label">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="login-reset-password-form" class="login-reset-password-form">
                    @csrf
                    <input type="hidden" name="email" id="login-reset-email">
                    <input type="hidden" name="otp" id="login-reset-otp">
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <input type="password" name="password" id="login-password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="login-password-confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="login-password-confirmation" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="login-reset-password-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
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
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        particlesJS('login-particles', {
            particles: {
                number: { value: 4 },
                color: { value: 'rgba(168, 85, 247, 0.6)' },
                shape: { type: 'circle' },
                size: { value: 6 },
                move: { enable: true, speed: 2, direction: 'top', random: false, straight: true }
            }
        });
    </script>
@endpush