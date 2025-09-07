@extends('layouts.app')

@section('content')
<div class="student-profile-container">
    <div class="student-profile-content">
        <h1 class="student-profile-title">My Profile</h1>
        
        <div id="student-profile-loading" class="student-profile-loading hidden">Loading...</div>
        <div id="student-profile-error" class="student-profile-error hidden"></div>
        
        <div class="student-profile-card">
            <div class="student-profile-header">
                <h2 class="student-profile-subtitle">Personal Information</h2>
                <button id="student-profile-edit-btn" class="student-profile-edit-btn">Edit Profile</button>
            </div>
            <div class="student-profile-details">
                <div class="student-profile-picture-container">
                    <img id="student-profile-picture" src="" alt="Profile Picture" class="student-profile-picture" style="display: none;">
                </div>
                <div class="student-profile-info">
                    <p><strong>Name:</strong> <span id="student-profile-name"></span></p>
                    <p><strong>Email:</strong> <span id="student-profile-email"></span></p>
                    <p><strong>Phone:</strong> <span id="student-profile-phone"></span></p>
                    <p><strong>Address:</strong> <span id="student-profile-address"></span></p>
                    <p><strong>NIC Number:</strong> <span id="student-profile-nic"></span></p>
                    <p><strong>School:</strong> <span id="student-profile-school"></span></p>
                    <p><strong>Grade Level:</strong> <span id="student-profile-grade"></span></p>
                    <p><strong>Learning Style:</strong> <span id="student-profile-learning-style"></span></p>
                    <p><strong>Subjects Interested:</strong> <span id="student-profile-subjects"></span></p>
                    <p><strong>Career Goals:</strong> <span id="student-profile-career-goals"></span></p>
                    <p><strong>Location:</strong> <span id="student-profile-location"></span></p>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div id="student-profile-edit-modal" class="student-profile-modal hidden">
            <div class="student-profile-modal-content">
                <span class="student-profile-modal-close" data-modal="edit">&times;</span>
                <h2>Edit Profile</h2>
                <form id="student-profile-edit-form" enctype="multipart/form-data">
                    <div class="student-profile-form-group">
                        <label for="edit-first-name">First Name</label>
                        <input type="text" id="edit-first-name" name="first_name" class="form-control" required>
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-last-name">Last Name</label>
                        <input type="text" id="edit-last-name" name="last_name" class="form-control" required>
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="text" id="edit-phone" name="phone" class="form-control">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-address">Address</label>
                        <input type="text" id="edit-address" name="address" class="form-control">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-nic-number">NIC Number</label>
                        <input type="text" id="edit-nic-number" name="nic_number" class="form-control">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-profile-picture">Profile Picture</label>
                        <img id="edit-profile-picture-preview" src="" alt="Profile Picture Preview" class="student-profile-picture-preview" style="display: none;">
                        <input type="file" id="edit-profile-picture" name="profile_picture" class="form-control" accept="image/*">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-school">School</label>
                        <input type="text" id="edit-school" name="school" class="form-control">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-grade-level">Grade Level</label>
                        <input type="text" id="edit-grade-level" name="grade_level" class="form-control">
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-learning-style">Learning Style</label>
                        <select id="edit-learning-style" name="learning_style" class="form-select">
                            <option value="">Select</option>
                            <option value="visual">Visual</option>
                            <option value="auditory">Auditory</option>
                            <option value="kinesthetic">Kinesthetic</option>
                        </select>
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-subjects-interested">Subjects Interested</label>
                        <select id="edit-subjects-interested" name="subjects_interested[]" class="form-select" multiple>
                            <option value="Math">Math</option>
                            <option value="Science">Science</option>
                            <option value="Literature">Literature</option>
                            <option value="Art">Art</option>
                        </select>
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-career-goals">Career Goals</label>
                        <textarea id="edit-career-goals" name="career_goals" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="student-profile-form-group">
                        <label for="edit-location">Location</label>
                        <input type="text" id="edit-location" name="location" class="form-control">
                    </div>
                    <button type="submit" class="student-profile-submit-btn">Save Changes</button>
                    <button type="button" class="student-profile-cancel-btn" data-modal="edit">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.student-profile-container {
    min-height: 100vh;
    background: #f9fafb;
    padding: 3rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.student-profile-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.student-profile-title {
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

.student-profile-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.student-profile-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(168, 85, 247, 0.3);
}

.student-profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.student-profile-subtitle {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1f2937;
}

.student-profile-edit-btn {
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

.student-profile-edit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.student-profile-loading {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #a855f7;
    border-radius: 10px;
    margin: 1rem 0;
    animation: student-profile-pulse 1.5s infinite ease-in-out;
}

.student-profile-error {
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

.student-profile-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.student-profile-picture-container {
    text-align: center;
    margin-bottom: 1rem;
}

.student-profile-picture {
    width: 150px;
    height: 150px;
    object-fit: contain;
    border-radius: 50%;
    background: #f1f1f1;
    padding: 0.5rem;
}

.student-profile-info p {
    font-size: 1rem;
    color: #4b5563;
    margin: 0.5rem 0;
}

.student-profile-info p strong {
    color: #1f2937;
    font-weight: 600;
}

.student-profile-modal {
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

.student-profile-modal-content {
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

.student-profile-modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    font-size: 1.8rem;
    color: #6b7280;
    cursor: pointer;
}

.student-profile-form-group {
    margin-bottom: 1.5rem;
}

.student-profile-form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.student-profile-form-group input,
.student-profile-form-group textarea,
.student-profile-form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1f2937;
    transition: all 0.3s ease;
}

.student-profile-form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.student-profile-form-group select[multiple] {
    min-height: 100px;
}

.student-profile-form-group input:focus,
.student-profile-form-group textarea:focus,
.student-profile-form-group select:focus {
    outline: none;
    border-color: #a855f7;
    box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);
}

.student-profile-picture-preview {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    border-radius: 10px;
    margin-bottom: 1rem;
    background: #f1f1f1;
    padding: 0.5rem;
}

.student-profile-submit-btn,
.student-profile-cancel-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.student-profile-submit-btn {
    background: #a855f7;
    color: #ffffff;
}

.student-profile-submit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
}

.student-profile-cancel-btn {
    background: #d1d5db;
    color: #1f2937;
    margin-left: 1rem;
}

.student-profile-cancel-btn:hover {
    background: #b9bfc9;
    transform: scale(1.05);
}

@keyframes student-profile-pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .student-profile-title {
        font-size: 2rem;
    }

    .student-profile-subtitle {
        font-size: 1.4rem;
    }

    .student-profile-edit-btn {
        width: 100%;
        max-width: 200px;
    }

    .student-profile-picture {
        width: 120px;
        height: 120px;
    }

    .student-profile-modal-content {
        padding: 1.5rem;
        width: 95%;
    }

    .student-profile-info p {
        font-size: 0.9rem;
    }
}

.hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('student-profile-loading');
    const error = document.getElementById('student-profile-error');
    const editBtn = document.getElementById('student-profile-edit-btn');
    const editModal = document.getElementById('student-profile-edit-modal');
    const editForm = document.getElementById('student-profile-edit-form');

    async function fetchProfile() {
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/student/profile', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            if (response.ok && result.success) {
                const data = result.data;
                document.getElementById('student-profile-name').textContent = `${data.first_name || 'N/A'} ${data.last_name || ''}`;
                document.getElementById('student-profile-email').textContent = data.email || 'N/A';
                document.getElementById('student-profile-phone').textContent = data.phone || 'N/A';
                document.getElementById('student-profile-address').textContent = data.address || 'N/A';
                document.getElementById('student-profile-nic').textContent = data.nic_number || 'N/A';
                document.getElementById('student-profile-school').textContent = data.school || 'N/A';
                document.getElementById('student-profile-grade').textContent = data.grade_level || 'N/A';
                document.getElementById('student-profile-learning-style').textContent = data.learning_style || 'N/A';
                document.getElementById('student-profile-subjects').textContent = data.subjects_interested?.length ? data.subjects_interested.join(', ') : 'N/A';
                document.getElementById('student-profile-career-goals').textContent = data.career_goals || 'N/A';
                document.getElementById('student-profile-location').textContent = data.location || 'N/A';

                const profilePicture = document.getElementById('student-profile-picture');
                if (data.profile_picture) {
                    profilePicture.src = data.profile_picture;
                    profilePicture.style.display = 'block';
                } else {
                    profilePicture.style.display = 'none';
                }
            } else {
                error.textContent = result.message || 'Failed to load profile. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch profile. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    function openEditModal() {
        editModal.style.display = 'flex';
        editModal.classList.remove('hidden');
        editModal.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Populate form with current profile data
        fetch('/api/student/profile', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                document.getElementById('edit-first-name').value = data.first_name || '';
                document.getElementById('edit-last-name').value = data.last_name || '';
                document.getElementById('edit-phone').value = data.phone || '';
                document.getElementById('edit-address').value = data.address || '';
                document.getElementById('edit-nic-number').value = data.nic_number || '';
                document.getElementById('edit-school').value = data.school || '';
                document.getElementById('edit-grade-level').value = data.grade_level || '';
                document.getElementById('edit-learning-style').value = data.learning_style || '';
                document.getElementById('edit-career-goals').value = data.career_goals || '';
                document.getElementById('edit-location').value = data.location || '';

                // Populate multi-select fields
                const subjectsSelect = document.getElementById('edit-subjects-interested');
                Array.from(subjectsSelect.options).forEach(option => {
                    option.selected = data.subjects_interested?.includes(option.value);
                });

                // Show profile picture preview
                const preview = document.getElementById('edit-profile-picture-preview');
                if (data.profile_picture) {
                    preview.src = data.profile_picture;
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            }
        })
        .catch(err => {
            console.error('Error populating form:', err);
            error.textContent = 'Unable to load profile data for editing.';
            error.classList.remove('hidden');
        });
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const formData = new FormData(editForm);
        const submitBtn = editForm.querySelector('.student-profile-submit-btn');
        submitBtn.disabled = true;

        try {
            const response = await fetch('/api/student/profile', {
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
                editModal.classList.add('hidden');
                editModal.style.display = 'none';
                await fetchProfile();
                alert(result.message);
            } else {
                error.textContent = result.message || 'Failed to update profile.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Form error:', err);
            error.textContent = 'Unable to save profile. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    }

    // Event Listeners
    editBtn.addEventListener('click', openEditModal);

    document.querySelector('.student-profile-modal-close').addEventListener('click', () => {
        editModal.classList.add('hidden');
        editModal.style.display = 'none';
    });

    editForm.addEventListener('submit', handleFormSubmit);

    // Profile picture preview
    document.getElementById('edit-profile-picture').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('edit-profile-picture-preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });

    fetchProfile();
});
</script>
@endsection
