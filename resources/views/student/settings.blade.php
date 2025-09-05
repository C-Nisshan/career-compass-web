@extends('layouts.app')

@section('content')
<div class="student-settings-container">
    <div class="student-settings-content">
        <h1 class="student-settings-title">Settings</h1>
        
        <div id="student-settings-loading" class="student-settings-loading hidden">Loading...</div>
        <div id="student-settings-error" class="student-settings-error hidden"></div>
        
        <div class="student-settings-card">
            <div class="student-settings-header">
                <h2 class="student-settings-subtitle">Account Settings</h2>
                <button id="student-settings-change-password-btn" class="student-settings-action-btn">Change Password</button>
            </div>
            <div class="student-settings-details">
                <p>Manage your account settings, including changing your password.</p>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div id="student-settings-change-password-modal" class="student-settings-modal hidden">
            <div class="student-settings-modal-content">
                <span class="student-settings-modal-close" data-modal="change-password">&times;</span>
                <h2>Change Password</h2>
                <form id="student-settings-change-password-form">
                    @csrf
                    <div class="student-settings-form-group">
                        <label for="current-password">Current Password</label>
                        <input type="password" id="current-password" name="current_password" class="form-control" required>
                    </div>
                    <div class="student-settings-form-group">
                        <label for="new-password">New Password</label>
                        <input type="password" id="new-password" name="new_password" class="form-control" required>
                    </div>
                    <div class="student-settings-form-group">
                        <label for="new-password-confirmation">Confirm New Password</label>
                        <input type="password" id="new-password-confirmation" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="student-settings-submit-btn">Save Changes</button>
                    <button type="button" class="student-settings-cancel-btn" data-modal="change-password">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.student-settings-container {
    min-height: 100vh;
    background: #f9fafb;
    padding: 3rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.student-settings-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.student-settings-title {
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

.student-settings-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.student-settings-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(168, 85, 247, 0.3);
}

.student-settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.student-settings-subtitle {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1f2937;
}

.student-settings-action-btn {
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

.student-settings-action-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.student-settings-loading {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #a855f7;
    border-radius: 10px;
    margin: 1rem 0;
    animation: student-settings-pulse 1.5s infinite ease-in-out;
}

.student-settings-error {
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

.student-settings-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.student-settings-details p {
    font-size: 1rem;
    color: #4b5563;
    margin: 0.5rem 0;
}

.student-settings-modal {
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

.student-settings-modal-content {
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

.student-settings-modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    font-size: 1.8rem;
    color: #6b7280;
    cursor: pointer;
}

.student-settings-form-group {
    margin-bottom: 1.5rem;
}

.student-settings-form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.student-settings-form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1f2937;
    transition: all 0.3s ease;
}

.student-settings-form-group input:focus {
    outline: none;
    border-color: #a855f7;
    box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);
}

.student-settings-submit-btn,
.student-settings-cancel-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.student-settings-submit-btn {
    background: #a855f7;
    color: #ffffff;
}

.student-settings-submit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
}

.student-settings-cancel-btn {
    background: #d1d5db;
    color: #1f2937;
    margin-left: 1rem;
}

.student-settings-cancel-btn:hover {
    background: #b9bfc9;
    transform: scale(1.05);
}

@keyframes student-settings-pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .student-settings-title {
        font-size: 2rem;
    }

    .student-settings-subtitle {
        font-size: 1.4rem;
    }

    .student-settings-action-btn {
        width: 100%;
        max-width: 200px;
    }

    .student-settings-modal-content {
        padding: 1.5rem;
        width: 95%;
    }

    .student-settings-details p {
        font-size: 0.9rem;
    }
}

.hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('student-settings-loading');
    const error = document.getElementById('student-settings-error');
    const changePasswordBtn = document.getElementById('student-settings-change-password-btn');
    const changePasswordModal = document.getElementById('student-settings-change-password-modal');
    const changePasswordForm = document.getElementById('student-settings-change-password-form');

    function openChangePasswordModal() {
        changePasswordModal.style.display = 'flex';
        changePasswordModal.classList.remove('hidden');
        changePasswordModal.scrollIntoView({ behavior: 'smooth', block: 'center' });
        changePasswordForm.reset(); // Clear form fields
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(changePasswordForm);
        const submitBtn = changePasswordForm.querySelector('.student-settings-submit-btn');
        submitBtn.disabled = true;
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/student/settings/change-password', {
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

    document.querySelector('.student-settings-modal-close').addEventListener('click', () => {
        changePasswordModal.classList.add('hidden');
        changePasswordModal.style.display = 'none';
        changePasswordForm.reset();
    });

    changePasswordForm.addEventListener('submit', handleFormSubmit);
});
</script>
@endsection