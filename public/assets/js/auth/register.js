(function() {
    const signUpForm = document.getElementById('sign-up-form');
    const signUpMessages = document.getElementById('sign-up-messages');
    const signUpButton = document.getElementById('sign-up-button');
    const roleSelect = document.getElementById('role');
    const providerFields = document.querySelectorAll('.provider-fields');

    if (signUpForm && signUpMessages && signUpButton && roleSelect && providerFields) {
        // Show/hide mentor-specific fields based on role
        roleSelect.addEventListener('change', () => {
            const isMentor = roleSelect.value === 'mentor';
            providerFields.forEach(field => field.classList.toggle('d-none', !isMentor));
        });

        signUpForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            toggleLoading(signUpButton, true);

            signUpMessages.innerText = '';
            signUpMessages.classList.remove('text-success', 'text-danger');

            const form = event.target;
            const formData = new FormData(form);
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const role = formData.get('role');

            // Determine API endpoint based on role
            let registerUrl;
            if (role === 'student') {
                registerUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.registerStudent;
            } else if (role === 'mentor') {
                registerUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.registerMentor;
            } else {
                showToast('Please select a valid role.', 'danger');
                toggleLoading(signUpButton, false);
                return;
            }

            try {
                console.log('Submitting sign-up form to:', registerUrl);
                const response = await fetch(registerUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const result = await response.json();
                console.log('Sign-up response:', { status: response.status, result });

                if (response.status === 201) {
                    signUpMessages.classList.add('text-success');
                    signUpMessages.innerText = result.message || 'Registration successful!';
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 2000);
                    }
                } else if (response.status === 422 || response.status === 401) {
                    signUpMessages.classList.add('text-danger');
                    const errors = result.errors || {};
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            showToast(errors[key][0], 'danger');
                        }
                    }
                    if (result.message) showToast(result.message, 'danger');
                    clearFormInputs(form, ['_token', 'role']);
                } else {
                    showToast('Something went wrong. Please try again.', 'danger');
                    clearFormInputs(form, ['_token', 'role']);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                showToast('Network or server error.', 'danger');
                clearFormInputs(form, ['_token', 'role']);
            } finally {
                toggleLoading(signUpButton, false);
            }
        });
    }

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
            button.innerText = button.dataset.originalText || 'Sign Up';
        }
    }

    function clearFormInputs(form, excludeFields = []) {
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');
        inputs.forEach(input => {
            if (!excludeFields.includes(input.name)) {
                if (input.type === 'file') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = '';
                }
            }
        });
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = 1000;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
})();