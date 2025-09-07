(function() {
    // Login Form
    const loginForm = document.getElementById('login-form');
    const loginButton = document.getElementById('login-button');

    if (loginForm && loginButton) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            toggleLoading(loginButton, true);

            const form = event.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const loginUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.login;

            try {
                console.log('Submitting login form to:', loginUrl);
                const response = await fetch(loginUrl, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('Login response:', { status: response.status, result });

                if (response.status === 200 && result.redirect) {
                    console.log('Redirecting to:', result.redirect);
                    window.location.href = result.redirect;
                } else if (response.status === 422 || response.status === 401) {
                    const errors = result.errors || {};
                    if (errors.email) showToast(errors.email[0], 'danger');
                    if (errors.password) showToast(errors.password[0], 'danger');
                    if (result.error) showToast(result.error, 'danger');
                    clearFormInputs(form, ['_token']);
                } else {
                    showToast('Something went wrong. Please try again.', 'danger');
                    clearFormInputs(form, ['_token']);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
                clearFormInputs(form, ['_token']);
            } finally {
                toggleLoading(loginButton, false);
            }
        });
    }

    // Forgot Password Form
    const forgotPasswordForm = document.getElementById('login-forgot-password-form');
    const forgotPasswordButton = document.getElementById('login-forgot-password-button');
    let otpTimeout = null;

    if (forgotPasswordForm && forgotPasswordButton) {
        forgotPasswordForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            toggleLoading(forgotPasswordButton, true);

            const form = event.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const forgotPasswordUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.forgotPassword;

            try {
                console.log('Sending forgot password request to:', forgotPasswordUrl);
                const response = await fetch(forgotPasswordUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('Forgot password response:', result);
                if (response.ok) {
                    showToast(result.message, 'success');
                    document.getElementById('login-otp-email').value = formData.get('email');
                    startOtpTimer();
                    bootstrap.Modal.getInstance(document.getElementById('login-forgot-password-modal')).hide();
                    new bootstrap.Modal(document.getElementById('login-otp-verification-modal')).show();
                } else {
                    showToast(result.errors?.email?.[0] || result.message || 'Failed to send OTP.', 'danger');
                    clearFormInputs(form, ['_token']);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
                clearFormInputs(form, ['_token']);
            } finally {
                toggleLoading(forgotPasswordButton, false);
            }
        });
    }

    // OTP Verification Form
    const otpForm = document.getElementById('login-otp-form');
    const otpButton = document.getElementById('login-otp-button');
    const resendOtpButton = document.getElementById('login-resend-otp-button');

    if (otpForm && otpButton && resendOtpButton) {
        otpForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            toggleLoading(otpButton, true);

            const form = event.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const verifyOtpUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.verifyOtp;

            try {
                console.log('Sending OTP verification to:', verifyOtpUrl);
                const response = await fetch(verifyOtpUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('OTP verification response:', result);
                if (response.ok) {
                    showToast(result.message, 'success');
                    document.getElementById('login-reset-email').value = formData.get('email');
                    document.getElementById('login-reset-otp').value = formData.get('otp');
                    clearTimeout(otpTimeout);
                    bootstrap.Modal.getInstance(document.getElementById('login-otp-verification-modal')).hide();
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('login-reset-password-modal')).show();
                } else {
                    showToast(result.errors?.otp?.[0] || result.message || 'Failed to verify OTP.', 'danger');
                    clearFormInputs(form, ['_token', 'email']);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
                clearFormInputs(form, ['_token', 'email']);
            } finally {
                toggleLoading(otpButton, false);
            }
        });

        resendOtpButton.addEventListener('click', async () => {
            toggleLoading(resendOtpButton, true);

            const email = document.getElementById('login-otp-email').value;
            const formData = new FormData();
            formData.append('email', email);
            const csrfToken = otpForm.querySelector('input[name="_token"]').value;
            const forgotPasswordUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.forgotPassword;

            try {
                console.log('Resending OTP to:', forgotPasswordUrl);
                const response = await fetch(forgotPasswordUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('Resend OTP response:', result);
                if (response.ok) {
                    showToast(result.message, 'success');
                    startOtpTimer();
                } else {
                    showToast(result.errors?.email?.[0] || result.message || 'Failed to resend OTP.', 'danger');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
            } finally {
                toggleLoading(resendOtpButton, false);
            }
        });
    }

    // Reset Password Form
    const resetPasswordForm = document.getElementById('login-reset-password-form');
    const resetPasswordButton = document.getElementById('login-reset-password-button');

    if (resetPasswordForm && resetPasswordButton) {
        resetPasswordForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            toggleLoading(resetPasswordButton, true);

            const form = event.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const resetPasswordUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.resetPassword;

            try {
                console.log('Sending reset password request to:', resetPasswordUrl);
                const response = await fetch(resetPasswordUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('Reset password response:', result);
                if (response.ok) {
                    showToast(result.message, 'success');
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('login-reset-password-modal')).hide();
                        window.location.href = result.redirect;
                    }, 2000);
                } else {
                    showToast(result.errors?.password?.[0] || result.errors?.otp?.[0] || result.message || 'Failed to reset password.', 'danger');
                    clearFormInputs(form, ['_token', 'email', 'otp']);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
                clearFormInputs(form, ['_token', 'email', 'otp']);
            } finally {
                toggleLoading(resetPasswordButton, false);
            }
        });
    }

    // Show toast notification with progress bar
    function showToast(message, type = 'danger') {
        const toastContainer = document.getElementById('login-toast-container');
        if (!toastContainer) return;

        const toastId = 'login-toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="login-toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="login-toast-progress-bar"></div>
            </div>
        `;
        toastContainer.innerHTML += toastHtml;

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
        toast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // Toggle loading state
    function toggleLoading(button, isLoading) {
        const spinner = button.querySelector('.spinner-border');
        if (isLoading) {
            button.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            if (!button.dataset.originalText) {
                button.dataset.originalText = button.innerText.trim();
            }
            button.innerText = 'Loading...';
        } else {
            button.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            button.innerText = button.dataset.originalText || 
                (button.id === 'login-button' ? 'Login' : 
                 button.id === 'login-forgot-password-button' ? 'Send OTP' : 
                 button.id === 'login-otp-button' ? 'Verify OTP' : 
                 button.id === 'login-resend-otp-button' ? 'Resend OTP' : 'Reset Password');
        }
    }

    // Start OTP timer
    function startOtpTimer() {
        const timerDisplay = document.getElementById('login-timer-display');
        const resendOtpButton = document.getElementById('login-resend-otp-button');
        let timeLeft = 120; // 2 minutes in seconds

        clearTimeout(otpTimeout);
        resendOtpButton.classList.add('d-none');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                timerDisplay.innerText = 'Expired';
                resendOtpButton.classList.remove('d-none');
                clearTimeout(otpTimeout);
            } else {
                timeLeft--;
                otpTimeout = setTimeout(updateTimer, 1000);
            }
        }

        updateTimer();
    }

    // Clear form inputs except excluded fields
    function clearFormInputs(form, excludeFields = []) {
        const inputs = form.querySelectorAll('input:not([type="hidden"])');
        inputs.forEach(input => {
            if (!excludeFields.includes(input.name)) {
                input.value = '';
            }
        });
    }
})();