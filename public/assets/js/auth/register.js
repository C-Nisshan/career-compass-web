(function() {
    const signUpForm = document.getElementById('sign-up-form');
    const signUpMessages = document.getElementById('sign-up-messages');
    const signUpButton = document.getElementById('sign-up-button');
    const roleSelect = document.getElementById('role');
    const studentFields = document.querySelectorAll('.provider-fields.student-fields');
    const mentorFields = document.querySelectorAll('.provider-fields.mentor-fields');
    const toastContainer = document.getElementById('register-toast-container');

    // Define valid roles (matching RoleEnum values)
    const validRoles = ['student', 'mentor'];

    if (!signUpForm || !signUpMessages || !signUpButton || !roleSelect || !toastContainer) {
        console.error('Required elements not found:', {
            signUpForm: !!signUpForm,
            signUpMessages: !!signUpMessages,
            signUpButton: !!signUpButton,
            roleSelect: !!roleSelect,
            toastContainer: !!toastContainer
        });
        return;
    }

    if (studentFields.length === 0 || mentorFields.length === 0) {
        console.error('Role-specific fields not found:', {
            studentFields: studentFields.length,
            mentorFields: mentorFields.length
        });
    }

    // Function to toggle role-specific fields
    function toggleRoleFields() {
        const role = roleSelect.value;
        console.log('Toggling fields for role:', role);
        studentFields.forEach(field => {
            field.classList.toggle('d-none', role !== 'student');
        });
        mentorFields.forEach(field => {
            field.classList.toggle('d-none', role !== 'mentor');
        });
    }

    // Initial toggle on page load
    toggleRoleFields();

    // Toggle fields on role change
    roleSelect.addEventListener('change', toggleRoleFields);

    signUpForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        toggleLoading(signUpButton, true);

        signUpMessages.innerText = '';
        signUpMessages.classList.remove('text-success', 'text-danger');

        const form = event.target;
        const formData = new FormData(form);
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const role = formData.get('role');

        // Validate role
        if (!validRoles.includes(role)) {
            showToast('Please select a valid role.', 'danger');
            toggleLoading(signUpButton, false);
            return;
        }

        // Determine API endpoint based on role
        let registerUrl;
        if (role === 'student') {
            registerUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.registerStudent;
        } else if (role === 'mentor') {
            registerUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.registerMentor;
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
                showToast(result.message || 'Registration successful!', 'success');
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
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select, textarea');
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
        toast.className = `register-toast alert alert-${type}`;
        toast.style.zIndex = 1055;
        toast.innerHTML = `
            <div class="toast-body">${message}</div>
            <div class="register-toast-progress-bar"></div>
        `;
        toastContainer.appendChild(toast);

        // Trigger reflow to enable animation
        toast.offsetHeight;

        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
})();