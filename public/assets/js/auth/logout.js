(function() {
    const logoutForm = document.getElementById('logout-form');
    const messageElement = document.getElementById('logout-message');

    if (logoutForm) {
        logoutForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            
            if (messageElement) {
                messageElement.innerText = '';
            }

            const form = event.target;
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const logoutUrl = window.apiConfig.baseUrl + window.apiConfig.endpoints.logout;

            try {
                console.log('Sending logout request to:', logoutUrl);
                const response = await fetch(logoutUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include',
                });

                const result = await response.json();
                console.log('Logout response:', result);

                if (response.ok && result.redirect) {
                    console.log('Redirecting to:', result.redirect);
                    window.location.href = result.redirect;
                } else {
                    if (messageElement) {
                        messageElement.innerText = result.error || 'Logout failed. Please try again.';
                    }
                }
            } catch (error) {
                console.error('Logout error:', error);
                if (messageElement) {
                    messageElement.innerText = 'Network error during logout.';
                }
            }
        });
    }
})();