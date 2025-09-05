@extends('layouts.app')

@section('content')
<div class="mentor-profile-container">
    <div class="mentor-profile-content">
        <h1 class="mentor-profile-title">My Profile</h1>
        
        <div id="mentor-profile-loading" class="mentor-profile-loading hidden">Loading...</div>
        <div id="mentor-profile-error" class="mentor-profile-error hidden"></div>
        
        <div class="mentor-profile-card">
            <div class="mentor-profile-header">
                <h2 class="mentor-profile-subtitle">Personal Information</h2>
                <button id="mentor-profile-edit-btn" class="mentor-profile-edit-btn">Edit Profile</button>
            </div>
            <div class="mentor-profile-details">
                <div class="mentor-profile-picture-container">
                    <img id="mentor-profile-picture" src="" alt="Profile Picture" class="mentor-profile-picture" style="display: none;">
                </div>
                <div class="mentor-profile-info">
                    <p><strong>Name:</strong> <span id="mentor-profile-name"></span></p>
                    <p><strong>Email:</strong> <span id="mentor-profile-email"></span></p>
                    <p><strong>Phone:</strong> <span id="mentor-profile-phone"></span></p>
                    <p><strong>Address:</strong> <span id="mentor-profile-address"></span></p>
                    <p><strong>NIC Number:</strong> <span id="mentor-profile-nic"></span></p>
                    <p><strong>Profession Title:</strong> <span id="mentor-profile-profession"></span></p>
                    <p><strong>Industry:</strong> <span id="mentor-profile-industry"></span></p>
                    <p><strong>Years of Experience:</strong> <span id="mentor-profile-experience"></span></p>
                    <p><strong>Bio:</strong> <span id="mentor-profile-bio"></span></p>
                    <p><strong>Areas of Expertise:</strong> <span id="mentor-profile-expertise"></span></p>
                    <p><strong>LinkedIn URL:</strong> <a id="mentor-profile-linkedin" href="#" target="_blank"></a></p>
                    <p><strong>Portfolio URL:</strong> <a id="mentor-profile-portfolio" href="#" target="_blank"></a></p>
                    <p><strong>Availability:</strong> <span id="mentor-profile-availability"></span></p>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div id="mentor-profile-edit-modal" class="mentor-profile-modal hidden">
            <div class="mentor-profile-modal-content">
                <span class="mentor-profile-modal-close" data-modal="edit">&times;</span>
                <h2>Edit Profile</h2>
                <form id="mentor-profile-edit-form" enctype="multipart/form-data">
                    <div class="mentor-profile-form-group">
                        <label for="edit-first-name">First Name</label>
                        <input type="text" id="edit-first-name" name="first_name" class="form-control" required>
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-last-name">Last Name</label>
                        <input type="text" id="edit-last-name" name="last_name" class="form-control" required>
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-phone">Phone</label>
                        <input type="text" id="edit-phone" name="phone" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-address">Address</label>
                        <input type="text" id="edit-address" name="address" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-nic-number">NIC Number</label>
                        <input type="text" id="edit-nic-number" name="nic_number" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-profile-picture">Profile Picture</label>
                        <img id="edit-profile-picture-preview" src="" alt="Profile Picture Preview" class="mentor-profile-picture-preview" style="display: none;">
                        <input type="file" id="edit-profile-picture" name="profile_picture" class="form-control" accept="image/*">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-profession-title">Profession Title</label>
                        <input type="text" id="edit-profession-title" name="profession_title" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-industry">Industry</label>
                        <input type="text" id="edit-industry" name="industry" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-experience-years">Years of Experience</label>
                        <input type="number" id="edit-experience-years" name="experience_years" class="form-control" min="0">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-bio">Bio</label>
                        <textarea id="edit-bio" name="bio" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-areas-of-expertise">Areas of Expertise</label>
                        <select id="edit-areas-of-expertise" name="areas_of_expertise[]" class="form-select" multiple>
                            <option value="AI">AI</option>
                            <option value="HR">HR</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Medicine">Medicine</option>
                        </select>
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-linkedin-url">LinkedIn URL</label>
                        <input type="url" id="edit-linkedin-url" name="linkedin_url" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-portfolio-url">Portfolio URL</label>
                        <input type="url" id="edit-portfolio-url" name="portfolio_url" class="form-control">
                    </div>
                    <div class="mentor-profile-form-group">
                        <label for="edit-availability">Availability</label>
                        <input type="text" id="edit-availability" name="availability" class="form-control">
                    </div>
                    <button type="submit" class="mentor-profile-submit-btn">Save Changes</button>
                    <button type="button" class="mentor-profile-cancel-btn" data-modal="edit">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.mentor-profile-container {
    min-height: 100vh;
    background: #f9fafb;
    padding: 3rem 1.5rem;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.mentor-profile-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.mentor-profile-title {
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

.mentor-profile-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.mentor-profile-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 30px rgba(168, 85, 247, 0.3);
}

.mentor-profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.mentor-profile-subtitle {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1f2937;
}

.mentor-profile-edit-btn {
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

.mentor-profile-edit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.mentor-profile-loading {
    text-align: center;
    padding: 2rem;
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 500;
    background: #a855f7;
    border-radius: 10px;
    margin: 1rem 0;
    animation: mentor-profile-pulse 1.5s infinite ease-in-out;
}

.mentor-profile-error {
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

.mentor-profile-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.mentor-profile-picture-container {
    text-align: center;
    margin-bottom: 1rem;
}

.mentor-profile-picture {
    width: 150px;
    height: 150px;
    object-fit: contain;
    border-radius: 50%;
    background: #f1f1f1;
    padding: 0.5rem;
}

.mentor-profile-info p {
    font-size: 1rem;
    color: #4b5563;
    margin: 0.5rem 0;
}

.mentor-profile-info p strong {
    color: #1f2937;
    font-weight: 600;
}

.mentor-profile-info a {
    color: #a855f7;
    text-decoration: none;
}

.mentor-profile-info a:hover {
    text-decoration: underline;
}

.mentor-profile-modal {
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

.mentor-profile-modal-content {
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

.mentor-profile-modal-close {
    position: absolute;
    top: 1rem;
    right: 1.5rem;
    font-size: 1.8rem;
    color: #6b7280;
    cursor: pointer;
}

.mentor-profile-form-group {
    margin-bottom: 1.5rem;
}

.mentor-profile-form-group label {
    display: block;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.mentor-profile-form-group input,
.mentor-profile-form-group textarea,
.mentor-profile-form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1f2937;
    transition: all 0.3s ease;
}

.mentor-profile-form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.mentor-profile-form-group select[multiple] {
    min-height: 100px;
}

.mentor-profile-form-group input:focus,
.mentor-profile-form-group textarea:focus,
.mentor-profile-form-group select:focus {
    outline: none;
    border-color: #a855f7;
    box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);
}

.mentor-profile-picture-preview {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    border-radius: 10px;
    margin-bottom: 1rem;
    background: #f1f1f1;
    padding: 0.5rem;
}

.mentor-profile-submit-btn,
.mentor-profile-cancel-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mentor-profile-submit-btn {
    background: #a855f7;
    color: #ffffff;
}

.mentor-profile-submit-btn:hover {
    background: #9333ea;
    transform: scale(1.05);
}

.mentor-profile-cancel-btn {
    background: #d1d5db;
    color: #1f2937;
    margin-left: 1rem;
}

.mentor-profile-cancel-btn:hover {
    background: #b9bfc9;
    transform: scale(1.05);
}

@keyframes mentor-profile-pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}

@media (max-width: 768px) {
    .mentor-profile-title {
        font-size: 2rem;
    }

    .mentor-profile-subtitle {
        font-size: 1.4rem;
    }

    .mentor-profile-edit-btn {
        width: 100%;
        max-width: 200px;
    }

    .mentor-profile-picture {
        width: 120px;
        height: 120px;
    }

    .mentor-profile-modal-content {
        padding: 1.5rem;
        width: 95%;
    }

    .mentor-profile-info p {
        font-size: 0.9rem;
    }
}

.hidden {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loading = document.getElementById('mentor-profile-loading');
    const error = document.getElementById('mentor-profile-error');
    const editBtn = document.getElementById('mentor-profile-edit-btn');
    const editModal = document.getElementById('mentor-profile-edit-modal');
    const editForm = document.getElementById('mentor-profile-edit-form');

    async function fetchProfile() {
        loading.classList.remove('hidden');
        error.classList.add('hidden');

        try {
            const response = await fetch('/api/mentor/profile', {
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
                document.getElementById('mentor-profile-name').textContent = `${data.first_name || 'N/A'} ${data.last_name || ''}`;
                document.getElementById('mentor-profile-email').textContent = data.email || 'N/A';
                document.getElementById('mentor-profile-phone').textContent = data.phone || 'N/A';
                document.getElementById('mentor-profile-address').textContent = data.address || 'N/A';
                document.getElementById('mentor-profile-nic').textContent = data.nic_number || 'N/A';
                document.getElementById('mentor-profile-profession').textContent = data.profession_title || 'N/A';
                document.getElementById('mentor-profile-industry').textContent = data.industry || 'N/A';
                document.getElementById('mentor-profile-experience').textContent = data.experience_years || '0';
                document.getElementById('mentor-profile-bio').textContent = data.bio || 'N/A';
                document.getElementById('mentor-profile-expertise').textContent = data.areas_of_expertise?.length ? data.areas_of_expertise.join(', ') : 'N/A';
                const linkedinLink = document.getElementById('mentor-profile-linkedin');
                linkedinLink.textContent = data.linkedin_url || 'N/A';
                linkedinLink.href = data.linkedin_url || '#';
                const portfolioLink = document.getElementById('mentor-profile-portfolio');
                portfolioLink.textContent = data.portfolio_url || 'N/A';
                portfolioLink.href = data.portfolio_url || '#';
                document.getElementById('mentor-profile-availability').textContent = data.availability || 'N/A';

                const profilePicture = document.getElementById('mentor-profile-picture');
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
        fetch('/api/mentor/profile', {
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
                document.getElementById('edit-profession-title').value = data.profession_title || '';
                document.getElementById('edit-industry').value = data.industry || '';
                document.getElementById('edit-experience-years').value = data.experience_years || '';
                document.getElementById('edit-bio').value = data.bio || '';
                document.getElementById('edit-linkedin-url').value = data.linkedin_url || '';
                document.getElementById('edit-portfolio-url').value = data.portfolio_url || '';
                document.getElementById('edit-availability').value = data.availability || '';

                // Populate multi-select field
                const expertiseSelect = document.getElementById('edit-areas-of-expertise');
                Array.from(expertiseSelect.options).forEach(option => {
                    option.selected = data.areas_of_expertise?.includes(option.value);
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
        const submitBtn = editForm.querySelector('.mentor-profile-submit-btn');
        submitBtn.disabled = true;

        try {
            const response = await fetch('/api/mentor/profile', {
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

    document.querySelector('.mentor-profile-modal-close').addEventListener('click', () => {
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
