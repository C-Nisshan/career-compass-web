@extends('layouts.app')

@section('content')
<div class="mentor-settings-container">
    <div class="mentor-settings-content">
        <h1 class="mentor-settings-title">Settings</h1>
        
        <div id="mentor-settings-loading" class="mentor-settings-loading hidden">Loading...</div>
        <div id="mentor-settings-error" class="mentor-settings-error hidden"></div>
        
        <div class="mentor-settings-card">
            <div class="mentor-settings-header">
                <h2 class="mentor-settings-subtitle">Account Settings</h2>
                <button id="mentor-settings-change-password-btn" class="mentor-settings-action-btn">Change Password</button>
            </div>
            <div class="mentor-settings-details">
                <p>Manage your account settings, including changing your password.</p>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div id="mentor-settings-change-password-modal" class="mentor-settings-modal hidden">
            <div class="mentor-settings-modal-content">
                <span class="mentor-settings-modal-close" data-modal="change-password">&times;</span>
                <h2>Change Password</h2>
                <form id="mentor-settings-change-password-form">
                    @csrf
                    <div class="mentor-settings-form-group">
                        <label for="current-password">Current Password</label>
                        <input type="password" id="current-password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mentor-settings-form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mentor-settings-form-group">
                        <label for="new-password-confirmation">Confirm New Password</label>
                        <input type="password" id="new-password-confirmation" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="mentor-settings-submit-btn">Save Changes</button>
                    <button type="button" class="mentor-settings-cancel-btn" data-modal="change-password">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.mentor-settings-container {
    min-height: 100vh;
    background: #f9fafb;
    padding: 3rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.mentor-settings-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.mentor-settings-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1f2937;
    margin-bottom: 2rem;
    background: linear-gradient(135deg, #ffffff 0%, #a855f7 100%);
    -webkit-background-clip: text;
    background-clip: text;
    letter-spacing: -0.02em;
    text-align: center;
}

.mentor-settings-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.mentor-settings-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(168, 85, 247, 0.3);
}

.mentor-settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.mentor-settings-subtitle {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1f2937;
}

.mentor-settings-action-btn {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    border: none;
    background: #a855f7;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mentor-settings-action-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.mentor-settings-loading {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #a855f7;
    border-radius: 10px;
    margin: 1rem 0;
    animation: mentor-settings-pulse 1.5s infinite ease-in-out;
}

.mentor-settings-error {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #dc2626;
    border-radius: 10px;
    margin: 1rem 0;
    border: 1px solid #dc2626;
}

.mentor-settings-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.mentor-settings-details p {
    font-size: 1rem;
    color: #4b5563;
    margin: 0.5rem 0;
}

.mentor-settings-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(31, 41, 55, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    overflow-y: auto;
}

.mentor-settings-modal-content {
    background: #ffffff;
    padding: 2rem;
    border-radius: 20px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
}

.mentor-settings-modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    font-size: 1.8rem;
    color: #6b7280;
    cursor: pointer;
}

.mentor-settings-form-group {
    margin-bottom: 1.5rem;
}

.mentor-settings-form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.mentor-settings-form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1f2937;
    transition: all 0.3s ease;
}

.mentor-settings-form-group input:focus {
    outline: none;
    border-color: #a855f7;
    box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);
}

.mentor-settings-submit-btn,
.mentor-settings-cancel-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mentor-settings-submit-btn {
    background: #a855f7;
    color: #ffffff;
}

.mentor-settings-submit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
}

.mentor-settings-cancel-btn {
    background: #d1d5db;
    color: #1f2937;
    margin-left: 1rem;
}

.mentor-settings-cancel-btn:hover {
    background: #b9bfc9;
    transform: scale(1.05);
}

@keyframes mentor-settings-pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .mentor-settings-title {
        font-size: 2rem;
    }

    .mentor-settings-subtitle {
        font-size: 1.4rem;
    }

    .mentor-settings-action-btn {
        width: 100%;
        max-width: 200px;
    }

    .mentor-settings-modal-content {
        padding: 1.5rem;
        width: 95%;
    }

    .mentor-settings-details p {
        font-size: 0.9rem;
    }
}

.hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('mentor-settings-loading');
    const error = document.getElementById('mentor-settings-error');
    const changePasswordBtn = document.getElementById('mentor-settings-change-password-btn');
    const changePasswordModal = document.getElementById('mentor-settings-change-password-modal');
    const changePasswordForm = document.getElementById('mentor-settings-change-password-form');

    function openChangePasswordModal() {
        changePasswordModal.style.display = 'flex';
        changePasswordModal.classList.remove('hidden');
        changePasswordModal.scrollIntoView({ behavior: 'smooth', block: 'center' });
        changePasswordForm.reset(); // Clear form fields
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(changePasswordForm);
        const submitBtn = changePasswordForm.querySelector('.mentor-settings-submit-btn');
        submitBtn.disabled = true;
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/mentor/settings/change-password', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData,
                credentials: 'include'
            });

            const result = await response.json();
            if (response.ok && result.success) {
                changePasswordModal.classList.add('hidden');
                changePasswordModal.style.display = 'none';
                changePasswordForm.reset();
                alert(result.message);
            } else {
                error.textContent = result.message || 'Failed to change password.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Form error:', err);
            error.textContent = 'Unable to change password. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    }

    // Event Listeners
    changePasswordBtn.addEventListener('click', openChangePasswordModal);

    document.querySelector('.mentor-settings-modal-close').addEventListener('click', () => {
        changePasswordModal.classList.add('hidden');
        changePasswordModal.style.display = 'none';
        changePasswordForm.reset();
    });

    changePasswordForm.addEventListener('submit', handleFormSubmit);
});
</script>
@endsection
