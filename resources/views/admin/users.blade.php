@extends('layouts.app')

@section('content')
<div class="admin-manage-users-container">
    <div class="admin-manage-users-content">
        <h1 class="admin-manage-users-title">User Management Dashboard</h1>
        
        <div class="admin-manage-users-card">
            <div class="admin-manage-users-header">
                <h2 class="admin-manage-users-subtitle">All Users</h2>
                <div class="admin-manage-users-filter">
                    <label for="admin-manage-users-role-filter">Filter by Role:</label>
                    <select id="admin-manage-users-role-filter">
                        <option value="">All</option>
                        <option value="student">Student</option>
                        <option value="mentor">Mentor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div id="admin-manage-users-loading" class="hidden">Loading...</div>
            <div id="admin-manage-users-error" class="hidden"></div>
            
            <div class="admin-manage-users-table-container">
                <table class="admin-manage-users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-manage-users-table-body">
                        <!-- Populated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User Detail Modal -->
        <div id="admin-manage-users-detail-modal" class="admin-manage-users-modal hidden">
            <div class="admin-manage-users-modal-content">
                <span id="admin-manage-users-modal-close" class="admin-manage-users-modal-close">&times;</span>
                <div id="admin-manage-users-modal-body">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded fired for user management');

    const loading = document.getElementById('admin-manage-users-loading');
    const error = document.getElementById('admin-manage-users-error');
    const tableBody = document.getElementById('admin-manage-users-table-body');
    const roleFilter = document.getElementById('admin-manage-users-role-filter');
    const modal = document.getElementById('admin-manage-users-detail-modal');

    if (!roleFilter) {
        console.error('Role filter element not found!');
        error.textContent = 'Role filter dropdown not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    if (!modal) {
        console.error('Detail modal not found!');
        error.textContent = 'Detail modal not found in the DOM.';
        error.classList.remove('hidden');
        return;
    }

    let userData = [];

    async function fetchUsers(role = '') {
        console.log('Fetching users with role:', role);
        loading.classList.remove('hidden');
        error.classList.add('hidden');
        tableBody.innerHTML = '';

        try {
            const url = role ? `/api/admin/users?role=${encodeURIComponent(role)}` : '/api/admin/users';
            console.log('API URL:', url);
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('API Response:', result);

            if (response.ok && result.success) {
                userData = result.data;
                if (userData.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4" class="admin-manage-users-no-data">No users found.</td></tr>';
                } else {
                    userData.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.first_name} ${user.last_name}</td>
                            <td>${user.email}</td>
                            <td>${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</td>
                            <td>
                                <div class="admin-manage-users-actions-wrapper">
                                    <button class="admin-manage-users-action-btn admin-manage-users-view-btn" data-uuid="${user.uuid}" title="View Details"></button>
                                    <button class="admin-manage-users-action-btn admin-manage-users-delete-btn" data-uuid="${user.uuid}" title="Delete"></button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            } else {
                error.textContent = result.message || 'Failed to load users. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            error.textContent = 'Unable to fetch users. Please check your connection or contact support.';
            error.classList.remove('hidden');
        } finally {
            loading.classList.add('hidden');
        }
    }

    window.handleDelete = async function(uuid) {
        if (!confirm(`Are you sure you want to delete this user? This action cannot be undone.`)) return;

        try {
            const response = await fetch(`/api/admin/users/${uuid}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const result = await response.json();
            console.log('Delete Response:', result);

            if (response.ok && result.success) {
                fetchUsers(roleFilter.value);
                alert(result.message);
            } else {
                error.textContent = result.message || 'Failed to delete user. Please try again.';
                error.classList.remove('hidden');
            }
        } catch (err) {
            console.error('Delete error:', err);
            error.textContent = 'Unable to delete user. Please check your connection or contact support.';
            error.classList.remove('hidden');
        }
    };

    // View Details modal open
    function openUserModal(user) {
        const modalBody = document.getElementById('admin-manage-users-modal-body');

        let profileDetails = '';
        if (user.role === 'student' && user.student_profile) {
            const subjects = Array.isArray(user.student_profile?.subjects_interested)
                ? user.student_profile.subjects_interested.join(', ')
                : user.student_profile?.subjects_interested || 'N/A';
            profileDetails = `
                <p><strong>Date of Birth:</strong> ${user.student_profile.date_of_birth || 'N/A'}</p>
                <p><strong>School:</strong> ${user.student_profile.school || 'N/A'}</p>
                <p><strong>Grade Level:</strong> ${user.student_profile.grade_level || 'N/A'}</p>
                <p><strong>Learning Style:</strong> ${user.student_profile.learning_style || 'N/A'}</p>
                <p><strong>Subjects Interested:</strong> ${subjects}</p>
                <p><strong>Career Goals:</strong> ${user.student_profile.career_goals || 'N/A'}</p>
                <p><strong>Location:</strong> ${user.student_profile.location || 'N/A'}</p>
            `;
        } else if (user.role === 'mentor' && user.mentor_profile) {
            const expertise = Array.isArray(user.mentor_profile?.areas_of_expertise)
                ? user.mentor_profile.areas_of_expertise.join(', ')
                : user.mentor_profile?.areas_of_expertise || 'N/A';
            profileDetails = `
                <p><strong>Profession:</strong> ${user.mentor_profile.profession_title || 'N/A'}</p>
                <p><strong>Industry:</strong> ${user.mentor_profile.industry || 'N/A'}</p>
                <p><strong>Experience:</strong> ${user.mentor_profile.experience_years || 'N/A'} years</p>
                <p><strong>Expertise:</strong> ${expertise}</p>
                <p><strong>Bio:</strong> ${user.mentor_profile.bio || 'N/A'}</p>
                <p><strong>LinkedIn:</strong> ${user.mentor_profile.linkedin_url ? `<a href="${user.mentor_profile.linkedin_url}" target="_blank">View Profile</a>` : 'N/A'}</p>
                <p><strong>Portfolio:</strong> ${user.mentor_profile.portfolio_url ? `<a href="${user.mentor_profile.portfolio_url}" target="_blank">View Portfolio</a>` : 'N/A'}</p>
                <p><strong>Availability:</strong> ${user.mentor_profile.availability || 'N/A'}</p>
            `;
        } else {
            profileDetails = '<p>No additional profile details available.</p>';
        }

        modalBody.innerHTML = `
            <h2>${user.first_name} ${user.last_name}</h2>
            <p><strong>Email:</strong> ${user.email}</p>
            <p><strong>Role:</strong> ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</p>
            <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
            <p><strong>Address:</strong> ${user.address || 'N/A'}</p>
            <p><strong>NIC Number:</strong> ${user.nic_number || 'N/A'}</p>
            ${profileDetails}
        `;

        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        modal.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Close modal
    document.getElementById('admin-manage-users-modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });

    // Delegate click event for action buttons
    tableBody.addEventListener('click', function (e) {
        const target = e.target.closest('.admin-manage-users-action-btn');
        if (!target) return;

        const uuid = target.getAttribute('data-uuid');
        const user = userData.find(u => u.uuid === uuid);

        if (target.classList.contains('admin-manage-users-view-btn')) {
            if (user) openUserModal(user);
        } else if (target.classList.contains('admin-manage-users-delete-btn')) {
            handleDelete(uuid);
        }
    });

    // Role filter change
    roleFilter.addEventListener('change', () => {
        console.log('Dropdown changed! Selected value:', roleFilter.value);
        fetchUsers(roleFilter.value);
    });

    fetchUsers(); // Initial load
});
</script>
@endsection
